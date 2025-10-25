<?php
include 'db.conn.php';

// Check if id is provided
if (!isset($_GET['id'])) {
    die("Error: booking id not provided");
}

$id = intval($_GET['id']); // sanitize

// Delete booking
if (!$mysqli->query("DELETE FROM booking WHERE booking_id = $id")) {
    die("DB Error (delete booking): " . $mysqli->error);
}

// Redirect back
header("Location: admindashboard.php");
exit();
?>
