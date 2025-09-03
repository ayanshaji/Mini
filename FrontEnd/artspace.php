<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "pixel";

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
  <title>ArtSpace - PixelMuse</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #1a1a1a;
      color: #e0e0e0;
    }

    header {
      background-color: #00bfff;
      color: white;
      padding: 20px;
      text-align: center;
      font-size: 2em;
      position: sticky;
      top: 0;
      z-index: 100;
      box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }

    .top-right-btn {
      position: absolute;
      top: 20px;
      right: 20px;
      background-color: #ffffff;
      color: #00bfff;
      padding: 10px 20px;
      border: none;
      border-radius: 24px;
      text-decoration: none;
      font-size: 16px;
      font-weight: bold;
      box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }

    .gallery {
      column-count: 5;
      column-gap: 15px;
      padding: 15px;
      max-width: 1800px;
      margin: 0 auto;
    }

    @media (max-width: 1200px) {
      .gallery {
        column-count: 4;
      }
    }
    @media (max-width: 1000px) {
      .gallery {
        column-count: 3;
      }
    }
    @media (max-width: 800px) {
      .gallery {
        column-count: 2;
      }
    }
    @media (max-width: 500px) {
      .gallery {
        column-count: 1;
      }
    }

    .pin {
      display: inline-block;
      margin-bottom: 15px;
      break-inside: avoid;
      position: relative;
      border-radius: 16px;
      overflow: hidden;
      background: #000000;
      box-shadow: 0 3px 6px rgba(0,0,0,0.3);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .pin:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 25px rgba(0,0,0,0.4);
    }

    .pin img {
      width: 100%;
      height: auto;
      display: block;
    }

    .pin-overlay {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, transparent 50%);
      opacity: 0;
      transition: opacity 0.2s ease;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      padding: 15px;
      color: white;
    }

    .pin:hover .pin-overlay {
      opacity: 1;
    }

    .pin-title {
      font-weight: bold;
      margin-bottom: 5px;
      font-size: 16px;
      text-shadow: 0 1px 3px rgba(0,0,0,0.8);
    }
  </style>
</head>
<body>

  <header>
    ArtSpace - Explore Unique Creations
    <a href="add.html" class="top-right-btn">Upload</a>
  </header>

  <div class="gallery">
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="pin">
        <a href="details.php?id=<?= $row['id'] ?>">
          <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
          <div class="pin-overlay">
            <div class="pin-title"><?= htmlspecialchars($row['title']) ?></div>
          </div>
        </a>
      </div>
    <?php endwhile; ?>
  </div>

</body>
</html>