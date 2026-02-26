<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';
requireAdmin();

// Filters
$status   = isset($_GET['status']) ? $_GET['status'] : '';
$priority = isset($_GET['priority']) ? $_GET['priority'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Build query dynamically based on filters
$where = [];
$params = [];

if ($status) {
    $where[] = "t.status = ?";
    $params[] = $status;
}
if ($priority) {
    $where[] = "t.priority = ?";
    $params[] = $priority;
}
if ($category) {
    $where[] = "t.category = ?";
    $params[] = $category;
}

$whereSQL = count($where) ? "WHERE " . implode(" AND ", $where) : "";

$tickets = $pdo->prepare("
    SELECT t.*, u.name as submitted_by, a.name as assigned_to_name
    FROM tickets t
    JOIN users u ON t.user_id = u.id
    LEFT JOIN users a ON t.assigned_to = a.id
    $whereSQL
    ORDER BY t.created_at DESC
");
$tickets->execute($params);
$tickets = $tickets->fetchAll();

$priorityColor = ['Low' => 'secondary', 'Medium' => 'info', 'High' => 'warning', 'Critical' => 'danger'];
$statusColor   = ['Open' => 'primary', 'In Progress' => 'warning', 'Resolved' => 'success', 'Closed' => 'secondary'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Tickets | IT Help Desk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">🖥️ IT Help Desk — Admin</span>
    <div class="d-flex gap-3">
        <a href="dashboard.php" class="text-white text-decoration-none">Dashboard</a>
        <a href="tickets.php" class="text-white text-decoration-none">All Tickets</a>
        <a href="users.php" class="text-white text-decoration-none">Users</a>
        <a href="../auth/logout.php" class="text-white text-decoration-none">Logout</a>
    </div>
</nav>

<div class="container mt-4">
    <h4 class="mb-4">All Tickets</h4>

    <!-- Filters -->
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3">
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <?php foreach (['Open', 'In Progress', 'Resolved', 'Closed'] as $s): ?>
                    <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="priority" class="form-select">
                <option value="">All Priorities</option>
                <?php foreach (['Low', 'Medium', 'High', 'Critical'] as $p): ?>
                    <option value="<?= $p ?>" <?= $priority === $p ? 'selected' : '' ?>><?= $p ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <?php foreach (['Hardware', 'Software', 'Network', 'Account', 'Other'] as $c): ?>
                    <option value="<?= $c ?>" <?= $category === $c ? 'selected' : '' ?>><?= $c ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-dark w-100">Filter</button>
        </div>
        <div class="col-md-1">
            <a href="tickets.php" class="btn btn-outline-secondary w-100">Clear</a>
        </div>
    </form>

    <!-- Tickets Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Submitted By</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Assigned To</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tickets)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4">No tickets found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><?= $ticket['id'] ?></td>
                            <td><?= htmlspecialchars($ticket['title']) ?></td>
                            <td><?= htmlspecialchars($ticket['submitted_by']) ?></td>
                            <td><?= $ticket['category'] ?></td>
                            <td>
                                <span class="badge bg-<?= $priorityColor[$ticket['priority']] ?>">
                                    <?= $ticket['priority'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $statusColor[$ticket['status']] ?>">
                                    <?= $ticket['status'] ?>
                                </span>
                            </td>
                            <td><?= $ticket['assigned_to_name'] ?? 'Unassigned' ?></td>
                            <td><?= date('M d, Y', strtotime($ticket['created_at'])) ?></td>
                            <td>
                                <a href="../tickets/view.php?id=<?= $ticket['id'] ?>" 
                                   class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>