<?php
require_once 'includes/auth_check.php';
requireLogin();
$pageTitle = 'Dashboard';
require_once 'includes/header.php';
?>

<div class="dashboard">
    <!-- Top controls: search, filter, add button -->
    <div class="controls-bar">
        <h1 class="page-title">My Tasks</h1>
        <div class="controls-row">
            <div class="search-group">
                <input type="text" id="searchInput" class="form-control" placeholder="Search by title...">
            </div>
            <div class="filter-group">
                <select id="statusFilter" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
            <button id="btnAddTask" class="btn btn-primary">+ New Task</button>
        </div>
    </div>

    <!-- Task table -->
    <div class="table-wrapper">
        <table class="task-table" id="taskTable">
            <thead>
                <tr>
                    <th class="sortable" data-sort="id">ID <span class="sort-arrow"></span></th>
                    <th class="sortable" data-sort="title">Title <span class="sort-arrow"></span></th>
                    <th>Description</th>
                    <th class="sortable" data-sort="status">Status <span class="sort-arrow"></span></th>
                    <th class="sortable" data-sort="due_date">Due Date <span class="sort-arrow"></span></th>
                    <th class="sortable" data-sort="created_at">Created <span class="sort-arrow"></span></th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="taskTableBody">
                <!-- Filled by AJAX -->
            </tbody>
        </table>
        <div id="noTasks" class="no-tasks" style="display:none;">
            <p>No tasks found. Click "+ New Task" to get started!</p>
        </div>
    </div>

    <!-- Pagination -->
    <div class="pagination" id="pagination"></div>
</div>

<!-- Add/Edit Task Modal -->
<div id="taskModal" class="modal-overlay" style="display:none;">
    <div class="modal">
        <div class="modal-header">
            <h2 id="modalTitle">Add New Task</h2>
            <button class="modal-close" id="modalClose">&times;</button>
        </div>
        <form id="taskForm" novalidate>
            <input type="hidden" id="taskId" value="">
            <div class="form-group">
                <label for="taskTitleInput">Title <span class="required">*</span></label>
                <input type="text" id="taskTitleInput" class="form-control" maxlength="255" required>
                <span class="error-msg" id="titleError"></span>
            </div>
            <div class="form-group">
                <label for="taskDescription">Description</label>
                <textarea id="taskDescription" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group half">
                    <label for="taskStatus">Status <span class="required">*</span></label>
                    <select id="taskStatus" class="form-control" required>
                        <option value="">Select Status</option>
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                    <span class="error-msg" id="statusError"></span>
                </div>
                <div class="form-group half">
                    <label for="taskDueDate">Due Date</label>
                    <input type="date" id="taskDueDate" class="form-control">
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary" id="btnCancelTask">Cancel</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitTask">Save Task</button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
