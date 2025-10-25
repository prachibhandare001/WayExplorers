<?php
session_start();
include 'db.conn.php';

// Handle search & price filter
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (int)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (int)$_GET['max_price'] : null;

// Build query dynamically
$query = "SELECT * FROM tour_package WHERE 1";
$params = [];
$types = "";

if ($search) {
    $query .= " AND (title LIKE ? OR location LIKE ? OR description LIKE ?)";
    $like = "%" . $search . "%";
    $params[] = $like; $params[] = $like; $params[] = $like;
    $types .= "sss";
}

if ($min_price !== null && $max_price !== null) {
    $query .= " AND price BETWEEN ? AND ?";
    $params[] = $min_price;
    $params[] = $max_price;
    $types .= "ii";
} elseif ($min_price !== null) {
    $query .= " AND price >= ?";
    $params[] = $min_price;
    $types .= "i";
} elseif ($max_price !== null) {
    $query .= " AND price <= ?";
    $params[] = $max_price;
    $types .= "i";
}

$stmt = $mysqli->prepare($query);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$tours = $stmt->get_result();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>WayExplorers Tours — Book Your Next Adventure</title>
    <style>
        :root{
            --accent:#ff6f3c;
            --muted:#6b7280;
            --card-bg:#ffffff;
            --page-bg:#f7fafc;
            --max-width:1100px;
            font-family: Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial;
        }
        *{box-sizing:border-box}
        body{margin:0;background:var(--page-bg);color:#111}
        .container{max-width:var(--max-width);margin:32px auto;padding:0 16px}
        header{display:flex;align-items:center;justify-content:space-between}
        .brand{display:flex;gap:12px;align-items:center}
        .logo {    width: 48px;
                height: 48px;
                border-radius: 10px;
                overflow: hidden; /* ensures image stays inside rounded shape */
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .logo img {
                width: 100%;
                height: 100%;
                object-fit: cover; /* keeps proportions */
            }

        nav a{margin-left:18px;text-decoration:none;color:var(--muted)}
        nav .btn-nav{padding:6px 12px;border-radius:6px;border:1px solid #e6e9ee;background:#fff;text-decoration:none;color:#111;margin-left:10px}
        .hero{display:flex;gap:24px;align-items:center;margin-top:28px}
        .hero-left{flex:1}
        .hero h1{font-size:32px;margin:0 0 8px}
        .hero p{color:var(--muted);margin:0 0 16px}
        .search{display:flex;gap:8px;flex-wrap:wrap}
        .search input[type="search"]{flex:1;min-width:200px}
        .search input[type="number"]{width:120px}
        .search input, .search select{padding:10px 12px;border-radius:10px;border:1px solid #e6e9ee}
        .btn{background:var(--accent);color:white;padding:10px 16px;border-radius:10px;border:none;cursor:pointer}
        .grid{display:grid;grid-template-columns: repeat(3, 1fr); ;gap:16px;margin-top:20px}
        .card{background:var(--card-bg);padding:14px;border-radius:12px;box-shadow:0 6px 18px rgba(17,24,39,0.04);display:flex;flex-direction:column}
        .card img{width:100%;height:180px;object-fit:cover;border-radius:8px}
        .card h3{margin:10px 0 6px;font-size:18px}
        .meta{display:flex;justify-content:space-between;align-items:center;margin-top:auto}
        .meta .price{font-weight:700;color:var(--accent)}
        footer{margin:40px 0 80px;color:var(--muted);text-align:center}
        @media (max-width:900px){.grid{grid-template-columns:repeat(2,1fr)}}
        @media (max-width:600px){.hero{flex-direction:column}.grid{grid-template-columns:1fr}.logo{width:40px;height:40px}}
    </style>
</head>
<body>
<div class="container">
    <header>
        <div class="brand">
            <div class="logo">
            	<img src="images/logo.jpg" alt="Way Explorers Logo">
            </div>
            <div>
                <div style="font-weight:700">Way Explorers</div>
                <div style="font-size:12px;color:var(--muted)">Tours &amp; Bookings</div>
            </div>
        </div>
        <nav>
            <a href="homepage.php">Home</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="userdashboard.php" class="btn-nav">Profile</a>
                <a href="booking_history.php" class="btn-nav">Booking History</a>
                <a href="logout.php" class="btn-nav">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-nav">Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-left">
            <h1>Find the perfect trip for every explorer</h1>
            <p>Handpicked tours, trusted guides, flexible booking. Explore mountains, beaches, and culture with easy online booking.</p>

            <form class="search" method="get">
                <input type="search" name="q" placeholder="Search destination, activity or keyword" value="<?= htmlspecialchars($search) ?>">
                <input type="number" name="min_price" placeholder="Min price" value="<?= htmlspecialchars($min_price ?? '') ?>">
                <input type="number" name="max_price" placeholder="Max price" value="<?= htmlspecialchars($max_price ?? '') ?>">
                <button class="btn" type="submit">Search</button>
            </form>
        </div>
    </section>

    <section id="tours">
        <h2 style="margin:24px 0 12px">Featured Tours</h2>
        <?php if($tours->num_rows==0): ?>
            <p style="color:var(--muted)">No tours match your search.</p>
        <?php else: ?>
        <div class="grid">
            <?php while($t = $tours->fetch_assoc()): ?>
                <?php 
                    // decode JSON images (links stored in DB)
                    $imgs = json_decode($t['images'], true); 
                    $firstImg = $imgs[0] ?? "images/placeholder.jpg"; 
                ?>
                <div class="card">
                    <img src="<?= htmlspecialchars($firstImg) ?>" alt="<?= htmlspecialchars($t['title']) ?>">
                    <h3><?= htmlspecialchars($t['title']) ?></h3>
                    <div style="font-size:13px;color:var(--muted)">
                        <?= htmlspecialchars($t['location']) ?> · <?= htmlspecialchars($t['duration']) ?> days
                    </div>
                    <p style="color:var(--muted);font-size:14px"><?= htmlspecialchars($t['description']) ?></p>
                    <div class="meta">
                        <div class="price">₹<?= number_format($t['price']) ?></div>
                        <a href="book.php?package_id=<?= (int)$t['package_id'] ?>" class="btn" style="padding:8px 10px;font-size:14px">Book</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </section>

    <footer>
        &copy; <?= date('Y') ?> WanderWay Tours — Crafted with ❤️ ·
    </footer>
</div>
</body>
</html>
