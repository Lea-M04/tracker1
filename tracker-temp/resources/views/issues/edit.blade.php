<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div>
                <h1 class="h3 mb-1">Edit Issue</h1>
                <p class="text-muted mb-0">{{ $issue->title }}</p>
            </div>
            <a href="{{ route('issues.show', $issue) }}" class="btn btn-outline-secondary">Back</a>
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

                            <form action="{{ route('issues.update', $issue) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="project_id" class="form-label">Project</label>
                                    <select name="project_id" id="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}" @selected(old('project_id', $issue->project_id) == $project->id)>{{ $project->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('project_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" name="title" id="title" value="{{ old('title', $issue->title) }}" class="form-control @error('title') is-invalid @enderror" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $issue->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                            <option value="open" @selected(old('status', $issue->status) === 'open')>Open</option>
                                            <option value="in_progress" @selected(old('status', $issue->status) === 'in_progress')>In Progress</option>
                                            <option value="closed" @selected(old('status', $issue->status) === 'closed')>Closed</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="priority" class="form-label">Priority</label>
                                        <select name="priority" id="priority" class="form-select @error('priority') is-invalid @enderror" required>
                                            <option value="low" @selected(old('priority', $issue->priority) === 'low')>Low</option>
                                            <option value="medium" @selected(old('priority', $issue->priority) === 'medium')>Medium</option>
                                            <option value="high" @selected(old('priority', $issue->priority) === 'high')>High</option>
                                        </select>
                                        @error('priority')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="due_date" class="form-label">Due Date</label>
                                        <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $issue->due_date?->format('Y-m-d')) }}" class="form-control @error('due_date') is-invalid @enderror">
                                        @error('due_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Tags</label>
                                    @php
                                        $selectedTags = old('tag_ids', $issue->tags->pluck('id')->all());
                                    @endphp
                                    <div class="d-flex flex-wrap gap-3">
                                        @foreach ($tags as $tag)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="tag_ids[]" value="{{ $tag->id }}" id="tag_{{ $tag->id }}" @checked(in_array($tag->id, $selectedTags))>
                                                <label class="form-check-label" for="tag_{{ $tag->id }}">{{ $tag->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between gap-2">
                                    <a href="{{ route('issues.show', $issue) }}" class="btn btn-light">Cancel</a>
                                    <button type="submit" class="btn btn-primary">Update Issue</button>
                                </div>
                            </form>

                            <hr>

                            <form action="{{ route('issues.destroy', $issue) }}" method="POST" onsubmit="return confirm('Delete this issue?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">Delete Issue</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
