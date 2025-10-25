<?php
session_start();
$user_id = $_SESSION['user_id'];
$booking_id = $_GET['booking_id'];
$order_id = $_GET['order_id'];
$amount = $_GET['amount']; // in INR
$key_id = "rzp_test_RMEhB3kdVNisCY";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pay with Razorpay</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <button id="payBtn">Pay ₹<?php echo $amount; ?></button>

    <script>
    document.getElementById('payBtn').onclick = function(e){
        var options = {
            "key": "<?php echo $key_id; ?>",
            "amount": "<?php echo $amount*100; ?>",
            "currency": "INR",
            "name": "Tour Booking",
            "description": "Booking ID #<?php echo $booking_id; ?>",
            "order_id": "<?php echo $order_id; ?>",
            "handler": function (response){
                window.location.href = "verify.php?booking_id=<?php echo $booking_id; ?>&razorpay_payment_id=" + response.razorpay_payment_id + "&razorpay_order_id=" + response.razorpay_order_id + "&razorpay_signature=" + response.razorpay_signature;
            },
            "theme": { "color": "#ff6f3c" }
        };
        var rzp1 = new Razorpay(options);
        rzp1.open();
        e.preventDefault();
    }
    </script>
</body>
</html>
