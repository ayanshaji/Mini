<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db = "pixel";

// Connect to database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$artwork_id = $_POST['artwork_id'] ?? 0;
$comment = $_POST['comment'] ?? '';
$author_name = $_SESSION['username'] ?? 'Anonymous'; // You can replace with your auth system

// Validate input
if (empty($comment) || !is_numeric($artwork_id) || $artwork_id <= 0) {
    header("Location: details.php?id=$artwork_id&error=invalid_input");
    exit();
}

// Insert comment into database
$stmt = $conn->prepare("INSERT INTO comments (artwork_id, author_name, content) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $artwork_id, $author_name, $comment);

if ($stmt->execute()) {
    header("Location: details.php?id=$artwork_id&success=comment_added");
} else {
    header("Location: details.php?id=$artwork_id&error=db_error");
}

$stmt->close();
$conn->close();
exit();
?>