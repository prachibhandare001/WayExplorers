<?php
session_start();
include 'db.conn.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') die("Invalid request");

$booking_id = intval($_POST['booking_id'] ?? 0);
$razorpay_order_id = $_POST['razorpay_order_id'] ?? '';
$razorpay_payment_id = $_POST['razorpay_payment_id'] ?? '';
$razorpay_signature = $_POST['razorpay_signature'] ?? '';

if (!$booking_id || !$razorpay_order_id || !$razorpay_payment_id || !$razorpay_signature) die("Missing payment data");

$keySecret = "xlxEtkLqHBkPWB8E0dcPVjhC";

// Verify signature
$generated_signature = hash_hmac('sha256', $razorpay_order_id . "|" . $razorpay_payment_id, $keySecret);
if ($generated_signature !== $razorpay_signature) die("Payment verification failed");

// Fetch booking
$stmt = $mysqli->prepare("SELECT user_id, package_id FROM booking WHERE booking_id=?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$booking) die("Booking not found");

// Fetch package price
$stmt = $mysqli->prepare("SELECT price FROM tour_package WHERE package_id=?");
$stmt->bind_param("i", $booking['package_id']);
$stmt->execute();
$price = $stmt->get_result()->fetch_assoc()['price'];
$stmt->close();

// Insert or update payment
$stmt = $mysqli->prepare("SELECT payment_id FROM payment WHERE booking_id=?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$exists = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($exists) {
    $stmt = $mysqli->prepare("
        UPDATE payment 
        SET status='paid', method='Razorpay', amount=?, payment_date=NOW(),
            razorpay_order_id=?, razorpay_payment_id=?, razorpay_signature=?
        WHERE booking_id=?
    ");
    $stmt->bind_param("isssi", $price, $razorpay_order_id, $razorpay_payment_id, $razorpay_signature, $booking_id);
} else {
    $stmt = $mysqli->prepare("
        INSERT INTO payment (booking_id, user_id, method, amount, payment_date, status, razorpay_order_id, razorpay_payment_id, razorpay_signature)
        VALUES (?, ?, 'Razorpay', ?, NOW(), 'paid', ?, ?, ?)
    ");
    $stmt->bind_param("iiisss", $booking_id, $booking['user_id'], $price, $razorpay_order_id, $razorpay_payment_id, $razorpay_signature);
}
$stmt->execute();
$stmt->close();

// Update booking
$stmt = $mysqli->prepare("UPDATE booking SET status='confirmed' WHERE booking_id=?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Payment Successful</title>
<style>
body { font-family: Arial,sans-serif; background:#f7fafc; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }
.card { background:#fff; padding:40px 30px; border-radius:12px; box-shadow:0 6px 15px rgba(0,0,0,0.15); text-align:center; max-width:500px; }
h2 { margin-top:0; color:#28a745; }
a { display:inline-block; margin-top:20px; text-decoration:none; background:#ff6f3c; color:#fff; padding:12px 20px; border-radius:8px; }
a:hover { background:#e65c2f; }
</style>
</head>
<body>
<div class="card">
<h2>✅ Payment Successful!</h2>
<p>Your booking has been confirmed.</p>
<a href="booking_history.php">View My Bookings</a>
</div>
</body>
</html>
