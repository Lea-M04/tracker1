<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class IssueController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:open,in_progress,closed'],
            'priority' => ['nullable', 'in:low,medium,high'],
            'tag' => ['nullable', 'integer', 'exists:tags,id'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $issues = Issue::query()
            ->with(['project', 'tags'])
            ->withCount('comments')
            ->whereHas('project', fn ($query) => $query->where('user_id', $request->user()->id))
            ->when(filled($filters['search'] ?? null), function ($query) use ($filters) {
                $search = $this->escapeLike(trim($filters['search']));

                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(filled($filters['status'] ?? null), function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->when(filled($filters['priority'] ?? null), function ($query) use ($filters) {
                $query->where('priority', $filters['priority']);
            })
            ->when(filled($filters['tag'] ?? null), function ($query) use ($filters) {
                $query->whereHas('tags', fn ($tagQuery) => $tagQuery->whereKey($filters['tag']));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        if ($request->expectsJson()) {
            return response()->json([
                'issues' => $issues->getCollection()
                    ->map(fn (Issue $issue) => $this->formatIssue($issue))
                    ->values(),
                'pagination' => [
                    'current_page' => $issues->currentPage(),
                    'last_page' => $issues->lastPage(),
                    'next_page_url' => $issues->nextPageUrl(),
                    'prev_page_url' => $issues->previousPageUrl(),
                    'total' => $issues->total(),
                ],
            ]);
        }

        $tags = Tag::query()
            ->whereHas('issues.project', fn ($query) => $query->where('user_id', $request->user()->id))
            ->orderBy('name')
            ->get();

        return view('issues.index', [
            'issues' => $issues,
            'tags' => $tags,
            'filters' => $request->only(['search', 'status', 'priority', 'tag']),
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
        $this->authorize('update', $this->userProject((int) $request->validated('project_id')));

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
        $this->authorize('view', $issue);

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
        $this->authorize('update', $issue);

        $issue->load(['project', 'tags']);

        return view('issues.edit', [
            'issue' => $issue,
            'projects' => $this->userProjects($request),
            'tags' => Tag::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateIssueRequest $request, Issue $issue): RedirectResponse
    {
        $this->authorize('update', $issue);
        $this->authorize('update', $this->userProject((int) $request->validated('project_id')));

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
        $this->authorize('delete', $issue);

        $issue->delete();

        return redirect()
            ->route('issues.index')
            ->with('success', 'Issue deleted successfully.');
    }

    public function attachTag(Request $request, Issue $issue): JsonResponse
    {
        $this->authorize('update', $issue);

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
        $this->authorize('update', $issue);

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

    private function userProject(int $projectId): Project
    {
        return Project::query()
            ->whereKey($projectId)
            ->where('user_id', auth()->id())
            ->firstOrFail();
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

    private function formatIssue(Issue $issue): array
    {
        return [
            'id' => $issue->id,
            'title' => $issue->title,
            'description' => $issue->description,
            'project' => $issue->project->name,
            'status' => $issue->status,
            'status_label' => str_replace('_', ' ', ucfirst($issue->status)),
            'priority' => $issue->priority,
            'priority_label' => ucfirst($issue->priority),
            'comments_count' => $issue->comments_count,
            'tags' => $this->formatTags($issue),
            'urls' => [
                'show' => route('issues.show', $issue),
                'edit' => route('issues.edit', $issue),
                'delete' => route('issues.destroy', $issue),
            ],
        ];
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
