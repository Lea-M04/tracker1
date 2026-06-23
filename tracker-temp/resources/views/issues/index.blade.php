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
                    <form id="issue-filter-form" method="GET" action="{{ route('issues.index') }}" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="search" name="search" id="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="Title or description">
                        </div>
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
                            <tbody id="issues-table-body">
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
                                        <td colspan="7" class="text-center py-5" data-empty-row>
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

            <div id="issues-pagination" class="d-flex justify-content-between align-items-center gap-3 mt-4">
                <button type="button" id="issues-prev" class="btn btn-outline-secondary btn-sm" @disabled(!$issues->previousPageUrl())>Previous</button>
                <span id="issues-page-info" class="small text-muted">
                    Page {{ $issues->currentPage() }} of {{ $issues->lastPage() }} - {{ $issues->total() }} total
                </span>
                <button type="button" id="issues-next" class="btn btn-outline-secondary btn-sm" @disabled(!$issues->nextPageUrl())>Next</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('issue-filter-form');
            const searchInput = document.getElementById('search');
            const statusSelect = document.getElementById('status');
            const prioritySelect = document.getElementById('priority');
            const tagSelect = document.getElementById('tag');
            const tableBody = document.getElementById('issues-table-body');
            const prevButton = document.getElementById('issues-prev');
            const nextButton = document.getElementById('issues-next');
            const pageInfo = document.getElementById('issues-page-info');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const baseUrl = @json(route('issues.index'));
            let debounceTimer = null;
            let nextPageUrl = @json($issues->nextPageUrl());
            let prevPageUrl = @json($issues->previousPageUrl());
            let currentPage = @json($issues->currentPage());
            let activeRequest = null;
            let requestSequence = 0;

            const escapeHtml = (value) => String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');

            const truncate = (value, length = 70) => {
                const text = String(value ?? '');
                return text.length > length ? `${text.slice(0, length)}...` : text;
            };

            const statusClass = (status) => {
                if (status === 'closed') return 'success';
                if (status === 'in_progress') return 'warning';
                return 'primary';
            };

            const priorityClass = (priority) => {
                if (priority === 'high') return 'danger';
                if (priority === 'medium') return 'secondary';
                return 'light';
            };

            const buildUrl = (url = baseUrl, options = {}) => {
                const targetUrl = new URL(url, window.location.origin);
                const params = new URLSearchParams();
                const requestedPage = options.page ?? targetUrl.searchParams.get('page') ?? (options.preservePage === false ? null : currentPage);

                if (searchInput.value.trim()) params.set('search', searchInput.value.trim());
                if (statusSelect.value) params.set('status', statusSelect.value);
                if (prioritySelect.value) params.set('priority', prioritySelect.value);
                if (tagSelect.value) params.set('tag', tagSelect.value);

                if (requestedPage && Number(requestedPage) > 1) {
                    params.set('page', requestedPage);
                }

                const query = params.toString();

                return query ? `${baseUrl}?${query}` : baseUrl;
            };

            const renderTags = (tags) => {
                if (!tags.length) {
                    return '<span class="text-muted small">No tags</span>';
                }

                return tags.map((tag) => `
                    <span class="badge" style="background-color: ${escapeHtml(tag.color)}">${escapeHtml(tag.name)}</span>
                `).join('');
            };

            const renderIssues = (issues) => {
                if (!issues.length) {
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <h2 class="h5">No issues found</h2>
                                <p class="text-muted mb-0">Create an issue or adjust your filters.</p>
                            </td>
                        </tr>
                    `;
                    return;
                }

                tableBody.innerHTML = issues.map((issue) => `
                    <tr>
                        <td>
                            <div class="fw-semibold">${escapeHtml(issue.title)}</div>
                            <div class="small text-muted">${escapeHtml(truncate(issue.description))}</div>
                        </td>
                        <td>${escapeHtml(issue.project)}</td>
                        <td>
                            <span class="badge text-bg-${statusClass(issue.status)}">${escapeHtml(issue.status_label)}</span>
                        </td>
                        <td>
                            <span class="badge text-bg-${priorityClass(issue.priority)}">${escapeHtml(issue.priority_label)}</span>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">${renderTags(issue.tags)}</div>
                        </td>
                        <td>${issue.comments_count}</td>
                        <td>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="${issue.urls.show}" class="btn btn-sm btn-outline-primary">View</a>
                                <a href="${issue.urls.edit}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                <form action="${issue.urls.delete}" method="POST" onsubmit="return confirm('Delete this issue?');">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                `).join('');
            };

            const updatePagination = (pagination) => {
                currentPage = pagination.current_page;
                nextPageUrl = pagination.next_page_url;
                prevPageUrl = pagination.prev_page_url;
                prevButton.disabled = !prevPageUrl;
                nextButton.disabled = !nextPageUrl;
                pageInfo.textContent = `Page ${pagination.current_page} of ${pagination.last_page} - ${pagination.total} total`;
            };

            const fetchIssues = async (url = baseUrl, options = {}) => {
                const requestUrl = buildUrl(url, options);
                const sequence = ++requestSequence;

                if (activeRequest) {
                    activeRequest.abort();
                }

                activeRequest = new AbortController();

                try {
                    const response = await fetch(requestUrl, {
                        headers: {
                            'Accept': 'application/json',
                        },
                        signal: activeRequest.signal,
                    });

                    if (!response.ok || sequence !== requestSequence) {
                        return;
                    }

                    const data = await response.json();
                    const requestedPage = Number(new URL(requestUrl, window.location.origin).searchParams.get('page') || 1);

                    if (!data.issues.length && requestedPage > data.pagination.last_page) {
                        fetchIssues(baseUrl, { page: data.pagination.last_page, preservePage: false });
                        return;
                    }

                    renderIssues(data.issues);
                    updatePagination(data.pagination);
                    window.history.replaceState({}, '', buildUrl(baseUrl, { page: data.pagination.current_page, preservePage: false }));
                } catch (error) {
                    if (error.name !== 'AbortError') {
                        console.error('Unable to load issues.', error);
                    }
                }
            };

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                fetchIssues();
            });

            [statusSelect, prioritySelect, tagSelect].forEach((input) => {
                input.addEventListener('change', () => fetchIssues());
            });

            searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => fetchIssues(), 500);
            });

            prevButton.addEventListener('click', () => {
                if (prevPageUrl) fetchIssues(prevPageUrl);
            });

            nextButton.addEventListener('click', () => {
                if (nextPageUrl) fetchIssues(nextPageUrl);
            });
        });
    </script>
</x-app-layout>
