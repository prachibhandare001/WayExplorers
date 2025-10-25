<?php
// profile.php
// User profile page with database-backed updates using external DB connection

session_start();
include 'db.conn.php'; // Include database connection

// Assume user is logged in and has id stored in session
if (!isset($_SESSION['user_id'])) {
    // For demo purposes, we'll set a default user_id
    $_SESSION['user_id'] = 1;
}
$user_id = $_SESSION['user_id'];

// Fetch user info from database
$stmt = $mysqli->prepare('SELECT name, email, phone, location, bio FROM users WHERE id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$updated = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $bio = $_POST['bio'];

    $stmt = $mysqli->prepare('UPDATE users SET name=?, email=?, phone=?, location=?, bio=? WHERE id=?');
    $stmt->bind_param('sssssi', $name, $email, $phone, $location, $bio, $user_id);
    $stmt->execute();
    $stmt->close();

    $updated = true;

    // Refresh user info
    $stmt = $mysqli->prepare('SELECT name, email, phone, location, bio FROM users WHERE id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>User Profile</title>
    <style>
        :root{
            --accent:#ff6f3c;
            --muted:#6b7280;
            font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
        }
        body{margin:0;background:#f7fafc;color:#111;}
        .container{max-width:800px;margin:40px auto;padding:20px;background:#fff;border-radius:12px;box-shadow:0 6px 18px rgba(17,24,39,0.08)}
        h1{margin-top:0;color:var(--accent)}
        label{display:block;margin-top:12px;font-weight:600}
        input,textarea{width:100%;padding:10px;border-radius:8px;border:1px solid #e5e7eb;margin-top:4px}
        textarea{resize:vertical}
        .btn{margin-top:16px;background:var(--accent);color:#fff;padding:10px 16px;border:none;border-radius:8px;cursor:pointer}
        .msg{margin-bottom:12px;color:green}
        .profile-info{margin-bottom:24px}
        .profile-info div{margin:6px 0;color:var(--muted)}
    </style>
</head>
<body>
<div class="container">
    <h1>Your Profile</h1>
    <?php if ($updated): ?>
        <div class="msg">Profile updated successfully ✅</div>
    <?php endif; ?>

    <div class="profile-info">
        <strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?><br>
        <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?><br>
        <strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?><br>
        <strong>Location:</strong> <?php echo htmlspecialchars($user['location']); ?><br>
        <strong>Bio:</strong> <?php echo htmlspecialchars($user['bio']); ?><br>
    </div>

    <form method="post">
        <label for="name">Full Name</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['name']); ?>">

        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>">

        <label for="phone">Phone</label>
        <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

        <label for="location">Location</label>
        <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($user['location']); ?>">

        <label for="bio">Bio</label>
        <textarea name="bio" id="bio" rows="4"><?php echo htmlspecialchars($user['bio']); ?></textarea>

        <button type="submit" class="btn">Update Profile</button>
    </form>
</div>
</body>
</html>