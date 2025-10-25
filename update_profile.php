<?php
session_start();
include 'db.conn.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = false;

// Fetch existing user details
$stmt = $mysqli->prepare("SELECT name, email, phone, profile_pic FROM users WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone, $profile_pic);
$stmt->fetch();
$stmt->close();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = $_POST['name'];
    $new_email = $_POST['email'];
    $new_phone = $_POST['phone'];
    $new_profile_pic = $profile_pic;

    // Handle profile picture upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . "_" . basename($_FILES['profile_pic']['name']);
        $target_file = $target_dir . $file_name;

        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $target_file)) {
                $new_profile_pic = $target_file;
            } else {
                $error = "Error uploading profile picture.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG & GIF allowed.";
        }
    }

    // If password field is filled, update it
    if (!empty($_POST['password'])) {
        $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("UPDATE users SET name=?, email=?, phone=?, password=?, profile_pic=? WHERE user_id=?");
        $stmt->bind_param("sssssi", $new_name, $new_email, $new_phone, $new_password, $new_profile_pic, $user_id);
    } else {
        $stmt = $mysqli->prepare("UPDATE users SET name=?, email=?, phone=?, profile_pic=? WHERE user_id=?");
        $stmt->bind_param("ssssi", $new_name, $new_email, $new_phone, $new_profile_pic, $user_id);
    }

    if ($stmt->execute()) {
        $success = true;
        $name = $new_name;
        $email = $new_email;
        $phone = $new_phone;
        $profile_pic = $new_profile_pic;
    } else {
        $error = "Failed to update profile.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Update Profile</title>
  <link rel="stylesheet" href="style.css"> <!-- same CSS -->
  <style>
    .profile-pic {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border: 2px dotted #007BFF;
      border-radius: 50%;
      margin-bottom: 10px;
    }
    .upload-label {
      display: block;
      margin-top: 10px;
      font-size: 14px;
      color: #333;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <h2>Update Profile</h2>
      <?php if($error) echo "<p class='message error'>$error</p>"; ?>
      <?php if($success) echo "<p class='message success'>Profile updated successfully!</p>"; ?>

      <form method="post" enctype="multipart/form-data">
        <?php if($profile_pic): ?>
          <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture" class="profile-pic">
        <?php else: ?>
          <img src="default.png" alt="Profile Picture" class="profile-pic">
        <?php endif; ?>

        <label class="upload-label">Change Profile Picture:</label>
        <input type="file" name="profile_pic" accept="image/*">

        <label>Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>

        <label>Phone</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>">

        <label>New Password (leave blank to keep current)</label>
        <input type="password" name="password" placeholder="Enter new password">

        <button type="submit">Update Profile</button>
      </form>
      <p class="small"><a href="homepage.php">Back to Dashboard</a></p>
    </div>
  </div>
</body>
</html>
