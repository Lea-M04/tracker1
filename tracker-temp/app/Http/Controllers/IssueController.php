<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class IssueController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $issues = Issue::query()
            ->with(['project', 'tags'])
            ->withCount('comments')
            ->whereHas('project', fn ($query) => $query->where('user_id', $request->user()->id))
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->string('status'));
            })
            ->when($request->filled('priority'), function ($query) use ($request) {
                $query->where('priority', $request->string('priority'));
            })
            ->when($request->filled('tag'), function ($query) use ($request) {
                $query->whereHas('tags', fn ($tagQuery) => $tagQuery->whereKey($request->integer('tag')));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $tags = Tag::query()
            ->whereHas('issues.project', fn ($query) => $query->where('user_id', $request->user()->id))
            ->orderBy('name')
            ->get();

        return view('issues.index', [
            'issues' => $issues,
            'tags' => $tags,
            'filters' => $request->only(['status', 'priority', 'tag']),
        ]);
    }

    public function create(Request $request): View
    {
        return view('issues.create', [
            'projects' => $this->userProjects($request),
            'tags' => Tag::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreIssueRequest $request): RedirectResponse
    {
        $this->authorizeProjectAccess((int) $request->validated('project_id'));

        $validated = $request->validated();
        $tagIds = $validated['tag_ids'] ?? [];
        unset($validated['tag_ids']);

        $issue = Issue::query()->create($validated);
        $issue->tags()->sync($tagIds);

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'Issue created successfully.');
    }

    public function show(Issue $issue): View
    {
        $this->authorizeIssueAccess($issue);

        $issue->load([
            'project',
            'tags',
            'comments' => fn ($query) => $query->latest(),
        ]);

        $tags = Tag::query()
            ->orderBy('name')
            ->get();

        return view('issues.show', compact('issue', 'tags'));
    }

    public function edit(Request $request, Issue $issue): View
    {
        $this->authorizeIssueAccess($issue);

        $issue->load(['project', 'tags']);

        return view('issues.edit', [
            'issue' => $issue,
            'projects' => $this->userProjects($request),
            'tags' => Tag::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateIssueRequest $request, Issue $issue): RedirectResponse
    {
        $this->authorizeIssueAccess($issue);
        $this->authorizeProjectAccess((int) $request->validated('project_id'));

        $validated = $request->validated();
        $tagIds = $validated['tag_ids'] ?? [];
        unset($validated['tag_ids']);

        $issue->update($validated);
        $issue->tags()->sync($tagIds);

        return redirect()
            ->route('issues.show', $issue)
            ->with('success', 'Issue updated successfully.');
    }

    public function destroy(Issue $issue): RedirectResponse
    {
        $this->authorizeIssueAccess($issue);

        $issue->delete();

        return redirect()
            ->route('issues.index')
            ->with('success', 'Issue deleted successfully.');
    }

    public function attachTag(Request $request, Issue $issue): JsonResponse
    {
        $this->authorizeIssueAccess($issue);

        $validated = $request->validate([
            'tag_id' => ['required', 'exists:tags,id'],
        ]);

        if ($issue->tags()->whereKey($validated['tag_id'])->exists()) {
            throw ValidationException::withMessages([
                'tag_id' => 'This tag is already attached to the issue.',
            ]);
        }

        $issue->tags()->attach($validated['tag_id']);
        $issue->load('tags');

        return response()->json([
            'message' => 'Tag attached successfully.',
            'tags' => $this->formatTags($issue),
        ]);
    }

    public function detachTag(Issue $issue, Tag $tag): JsonResponse
    {
        $this->authorizeIssueAccess($issue);

        $issue->tags()->detach($tag->id);
        $issue->load('tags');

        return response()->json([
            'message' => 'Tag detached successfully.',
            'tags' => $this->formatTags($issue),
        ]);
    }

    private function userProjects(Request $request)
    {
        return Project::query()
            ->where('user_id', $request->user()->id)
            ->orderBy('name')
            ->get();
    }

    private function authorizeIssueAccess(Issue $issue): void
    {
        $issue->loadMissing('project');

        abort_unless($issue->project->user_id === auth()->id(), 403);
    }

    private function authorizeProjectAccess(int $projectId): void
    {
        abort_unless(
            Project::query()
                ->whereKey($projectId)
                ->where('user_id', auth()->id())
                ->exists(),
            403
        );
    }

    private function formatTags(Issue $issue): array
    {
        return $issue->tags
            ->sortBy('name')
            ->values()
            ->map(fn (Tag $tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
                'color' => $tag->color ?: '#6c757d',
            ])
            ->all();
    }
}
