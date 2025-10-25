<?php
session_start();
include 'db.conn.php';

// Fetch all users
$result = $mysqli->query("SELECT user_id, name, email, phone, created_at FROM users");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f8f9fa; padding:20px; }
        table { width:100%; border-collapse:collapse; background:#fff; }
        th, td { padding:10px; border:1px solid #ddd; text-align:left; }
        th { background:#f0f0f0; }
        a.btn { padding:6px 10px; border-radius:4px; text-decoration:none; }
        .edit { background:#28a745; color:white; }
        .delete { background:#dc3545; color:white; }
    </style>
</head>
<body>
    <h2>All Users</h2>
    <a href="add_user.php" class="btn edit">➕ Add New User</a>
    <br><br>
    <table>
        <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Created</th><th>Actions</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['user_id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= $row['created_at'] ?></td>
            <td>
                <a href="edit_user.php?id=<?= $row['user_id'] ?>" class="btn edit">Edit</a>
                <a href="delete_user.php?id=<?= $row['user_id'] ?>" class="btn delete" onclick="return confirm('Delete this user?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
