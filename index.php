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

// Fetch tweets from followed users and self
$stmt = $pdo->prepare("
    SELECT t.*, u.username, u.name, u.profile_picture
    FROM tweets t
    JOIN users u ON t.user_id = u.id
    WHERE t.user_id = ? OR t.user_id IN (
        SELECT followed_id FROM follows WHERE follower_id = ?
    )
    ORDER BY t.created_at DESC
");
$stmt->execute([$user_id, $user_id]);
$tweets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Twitter Clone</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        }
        body {
            background-color: #E1E8ED;
            color: #14171A;
            line-height: 1.5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            padding: 15px;
        }
        .sidebar {
            width: 275px;
            padding: 15px;
            background-color: #FFFFFF;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .sidebar a {
            display: block;
            padding: 12px 15px;
            color: #1DA1F2;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            border-radius: 20px;
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: #E8F5FD;
            color: #1A91DA;
        }
        .main {
            flex: 1;
            max-width: 600px;
            margin-left: 20px;
            background-color: #FFFFFF;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .tweet-box {
            padding: 15px;
            border-bottom: 1px solid #E1E8ED;
        }
        .tweet-box textarea {
            width: 100%;
            border: none;
            resize: none;
            font-size: 18px;
            padding: 10px;
            outline: none;
            background-color: #F5F8FA;
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }
        .tweet-box textarea:focus {
            border-color: #1DA1F2;
            background-color: #FFFFFF;
        }
        .tweet-box button {
            background-color: #1DA1F2;
            color: #FFFFFF;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 700;
            float: right;
            margin-top: 10px;
            transition: background-color 0.3s ease;
        }
        .tweet-box button:hover {
            background-color: #1A91DA;
        }
        .tweet {
            padding: 15px;
            border-bottom: 1px solid #E1E8ED;
            display: flex;
            transition: background-color 0.3s ease;
        }
        .tweet:hover {
            background-color: #F5F8FA;
        }
        .tweet img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            margin-right: 12px;
            object-fit: cover;
            border: 2px solid #E1E8ED;
        }
        .tweet-content {
            flex: 1;
            position: relative;
        }
        .tweet-content .username {
            font-weight: 700;
            color: #14171A;
            font-size: 15px;
        }
        .tweet-content .handle {
            color: #657786;
            font-size: 14px;
            margin-left: 5px;
        }
        .tweet-content .time {
            color: #657786;
            font-size: 13px;
            margin-top: 2px;
        }
        .tweet-content p {
            margin: 8px 0;
            color: #14171A;
            font-size: 15px;
            line-height: 1.3;
        }
        .tweet-actions {
            display: flex;
            gap: 20px;
            color: #657786;
            margin-top: 8px;
        }
        .tweet-actions span {
            cursor: pointer;
            font-size: 14px;
            padding: 5px 8px;
            border-radius: 50%;
            transition: color 0.3s ease, background-color 0.3s ease;
        }
        .tweet-actions span:hover {
            color: #1DA1F2;
            background-color: #E8F5FD;
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                padding: 10px;
            }
            .sidebar {
                width: 100%;
                margin-bottom: 15px;
                display: flex;
                justify-content: space-between;
                padding: 10px;
            }
            .sidebar a {
                padding: 10px;
                font-size: 14px;
            }
            .main {
                width: 100%;
                margin-left: 0;
            }
            .tweet-box button {
                width: 100%;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <a href="index.php">Home</a>
            <a href="#" onclick="navigate('profile.php')">Profile</a>
            <a href="#" onclick="navigate('logout.php')">Logout</a>
        </div>
        <div class="main">
            <div class="tweet-box">
                <form action="post_tweet.php" method="POST">
                    <textarea name="content" placeholder="What's happening?" rows="3" maxlength="280"></textarea>
                    <button type="submit">Tweet</button>
                </form>
            </div>
            <?php foreach ($tweets as $tweet): ?>
                <div class="tweet">
                    <img src="<?php echo htmlspecialchars($tweet['profile_picture']); ?>" alt="Profile">
                    <div class="tweet-content">
                        <span class="username"><?php echo htmlspecialchars($tweet['name']); ?></span>
                        <span class="handle">@<?php echo htmlspecialchars($tweet['username']); ?></span>
                        <span class="time"><?php echo $tweet['created_at']; ?></span>
                        <p><?php echo htmlspecialchars($tweet['content']); ?></p>
                        <div class="tweet-actions">
                            <span onclick="comment(<?php echo $tweet['id']; ?>)">💬</span>
                            <span onclick="like(<?php echo $tweet['id']; ?>)">❤️</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        function navigate(url) {
            window.location.href = url;
        }
        function like(tweetId) {
            fetch('like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'tweet_id=' + tweetId
            }).then(() => location.reload());
        }
        function comment(tweetId) {
            let content = prompt('Enter your comment:');
            if (content) {
                fetch('comment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'tweet_id=' + tweetId + '&content=' + encodeURIComponent(content)
                }).then(() => location.reload());
            }
        }
    </script>
</body>
</html>
