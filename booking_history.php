<?php
session_start();
include 'db.conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch booking history with payment + booking status
$sql = "
    SELECT b.booking_id, b.booking_date, b.travel_date, b.status,
           t.title, t.location, t.duration, t.price, t.image,
           IF(p.status='paid', 'Paid', 'Pending') AS payment_status
    FROM booking b
    JOIN tour_package t ON b.package_id = t.package_id
    LEFT JOIN payment p ON b.booking_id = p.booking_id
    WHERE b.user_id = ?
    GROUP BY b.booking_id
    ORDER BY b.booking_date DESC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Booking History</title>
<style>
body { font-family: Arial, sans-serif; background:#f4f4f9; margin:0; padding:20px; }
h2 { color:#333; display:inline-block; margin-right:20px; }
.home-btn { text-decoration:none; background:#ff6f3c; color:#fff; padding:10px 18px; border-radius:8px; font-weight:bold; }
.home-btn:hover { background:#e65c2f; }
.card { background:#fff; border-radius:10px; padding:15px; margin-bottom:20px; box-shadow:0 2px 5px rgba(0,0,0,0.1); display:flex; gap:15px; }
.card img { max-width:150px; border-radius:5px; object-fit:cover; }
.card-content { flex:1; }
.status-tag, .payment-status { font-weight:bold; padding:4px 8px; border-radius:6px; display:inline-block; }
.paid { background:#d4edda; color:#155724; }
.pending { background:#fff3cd; color:#856404; }
.confirmed { background:#cce5ff; color:#004085; }
.cancelled { background:#f8d7da; color:#721c24; }
.cancel-btn, .pay-btn { display:inline-block; margin-top:10px; padding:8px 14px; border:none; border-radius:6px; font-weight:bold; cursor:pointer; text-decoration:none; color:#fff; }
.cancel-btn { background:#dc3545; }
.cancel-btn:hover { background:#c82333; }
.pay-btn { background:#28a745; }
.pay-btn:hover { background:#218838; }
</style>
</head>
<body>

<h2>My Booking History</h2>

<a href="homepage.php" class="home-btn">Go to Homepage</a>
<?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="card">
            <?php if ($row['image']): ?>
                <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
            <?php endif; ?>
            <div class="card-content">
                <h3><?= htmlspecialchars($row['title']) ?> (<?= htmlspecialchars($row['location']) ?>)</h3>
                <p><strong>Duration:</strong> <?= htmlspecialchars($row['duration']) ?> days</p>
                <p><strong>Price:</strong> ₹<?= number_format($row['price']) ?></p>
                <p><strong>Travel Date:</strong> <?= $row['travel_date'] ?></p>
                <p><strong>Booked On:</strong> <?= $row['booking_date'] ?></p>

                <p>
                    <strong>Booking Status:</strong>
                    <span class="status-tag <?= strtolower($row['status']) ?>">
                        <?= ucfirst($row['status']) ?>
                    </span>
                </p>

                <p>
                    <strong>Payment Status:</strong>
                    <span class="payment-status <?= strtolower($row['payment_status']) ?>">
                        <?= $row['payment_status'] ?>
                    </span>
                </p>

                <?php if (strtolower($row['status']) !== 'cancelled'): ?>
                    <a href="cancel_booking.php?id=<?= $row['booking_id'] ?>" class="cancel-btn" 
                       onclick="return confirm('Are you sure you want to cancel this booking?');">
                        Cancel Booking
                    </a>
                <?php endif; ?>

                <?php if ($row['payment_status'] === 'Pending' && strtolower($row['status']) !== 'cancelled'): ?>
                    <a href="pay_now.php?id=<?= $row['booking_id'] ?>" class="pay-btn">
                        Pay Now
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>You have no bookings yet.</p>
<?php endif; ?>

</body>
</html>
