<?php
include 'db.conn.php';
$id = $_GET['id'];

$mysqli->query("DELETE FROM users WHERE user_id=$id");
header("Location: admindashboard.php");
exit();
?>
