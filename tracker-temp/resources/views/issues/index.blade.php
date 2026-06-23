<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h1 class="h3 mb-1">Issues</h1>
                <p class="text-muted mb-0">Track, filter, and review project issues.</p>
            </div>
            <a href="{{ route('issues.create') }}" class="btn btn-primary">Create Issue</a>
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

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('issues.index') }}" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">All statuses</option>
                                <option value="open" @selected(($filters['status'] ?? '') === 'open')>Open</option>
                                <option value="in_progress" @selected(($filters['status'] ?? '') === 'in_progress')>In Progress</option>
                                <option value="closed" @selected(($filters['status'] ?? '') === 'closed')>Closed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="priority" class="form-label">Priority</label>
                            <select name="priority" id="priority" class="form-select">
                                <option value="">All priorities</option>
                                <option value="low" @selected(($filters['priority'] ?? '') === 'low')>Low</option>
                                <option value="medium" @selected(($filters['priority'] ?? '') === 'medium')>Medium</option>
                                <option value="high" @selected(($filters['priority'] ?? '') === 'high')>High</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="tag" class="form-label">Tag</label>
                            <select name="tag" id="tag" class="form-select">
                                <option value="">All tags</option>
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}" @selected((string) ($filters['tag'] ?? '') === (string) $tag->id)>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                            <a href="{{ route('issues.index') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Issue</th>
                                    <th>Project</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Tags</th>
                                    <th>Comments</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($issues as $issue)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $issue->title }}</div>
                                            <div class="small text-muted">{{ Str::limit($issue->description, 70) }}</div>
                                        </td>
                                        <td>{{ $issue->project->name }}</td>
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
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @forelse ($issue->tags as $tag)
                                                    <span class="badge" style="background-color: {{ $tag->color ?: '#6c757d' }}">{{ $tag->name }}</span>
                                                @empty
                                                    <span class="text-muted small">No tags</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td>{{ $issue->comments_count }}</td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('issues.show', $issue) }}" class="btn btn-sm btn-outline-primary">View</a>
                                                <a href="{{ route('issues.edit', $issue) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                                <form action="{{ route('issues.destroy', $issue) }}" method="POST" onsubmit="return confirm('Delete this issue?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <h2 class="h5">No issues found</h2>
                                            <p class="text-muted mb-3">Create an issue or adjust your filters.</p>
                                            <a href="{{ route('issues.create') }}" class="btn btn-primary">Create Issue</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                {{ $issues->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
