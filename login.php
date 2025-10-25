<?php
session_start();
include 'db.conn.php';

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // ✅ also select is_admin column
    $stmt = $mysqli->prepare('SELECT user_id, password, is_admin FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($user_id, $hashed_password, $is_admin);

    if ($stmt->fetch()) {
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['is_admin'] = $is_admin;

            // ✅ Redirect based on role
            if ($is_admin == 1) {
                header('Location: admindashboard.php');
            } else {
                header('Location: homepage.php');
            }
            exit;
        } else {
            $login_error = 'Invalid password';
        }
    } else {
        $login_error = 'Email not found';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <div class="card">
      <h2>Login</h2>
      <?php if($login_error) echo "<p class='message error'>$login_error</p>"; ?>
      <form method="post">
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
      </form>
      <p class="small">Don’t have an account? <a href="index.php">Register</a></p>
    </div>
  </div>
</body>
</html>
