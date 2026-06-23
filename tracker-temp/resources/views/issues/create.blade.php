<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div>
                <h1 class="h3 mb-1">Create Issue</h1>
                <p class="text-muted mb-0">Add a new issue to a project.</p>
            </div>
            <a href="{{ route('issues.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
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

                            <form action="{{ route('issues.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="project_id" class="form-label">Project</label>
                                    <select name="project_id" id="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                        <option value="">Choose project</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}" @selected(old('project_id') == $project->id)>{{ $project->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('project_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                            <option value="open" @selected(old('status', 'open') === 'open')>Open</option>
                                            <option value="in_progress" @selected(old('status') === 'in_progress')>In Progress</option>
                                            <option value="closed" @selected(old('status') === 'closed')>Closed</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="priority" class="form-label">Priority</label>
                                        <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                            <option value="low" @selected(old('priority') === 'low')>Low</option>
                                            <option value="medium" @selected(old('priority', 'medium') === 'medium')>Medium</option>
                                            <option value="high" @selected(old('priority') === 'high')>High</option>
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="due_date" class="form-label">Due Date</label>
                                        <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" class="form-control @error('due_date') is-invalid @enderror">
                                        @error('due_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Tags</label>
                                    <div class="d-flex flex-wrap gap-3">
                                        @foreach ($tags as $tag)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" id="tag_{{ $tag->id }}" @checked(in_array($tag->id, old('tag_ids', [])))>
                                                <label class="form-check-label" for="tag_{{ $tag->id }}">{{ $tag->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('tag_ids')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('issues.index') }}" class="btn btn-light">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Save Issue</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
