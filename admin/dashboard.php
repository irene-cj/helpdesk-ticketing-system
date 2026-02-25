<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';
requireAdmin();

// Get ticket stats
$stats = [];

$stmt = $pdo->query("SELECT COUNT(*) FROM tickets WHERE status = 'Open'");
$stats['open'] = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM tickets WHERE status = 'In Progress'");
$stats['in_progress'] = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM tickets WHERE status = 'Resolved'");
$stats['resolved'] = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM tickets WHERE status = 'Closed'");
$stats['closed'] = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM tickets WHERE priority = 'Critical'");
$stats['critical'] = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'staff'");
$stats['staff'] = $stmt->fetchColumn();

// Get recent tickets
$recent = $pdo->query("
    SELECT t.*, u.name as submitted_by 
    FROM tickets t 
    JOIN users u ON t.user_id = u.id 
    ORDER BY t.created_at DESC 
    LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | IT Help Desk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">🖥️ IT Help Desk — Admin</span>
    <div class="d-flex gap-3">
        <a href="tickets.php" class="text-white text-decoration-none">All Tickets</a>
        <a href="users.php" class="text-white text-decoration-none">Users</a>
        <a href="../auth/logout.php" class="text-white text-decoration-none">Logout</a>
    </div>
</nav>

<div class="container mt-4">
    <h4 class="mb-4">Welcome back, <?= htmlspecialchars($_SESSION['name']) ?>!</h4>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card text-white bg-danger text-center p-3">
                <h2><?= $stats['critical'] ?></h2>
                <small>Critical</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-primary text-center p-3">
                <h2><?= $stats['open'] ?></h2>
                <small>Open</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-warning text-center p-3">
                <h2><?= $stats['in_progress'] ?></h2>
                <small>In Progress</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-success text-center p-3">
                <h2><?= $stats['resolved'] ?></h2>
                <small>Resolved</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-secondary text-center p-3">
                <h2><?= $stats['closed'] ?></h2>
                <small>Closed</small>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-white bg-info text-center p-3">
                <h2><?= $stats['staff'] ?></h2>
                <small>Staff Users</small>
            </div>
        </div>
    </div>

    <!-- Recent Tickets -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Recent Tickets</strong>
            <a href="tickets.php" class="btn btn-sm btn-primary">View All</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Submitted By</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent)): ?>
                        <tr><td colspan="7" class="text-center py-3">No tickets yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recent as $ticket): ?>
                        <tr>
                            <td><?= $ticket['id'] ?></td>
                            <td><?= htmlspecialchars($ticket['title']) ?></td>
                            <td><?= htmlspecialchars($ticket['submitted_by']) ?></td>
                            <td>
                                <span class="badge bg-<?= $ticket['priority'] === 'Critical' ? 'danger' : ($ticket['priority'] === 'High' ? 'warning' : 'secondary') ?>">
                                    <?= $ticket['priority'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $ticket['status'] === 'Open' ? 'primary' : ($ticket['status'] === 'In Progress' ? 'warning' : 'success') ?>">
                                    <?= $ticket['status'] ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($ticket['created_at'])) ?></td>
                            <td><a href="../tickets/view.php?id=<?= $ticket['id'] ?>" class="btn btn-sm btn-outline-primary">View</a></td>
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
