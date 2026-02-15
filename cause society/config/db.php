<?php
// Database connection configuration
// XAMPP ke liye PDO MySQL connection setup

$host = 'localhost';
$dbname = 'cause_db';
$username = 'root';
$password = '';

try {
    // PDO connection banao with error handling
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Error mode set karo
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    // Agar connection fail ho to error dikhao
    die("Database connection failed: " . $e->getMessage());
}
?>
