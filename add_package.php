<?php
include 'db.conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $location = $_POST['location'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];
    $desc = $_POST['description'];

    // handle image links (comma separated)
    $images = array_map('trim', explode(",", $_POST['images']));
    $imagesJson = json_encode($images);

    $stmt = $mysqli->prepare("INSERT INTO tour_package (title, location, price, duration, description, images) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisss", $title, $location, $price, $duration, $desc, $imagesJson);
    $stmt->execute();

    header("Location: admindashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Package</title>
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
    input, textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ddd;
      border-radius: 6px;
      font-size: 14px;
      transition: border 0.2s;
    }
    input:focus, textarea:focus {
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
    small {
      font-size: 12px;
      color: #777;
      display: block;
      margin-top: -10px;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <form method="post">
    <h2>Add Package</h2>
    
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" required>
    
    <label for="location">Location:</label>
    <input type="text" id="location" name="location" required>
    
    <label for="price">Price:</label>
    <input type="number" id="price" name="price" required>
    
    <label for="duration">Duration:</label>
    <input type="text" id="duration" name="duration" required>
    
    <label for="description">Description:</label>
    <textarea id="description" name="description"></textarea>
    
    <label for="images">Image Links:</label>
    <input type="text" id="images" name="images" placeholder="Paste URLs separated by commas">
    <small>Example: https://img1.com, https://img2.com, https://img3.com</small>
    
    <button type="submit">Add</button>
  </form>
</body>
</html>
