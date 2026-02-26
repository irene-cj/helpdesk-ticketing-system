<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';
requireLogin();

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category    = $_POST['category'];
    $priority    = $_POST['priority'];
    $user_id     = $_SESSION['user_id'];

    if (empty($title) || empty($description)) {
        $error = "Please fill in all required fields.";
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO tickets (user_id, title, description, category, priority, status) 
            VALUES (?, ?, ?, ?, ?, 'Open')
        ");
        $stmt->execute([$user_id, $title, $description, $category, $priority]);
$ticket_id = $pdo->lastInsertId();

// Send email notification
require_once '../email/notify.php';
$user_email = $_SESSION['name'];
$stmt2 = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt2->execute([$user_id]);
$user = $stmt2->fetch();


sendTicketNotification(
    $user['email'],
    "Ticket #$ticket_id Created — $title",
    "Hi {$_SESSION['name']},<br><br>
    Your support ticket has been submitted successfully.<br><br>
    <strong>Ticket #:</strong> $ticket_id<br>
    <strong>Title:</strong> $title<br>
    <strong>Priority:</strong> $priority<br>
    <strong>Category:</strong> $category<br>
    <strong>Status:</strong> Open<br><br>
    Our IT team will review your ticket shortly."
);

// Log notification
$stmt3 = $pdo->prepare("INSERT INTO notifications (ticket_id, sent_to, message) VALUES (?, ?, ?)");
$stmt3->execute([$ticket_id, $user['email'], "Ticket created notification sent"]);

header("Location: /helpdesk/tickets/view.php?id=" . $ticket_id);
exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Ticket | IT Help Desk</title>
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
        <?php else: ?>
            <a href="/helpdesk/staff/dashboard.php" class="text-white text-decoration-none">Dashboard</a>
        <?php endif; ?>
        <a href="/helpdesk/auth/logout.php" class="text-white text-decoration-none">Logout</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header">
                    <strong>Submit a New Support Ticket</strong>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" 
                                   placeholder="Brief description of the issue" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="5" 
                                      placeholder="Describe the issue in detail..." required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select">
                                    <option value="Hardware">Hardware</option>
                                    <option value="Software">Software</option>
                                    <option value="Network">Network</option>
                                    <option value="Account">Account</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Priority</label>
                                <select name="priority" class="form-select">
                                    <option value="Low">Low</option>
                                    <option value="Medium" selected>Medium</option>
                                    <option value="High">High</option>
                                    <option value="Critical">Critical</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Submit Ticket</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>