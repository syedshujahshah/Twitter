<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $follower_id = $_SESSION['user_id'];
    $followed_id = $_POST['followed_id'];

    // Check if already following
    $stmt = $pdo->prepare("SELECT * FROM follows WHERE follower_id = ? AND followed_id = ?");
    $stmt->execute([$follower_id, $followed_id]);
    $follow = $stmt->fetch();

    if ($follow) {
        // Unfollow
        $stmt = $pdo->prepare("DELETE FROM follows WHERE follower_id = ? AND followed_id = ?");
        $stmt->execute([$follower_id, $followed_id]);
    } else {
        // Follow
        $stmt = $pdo->prepare("INSERT INTO follows (follower_id, followed_id) VALUES (?, ?)");
        $stmt->execute([$follower_id, $followed_id]);
    }
}

echo "<script>window.location.href = 'index.php';</script>";
?>
