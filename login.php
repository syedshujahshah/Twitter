<?php
session_start();
require_once 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];

    error_log("Login attempt for username: $username");

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        if (!$stmt->execute([$username])) {
            $error = 'Database query failed: ' . print_r($stmt->errorInfo(), true);
        } else {
            $user = $stmt->fetch();
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                echo "<script>window.location.href = 'index.php';</script>";
                exit;
            } else {
                $error = 'Invalid username or password';
                error_log("Authentication failed for username: $username");
            }
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
        error_log("Database error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Twitter Clone</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 400px;
            background: white;
            padding: 20px;
            border: 1px solid #e1e8ed;
            border-radius: 10px;
            text-align: center;
        }
        .container h2 {
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #e1e8ed;
            border-radius: 5px;
            font-size: 16px;
            outline: none;
        }
        .form-group input:focus {
            border-color: #1da1f2;
        }
        button {
            background: #1da1f2;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }
        button:hover {
            background: #1a91da;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .register-link {
            margin-top: 10px;
        }
        .register-link a {
            color: #1da1f2;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        @media (max-width: 480px) {
            .container {
                width: 90%;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
            <div class="register-link">
                <p>Don't have an account? <a href="#" onclick="navigate('register.php')">Register here</a></p>
            </div>
        </form>
    </div>
    <script>
        function navigate(url) {
            window.location.href = url;
        }
    </script>
</body>
</html>
