<?php
session_start();

// Security check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.html');
    exit;
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "pixel";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query("SELECT * FROM artworks ORDER BY uploaded_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Gallery | PixelMuse</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f0f2f5;
      display: flex;
      flex-direction: column;
      height: 100vh;
    }
    .admin-header {
      background-color: #0a192f;
      color: white;
      padding: 15px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .header-left {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .back-btn {
      font-size: 20px;
      text-decoration: none;
      color: #64ffda;
      padding: 5px 10px;
      border-radius: 4px;
      transition: background 0.2s;
    }
    .back-btn:hover {
      background: #112240;
    }
    .logout-btn {
      background-color: #64ffda;
      color: #0a192f;
      border: none;
      padding: 8px 16px;
      border-radius: 4px;
      cursor: pointer;
      font-weight: bold;
    }
    .admin-content {
      padding: 20px;
      overflow-y: auto;
      flex: 1;
    }

    /* Pinterest-style Masonry */
    .gallery {
      column-count: 4;   /* number of columns */
      column-gap: 15px;  /* gap between columns */
    }
    @media (max-width: 1200px) {
      .gallery { column-count: 3; }
    }
    @media (max-width: 768px) {
      .gallery { column-count: 2; }
    }
    @media (max-width: 480px) {
      .gallery { column-count: 1; }
    }

    .card {
      background: white;
      display: inline-block; /* required for masonry */
      margin: 0 0 15px;
      width: 100%;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      overflow: hidden;
      break-inside: avoid; /* prevents breaking inside column */
    }
    .card img {
      width: 100%;
      display: block;
      border-bottom: 1px solid #ddd;
    }
    .card h4 {
      margin: 10px;
      color: #0a192f;
    }
    .card p {
      font-size: 14px;
      color: #555;
      margin: 0 10px 10px;
    }
  </style>
</head>
<body>

  <!-- Header -->
  <div class="admin-header">
    <div class="header-left">
      <a href="Admin-dash.php" class="back-btn">⬅</a>
      <h2>Gallery</h2>
    </div>
    <form action="admin-logout.php" method="post">
      <button type="submit" class="logout-btn">Logout</button>
    </form>
  </div>

  <!-- Main content -->
  <div class="admin-content">
    <h3>Uploaded Artworks</h3>
    <div class="gallery">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card">
          <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
          <h4><?= htmlspecialchars($row['title']) ?></h4>
          <p>By <?= htmlspecialchars($row['artist'] ?? "Unknown") ?></p>
          <p>₹<?= htmlspecialchars($row['price']) ?></p>
        </div>
      <?php endwhile; ?>
    </div>
  </div>

</body>
</html>
