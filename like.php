<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $tweet_id = $_POST['tweet_id'];

    // Check if already liked
    $stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = ? AND tweet_id = ?");
    $stmt->execute([$user_id, $tweet_id]);
    $like = $stmt->fetch();

    if ($like) {
        // Unlike
        $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND tweet_id = ?");
        $stmt->execute([$user_id, $tweet_id]);
    } else {
        // Like
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, tweet_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $tweet_id]);
    }
}

echo "<script>window.location.href = 'index.php';</script>";
?>
