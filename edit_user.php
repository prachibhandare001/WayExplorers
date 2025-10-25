<?php
include 'db.conn.php';
$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];

    $stmt = $mysqli->prepare("UPDATE users SET name=?, phone=? WHERE user_id=?");
    $stmt->bind_param("ssi", $name, $phone, $id);
    $stmt->execute();
    header("Location: admindashboard.php");
    exit();
}

$user = $mysqli->query("SELECT * FROM users WHERE user_id=$id")->fetch_assoc();
?>
<form method="post">
    <h2>Edit User</h2>
    Name: <input type="text" name="name" value="<?= $user['name'] ?>"><br>
    Phone: <input type="text" name="phone" value="<?= $user['phone'] ?>"><br>
    <button type="submit">Update</button>
</form>
