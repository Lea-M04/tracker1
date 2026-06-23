<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="h3 mb-1">Tags</h1>
            <p class="text-muted mb-0">Create and review labels used for issues.</p>
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
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h2 class="h5 mb-3">Create Tag</h2>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('tags.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="color" class="form-label">Color</label>
                                    <input type="color" name="color" id="color" value="{{ old('color', '#6c757d') }}" class="form-control form-control-color @error('color') is-invalid @enderror">
                                    @error('color')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary w-100">Save Tag</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Color</th>
                                            <th>Issues</th>
                                            <th>Created</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($tags as $tag)
                                            <tr>
                                                <td class="fw-semibold">{{ $tag->name }}</td>
                                                <td>
                                                    <span class="badge" style="background-color: {{ $tag->color ?: '#6c757d' }}">
                                                        {{ $tag->color ?: 'default' }}
                                                    </span>
                                                </td>
                                                <td>{{ $tag->issues_count }}</td>
                                                <td>{{ $tag->created_at->format('M d, Y') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-5">
                                                    <h2 class="h5">No tags yet</h2>
                                                    <p class="text-muted mb-0">Create a tag to start organizing issues.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        {{ $tags->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
