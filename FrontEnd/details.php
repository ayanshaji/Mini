<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "pixel";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? 0;

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    $author_name = "Anonymous"; // You can replace with logged-in user's name
    
    if (!empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO comments (artwork_id, author_name, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id, $author_name, $comment);
        
        if ($stmt->execute()) {
            $success_message = "Comment posted successfully!";
        } else {
            $error_message = "Failed to post comment. Please try again.";
        }
        $stmt->close();
    } else {
        $error_message = "Comment cannot be empty!";
    }
}

// Fetch artwork details
$stmt = $conn->prepare("SELECT * FROM artworks WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$art = $result->fetch_assoc();
$stmt->close();

// Fetch comments
$comments = [];
$comment_stmt = $conn->prepare("SELECT * FROM comments WHERE artwork_id = ? ORDER BY created_at DESC");
$comment_stmt->bind_param("i", $id);
$comment_stmt->execute();
$comment_result = $comment_stmt->get_result();
while ($row = $comment_result->fetch_assoc()) {
    $comments[] = $row;
}
$comment_stmt->close();
$conn->close();

if (!$art) {
    die("Artwork not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($art['title']) ?> - Details</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #121212;
      color: #f5f5f5;
      min-height: 100vh;
      padding: 20px;
    }

    .card {
      background: #1e1e1e;
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.6);
      max-width: 800px;
      width: 100%;
      overflow: hidden;
      text-align: center;
      padding: 20px;
      margin: 20px auto;
      animation: fadeIn 0.4s ease;
    }

    .card img {
      width: 100%;
      max-height: 500px;
      object-fit: contain;
      border-radius: 12px;
      margin-bottom: 15px;
    }

    .action-buttons {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin: 20px 0;
    }

    .action-btn {
      background: none;
      border: none;
      color: white;
      font-size: 16px;
      cursor: pointer;
      padding: 8px 15px;
      border-radius: 8px;
      transition: 0.2s;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .like-btn {
      color: #ff6b6b;
    }

    .like-btn.liked {
      color: #ff0000;
    }

    .report-btn {
      color: #ff6b6b;
      background: rgba(255, 107, 107, 0.1);
    }

    .comments-section {
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #333;
      text-align: left;
    }

    .comment-form textarea {
      width: 100%;
      background: #2a2a2a;
      border: none;
      border-radius: 8px;
      padding: 12px;
      color: white;
      resize: vertical;
      min-height: 100px;
      margin-bottom: 10px;
    }

    .comment-form button {
      background: #00bfff;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
    }

    .comment {
      background: #2a2a2a;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
    }

    .comment-header {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
    }

    .comment-author {
      font-weight: bold;
      color: #00bfff;
    }

    .comment-date {
      color: #888;
      font-size: 14px;
    }

    .success-message {
      color: #00ffcc;
      padding: 10px;
      background: rgba(0, 255, 204, 0.1);
      border-radius: 8px;
      margin-bottom: 15px;
    }

    .error-message {
      color: #ff6b6b;
      padding: 10px;
      background: rgba(255, 107, 107, 0.1);
      border-radius: 8px;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="card">
    <img src="<?= htmlspecialchars($art['image_path']) ?>" alt="<?= htmlspecialchars($art['title']) ?>">
    
    <div class="action-buttons">
      <button class="action-btn like-btn" id="likeBtn">
        <i class="far fa-heart"></i> Like
      </button>
      <button class="action-btn report-btn" onclick="window.location.href='report.php?id=<?= $id ?>'">
        <i class="fas fa-flag"></i> Report
      </button>
    </div>
    
    <h2><?= htmlspecialchars($art['title']) ?></h2>
    <p><?= nl2br(htmlspecialchars($art['description'])) ?></p>
    <p class="price">Price: ₹<?= htmlspecialchars($art['price']) ?></p>
    <p>Size: <?= htmlspecialchars($art['size']) ?></p>
    
    <div class="comments-section">
      <h3>Comments</h3>
      
      <?php if (isset($success_message)): ?>
        <div class="success-message"><?= $success_message ?></div>
      <?php endif; ?>
      
      <?php if (isset($error_message)): ?>
        <div class="error-message"><?= $error_message ?></div>
      <?php endif; ?>
      
      <form class="comment-form" method="POST">
        <textarea name="comment" placeholder="Share your thoughts about this artwork..." required></textarea>
        <button type="submit">Post Comment</button>
      </form>
      
      <div class="comment-list">
        <?php if (empty($comments)): ?>
          <p>No comments yet. Be the first to comment!</p>
        <?php else: ?>
          <?php foreach ($comments as $comment): ?>
            <div class="comment">
              <div class="comment-header">
                <span class="comment-author"><?= htmlspecialchars($comment['author_name']) ?></span>
                <span class="comment-date"><?= date('M j, Y g:i a', strtotime($comment['created_at'])) ?></span>
              </div>
              <div class="comment-content">
                <?= nl2br(htmlspecialchars($comment['content'])) ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
    
    <a href="artspace.php" class="back-btn">⬅ Back to Gallery</a>
  </div>

  <script>
    // Like button functionality
    const likeBtn = document.getElementById('likeBtn');
    likeBtn.addEventListener('click', function() {
      this.classList.toggle('liked');
      const icon = this.querySelector('i');
      
      if (this.classList.contains('liked')) {
        icon.classList.replace('far', 'fas');
        this.innerHTML = '<i class="fas fa-heart"></i> Liked';
        // Here you would typically send an AJAX request to save the like
      } else {
        icon.classList.replace('fas', 'far');
        this.innerHTML = '<i class="far fa-heart"></i> Like';
      }
    });
  </script>
</body>
</html>