<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h1 class="h3 mb-1">Dashboard</h1>
                <p class="text-muted mb-0">Overview of projects, issues, and tags.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('projects.create') }}" class="btn btn-outline-primary">Create Project</a>
                <a href="{{ route('issues.create') }}" class="btn btn-primary">Create Issue</a>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small mb-1">Total Projects</div>
                            <div class="display-6 fw-semibold">{{ $totalProjects }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small mb-1">Total Issues</div>
                            <div class="display-6 fw-semibold">{{ $totalIssues }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small mb-1">Open Issues</div>
                            <div class="display-6 fw-semibold text-primary">{{ $openIssues }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small mb-1">In Progress Issues</div>
                            <div class="display-6 fw-semibold text-warning">{{ $inProgressIssues }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small mb-1">Closed Issues</div>
                            <div class="display-6 fw-semibold text-success">{{ $closedIssues }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="text-muted small mb-1">Total Tags</div>
                            <div class="display-6 fw-semibold">{{ $totalTags }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3">
                        <div>
                            <h2 class="h5 mb-1">Issue Search</h2>
                            <p class="text-muted small mb-0">Search by title or description and combine it with status, priority, and tag filters.</p>
                        </div>
                        <a href="{{ route('issues.index') }}" class="btn btn-sm btn-outline-secondary align-self-start align-self-lg-center">View All</a>
                    </div>

                    <form id="dashboard-issue-search-form" class="row g-2 align-items-end mb-3">
                        <div class="col-lg-4">
                            <label for="dashboard-issue-search" class="form-label small text-muted">Search</label>
                            <input type="search" id="dashboard-issue-search" class="form-control" placeholder="Search title or description">
                        </div>
                        <div class="col-sm-6 col-lg-2">
                            <label for="dashboard-issue-status" class="form-label small text-muted">Status</label>
                            <select id="dashboard-issue-status" class="form-select">
                                <option value="">All statuses</option>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-lg-2">
                            <label for="dashboard-issue-priority" class="form-label small text-muted">Priority</label>
                            <select id="dashboard-issue-priority" class="form-select">
                                <option value="">All priorities</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="col-sm-8 col-lg-2">
                            <label for="dashboard-issue-tag" class="form-label small text-muted">Tag</label>
                            <select id="dashboard-issue-tag" class="form-select">
                                <option value="">All tags</option>
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-4 col-lg-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">Search</button>
                            <button type="button" id="dashboard-issue-reset" class="btn btn-outline-secondary">Reset</button>
                        </div>
                    </form>

                    <div id="dashboard-issue-feedback" class="alert alert-danger d-none py-2 small" role="alert">
                        Could not load issues. Please try again.
                    </div>

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
                                </tr>
                            </thead>
                            <tbody id="dashboard-issues-table-body">
                                @forelse ($recentIssues as $issue)
                                    <tr>
                                        <td>
                                            <a href="{{ route('issues.show', $issue) }}" class="fw-semibold text-decoration-none">
                                                {{ $issue->title }}
                                            </a>
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
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <h2 class="h5">No issues yet</h2>
                                            <p class="text-muted mb-0">Create your first issue to populate the dashboard.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div id="dashboard-issues-pagination" class="d-flex justify-content-between align-items-center gap-3 mt-3">
                        <button type="button" id="dashboard-issues-prev" class="btn btn-outline-secondary btn-sm" disabled>Previous</button>
                        <span id="dashboard-issues-page-info" class="small text-muted">
                            Showing latest {{ $recentIssues->count() }} issues
                        </span>
                        <button type="button" id="dashboard-issues-next" class="btn btn-outline-secondary btn-sm" disabled>Next</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('dashboard-issue-search-form');
            const searchInput = document.getElementById('dashboard-issue-search');
            const statusSelect = document.getElementById('dashboard-issue-status');
            const prioritySelect = document.getElementById('dashboard-issue-priority');
            const tagSelect = document.getElementById('dashboard-issue-tag');
            const resetButton = document.getElementById('dashboard-issue-reset');
            const tableBody = document.getElementById('dashboard-issues-table-body');
            const feedback = document.getElementById('dashboard-issue-feedback');
            const prevButton = document.getElementById('dashboard-issues-prev');
            const nextButton = document.getElementById('dashboard-issues-next');
            const pageInfo = document.getElementById('dashboard-issues-page-info');
            const issuesUrl = @json(route('issues.index'));

            let debounceTimer = null;
            let nextPageUrl = null;
            let prevPageUrl = null;
            let currentPage = 1;
            let activeRequest = null;

            const escapeHtml = (value) => String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');

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

            const buildUrl = (url = issuesUrl) => {
                const targetUrl = new URL(url, window.location.origin);
                const params = new URLSearchParams();
                const page = targetUrl.searchParams.get('page') ?? currentPage;

                if (searchInput.value.trim()) params.set('search', searchInput.value.trim());
                if (statusSelect.value) params.set('status', statusSelect.value);
                if (prioritySelect.value) params.set('priority', prioritySelect.value);
                if (tagSelect.value) params.set('tag', tagSelect.value);
                if (page && Number(page) > 1) params.set('page', page);

                const query = params.toString();

                return query ? `${issuesUrl}?${query}` : issuesUrl;
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
                            <td colspan="6" class="text-center py-5">
                                <h2 class="h5">No issues found</h2>
                                <p class="text-muted mb-0">Try another search or filter.</p>
                            </td>
                        </tr>
                    `;
                    return;
                }

                tableBody.innerHTML = issues.map((issue) => `
                    <tr>
                        <td>
                            <a href="${issue.urls.show}" class="fw-semibold text-decoration-none">${escapeHtml(issue.title)}</a>
                            <div class="small text-muted">${escapeHtml(String(issue.description ?? '').slice(0, 70))}</div>
                        </td>
                        <td>${escapeHtml(issue.project)}</td>
                        <td><span class="badge text-bg-${statusClass(issue.status)}">${escapeHtml(issue.status_label)}</span></td>
                        <td><span class="badge text-bg-${priorityClass(issue.priority)}">${escapeHtml(issue.priority_label)}</span></td>
                        <td><div class="d-flex flex-wrap gap-1">${renderTags(issue.tags)}</div></td>
                        <td>${issue.comments_count}</td>
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

            const fetchIssues = async (url = issuesUrl) => {
                if (activeRequest) {
                    activeRequest.abort();
                }

                activeRequest = new AbortController();
                feedback.classList.add('d-none');

                try {
                    const response = await fetch(buildUrl(url), {
                        headers: {
                            Accept: 'application/json',
                        },
                        signal: activeRequest.signal,
                    });

                    if (!response.ok) {
                        throw new Error('Request failed');
                    }

                    const data = await response.json();
                    renderIssues(data.issues);
                    updatePagination(data.pagination);
                } catch (error) {
                    if (error.name !== 'AbortError') {
                        feedback.classList.remove('d-none');
                    }
                }
            };

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                currentPage = 1;
                fetchIssues();
            });

            [statusSelect, prioritySelect, tagSelect].forEach((input) => {
                input.addEventListener('change', () => {
                    currentPage = 1;
                    fetchIssues();
                });
            });

            searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    currentPage = 1;
                    fetchIssues();
                }, 500);
            });

            resetButton.addEventListener('click', () => {
                form.reset();
                currentPage = 1;
                fetchIssues();
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
