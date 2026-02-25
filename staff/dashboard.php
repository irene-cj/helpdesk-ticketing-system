<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';
requireLogin();

// Get this staff member's ticket stats
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE user_id = ? AND status = 'Open'");
$stmt->execute([$user_id]);
$open = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE user_id = ? AND status = 'In Progress'");
$stmt->execute([$user_id]);
$in_progress = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE user_id = ? AND status = 'Resolved'");
$stmt->execute([$user_id]);
$resolved = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM tickets WHERE user_id = ? AND status = 'Closed'");
$stmt->execute([$user_id]);
$closed = $stmt->fetchColumn();

// Get their recent tickets
$stmt = $pdo->prepare("
    SELECT * FROM tickets 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 5
");
$stmt->execute([$user_id]);
$tickets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard | IT Help Desk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-dark bg-primary px-4">
    <span class="navbar-brand">🖥️ IT Help Desk</span>
    <div class="d-flex gap-3">
        <a href="../tickets/create.php" class="text-white text-decoration-none">+ New Ticket</a>
        <a href="my_tickets.php" class="text-white text-decoration-none">My Tickets</a>
        <a href="../auth/logout.php" class="text-white text-decoration-none">Logout</a>
    </div>
</nav>

<div class="container mt-4">
    <h4 class="mb-4">Welcome, <?= htmlspecialchars($_SESSION['name']) ?>!</h4>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary text-center p-3">
                <h2><?= $open ?></h2>
                <small>Open</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning text-center p-3">
                <h2><?= $in_progress ?></h2>
                <small>In Progress</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success text-center p-3">
                <h2><?= $resolved ?></h2>
                <small>Resolved</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-secondary text-center p-3">
                <h2><?= $closed ?></h2>
                <small>Closed</small>
            </div>
        </div>
    </div>

    <!-- Recent Tickets -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <strong>My Recent Tickets</strong>
            <a href="../tickets/create.php" class="btn btn-sm btn-success">+ Submit New Ticket</a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tickets)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                No tickets yet. <a href="../tickets/create.php">Submit your first ticket!</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tickets as $ticket): ?>
                        <tr>
                            <td><?= $ticket['id'] ?></td>
                            <td><?= htmlspecialchars($ticket['title']) ?></td>
                            <td><?= $ticket['category'] ?></td>
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
