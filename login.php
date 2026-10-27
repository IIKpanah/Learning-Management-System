<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT user_id, full_name, email, password_hash, role, status FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && verifyPassword($password, $user['password_hash'])) {
        if ($user['status'] !== 'active') {
            $error = 'Account not activated. Check your email.';
        } else {
            // Generate JWT and set cookie
            $jwt = generateJWT($user['user_id'], $user['email'], $user['role']);
            setcookie('auth_token', $jwt, time() + 86400, '/', '', false, true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['full_name'];
            
            // Redirect to appropriate dashboard
            switch($user['role']) {
                case 'admin': header('Location: admin/dashboard.php'); break;
                case 'lecturer': header('Location: lecturer/dashboard.php'); break;
                default: header('Location: student/dashboard.php');
            }
            exit;
        }
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!-- HTML form omitted for brevity, but standard Bootstrap form -->