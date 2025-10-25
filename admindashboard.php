<?php
session_start();
// optional: check if logged-in user is admin
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .header {
            background: #333;
            color: #fff;
            padding: 15px;
            text-align: center;
        }
        .container {
            width: 80%;
            margin: 30px auto;
        }
        .card {
            background: #fff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        h2 {
            margin-top: 0;
        }
        .actions a {
            display: inline-block;
            margin: 5px;
            padding: 10px 15px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }
        .actions a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Dashboard</h1>
    </div>

    <div class="container">

        <!-- Users Management -->
        <div class="card">
            <h2>Manage Users</h2>
            <div class="actions">
                <a href="add_user.php">Add User</a>
                <a href="list_users.php">View / Edit / Delete Users</a>
            </div>
        </div>

        <!-- Packages Management -->
        <div class="card">
            <h2>Manage Tour Packages</h2>
            <div class="actions">
                <a href="add_package.php">Add Package</a>
                <a href="list_packages.php">View / Edit / Delete Packages</a>
            </div>
        </div>

        <!-- Bookings -->
        <div class="card">
            <h2>Manage Bookings</h2>
            <div class="actions">
                <a href="list_bookings.php">View / Edit / Delete Bookings</a>
            </div>
        </div>


    </div>
</body>
</html>
