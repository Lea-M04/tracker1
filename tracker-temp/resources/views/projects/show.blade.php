<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h1 class="h3 mb-1">{{ $project->name }}</h1>
                <p class="text-muted mb-0">Project details and related issues.</p>
            </div>
            <div class="d-flex gap-2">
                @can('update', $project)
                    <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-secondary">Edit</a>
                @endcan
                <a href="{{ route('projects.index') }}" class="btn btn-primary">Projects</a>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h2 class="h5 mb-3">Project Details</h2>
                            <dl class="row mb-0">
                                <dt class="col-5 text-muted">Owner</dt>
                                <dd class="col-7">{{ $project->user->name }}</dd>

                                <dt class="col-5 text-muted">Start Date</dt>
                                <dd class="col-7">{{ $project->start_date?->format('M d, Y') ?? '-' }}</dd>

                                <dt class="col-5 text-muted">Deadline</dt>
                                <dd class="col-7">{{ $project->deadline?->format('M d, Y') ?? '-' }}</dd>

                                <dt class="col-5 text-muted">Issues</dt>
                                <dd class="col-7">{{ $project->issues->count() }}</dd>
                            </dl>

                            <hr>

                            <h3 class="h6 text-muted">Description</h3>
                            <p class="mb-0">{{ $project->description ?: 'No description provided.' }}</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                                <h2 class="h5 mb-0">Related Issues</h2>
                                <span class="badge text-bg-secondary">{{ $project->issues->count() }}</span>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                            <th>Due Date</th>
                                            <th>Tags</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($project->issues as $issue)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">{{ $issue->title }}</div>
                                                    <div class="small text-muted">{{ Str::limit($issue->description, 70) }}</div>
                                                </td>
                                                <td>
                                                    <span class="badge text-bg-{{ $issue->status === 'closed' ? 'success' : ($issue->status === 'in_progress' ? 'warning' : 'primary') }}">
                                                        {{ str_replace('_', ' ', ucfirst($issue->status)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge text-bg-{{ $issue->priority === 'high' ? 'danger' : ($issue->priority === 'medium' ? 'secondary' : 'light') }}">
                                                        {{ ucfirst($issue->priority) }}
                                                    </span>
                                                </td>
                                                <td>{{ $issue->due_date?->format('M d, Y') ?? '-' }}</td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @forelse ($issue->tags as $tag)
                                                            <span class="badge" style="background-color: {{ $tag->color ?: '#6c757d' }};">
                                                                {{ $tag->name }}
                                                            </span>
                                                        @empty
                                                            <span class="text-muted small">No tags</span>
                                                        @endforelse
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5">
                                                    <h3 class="h6">No issues found</h3>
                                                    <p class="text-muted mb-0">Issues created for this project will appear here.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
