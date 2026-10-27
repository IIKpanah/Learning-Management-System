<?php
require_once 'includes/config.php';

$full_name = 'Admin User';
$email = 'admin@adsu.edu.ng';
$password = 'Admin@123';
$hash = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role, status, email_verified_at) VALUES (?, ?, ?, 'admin', 'active', NOW())");
    $stmt->execute([$full_name, $email, $hash]);
    echo "Admin user created successfully!<br>";
    echo "Email: admin@adsu.edu.ng<br>";
    echo "Password: Admin@123";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>