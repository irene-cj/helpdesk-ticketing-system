<?php
$host = '127.0.0.1';
$db   = 'helpdesk_db';
$user = 'root';
$pass = 'Fo.12beans14'; 
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;port=3306;dbname=$db;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>