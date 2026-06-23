<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $projects = auth()->user()
            ->projects()
            ->withCount('issues')
            ->latest()
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }

    public function create(): View
    {
        return view('projects.create');
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $project = $request->user()
            ->projects()
            ->create($request->validated());

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project): View
    {
        $this->authorizeProjectOwner($project);

        $project->load([
            'user',
            'issues' => fn ($query) => $query->latest(),
            'issues.tags',
            'issues.comments',
        ]);

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        $this->authorizeProjectOwner($project);

        return view('projects.edit', compact('project'));
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->authorizeProjectOwner($project);

        $project->update($request->validated());

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorizeProjectOwner($project);

        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Project deleted successfully.');
    }

    private function authorizeProjectOwner(Project $project): void
    {
        abort_unless($project->user_id === auth()->id(), 403);
    }
}
