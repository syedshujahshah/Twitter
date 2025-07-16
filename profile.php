<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch user's tweets
$stmt = $pdo->prepare("SELECT * FROM tweets WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$tweets = $stmt->fetchAll();

// Fetch followers count
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM follows WHERE followed_id = ?");
$stmt->execute([$user_id]);
$followers = $stmt->fetch()['count'];

// Fetch following count
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM follows WHERE follower_id = ?");
$stmt->execute([$user_id]);
$following = $stmt->fetch()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Twitter Clone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }
        body {
            background: #f5f8fa;
            color: #14171a;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .profile-header {
            background: white;
            border: 1px solid #e1e8ed;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }
        .profile-header img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .profile-header h2 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .profile-header .handle {
            color: #657786;
            font-size: 16px;
        }
        .profile-header p {
            margin: 10px 0;
        }
        .profile-header .stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 10px;
        }
        .profile-header button {
            background: #1da1f2;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
        }
        .profile-header button:hover {
            background: #1a91da;
        }
        .tweets {
            margin-top: 20px;
            background: white;
            border: 1px solid #e1e8ed;
            border-radius: 10px;
        }
        .tweet {
            padding: 20px;
            border-bottom: 1px solid #e1e8ed;
            display: flex;
        }
        .tweet img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .tweet-content {
            flex: 1;
        }
        .tweet-content .time {
            color: #657786;
            font-size: 14px;
        }
        .tweet-content p {
            margin: 10px 0;
        }
        @media (max-width: 768px) {
            .profile-header {
                padding: 10px;
            }
            .profile-header img {
                width: 80px;
                height: 80px;
            }
            .stats {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="profile-header">
            <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile">
            <h2><?php echo htmlspecialchars($user['name']); ?></h2>
            <span class="handle">@<?php echo htmlspecialchars($user['username']); ?></span>
            <p><?php echo htmlspecialchars($user['bio']); ?></p>
            <div class="stats">
                <span><strong><?php echo $following; ?></strong> Following</span>
                <span><strong><?php echo $followers; ?></strong> Followers</span>
            </div>
            <button onclick="navigate('edit_profile.php')">Edit Profile</button>
        </div>
        <div class="tweets">
            <?php foreach ($tweets as $tweet): ?>
                <div class="tweet">
                    <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile">
                    <div class="tweet-content">
                        <span class="time"><?php echo $tweet['created_at']; ?></span>
                        <p><?php echo htmlspecialchars($tweet['content']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        function navigate(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
