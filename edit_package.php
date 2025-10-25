<?php
// Turn on error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.conn.php';

if (!isset($_GET['id'])) {
    die("Error: package id not provided");
}

$id = intval($_GET['id']);  // sanitize

// Fetch package
$result = $mysqli->query("SELECT * FROM tour_package WHERE package_id = $id");
if (!$result) {
    die("DB Error (select package): " . $mysqli->error);
}
$pkg = $result->fetch_assoc();
if (!$pkg) {
    die("Error: Package not found");
}

// Decode safely
$existing_images = json_decode($pkg['images'] ?? '[]', true);
if (!is_array($existing_images)) {
    $existing_images = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $title = $_POST['title'] ?? '';
    $location = $_POST['location'] ?? '';
    $price = $_POST['price'] ?? '';
    $duration = $_POST['duration'] ?? '';
    $desc = $_POST['description'] ?? '';

    // Validate required fields
    if ($title === '' || $location === '' || $price === '' || $duration === '') {
        die("Please fill all required fields");
    }

    // Handle image_links
    $image_links = $_POST['image_links'] ?? [];
    $image_links = array_filter($image_links, function($v) { return trim($v) !== ''; });

    // old images
    $old_images = $existing_images;

    // remove_images
    $remove_images = $_POST['remove_images'] ?? [];
    if (!is_array($remove_images)) {
        $remove_images = [];
    }

    $remaining_images = array_diff($old_images, $remove_images);

    // merge with new
    $final_images = !empty($image_links) ? array_merge($remaining_images, $image_links) : $remaining_images;

    // reorder keys
    $final_images = array_values($final_images);

    $images_json = json_encode($final_images);

    $stmt = $mysqli->prepare("UPDATE tour_package SET title = ?, location = ?, price = ?, duration = ?, description = ?, images = ? WHERE package_id = ?");
    if (!$stmt) {
        die("DB prepare error: " . $mysqli->error);
    }
    $stmt->bind_param("ssisssi", $title, $location, $price, $duration, $desc, $images_json, $id);
    if (!$stmt->execute()) {
        die("DB execute error: " . $stmt->error);
    }

    header("Location: admindashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Package</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .form-container {
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 450px;
        }

        h2, h3 {
            text-align: center;
            color: #333;
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        textarea {
            resize: none;
            height: 80px;
        }

        button {
            background-color: #007BFF;
            color: white;
            font-weight: bold;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0056b3;
        }

        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .image-item {
            text-align: center;
            width: 100px;
        }

        .image-item img {
            width: 100%;
            border: 1px dashed #ccc;
            border-radius: 6px;
            padding: 4px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Edit Package</h2>
    <form method="post">
        <label>Title</label>
        <input type="text" name="title" value="<?= htmlspecialchars($pkg['title']) ?>">

        <label>Location</label>
        <input type="text" name="location" value="<?= htmlspecialchars($pkg['location']) ?>">

        <label>Price</label>
        <input type="number" name="price" value="<?= htmlspecialchars($pkg['price']) ?>">

        <label>Duration</label>
        <input type="text" name="duration" value="<?= htmlspecialchars($pkg['duration']) ?>">

        <label>Description</label>
        <textarea name="description"><?= htmlspecialchars($pkg['description']) ?></textarea>

        <h3>Existing Images</h3>
        <div class="image-preview">
            <?php if (count($existing_images) === 0): ?>
                <p>No existing images.</p>
            <?php else: ?>
                <?php foreach ($existing_images as $img): ?>
                    <div class="image-item">
                        <img src="<?= htmlspecialchars($img) ?>" alt="Package Image">
                        <label>
                            <input type="checkbox" name="remove_images[]" value="<?= htmlspecialchars($img) ?>"> Remove
                        </label>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <h3>Add New Image Links</h3>
        <input type="url" name="image_links[]" placeholder="Paste image link here">
        <input type="url" name="image_links[]" placeholder="Paste another link (optional)">
        <input type="url" name="image_links[]" placeholder="Paste another link (optional)">

        <button type="submit">Update</button>
    </form>
</div>
</body>
</html>
