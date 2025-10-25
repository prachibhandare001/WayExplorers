<?php
session_start();
include 'db.conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Razorpay keys
$keyId = "rzp_test_RMEhB3kdVNisCY";  
$keySecret = "xlxEtkLqHBkPWB8E0dcPVjhC"; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['travel_date'], $_POST['package_id'], $_POST['action'])) {
    $package_id = intval($_POST['package_id']);
    $travel_date = $_POST['travel_date'];
    $action = $_POST['action']; // pay_now or pay_later

    // Fetch package info
    $stmt = $mysqli->prepare("SELECT title, location, price, duration FROM tour_package WHERE package_id=?");
    $stmt->bind_param("i", $package_id);
    $stmt->execute();
    $package = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$package) die("Package not found.");

    $amount = $package['price'];

    if ($action === "pay_later") {
        // Insert booking (confirmed but no payment yet)
        $stmt = $mysqli->prepare("INSERT INTO booking (package_id, user_id, travel_date, booking_date, status) VALUES (?, ?, ?, NOW(), 'confirmed')");
        $stmt->bind_param("iis", $package_id, $user_id, $travel_date);
        $stmt->execute();
        $booking_id = $stmt->insert_id;
        $stmt->close();

        // Insert into payment table with pending status
        $stmt = $mysqli->prepare("INSERT INTO payment (booking_id, user_id, method, amount, status) VALUES (?, ?, 'Pay Later', ?, 'pending')");
        $stmt->bind_param("iid", $booking_id, $user_id, $amount);
        $stmt->execute();
        $stmt->close();

        header("Location: booking_history.php?msg=Booking confirmed (Pay Later selected)");
        exit();
    }

    if ($action === "pay_now") {
        // Insert booking (pending until Razorpay confirmation)
        $stmt = $mysqli->prepare("INSERT INTO booking (package_id, user_id, travel_date, booking_date, status) VALUES (?, ?, ?, NOW(), 'pending')");
        $stmt->bind_param("iis", $package_id, $user_id, $travel_date);
        $stmt->execute();
        $booking_id = $stmt->insert_id;
        $stmt->close();

        // Create Razorpay order
        $url = "https://api.razorpay.com/v1/orders";
        $orderData = [
            "amount" => $amount * 100,
            "currency" => "INR",
            "receipt" => "booking_rcptid_" . $booking_id,
            "payment_capture" => 1
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_USERPWD, $keyId . ":" . $keySecret);
        $response = curl_exec($ch);
        if (curl_errno($ch)) die("Curl error: " . curl_error($ch));
        curl_close($ch);

        $razorpayOrder = json_decode($response, true);
        if (!isset($razorpayOrder['id'])) die("Razorpay order creation failed");
        $razorpayOrderId = $razorpayOrder['id'];

        // Insert into payment table (initiated)
        $stmt = $mysqli->prepare("INSERT INTO payment (booking_id, user_id, method, amount, status, razorpay_order_id) VALUES (?, ?, 'Razorpay', ?, 'initiated', ?)");
        $stmt->bind_param("iids", $booking_id, $user_id, $amount, $razorpayOrderId);
        $stmt->execute();
        $stmt->close();
    }
}
elseif (isset($_GET['package_id'])) {
    $package_id = intval($_GET['package_id']);
    $stmt = $mysqli->prepare("SELECT package_id, title, location, price, duration FROM tour_package WHERE package_id=?");
    $stmt->bind_param("i", $package_id);
    $stmt->execute();
    $package = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$package) die("Package not found.");
} else { 
    die("No package selected."); 
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Book Tour</title>
<style>
body { font-family: Arial, sans-serif; background:#f7fafc; margin:0; padding:0; height:100vh; display:flex; justify-content:center; align-items:center; }
.card { max-width:700px; min-width:400px; width:90%; min-height:500px; background:#fff; padding:40px 25px; border-radius:12px; box-shadow:0 6px 15px rgba(0,0,0,0.15); text-align:center; display:flex; flex-direction:column; justify-content:center; }
h2 { margin-top:0; } 
p { margin:8px 0; } 
label { display:block; margin:15px 0 8px; text-align:left; font-weight:bold; }
input[type=date] { padding:12px; width:100%; border:1px solid #ccc; border-radius:8px; font-size:14px; }
button { display:block; width:100%; margin-top:20px; padding:12px 16px; border:none; background:#ff6f3c; color:white; border-radius:8px; cursor:pointer; font-size:16px; }
button:hover { background:#e65c2f; }
.cancel-link { display:block; width:100%; margin-top:15px; padding:12px 0; border:1px solid #ccc; border-radius:8px; text-align:center; text-decoration:none; color:#555; background:#f4f4f9; font-size:16px; }
.cancel-link:hover { background:#eaeaea; }
.paylater-btn { background:#555; }
</style>
</head>
<body>
<div class="card">
    <h2><?= isset($booking_id) && isset($razorpayOrderId) ? "Complete Payment" : "Confirm Booking" ?></h2>
    <p><strong><?= htmlspecialchars($package['title']) ?></strong></p>
    <p><?= htmlspecialchars($package['location']) ?> · <?= htmlspecialchars($package['duration']) ?> days</p>
    <p>Price: ₹<?= number_format($package['price']) ?></p>

    <?php if (!isset($booking_id)): ?>
    <!-- Booking form -->
    <form method="post" action="book.php">
        <input type="hidden" name="package_id" value="<?= $package['package_id'] ?>">
        <label for="travel_date">Select Travel Date:</label>
        <input type="date" name="travel_date" id="travel_date" required min="<?= date('Y-m-d') ?>">
        <button type="submit" name="action" value="pay_now">Proceed to Pay</button>
        <button type="submit" name="action" value="pay_later" class="paylater-btn">Pay Later</button>
    </form>
    <a href="tours_homepage.php" class="cancel-link">Cancel</a>
    <?php elseif(isset($razorpayOrderId)): ?>
    <!-- Razorpay Checkout -->
    <form action="verify_payment.php" method="POST">
        <script src="https://checkout.razorpay.com/v1/checkout.js"
            data-key="<?= $keyId ?>"
            data-amount="<?= $amount * 100 ?>"
            data-currency="INR"
            data-order_id="<?= $razorpayOrderId ?>"
            data-buttontext="Pay with Razorpay"
            data-name="Tour Booking"
            data-description="Booking Payment"
            data-prefill.name="Your Name"
            data-prefill.email="you@example.com"
            data-theme.color="#ff6f3c">
        </script>
        <input type="hidden" name="booking_id" value="<?= $booking_id ?>">
        <input type="hidden" name="razorpay_order_id" value="<?= $razorpayOrderId ?>">
    </form>
    <?php endif; ?>
</div>
</body>
</html>
