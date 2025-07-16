<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];

    if (!empty($content) && strlen($content) <= 280) {
        $stmt = $pdo->prepare("INSERT INTO tweets (user_id, content) VALUES (?, ?)");
        $stmt->execute([$user_id, $content]);
    }
}

echo "<script>window.location.href = 'index.php';</script>";
?>
