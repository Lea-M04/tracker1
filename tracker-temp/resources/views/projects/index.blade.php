<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h1 class="h3 mb-1">Projects</h1>
                <p class="text-muted mb-0">Manage your issue tracker projects.</p>
            </div>
            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                Create Project
            </a>
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

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Start Date</th>
                                    <th>Deadline</th>
                                    <th>Issues</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($projects as $project)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $project->name }}</div>
                                            @if ($project->description)
                                                <div class="small text-muted">{{ Str::limit($project->description, 80) }}</div>
                                            @endif
                                        </td>
                                        <td>{{ $project->start_date?->format('M d, Y') ?? '-' }}</td>
                                        <td>{{ $project->deadline?->format('M d, Y') ?? '-' }}</td>
                                        <td>
                                            <span class="badge text-bg-secondary">{{ $project->issues_count }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-outline-primary">View</a>
                                                <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                                <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Delete this project? This will also delete its issues.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <h2 class="h5">No projects yet</h2>
                                            <p class="text-muted mb-3">Create your first project to start tracking issues.</p>
                                            <a href="{{ route('projects.create') }}" class="btn btn-primary">Create Project</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                {{ $projects->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
