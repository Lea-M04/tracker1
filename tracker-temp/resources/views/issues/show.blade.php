<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h1 class="h3 mb-1">{{ $issue->title }}</h1>
                <p class="text-muted mb-0">Issue details, project information, tags, and comments.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('issues.edit', $issue) }}" class="btn btn-outline-secondary">Edit</a>
                <a href="{{ route('issues.index') }}" class="btn btn-primary">Issues</a>
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
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge text-bg-{{ $issue->status === 'closed' ? 'success' : ($issue->status === 'in_progress' ? 'warning' : 'primary') }}">
                                    {{ str_replace('_', ' ', ucfirst($issue->status)) }}
                                </span>
                                <span class="badge text-bg-{{ $issue->priority === 'high' ? 'danger' : ($issue->priority === 'medium' ? 'secondary' : 'light') }}">
                                    {{ ucfirst($issue->priority) }} priority
                                </span>
                            </div>
                            <h2 class="h5">Description</h2>
                            <p class="mb-0">{{ $issue->description }}</p>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h2 class="h5 mb-3">Comments</h2>

                            <div id="comment-alert" class="alert d-none" role="alert"></div>

                            <form id="comment-form" class="mb-4">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="body" class="form-label">Comment</label>
                                        <textarea name="body" id="body" rows="3" class="form-control" required></textarea>
                                        <div id="body-error" class="text-danger small mt-1"></div>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">Add Comment</button>
                                    </div>
                                </div>
                            </form>

                            <div id="comments-list" class="list-group list-group-flush"></div>

                            <div class="d-flex justify-content-between align-items-center gap-3 mt-3">
                                <button type="button" id="comments-prev" class="btn btn-outline-secondary btn-sm" disabled>Previous</button>
                                <span id="comments-page-info" class="small text-muted"></span>
                                <button type="button" id="comments-next" class="btn btn-outline-secondary btn-sm" disabled>Next</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h2 class="h5 mb-3">Project Information</h2>
                            <dl class="row mb-0">
                                <dt class="col-5 text-muted">Project</dt>
                                <dd class="col-7">
                                    <a href="{{ route('projects.show', $issue->project) }}">{{ $issue->project->name }}</a>
                                </dd>
                                <dt class="col-5 text-muted">Due Date</dt>
                                <dd class="col-7">{{ $issue->due_date?->format('M d, Y') ?? '-' }}</dd>
                                <dt class="col-5 text-muted">Created</dt>
                                <dd class="col-7">{{ $issue->created_at->format('M d, Y') }}</dd>
                            </dl>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h2 class="h5 mb-3">Tags</h2>

                            <div id="tag-alert" class="alert d-none" role="alert"></div>

                            <form id="attach-tag-form" class="mb-3">
                                <label for="tag_id" class="form-label">Attach Tag</label>
                                <div class="input-group">
                                    <select name="tag_id" id="tag_id" class="form-select" required>
                                        <option value="">Choose tag</option>
                                        @foreach ($tags as $tag)
                                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-primary">Attach</button>
                                </div>
                                <div id="tag-error" class="text-danger small mt-1"></div>
                            </form>

                            <div id="issue-tags-list" class="d-flex flex-wrap gap-2">
                                @forelse ($issue->tags as $tag)
                                    <span class="badge d-inline-flex align-items-center gap-2 py-2" data-tag-id="{{ $tag->id }}" style="background-color: {{ $tag->color ?: '#6c757d' }}">
                                        <span>{{ $tag->name }}</span>
                                        <button type="button" class="btn-close btn-close-white btn-sm detach-tag-button" aria-label="Detach {{ $tag->name }}" data-tag-id="{{ $tag->id }}"></button>
                                    </span>
                                @empty
                                    <span class="text-muted" data-empty-tags>No tags attached.</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('attach-tag-form');
            const select = document.getElementById('tag_id');
            const tagList = document.getElementById('issue-tags-list');
            const tagError = document.getElementById('tag-error');
            const tagAlert = document.getElementById('tag-alert');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const attachUrl = @json(route('issues.tags.attach', $issue));
            const detachUrlTemplate = @json(route('issues.tags.detach', ['issue' => $issue, 'tag' => '__TAG_ID__']));
            const commentsUrl = @json(route('issues.comments.index', $issue));
            const storeCommentUrl = @json(route('issues.comments.store', $issue));
            const commentForm = document.getElementById('comment-form');
            const commentsList = document.getElementById('comments-list');
            const commentAlert = document.getElementById('comment-alert');
            const bodyInput = document.getElementById('body');
            const bodyError = document.getElementById('body-error');
            const commentsPrev = document.getElementById('comments-prev');
            const commentsNext = document.getElementById('comments-next');
            const commentsPageInfo = document.getElementById('comments-page-info');
            let nextCommentsUrl = null;
            let prevCommentsUrl = null;

            const showMessage = (message, type = 'success') => {
                tagAlert.textContent = message;
                tagAlert.className = `alert alert-${type}`;
            };

            const clearMessage = () => {
                tagAlert.textContent = '';
                tagAlert.className = 'alert d-none';
                tagError.textContent = '';
            };

            const renderTags = (tags) => {
                tagList.innerHTML = '';

                if (!tags.length) {
                    tagList.innerHTML = '<span class="text-muted" data-empty-tags>No tags attached.</span>';
                    return;
                }

                tags.forEach((tag) => {
                    const badge = document.createElement('span');
                    badge.className = 'badge d-inline-flex align-items-center gap-2 py-2';
                    badge.dataset.tagId = tag.id;
                    badge.style.backgroundColor = tag.color;

                    const name = document.createElement('span');
                    name.textContent = tag.name;

                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn-close btn-close-white btn-sm detach-tag-button';
                    button.dataset.tagId = tag.id;
                    button.setAttribute('aria-label', `Detach ${tag.name}`);

                    badge.append(name, button);
                    tagList.appendChild(badge);
                });
            };

            const handleJsonResponse = async (response) => {
                const data = await response.json();

                if (!response.ok) {
                    const firstError = data.errors ? Object.values(data.errors).flat()[0] : data.message;
                    throw new Error(firstError || 'Something went wrong.');
                }

                return data;
            };

            const showCommentMessage = (message, type = 'success') => {
                commentAlert.textContent = message;
                commentAlert.className = `alert alert-${type}`;
            };

            const clearCommentErrors = () => {
                bodyError.textContent = '';
                commentAlert.textContent = '';
                commentAlert.className = 'alert d-none';
            };

            const renderComment = (comment) => {
                const item = document.createElement('div');
                item.className = 'list-group-item px-0';
                item.dataset.commentId = comment.id;

                const header = document.createElement('div');
                header.className = 'd-flex justify-content-between gap-3';

                const author = document.createElement('strong');
                author.textContent = comment.author_name;

                const date = document.createElement('span');
                date.className = 'small text-muted';
                date.textContent = comment.created_at_human;

                const body = document.createElement('p');
                body.className = 'mb-0 mt-1';
                body.textContent = comment.body;

                header.append(author, date);
                item.append(header, body);

                return item;
            };

            const renderComments = (comments) => {
                commentsList.innerHTML = '';

                if (!comments.length) {
                    commentsList.innerHTML = '<div class="text-muted py-3">No comments yet.</div>';
                    return;
                }

                comments.forEach((comment) => {
                    commentsList.appendChild(renderComment(comment));
                });
            };

            const updateCommentsPagination = (pagination) => {
                nextCommentsUrl = pagination.next_page_url;
                prevCommentsUrl = pagination.prev_page_url;
                commentsPrev.disabled = !prevCommentsUrl;
                commentsNext.disabled = !nextCommentsUrl;
                commentsPageInfo.textContent = `Page ${pagination.current_page} of ${pagination.last_page}`;
            };

            const loadComments = async (url = commentsUrl) => {
                try {
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                        },
                    });
                    const data = await handleJsonResponse(response);
                    renderComments(data.comments);
                    updateCommentsPagination(data.pagination);
                } catch (error) {
                    showCommentMessage(error.message, 'danger');
                }
            };

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                clearMessage();

                try {
                    const response = await fetch(attachUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            tag_id: select.value,
                        }),
                    });

                    const data = await handleJsonResponse(response);
                    renderTags(data.tags);
                    select.value = '';
                    showMessage(data.message);
                } catch (error) {
                    tagError.textContent = error.message;
                    showMessage('Could not attach tag.', 'danger');
                }
            });

            tagList.addEventListener('click', async (event) => {
                const button = event.target.closest('.detach-tag-button');

                if (!button) {
                    return;
                }

                clearMessage();

                try {
                    const response = await fetch(detachUrlTemplate.replace('__TAG_ID__', button.dataset.tagId), {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                    });

                    const data = await handleJsonResponse(response);
                    renderTags(data.tags);
                    showMessage(data.message);
                } catch (error) {
                    showMessage(error.message, 'danger');
                }
            });

            commentForm.addEventListener('submit', async (event) => {
                event.preventDefault();
                clearCommentErrors();

                try {
                    const response = await fetch(storeCommentUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            body: bodyInput.value,
                        }),
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (data.errors) {
                            bodyError.textContent = data.errors.body ? data.errors.body[0] : '';
                        }

                        throw new Error(data.message || 'Could not create comment.');
                    }

                    const emptyState = commentsList.querySelector('.text-muted');
                    if (emptyState) {
                        commentsList.innerHTML = '';
                    }

                    commentsList.prepend(renderComment(data.comment));
                    bodyInput.value = '';
                    showCommentMessage(data.message);
                } catch (error) {
                    showCommentMessage(error.message, 'danger');
                }
            });

            commentsPrev.addEventListener('click', () => {
                if (prevCommentsUrl) {
                    loadComments(prevCommentsUrl);
                }
            });

            commentsNext.addEventListener('click', () => {
                if (nextCommentsUrl) {
                    loadComments(nextCommentsUrl);
                }
            });

            loadComments();
        });
    </script>
</x-app-layout>
