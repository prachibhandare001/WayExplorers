<?php
session_start();
include 'db.conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$user_query = $mysqli->prepare("SELECT name, email, phone, profile_photo, created_at FROM users WHERE user_id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user = $user_query->get_result()->fetch_assoc();

// Fetch bookings
$booking_query = $mysqli->prepare("
    SELECT b.booking_id, b.travel_date, b.booking_date, t.title, t.location, t.price
    FROM booking b
    JOIN tour_package t ON b.package_id = t.package_id
    WHERE b.user_id = ?
");
$booking_query->bind_param("i", $user_id);
$booking_query->execute();
$bookings = $booking_query->get_result();

// Fetch payments
$payment_query = $mysqli->prepare("
    SELECT p.payment_id, p.method, p.amount, p.payment_date, p.status, b.booking_id
    FROM payment p
    JOIN booking b ON p.booking_id = b.booking_id
    WHERE p.user_id = ?
");
$payment_query->bind_param("i", $user_id);
$payment_query->execute();
$payments = $payment_query->get_result();


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f9;
      margin: 0;
      padding: 20px;
    }
    h2 { color: #333; }
    .card {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .profile {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .profile img {
      border-radius: 50%;
      margin-right: 20px;
      width: 80px;
      height: 80px;
      object-fit: cover;
      border: 2px solid #ccc;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    table, th, td {
      border: 1px solid #ddd;
    }
    th, td {
      padding: 10px;
      text-align: left;
    }
    th {
      background: #f0f0f0;
    }
    img.blog-img {
      max-width: 300px;
      margin: 10px 0;
      border-radius: 5px;
    }
    .btn {
      padding: 8px 15px;
      background: orange;
      border: none;
      border-radius: 5px;
      color: white;
      cursor: pointer;
      text-decoration: none;
    }
    .btn:hover {
      background: darkorange;
    }
  </style>
</head>
<body>

<h2>Welcome, <?= htmlspecialchars($user['name']) ?> 👋</h2>

<!-- Profile Section -->
<div class="card profile">
  <div style="display:flex;align-items:center;">
    <img src="<?= $user['profile_photo'] ?: 'default.png' ?>">
    <div>
      <h3><?= htmlspecialchars($user['name']) ?></h3>
      <p><?= htmlspecialchars($user['email']) ?> | <?= htmlspecialchars($user['phone']) ?></p>
      <small>Member since: <?= $user['created_at'] ?></small>
    </div>
  </div>
  <a href="update_profile.php" class="btn">Update Profile</a>
</div>

<!-- Bookings Section -->
<div class="card">
  <h3>My Bookings</h3>
  <?php if ($bookings->num_rows > 0): ?>
    <table>
      <tr>
        <th>Package</th>
        <th>Location</th>
        <th>Travel Date</th>
        <th>Booking Date</th>
        <th>Price</th>
      </tr>
      <?php while ($b = $bookings->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($b['title']) ?></td>
          <td><?= htmlspecialchars($b['location']) ?></td>
          <td><?= $b['travel_date'] ?></td>
          <td><?= $b['booking_date'] ?></td>
          <td>₹<?= $b['price'] ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p>No bookings yet.</p>
  <?php endif; ?>
</div>

<!-- Payments Section -->
<div class="card">
  <h3>My Payments</h3>
  <?php if ($payments->num_rows > 0): ?>
    <table>
      <tr>
        <th>Payment ID</th>
        <th>Booking ID</th>
        <th>Method</th>
        <th>Amount</th>
        <th>Date</th>
        <th>Status</th>
      </tr>
      <?php while ($p = $payments->fetch_assoc()): ?>
        <tr>
          <td><?= $p['payment_id'] ?></td>
          <td><?= $p['booking_id'] ?></td>
          <td><?= htmlspecialchars($p['method']) ?></td>
          <td>₹<?= $p['amount'] ?></td>
          <td><?= $p['payment_date'] ?></td>
          <td><?= htmlspecialchars($p['status']) ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p>No payments made yet.</p>
  <?php endif; ?>
</div>


</body>
</html>
