<?php
session_start();
include 'db.conn.php';

if (!isset($_SESSION['user_id'])) header("Location: login.php");

$booking_id = intval($_GET['id'] ?? 0);
if (!$booking_id) die("Invalid booking ID");

// Fetch booking and package info
$stmt = $mysqli->prepare("SELECT b.booking_id, b.user_id, t.price, t.title, t.location, t.duration, t.image
                          FROM booking b 
                          JOIN tour_package t ON b.package_id=t.package_id
                          WHERE b.booking_id=?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$booking) die("Booking not found");

// Razorpay keys
$keyId = "rzp_test_RMEhB3kdVNisCY";
$keySecret = "xlxEtkLqHBkPWB8E0dcPVjhC";

// Create Razorpay order
$orderData = [
    "amount" => $booking['price'] * 100,
    "currency" => "INR",
    "receipt" => "booking_rcptid_" . $booking_id,
    "payment_capture" => 1
];

$ch = curl_init("https://api.razorpay.com/v1/orders");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
curl_setopt($ch, CURLOPT_USERPWD, $keyId . ":" . $keySecret);
$response = curl_exec($ch);
curl_close($ch);

$razorpayOrder = json_decode($response, true);
if (!isset($razorpayOrder['id'])) die("Failed to create Razorpay order");
$razorpayOrderId = $razorpayOrder['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Complete Payment</title>
<style>
body { font-family: Arial,sans-serif; background:#f4f4f9; margin:0; display:flex; justify-content:center; align-items:center; height:100vh; }
.card { background:#fff; padding:30px; border-radius:12px; box-shadow:0 6px 15px rgba(0,0,0,0.15); width:400px; text-align:center; }
.card img { max-width:100%; border-radius:8px; margin-bottom:15px; }
h2 { margin-top:0; color:#333; }
p { margin:8px 0; }
</style>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
<div class="card">
    <h2><?= htmlspecialchars($booking['title']) ?></h2>
    <?php if ($booking['image']): ?>
        <img src="<?= htmlspecialchars($booking['image']) ?>" alt="<?= htmlspecialchars($booking['title']) ?>">
    <?php endif; ?>
    <p><strong>Location:</strong> <?= htmlspecialchars($booking['location']) ?></p>
    <p><strong>Duration:</strong> <?= htmlspecialchars($booking['duration']) ?> days</p>
    <p><strong>Price:</strong> ₹<?= number_format($booking['price']) ?></p>

    <form action="verify_payment.php" method="POST">
        <script
            src="https://checkout.razorpay.com/v1/checkout.js"
            data-key="<?= $keyId ?>"
            data-amount="<?= $booking['price'] * 100 ?>"
            data-currency="INR"
            data-order_id="<?= $razorpayOrderId ?>"
            data-buttontext="Pay Now"
            data-name="Tour Booking"
            data-description="<?= htmlspecialchars($booking['title']) ?>"
            data-prefill.name="Your Name"
            data-prefill.email="you@example.com"
            data-theme.color="#ff6f3c">
        </script>
        <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
    </form>
</div>
</body>
</html>
