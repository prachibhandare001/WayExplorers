<?php
session_start();
include 'db.conn.php';

// Fetch all bookings with package title + user name
$query = "SELECT b.booking_id, b.travel_date, b.booking_date,
                 p.title AS package_title,
                 u.name AS customer_name,
                 u.email AS customer_email
          FROM booking b
          JOIN tour_package p ON b.package_id = p.package_id
          JOIN users u ON b.user_id = u.user_id
          ORDER BY b.booking_id DESC";

$result = $mysqli->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Bookings</title>
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
    <h2>All Bookings</h2>
    <a href="add_booking.php" class="btn edit">➕ Add New Booking</a>
    <br><br>
    <table>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Email</th>
            <th>Package</th>
            <th>Travel Date</th>
            <th>Booking Date</th>
            <th>Actions</th>
        </tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['booking_id'] ?></td>
            <td><?= htmlspecialchars($row['customer_name']) ?></td>
            <td><?= htmlspecialchars($row['customer_email']) ?></td>
            <td><?= htmlspecialchars($row['package_title']) ?></td>
            <td><?= $row['travel_date'] ?></td>
            <td><?= $row['booking_date'] ?></td>
            <td>
                <a href="edit_booking.php?id=<?= $row['booking_id'] ?>" class="btn edit">Edit</a>
                <a href="delete_booking.php?id=<?= $row['booking_id'] ?>" class="btn delete" onclick="return confirm('Delete this booking?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
