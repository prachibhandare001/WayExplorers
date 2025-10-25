<?php
// db.conn.php
// Database connection file

$host = 'sql303.infinityfree.com';
$db   = 'if0_39990373_toursandtravels';
$user = 'if0_39990373';
$pass = 'prachu040705';

// Enable error reporting (for debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die('Database connection failed: ' . $mysqli->connect_error);
}

// Force MySQLi to throw exceptions (instead of silent fails)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
?>
