<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $userId = auth()->id();

        $projects = Project::query()
            ->with('issues')
            ->where('user_id', $userId)
            ->get();

        $issues = $projects->flatMap->issues;
        $tags = Tag::query()
            ->whereHas('issues.project', fn ($query) => $query->where('user_id', $userId))
            ->orderBy('name')
            ->get();

        return view('dashboard', [
            'totalProjects' => $projects->count(),
            'totalIssues' => $issues->count(),
            'openIssues' => $issues->where('status', 'open')->count(),
            'inProgressIssues' => $issues->where('status', 'in_progress')->count(),
            'closedIssues' => $issues->where('status', 'closed')->count(),
            'totalTags' => $tags->count(),
            'tags' => $tags,
            'recentIssues' => Issue::query()
                ->with(['project', 'tags'])
                ->withCount('comments')
                ->whereHas('project', fn ($query) => $query->where('user_id', $userId))
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }
}
