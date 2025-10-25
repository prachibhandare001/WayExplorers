<?php
session_start();
include 'db.conn.php';

$register_error = '';
$register_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = $_POST['password'];

    $errors = [];

    // Name validation (at least 2 chars)
    if (empty($name) || strlen($name) < 2) {
        $errors[] = "Name must be at least 2 characters long.";
    }

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strpos($email, '@') === false || strpos($email, '..') !== false) {
        $errors[] = "Invalid email format.";
    }

    // Phone validation (10 digits)
    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        $errors[] = "Phone number must be exactly 10 digits.";
    }

    // Password validation
    if (empty($password) || strlen($password) < 6 || strlen($password) > 18 || 
        !preg_match('/[^a-zA-Z0-9]/', $password)) {
        $errors[] = "Password must be 6–18 characters and include at least one special character.";
    }

    if (empty($errors)) {
        // Check if email exists
        $stmt = $mysqli->prepare('SELECT user_id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows > 0){
            $register_error = 'Email already registered';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt_insert = $mysqli->prepare('INSERT INTO users (name,email,phone,password) VALUES (?,?,?,?)');
            $stmt_insert->bind_param('ssss', $name, $email, $phone, $hashed_password);
            if($stmt_insert->execute()){
                $register_success = true;
            } else {
                $register_error = 'Registration failed';
            }
            $stmt_insert->close();
        }
        $stmt->close();
    } else {
        $register_error = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <link rel="stylesheet" href="style.css"> <!-- same CSS as login -->
  <script>
    function validateForm() {
      let name = document.forms["regForm"]["name"].value.trim();
      let email = document.forms["regForm"]["email"].value.trim();
      let phone = document.forms["regForm"]["phone"].value.trim();
      let password = document.forms["regForm"]["password"].value;

      let errors = [];

      if (name.length < 2) {
        errors.push("Name must be at least 2 characters long.");
      }

      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) || email.includes("..")) {
        errors.push("Invalid email format.");
      }

      if (!/^[0-9]{10}$/.test(phone)) {
        errors.push("Phone number must be exactly 10 digits.");
      }

      if (password.length < 6 || password.length > 18 || !/[^a-zA-Z0-9]/.test(password)) {
        errors.push("Password must be 6–18 characters and include at least one special character.");
      }

      if (errors.length > 0) {
        alert(errors.join("\n"));
        return false;
      }
      return true;
    }
  </script>
</head>
<body>
  <div class="container">
    <div class="card">
      <h2>Register</h2>
      <?php if($register_error) echo "<p class='message error'>$register_error</p>"; ?>
      <?php if($register_success) echo "<p class='message success'>Registration successful! <a href='login.php'>Login</a></p>"; ?>
      
      <form name="regForm" method="post" onsubmit="return validateForm()">
        <label>Name</label>
        <input type="text" name="name" required>
        <label>Email</label>
        <input type="email" name="email" required>
        <label>Phone</label>
        <input type="text" name="phone" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit">Register</button>
      </form>

      <p class="small">Already have an account? <a href="login.php">Login</a></p>
    </div>
  </div>
</body>
</html>
