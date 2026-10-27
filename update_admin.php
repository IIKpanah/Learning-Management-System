<?php
require_once 'includes/config.php';

$email = 'admin@adsu.edu.ng';
$new_password = 'Admin@123';
$hash = password_hash($new_password, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("UPDATE users SET password_hash = ?, status = 'active', email_verified_at = NOW() WHERE email = ?");
if ($stmt->execute([$hash, $email])) {
    echo "Admin password updated successfully!<br>";
    echo "Email: admin@adsu.edu.ng<br>";
    echo "New Password: Admin@123";
} else {
    echo "Update failed.";
}
?>