<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';

$email = 'admin@adsu.edu.ng';
$plain_password = 'Admin@123';

// Fetch user from database
$stmt = $pdo->prepare("SELECT user_id, full_name, email, password_hash, role, status FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

echo "<h3>User Record:</h3>";
echo "<pre>";
print_r($user);
echo "</pre>";

if ($user) {
    // Test password verification
    $verify = password_verify($plain_password, $user['password_hash']);
    echo "<p>Password verification result: " . ($verify ? "TRUE (matches)" : "FALSE (does not match)") . "</p>";
    
    // If false, let's try to re-hash the password and update
    if (!$verify) {
        $new_hash = password_hash($plain_password, PASSWORD_BCRYPT);
        echo "<p>New hash for '$plain_password': $new_hash</p>";
        
        // Optionally update the database with new hash
        $update = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
        $update->execute([$new_hash, $email]);
        echo "<p>Database updated with new hash. Try logging in again.</p>";
    }
} else {
    echo "<p>No user found with email: $email</p>";
}