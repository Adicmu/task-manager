<?php
define('IS_API', true);
require_once '../config/database.php';
require_once '../includes/auth_check.php';

header('Content-Type: application/json');

// Must be logged in to use API
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$pdo = getDBConnection();
$userId = getCurrentUserId();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        handleGet($pdo, $userId);
        break;
    case 'POST':
        handlePost($pdo, $userId);
        break;
    case 'PUT':
        handlePut($pdo, $userId);
        break;
    case 'DELETE':
        handleDelete($pdo, $userId);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}

// GET - List tasks or fetch single task by ID
function handleGet($pdo, $userId) {
    // Single task fetch (for edit modal)
    if (isset($_GET['id']) && intval($_GET['id']) > 0) {
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
        $stmt->execute([intval($_GET['id']), $userId]);
        $task = $stmt->fetch();
        if (!$task) {
            http_response_code(404);
            echo json_encode(['error' => 'Task not found.']);
            return;
        }
        echo json_encode(['task' => $task]);
        return;
    }

    $search = trim($_GET['search'] ?? '');
    $status = trim($_GET['status'] ?? '');
    $sort = $_GET['sort'] ?? 'created_at';
    $order = strtoupper($_GET['order'] ?? 'DESC');
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = 8;
    $offset = ($page - 1) * $limit;

    // Whitelist allowed sort columns
    $allowedSorts = ['id', 'title', 'status', 'due_date', 'created_at'];
    if (!in_array($sort, $allowedSorts)) {
        $sort = 'created_at';
    }
    $order = ($order === 'ASC') ? 'ASC' : 'DESC';

    // Build query
    $where = "WHERE user_id = ?";
    $params = [$userId];

    if ($search !== '') {
        $where .= " AND title LIKE ?";
        $params[] = "%$search%";
    }

    if ($status !== '' && in_array($status, ['Pending', 'In Progress', 'Completed'])) {
        $where .= " AND status = ?";
        $params[] = $status;
    }

    // Count total for pagination
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM tasks $where");
    $countStmt->execute($params);
    $total = $countStmt->fetchColumn();
    $totalPages = max(1, ceil($total / $limit));

    // Fetch tasks
    $sql = "SELECT * FROM tasks $where ORDER BY $sort $order LIMIT $limit OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $tasks = $stmt->fetchAll();

    echo json_encode([
        'tasks' => $tasks,
        'total' => (int)$total,
        'page' => $page,
        'totalPages' => (int)$totalPages,
        'limit' => $limit
    ]);
}

// POST - Create a new task
function handlePost($pdo, $userId) {
    $input = json_decode(file_get_contents('php://input'), true);

    $title = trim($input['title'] ?? '');
    $description = trim($input['description'] ?? '');
    $status = trim($input['status'] ?? 'Pending');
    $dueDate = trim($input['due_date'] ?? '');

    // Validation
    $errors = [];
    if ($title === '') {
        $errors[] = 'Title is required.';
    } elseif (strlen($title) > 255) {
        $errors[] = 'Title must be under 255 characters.';
    }

    $validStatuses = ['Pending', 'In Progress', 'Completed'];
    if (!in_array($status, $validStatuses)) {
        $errors[] = 'Invalid status value.';
    }

    if ($dueDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate)) {
        $errors[] = 'Invalid date format.';
    }

    if (!empty($errors)) {
        http_response_code(422);
        echo json_encode(['errors' => $errors]);
        return;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO tasks (user_id, title, description, status, due_date) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $userId,
        $title,
        $description,
        $status,
        $dueDate ?: null
    ]);

    $taskId = $pdo->lastInsertId();

    // Return the created task
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$taskId]);
    $task = $stmt->fetch();

    http_response_code(201);
    echo json_encode(['message' => 'Task created successfully.', 'task' => $task]);
}

// PUT - Update an existing task
function handlePut($pdo, $userId) {
    $input = json_decode(file_get_contents('php://input'), true);

    $id = intval($input['id'] ?? 0);
    $title = trim($input['title'] ?? '');
    $description = trim($input['description'] ?? '');
    $status = trim($input['status'] ?? '');
    $dueDate = trim($input['due_date'] ?? '');

    // Check ownership
    $check = $pdo->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
    $check->execute([$id, $userId]);
    if (!$check->fetch()) {
        http_response_code(404);
        echo json_encode(['error' => 'Task not found.']);
        return;
    }

    // Validation
    $errors = [];
    if ($title === '') {
        $errors[] = 'Title is required.';
    } elseif (strlen($title) > 255) {
        $errors[] = 'Title must be under 255 characters.';
    }

    $validStatuses = ['Pending', 'In Progress', 'Completed'];
    if (!in_array($status, $validStatuses)) {
        $errors[] = 'Invalid status value.';
    }

    if ($dueDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate)) {
        $errors[] = 'Invalid date format.';
    }

    if (!empty($errors)) {
        http_response_code(422);
        echo json_encode(['errors' => $errors]);
        return;
    }

    $stmt = $pdo->prepare(
        "UPDATE tasks SET title = ?, description = ?, status = ?, due_date = ? WHERE id = ? AND user_id = ?"
    );
    $stmt->execute([
        $title,
        $description,
        $status,
        $dueDate ?: null,
        $id,
        $userId
    ]);

    // Return updated task
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$id]);
    $task = $stmt->fetch();

    echo json_encode(['message' => 'Task updated successfully.', 'task' => $task]);
}

// DELETE - Delete a task
function handleDelete($pdo, $userId) {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = intval($input['id'] ?? 0);

    // Check ownership
    $check = $pdo->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
    $check->execute([$id, $userId]);
    if (!$check->fetch()) {
        http_response_code(404);
        echo json_encode(['error' => 'Task not found.']);
        return;
    }

    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $userId]);

    echo json_encode(['message' => 'Task deleted successfully.']);
}
