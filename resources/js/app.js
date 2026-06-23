document.addEventListener('DOMContentLoaded', function () {

      const searchInput = document.getElementById('issue-search');
if (searchInput) {
    const searchResults = document.getElementById('search-results');
    let debounceTimer;

    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.classList.add('hidden');
            searchResults.innerHTML = '';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`/issues/search?q=${encodeURIComponent(query)}`, {
                headers: { 'Accept': 'application/json' },
            })
            .then(response => response.json())
            .then(data => {
                if (data.data.length === 0) {
                    searchResults.innerHTML = '<p class="text-gray-500 text-sm">No issues found.</p>';
                } else {
                    searchResults.innerHTML = data.data.map(issue => `
                        <a href="/projects/${issue.project_id}/issues/${issue.id}"
                           class="block bg-white rounded shadow mb-2 p-3 hover:bg-gray-50">
                            <div class="flex items-center justify-between">
                                <span class="text-blue-600 font-medium text-sm">${escapeHtml(issue.title)}</span>
                                <span class="text-xs text-gray-400">${escapeHtml(issue.project?.name ?? '')}</span>
                            </div>
                        </a>
                    `).join('');
                }
                searchResults.classList.remove('hidden');
            });
        }, 300);
    });

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
            }
        }
    const commentForm = document.getElementById('comment-form');
    if (!commentForm) return;

    const issueId = document.querySelector('meta[name="issue-id"]').content;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const commentsList = document.getElementById('comments-list');
    const pagination = document.getElementById('comments-pagination');

    let currentPage = 1;

    loadComments();

    function loadComments(page = 1) {
        fetch(`/issues/${issueId}/comments?page=${page}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(response => response.json())
        .then(data => {
            if (page === 1) {
                commentsList.innerHTML = '';
            }

            data.data.forEach(comment => {
                commentsList.insertAdjacentHTML('beforeend', renderComment(comment));
            });

            currentPage = data.current_page;

            if (data.next_page_url) {
                pagination.innerHTML = `
                    <button id="load-more"
                            class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 text-sm">
                        Load More
                    </button>`;
                document.getElementById('load-more').addEventListener('click', function () {
                    loadComments(currentPage + 1);
                });
            } else {
                pagination.innerHTML = '';
            }

            if (data.data.length === 0 && page === 1) {
                commentsList.innerHTML = '<p class="text-gray-500 text-sm">No comments yet.</p>';
            }
        });
    }

    function renderComment(comment) {
        const date = new Date(comment.created_at).toLocaleDateString('en-US', {
            month: 'short', day: 'numeric', year: 'numeric'
        });

        return `
            <div class="bg-white rounded shadow p-4 mb-3">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-medium text-sm text-gray-800">${escapeHtml(comment.author_name)}</span>
                    <span class="text-xs text-gray-500">${date}</span>
                </div>
                <p class="text-gray-600 text-sm">${escapeHtml(comment.body)}</p>
            </div>`;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    commentForm.addEventListener('submit', function (e) {
        e.preventDefault();
        clearErrors();

        const formData = {
            author_name: commentForm.querySelector('[name="author_name"]').value,
            body: commentForm.querySelector('[name="body"]').value,
        };

        fetch(`/issues/${issueId}/comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(formData),
        })
        .then(response => {
            if (response.status === 422) {
                return response.json().then(data => { throw data; });
            }
            return response.json();
        })
        .then(comment => {
            const noComments = commentsList.querySelector('p.text-gray-500');
            if (noComments) noComments.remove();

            commentsList.insertAdjacentHTML('afterbegin', renderComment(comment));
            commentForm.reset();
        })
        .catch(errorData => {
            if (errorData.errors) {
                Object.entries(errorData.errors).forEach(([field, messages]) => {
                    const errorEl = commentForm.querySelector(`[data-error="${field}"]`);
                    if (errorEl) {
                        errorEl.textContent = messages[0];
                        errorEl.classList.remove('hidden');
                    }
                });
            }
        });
    });

    function clearErrors() {
        commentForm.querySelectorAll('[data-error]').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
    }

    // --- Tag attach/detach ---
    const tagsList = document.getElementById('tags-list');
    const toggleBtn = document.getElementById('toggle-tag-form');
    const tagForm = document.getElementById('tag-form');
    const tagSelect = document.getElementById('tag-select');
    const attachBtn = document.getElementById('attach-tag');

    if (!tagsList) return;

    const allTags = window.allTags || [];

    toggleBtn.addEventListener('click', function () {
        tagForm.classList.toggle('hidden');
        if (!tagForm.classList.contains('hidden')) {
            populateTagSelect();
        }
    });

    function populateTagSelect() {
        const attachedIds = [...tagsList.querySelectorAll('[data-tag-id]')]
            .map(el => parseInt(el.dataset.tagId));

        tagSelect.innerHTML = '<option value="">Select a tag...</option>';
        allTags.filter(tag => !attachedIds.includes(tag.id)).forEach(tag => {
            tagSelect.innerHTML += `<option value="${tag.id}">${escapeHtml(tag.name)}</option>`;
        });
    }

    attachBtn.addEventListener('click', function () {
        const tagId = tagSelect.value;
        if (!tagId) return;

        fetch(`/issues/${issueId}/tags`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ tag_id: tagId }),
        })
        .then(response => response.json())
        .then(tags => {
            renderTags(tags);
            populateTagSelect();
        });
    });

    tagsList.addEventListener('click', function (e) {
        const btn = e.target.closest('.detach-tag');
        if (!btn) return;

        const tagSpan = btn.closest('[data-tag-id]');
        const tagId = tagSpan.dataset.tagId;

        fetch(`/issues/${issueId}/tags/${tagId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        })
        .then(response => response.json())
        .then(tags => {
            renderTags(tags);
            populateTagSelect();
        });
    });

    function renderTags(tags) {
        if (tags.length === 0) {
            tagsList.innerHTML = '<span class="text-xs text-gray-400 no-tags">No tags</span>';
            return;
        }

        tagsList.innerHTML = tags.map(tag => `
            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full inline-flex items-center gap-1"
                  data-tag-id="${tag.id}">
                ${escapeHtml(tag.name)}
                <button class="detach-tag text-gray-400 hover:text-red-600 ml-0.5">&times;</button>
            </span>
        `).join('');
    }
    // --- User assign/detach ---
const usersList = document.getElementById('users-list');
const toggleUserBtn = document.getElementById('toggle-user-form');
const userForm = document.getElementById('user-form');
const userSelect = document.getElementById('user-select');
const attachUserBtn = document.getElementById('attach-user');

if (usersList && toggleUserBtn) {
    const allUsers = window.allUsers || [];

    toggleUserBtn.addEventListener('click', function () {
        userForm.classList.toggle('hidden');
        if (!userForm.classList.contains('hidden')) {
            populateUserSelect();
        }
    });

    function populateUserSelect() {
        const assignedIds = [...usersList.querySelectorAll('[data-user-id]')]
            .map(el => parseInt(el.dataset.userId));

        userSelect.innerHTML = '<option value="">Select a user...</option>';
        allUsers.filter(u => !assignedIds.includes(u.id)).forEach(u => {
            userSelect.innerHTML += `<option value="${u.id}">${escapeHtml(u.name)}</option>`;
        });
    }

    attachUserBtn.addEventListener('click', function () {
        const userId = userSelect.value;
        if (!userId) return;

        fetch(`/issues/${issueId}/users`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ user_id: userId }),
        })
        .then(response => response.json())
        .then(users => {
            renderUsers(users);
            populateUserSelect();
        });
    });

    usersList.addEventListener('click', function (e) {
        const btn = e.target.closest('.detach-user');
        if (!btn) return;

        const userSpan = btn.closest('[data-user-id]');
        const userId = userSpan.dataset.userId;

        fetch(`/issues/${issueId}/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        })
        .then(response => response.json())
        .then(users => {
            renderUsers(users);
            populateUserSelect();
        });
    });

    function renderUsers(users) {
        if (users.length === 0) {
            usersList.innerHTML = '<span class="text-xs text-gray-400 no-users">No members assigned</span>';
            return;
        }

        usersList.innerHTML = users.map(user => `
            <span class="text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded-full inline-flex items-center gap-1"
                  data-user-id="${user.id}">
                ${escapeHtml(user.name)}
                <button class="detach-user text-blue-400 hover:text-red-600 ml-0.5">&times;</button>
            </span>
            `).join('');
        }
    }
});
