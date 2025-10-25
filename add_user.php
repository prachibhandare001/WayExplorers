<?php
include 'db.conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Handle profile picture upload
    $profilePic = null;
    if (!empty($_FILES['profile_pic']['name'])) {
        $uploadDir = "uploads/"; // make sure this folder exists and is writable
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES['profile_pic']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetPath)) {
            $profilePic = $targetPath;
        }
    }

    $stmt = $mysqli->prepare("INSERT INTO users (name, email, phone, password, profile_pic, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssss", $name, $email, $phone, $password, $profilePic);
    $stmt->execute();

    header("Location: admindashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add User</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f9fafb;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    form {
      background: #fff;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      width: 340px;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }
    label {
      display: block;
      margin-bottom: 5px;
      font-size: 14px;
      color: #555;
    }
    input {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 14px;
      transition: border 0.2s;
    }
    input:focus {
      border-color: #ff6600;
      outline: none;
    }
    button {
      width: 100%;
      padding: 10px;
      background: #ff6600;
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 15px;
      cursor: pointer;
      transition: background 0.2s;
    }
    button:hover {
      background: #e65c00;
    }
  </style>
</head>
<body>
  <form method="post" enctype="multipart/form-data">
    <h2>Add User</h2>
    
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required>
    
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    
    <label for="phone">Phone:</label>
    <input type="text" id="phone" name="phone">
    
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    
    <label for="profile_pic">Profile Picture:</label>
    <input type="file" id="profile_pic" name="profile_pic" accept="image/*">
    
    <button type="submit">Add</button>
  </form>
</body>
</html>
