<?php
include 'db.conn.php';
$id = $_GET['id'];

$mysqli->query("DELETE FROM tour_package WHERE package_id=$id");
header("Location: admindashboard.php");
exit();
?>
