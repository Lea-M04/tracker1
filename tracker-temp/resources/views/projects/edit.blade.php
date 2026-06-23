<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div>
                <h1 class="h3 mb-1">Edit Project</h1>
                <p class="text-muted mb-0">{{ $project->name }}</p>
            </div>
            <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <div class="fw-semibold mb-2">Please fix the following errors:</div>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('projects.update', $project) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="name" class="form-label">Project Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror">{{ old('description', $project->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="start_date" class="form-label">Start Date</label>
                                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}" class="form-control @error('start_date') is-invalid @enderror">
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="deadline" class="form-label">Deadline</label>
                                        <input type="date" name="deadline" id="deadline" value="{{ old('deadline', $project->deadline?->format('Y-m-d')) }}" class="form-control @error('deadline') is-invalid @enderror">
                                        @error('deadline')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-light">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update Project</button>
                                </div>
                            </form>

                            <hr>

                            <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Delete this project? This will also delete its issues.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">Delete Project</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
