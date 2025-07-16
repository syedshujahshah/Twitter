<?php
$host = 'localhost';
$dbname = 'dbtiync23a3pbk';
$username = 'ulnrcogla9a1t';
$password = 'yolpwow1mwr2';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
