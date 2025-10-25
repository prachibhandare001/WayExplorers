<?php
session_start();
include 'db.conn.php';

// Check if user logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if booking ID is given
if (!isset($_GET['id'])) {
    die("Error: booking id not provided");
}

$booking_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Update booking status to cancelled (only if it belongs to this user)
$stmt = $mysqli->prepare("UPDATE booking SET status = 'cancelled' WHERE booking_id = ? AND user_id = ?");
$stmt->bind_param("ii", $booking_id, $user_id);

if (!$stmt->execute()) {
    die("DB Error: " . $stmt->error);
}

// Redirect back
header("Location: booking_history.php");
exit();
?>
