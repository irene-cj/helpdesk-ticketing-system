<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';
requireAdmin();

// Handle role change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$_POST['role'], $_POST['user_id']]);
    header("Location: users.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    if ($delete_id !== $_SESSION['user_id']) {
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$delete_id]);
    }
    header("Location: users.php");
    exit();
}

$users = $pdo->query("
    SELECT u.*, COUNT(t.id) as ticket_count 
    FROM users u 
    LEFT JOIN tickets t ON u.id = t.user_id 
    GROUP BY u.id 
    ORDER BY u.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users | IT Help Desk</title>
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
    <h4 class="mb-4">Manage Users</h4>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Tickets</th>
                        <th>Joined</th>
                        <th>Change Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span class="badge bg-<?= $user['role'] === 'admin' ? 'dark' : 'primary' ?>">
                                <?= $user['role'] ?>
                            </span>
                        </td>
                        <td><?= $user['ticket_count'] ?></td>
                        <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                <select name="role" class="form-select form-select-sm">
                                    <option value="staff" <?= $user['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-dark">Save</button>
                            </form>
                            <?php else: ?>
                                <span class="text-muted small">You</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                <a href="users.php?delete=<?= $user['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete this user?')">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>