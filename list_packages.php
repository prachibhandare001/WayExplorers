<?php
session_start();
include 'db.conn.php';

// Fetch all packages
$result = $mysqli->query("SELECT package_id, title, location, price, duration FROM tour_package");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Packages</title>
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
    <h2>All Tour Packages</h2>
    <a href="add_package.php" class="btn edit">➕ Add New Package</a>
    <br><br>
    <table>
        <tr>
            <th>ID</th><th>Title</th><th>Location</th><th>Price</th><th>Duration</th><th>Actions</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['package_id'] ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['location']) ?></td>
            <td>₹<?= $row['price'] ?></td>
            <td><?= $row['duration'] ?> days</td>
            <td>
                <a href="edit_package.php?id=<?= $row['package_id'] ?>" class="btn edit">Edit</a>
                <a href="delete_package.php?id=<?= $row['package_id'] ?>" class="btn delete" onclick="return confirm('Delete this package?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
