/* ============================================
   Task Manager - jQuery/AJAX Application Logic
   ============================================ */

$(document).ready(function () {

    // =====================
    // State Management
    // =====================
    var state = {
        page: 1,
        sort: 'created_at',
        order: 'DESC',
        search: '',
        status: ''
    };

    var searchTimer = null;

    // =====================
    // Toast Notifications
    // =====================
    function showToast(message, type) {
        var container = $('.toast-container');
        if (!container.length) {
            $('body').append('<div class="toast-container"></div>');
            container = $('.toast-container');
        }

        var toast = $('<div class="toast toast-' + type + '">' + escapeHtml(message) + '</div>');
        container.append(toast);

        setTimeout(function () {
            toast.fadeOut(300, function () {
                $(this).remove();
            });
        }, 3000);
    }

    // =====================
    // Utility: Escape HTML
    // =====================
    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    // =====================
    // Format Date
    // =====================
    function formatDate(dateStr) {
        if (!dateStr) return '—';
        var d = new Date(dateStr);
        if (isNaN(d.getTime())) return dateStr;
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
    }

    function isOverdue(dateStr, status) {
        if (!dateStr || status === 'Completed') return false;
        var today = new Date();
        today.setHours(0,0,0,0);
        var due = new Date(dateStr + 'T00:00:00');
        return due < today;
    }

    // =====================
    // Status Badge
    // =====================
    function statusBadge(status) {
        var cls = 'badge-pending';
        if (status === 'In Progress') cls = 'badge-in-progress';
        if (status === 'Completed') cls = 'badge-completed';
        return '<span class="badge ' + cls + '">' + escapeHtml(status) + '</span>';
    }

    // =========================================
    // AUTH: Login & Register (only on auth pages)
    // =========================================
    $('#loginForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        var username = $.trim($('#loginUsername').val());
        var password = $('#loginPassword').val();
        var valid = true;

        if (!username) {
            showFieldError('loginUsername', 'loginUsernameError', 'Username is required.');
            valid = false;
        }
        if (!password) {
            showFieldError('loginPassword', 'loginPasswordError', 'Password is required.');
            valid = false;
        }
        if (!valid) return;

        $('#btnLogin').prop('disabled', true).text('Signing in...');

        $.ajax({
            url: 'api/auth.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ action: 'login', username: username, password: password }),
            success: function () {
                window.location.href = 'index.php';
            },
            error: function (xhr) {
                var resp = xhr.responseJSON || {};
                var msg = (resp.errors && resp.errors[0]) || 'Login failed. Please try again.';
                $('#loginAlert').text(msg).show();
                $('#btnLogin').prop('disabled', false).text('Sign In');
            }
        });
    });

    $('#registerForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        var username = $.trim($('#regUsername').val());
        var password = $('#regPassword').val();
        var confirm = $('#regConfirmPassword').val();
        var valid = true;

        if (!username) {
            showFieldError('regUsername', 'regUsernameError', 'Username is required.');
            valid = false;
        } else if (username.length < 3) {
            showFieldError('regUsername', 'regUsernameError', 'Username must be at least 3 characters.');
            valid = false;
        }

        if (!password || password.length < 6) {
            showFieldError('regPassword', 'regPasswordError', 'Password must be at least 6 characters.');
            valid = false;
        }

        if (password !== confirm) {
            showFieldError('regConfirmPassword', 'regConfirmError', 'Passwords do not match.');
            valid = false;
        }

        if (!valid) return;

        $('#btnRegister').prop('disabled', true).text('Creating account...');

        $.ajax({
            url: 'api/auth.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                action: 'register',
                username: username,
                password: password,
                confirm_password: confirm
            }),
            success: function () {
                window.location.href = 'index.php';
            },
            error: function (xhr) {
                var resp = xhr.responseJSON || {};
                var msg = (resp.errors && resp.errors.join(' ')) || 'Registration failed.';
                $('#registerAlert').text(msg).show();
                $('#btnRegister').prop('disabled', false).text('Create Account');
            }
        });
    });

    // =====================
    // Field Error Helpers
    // =====================
    function showFieldError(inputId, errorId, message) {
        $('#' + inputId).addClass('is-invalid');
        $('#' + errorId).text(message);
    }

    function clearErrors() {
        $('.form-control').removeClass('is-invalid');
        $('.error-msg').text('');
        $('.alert').hide();
    }

    // =========================================
    // TASKS: Dashboard Functionality
    // =========================================

    // Only run task logic on dashboard
    if ($('#taskTable').length === 0) return;

    // Initial load
    loadTasks();

    // Search with debounce
    $('#searchInput').on('input', function () {
        clearTimeout(searchTimer);
        var val = $(this).val();
        searchTimer = setTimeout(function () {
            state.search = val;
            state.page = 1;
            loadTasks();
        }, 350);
    });

    // Filter by status
    $('#statusFilter').on('change', function () {
        state.status = $(this).val();
        state.page = 1;
        loadTasks();
    });

    // Sort columns
    $('.sortable').on('click', function () {
        var col = $(this).data('sort');
        if (state.sort === col) {
            state.order = (state.order === 'ASC') ? 'DESC' : 'ASC';
        } else {
            state.sort = col;
            state.order = 'ASC';
        }
        state.page = 1;
        loadTasks();
    });

    // =====================
    // Load Tasks (AJAX GET)
    // =====================
    function loadTasks() {
        var tbody = $('#taskTableBody');
        tbody.html('<tr><td colspan="7" class="loading">Loading tasks</td></tr>');
        $('#noTasks').hide();

        $.ajax({
            url: 'api/tasks.php',
            method: 'GET',
            data: {
                search: state.search,
                status: state.status,
                sort: state.sort,
                order: state.order,
                page: state.page
            },
            success: function (resp) {
                renderTasks(resp.tasks);
                renderPagination(resp.page, resp.totalPages, resp.total);
                updateSortIndicators();
            },
            error: function (xhr) {
                if (xhr.status === 401) {
                    window.location.href = 'login.php';
                    return;
                }
                tbody.html('<tr><td colspan="7" class="no-tasks">Failed to load tasks.</td></tr>');
            }
        });
    }

    // =====================
    // Render Tasks Table
    // =====================
    function renderTasks(tasks) {
        var tbody = $('#taskTableBody');
        tbody.empty();

        if (!tasks || tasks.length === 0) {
            tbody.parent().hide();
            $('#noTasks').show();
            return;
        }

        tbody.parent().show();
        $('#noTasks').hide();

        $.each(tasks, function (i, task) {
            var dueDateClass = isOverdue(task.due_date, task.status) ? 'task-overdue' : 'task-date';
            var row = '<tr data-id="' + task.id + '">' +
                '<td>' + task.id + '</td>' +
                '<td><strong>' + escapeHtml(task.title) + '</strong></td>' +
                '<td class="task-desc">' + escapeHtml(task.description || '') + '</td>' +
                '<td>' + statusBadge(task.status) + '</td>' +
                '<td class="' + dueDateClass + '">' + formatDate(task.due_date) + '</td>' +
                '<td class="task-date">' + formatDate(task.created_at) + '</td>' +
                '<td class="task-actions">' +
                    '<button class="btn btn-sm btn-secondary btn-edit" data-id="' + task.id + '">Edit</button>' +
                    '<button class="btn btn-sm btn-danger btn-delete" data-id="' + task.id + '" data-title="' + escapeHtml(task.title) + '">Delete</button>' +
                '</td>' +
                '</tr>';
            tbody.append(row);
        });
    }

    // =====================
    // Render Pagination
    // =====================
    function renderPagination(current, total, totalItems) {
        var container = $('#pagination');
        container.empty();

        if (total <= 1) return;

        // Previous button
        container.append(
            '<button class="page-btn" data-page="' + (current - 1) + '"' +
            (current <= 1 ? ' disabled' : '') + '>&laquo; Prev</button>'
        );

        // Page numbers
        var start = Math.max(1, current - 2);
        var end = Math.min(total, current + 2);

        if (start > 1) {
            container.append('<button class="page-btn" data-page="1">1</button>');
            if (start > 2) container.append('<span class="page-info">...</span>');
        }

        for (var i = start; i <= end; i++) {
            container.append(
                '<button class="page-btn' + (i === current ? ' active' : '') + '" data-page="' + i + '">' + i + '</button>'
            );
        }

        if (end < total) {
            if (end < total - 1) container.append('<span class="page-info">...</span>');
            container.append('<button class="page-btn" data-page="' + total + '">' + total + '</button>');
        }

        // Next button
        container.append(
            '<button class="page-btn" data-page="' + (current + 1) + '"' +
            (current >= total ? ' disabled' : '') + '>Next &raquo;</button>'
        );

        // Info
        container.append('<span class="page-info">' + totalItems + ' tasks</span>');
    }

    // Pagination click
    $(document).on('click', '.page-btn:not(:disabled)', function () {
        state.page = parseInt($(this).data('page'));
        loadTasks();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // =====================
    // Sort Indicators
    // =====================
    function updateSortIndicators() {
        $('.sortable').removeClass('active asc desc');
        var th = $('.sortable[data-sort="' + state.sort + '"]');
        th.addClass('active');
        th.addClass(state.order === 'ASC' ? 'asc' : 'desc');
    }

    // =====================
    // Modal Handling
    // =====================
    function openModal(title, task) {
        $('#modalTitle').text(title);
        clearErrors();

        if (task) {
            $('#taskId').val(task.id);
            $('#taskTitleInput').val(task.title);
            $('#taskDescription').val(task.description || '');
            $('#taskStatus').val(task.status);
            $('#taskDueDate').val(task.due_date || '');
        } else {
            $('#taskForm')[0].reset();
            $('#taskId').val('');
        }

        $('#taskModal').fadeIn(150);
        $('#taskTitleInput').focus();
    }

    function closeModal() {
        $('#taskModal').fadeOut(150);
    }

    $('#btnAddTask').on('click', function () {
        openModal('Add New Task', null);
    });

    $('#modalClose, #btnCancelTask').on('click', closeModal);

    // Close modal on overlay click
    $('#taskModal').on('click', function (e) {
        if ($(e.target).hasClass('modal-overlay')) {
            closeModal();
        }
    });

    // Close modal on Escape
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') closeModal();
    });

    // =====================
    // Add / Edit Task (AJAX)
    // =====================
    $('#taskForm').on('submit', function (e) {
        e.preventDefault();
        clearErrors();

        var taskId = $('#taskId').val();
        var title = $.trim($('#taskTitleInput').val());
        var description = $.trim($('#taskDescription').val());
        var status = $('#taskStatus').val();
        var dueDate = $('#taskDueDate').val();
        var valid = true;

        // jQuery validation
        if (!title) {
            showFieldError('taskTitleInput', 'titleError', 'Title is required.');
            valid = false;
        } else if (title.length > 255) {
            showFieldError('taskTitleInput', 'titleError', 'Title must be under 255 characters.');
            valid = false;
        }

        if (!status) {
            showFieldError('taskStatus', 'statusError', 'Status is required.');
            valid = false;
        }

        if (!valid) return;

        var payload = {
            title: title,
            description: description,
            status: status,
            due_date: dueDate
        };

        var method = 'POST';
        if (taskId) {
            method = 'PUT';
            payload.id = parseInt(taskId);
        }

        $('#btnSubmitTask').prop('disabled', true).text('Saving...');

        $.ajax({
            url: 'api/tasks.php',
            method: method,
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: function (resp) {
                closeModal();
                loadTasks();
                showToast(resp.message || 'Task saved!', 'success');
                $('#btnSubmitTask').prop('disabled', false).text('Save Task');
            },
            error: function (xhr) {
                var resp = xhr.responseJSON || {};
                if (resp.errors) {
                    // Show first backend error
                    showToast(resp.errors[0], 'error');
                } else {
                    showToast('Failed to save task.', 'error');
                }
                $('#btnSubmitTask').prop('disabled', false).text('Save Task');
            }
        });
    });

    // =====================
    // Edit Task - Load Data
    // =====================
    $(document).on('click', '.btn-edit', function () {
        var id = $(this).data('id');

        // Fetch single task by ID
        $.ajax({
            url: 'api/tasks.php',
            method: 'GET',
            data: { id: id },
            success: function (resp) {
                if (resp.task) {
                    openModal('Edit Task', resp.task);
                } else {
                    showToast('Task not found.', 'error');
                }
            },
            error: function () {
                showToast('Failed to load task data.', 'error');
            }
        });
    });

    // =====================
    // Delete Task (AJAX)
    // =====================
    $(document).on('click', '.btn-delete', function () {
        var id = $(this).data('id');
        var title = $(this).data('title');

        if (!confirm('Are you sure you want to delete "' + title + '"? This cannot be undone.')) {
            return;
        }

        $.ajax({
            url: 'api/tasks.php',
            method: 'DELETE',
            contentType: 'application/json',
            data: JSON.stringify({ id: parseInt(id) }),
            success: function (resp) {
                loadTasks();
                showToast(resp.message || 'Task deleted.', 'success');
            },
            error: function () {
                showToast('Failed to delete task.', 'error');
            }
        });
    });

});
