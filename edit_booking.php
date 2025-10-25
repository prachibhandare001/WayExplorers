<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.conn.php';

// Validate booking_id
if (!isset($_GET['id'])) {
    die("Error: booking id not provided");
}
$id = intval($_GET['id']);

// Fetch booking
$result = $mysqli->query("SELECT * FROM booking WHERE booking_id = $id");
if (!$result) {
    die("DB Error (select booking): " . $mysqli->error);
}
$booking = $result->fetch_assoc();
if (!$booking) {
    die("Error: Booking not found");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $package_id   = intval($_POST['package_id'] ?? 0);
    $user_id      = intval($_POST['user_id'] ?? 0);
    $travel_date  = $_POST['travel_date'] ?? '';
    $booking_date = $_POST['booking_date'] ?? '';
    $status       = $_POST['status'] ?? '';

    // Basic validation
    if ($package_id <= 0 || $user_id <= 0 || $travel_date === '' || $booking_date === '' || $status === '') {
        die("Please fill all required fields.");
    }

    // Prepare update
    $stmt = $mysqli->prepare("UPDATE booking SET package_id = ?, user_id = ?, travel_date = ?, booking_date = ?, status = ? WHERE booking_id = ?");
    if (!$stmt) {
        die("DB prepare error: " . $mysqli->error);
    }
    $stmt->bind_param("iisssi", $package_id, $user_id, $travel_date, $booking_date, $status, $id);

    if (!$stmt->execute()) {
        die("DB execute error: " . $stmt->error);
    }

    header("Location: admindashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .form-container {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 450px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        button {
            background-color: #007BFF;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Edit Booking</h2>
    <form method="post">
        <label>Package ID</label>
        <input type="number" name="package_id" value="<?= htmlspecialchars($booking['package_id']) ?>">

        <label>User ID</label>
        <input type="number" name="user_id" value="<?= htmlspecialchars($booking['user_id']) ?>">

        <label>Travel Date</label>
        <input type="date" name="travel_date" value="<?= htmlspecialchars($booking['travel_date']) ?>">

        <label>Booking Date</label>
        <input type="date" name="booking_date" value="<?= htmlspecialchars($booking['booking_date']) ?>">

        <label>Status</label>
        <select name="status">
            <option value="pending" <?= $booking['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="confirmed" <?= $booking['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="cancelled" <?= $booking['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>

        <button type="submit">Update Booking</button>
    </form>
</div>
</body>
</html>
