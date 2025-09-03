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

$artwork_id = $_GET['id'] ?? 0;
if (!$artwork_id) {
    header("Location: artspace.php");
    exit();
}

// Handle form submission
$submitted = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = trim($_POST['reason'] ?? '');
    
    if (!empty($reason)) {
        $stmt = $conn->prepare("INSERT INTO reports (artwork_id, reason) VALUES (?, ?)");
        $stmt->bind_param("is", $artwork_id, $reason);
        
        if ($stmt->execute()) {
            $submitted = true;
        } else {
            $error = "Failed to submit report. Please try again.";
        }
        $stmt->close();
    } else {
        $error = "Please provide a reason for reporting.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Report Artwork</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #121212;
      color: #f5f5f5;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }
    
    .report-container {
      background: #1e1e1e;
      border-radius: 16px;
      box-shadow: 0 10px 20px rgba(146, 9, 9, 0.83);
      max-width: 600px;
      width: 100%;
      padding: 30px;
      text-align: center;
    }
    
    h1 {
      color: #ff6b6b;
      margin-bottom: 20px;
    }
    
    .success-message {
      color: #00ffcc;
      padding: 15px;
      background: rgba(0, 255, 204, 0.1);
      border-radius: 8px;
      margin-bottom: 20px;
    }
    
    .error-message {
      color: #ff6b6b;
      padding: 15px;
      background: rgba(255, 107, 107, 0.1);
      border-radius: 8px;
      margin-bottom: 20px;
    }
    
    .report-form textarea {
      width: 100%;
      background: #2a2a2a;
      border: none;
      border-radius: 8px;
      padding: 15px;
      color: white;
      resize: vertical;
      min-height: 150px;
      margin-bottom: 20px;
    }
    
    /* Updated Button Styles */
    .btn {
      display: inline-block;
      padding: 12px 25px;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
      margin: 0 10px;
      transition: all 0.3s ease;
      text-decoration: none;
      border: none;
      font-size: 16px;
    }
    
    .btn-primary {
      background: #00bfff;
      color: white;
    }
    
    .btn-primary:hover {
      background: #0091c9;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 191, 255, 0.3);
    }
    
    .btn-danger {
      background: #ff6b6b;
      color: white;
    }
    
    .btn-danger:hover {
      background: #ff5252;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(255, 107, 107, 0.3);
    }
    
    .btn-secondary {
      background: #2a2a2a;
      color: white;
    }
    
    .btn-secondary:hover {
      background: #333;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(42, 42, 42, 0.3);
    }
    
    .button-group {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <div class="report-container">
    <?php if ($submitted): ?>
      <h1>Report Submitted</h1>
      <div class="success-message">
        Thank you for your report. Our team will review it shortly.
      </div>
      <div class="button-group">
        <a href="artspace.php" class="btn btn-primary">
          <i class="fas fa-arrow-left"></i> Back to Gallery
        </a>
      </div>
    <?php else: ?>
      <h1>Report Artwork</h1>
      <p>Please explain why you're reporting this artwork. Our team will review your report.</p>
      
      <?php if (!empty($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      
      <form class="report-form" method="POST">
        <input type="hidden" name="artwork_id" value="<?= $artwork_id ?>">
        <textarea name="reason" placeholder="Enter your reason for reporting..." required></textarea>
        <div class="button-group">
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-flag"></i> Submit Report
          </button>
          <button type="button" class="btn btn-secondary" onclick="window.location.href='artspace.php'">
            <i class="fas fa-times"></i> Cancel
          </button>
        </div>
      </form>
    <?php endif; ?>
  </div>

  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>