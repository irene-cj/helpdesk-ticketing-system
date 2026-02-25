<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';
requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get ticket details
$stmt = $pdo->prepare("
    SELECT t.*, u.name as submitted_by, a.name as assigned_to_name
    FROM tickets t
    JOIN users u ON t.user_id = u.id
    LEFT JOIN users a ON t.assigned_to = a.id
    WHERE t.id = ?
");
$stmt->execute([$id]);
$ticket = $stmt->fetch();

if (!$ticket) {
    die("Ticket not found.");
}

// Only allow staff to view their own tickets, admins can view all
if (!isAdmin() && $ticket['user_id'] !== $_SESSION['user_id']) {
    header("Location: /helpdesk/staff/dashboard.php");
    exit();
}

// Get comments
$stmt = $pdo->prepare("
    SELECT c.*, u.name, u.role 
    FROM comments c 
    JOIN users u ON c.user_id = u.id 
    WHERE c.ticket_id = ? 
    ORDER BY c.created_at ASC
");
$stmt->execute([$id]);
$comments = $stmt->fetchAll();

// Handle new comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    if (!empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO comments (ticket_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->execute([$id, $_SESSION['user_id'], $comment]);
        header("Location: /helpdesk/tickets/view.php?id=" . $id);
        exit();
    }
}

// Handle status update (admin only)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status']) && isAdmin()) {
    $status = $_POST['status'];
    $assigned_to = !empty($_POST['assigned_to']) ? $_POST['assigned_to'] : null;
    $stmt = $pdo->prepare("UPDATE tickets SET status = ?, assigned_to = ? WHERE id = ?");
    $stmt->execute([$status, $assigned_to, $id]);
    header("Location: /helpdesk/tickets/view.php?id=" . $id);
    exit();
}

// Get all staff for assignment dropdown (admin only)
$staff = [];
if (isAdmin()) {
    $staff = $pdo->query("SELECT id, name FROM users WHERE role = 'staff'")->fetchAll();
}

// Priority and status colors
$priorityColor = ['Low' => 'secondary', 'Medium' => 'info', 'High' => 'warning', 'Critical' => 'danger'];
$statusColor   = ['Open' => 'primary', 'In Progress' => 'warning', 'Resolved' => 'success', 'Closed' => 'secondary'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?= $ticket['id'] ?> | IT Help Desk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark <?= isAdmin() ? 'bg-dark' : 'bg-primary' ?> px-4">
    <span class="navbar-brand">🖥️ IT Help Desk</span>
    <div class="d-flex gap-3">
        <?php if (isAdmin()): ?>
            <a href="/helpdesk/admin/dashboard.php" class="text-white text-decoration-none">Dashboard</a>
            <a href="/helpdesk/admin/tickets.php" class="text-white text-decoration-none">All Tickets</a>
        <?php else: ?>
            <a href="/helpdesk/staff/dashboard.php" class="text-white text-decoration-none">Dashboard</a>
            <a href="/helpdesk/tickets/create.php" class="text-white text-decoration-none">+ New Ticket</a>
        <?php endif; ?>
        <a href="/helpdesk/auth/logout.php" class="text-white text-decoration-none">Logout</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">

        <!-- Ticket Details -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Ticket #<?= $ticket['id'] ?> — <?= htmlspecialchars($ticket['title']) ?></strong>
                    <span class="badge bg-<?= $statusColor[$ticket['status']] ?>"><?= $ticket['status'] ?></span>
                </div>
                <div class="card-body">
                    <p><?= nl2br(htmlspecialchars($ticket['description'])) ?></p>
                    <hr>
                    <div class="row text-muted small">
                        <div class="col-md-4">
                            <strong>Submitted By:</strong><br><?= htmlspecialchars($ticket['submitted_by']) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Category:</strong><br><?= $ticket['category'] ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Priority:</strong><br>
                            <span class="badge bg-<?= $priorityColor[$ticket['priority']] ?>"><?= $ticket['priority'] ?></span>
                        </div>
                    </div>
                    <div class="row text-muted small mt-2">
                        <div class="col-md-4">
                            <strong>Assigned To:</strong><br>
                            <?= $ticket['assigned_to_name'] ?? 'Unassigned' ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Created:</strong><br><?= date('M d, Y g:i A', strtotime($ticket['created_at'])) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Last Updated:</strong><br><?= date('M d, Y g:i A', strtotime($ticket['updated_at'])) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments -->
            <div class="card shadow-sm mb-4">
                <div class="card-header"><strong>Comments</strong></div>
                <div class="card-body">
                    <?php if (empty($comments)): ?>
                        <p class="text-muted">No comments yet.</p>
                    <?php else: ?>
                        <?php foreach ($comments as $c): ?>
                        <div class="mb-3 p-3 rounded <?= $c['role'] === 'admin' ? 'bg-light border-start border-primary border-3' : 'bg-white border' ?>">
                            <div class="d-flex justify-content-between">
                                <strong><?= htmlspecialchars($c['name']) ?> 
                                    <?php if ($c['role'] === 'admin'): ?>
                                        <span class="badge bg-dark">Admin</span>
                                    <?php endif; ?>
                                </strong>
                                <small class="text-muted"><?= date('M d, Y g:i A', strtotime($c['created_at'])) ?></small>
                            </div>
                            <p class="mb-0 mt-1"><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Add Comment Form -->
                    <form method="POST" class="mt-3">
                        <div class="mb-2">
                            <textarea name="comment" class="form-control" rows="3" 
                                      placeholder="Add a comment..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Post Comment</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Admin Sidebar -->
        <?php if (isAdmin()): ?>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header"><strong>Manage Ticket</strong></div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <?php foreach (['Open', 'In Progress', 'Resolved', 'Closed'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $ticket['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Assign To</label>
                            <select name="assigned_to" class="form-select">
                                <option value="">Unassigned</option>
                                <?php foreach ($staff as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= $ticket['assigned_to'] == $s['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-dark">Update Ticket</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>