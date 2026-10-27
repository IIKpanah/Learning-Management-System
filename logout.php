<?php
require_once 'includes/auth.php';
logout();
header('Location: index.php');
exit;

function logout() {
    $_SESSION = [];
    if (isset($_COOKIE['auth_token'])) {
        setcookie('auth_token', '', time() - 3600, '/');
    }
    session_destroy();
}
?>