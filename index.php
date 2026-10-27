<?php
// Enable error reporting (only for development)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT user_id, full_name, email, password_hash, role, status FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password_hash'])) {
        if ($user['status'] === 'active') {
            $jwt = generateJWT($user['user_id'], $user['email'], $user['role']);
            setcookie('auth_token', $jwt, time() + 86400, '/', '', false, true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['full_name'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Account not activated.';
        }
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADSU LMS Login</title>
    <!-- Bootstrap 5 (or keep 4 – classes are compatible) -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.min.css">
    <!-- Font Awesome for icons (optional but adds polish) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <style>
        /* Inline custom styles for a clean, professional look */
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            border: none;
            border-radius: 1.25rem;
            box-shadow: 0 20px 60px -10px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            background: #fff;
        }
        .login-card .card-header {
            background: transparent;
            border-bottom: none;
            padding-top: 2rem;
            text-align: center;
        }
        .login-card .card-header .brand-icon {
            font-size: 2.8rem;
            color: #0d6efd;
            margin-bottom: 0.5rem;
        }
        .login-card .card-header h3 {
            font-weight: 700;
            color: #1a2a3a;
            letter-spacing: -0.5px;
        }
        .login-card .card-header p {
            color: #6c757d;
            font-weight: 400;
            margin-top: 0.25rem;
        }
        .login-card .card-body {
            padding: 1.5rem 2rem 2rem;
        }
        .login-card .form-label {
            font-weight: 500;
            color: #2d3e50;
        }
        .login-card .form-control {
            border-radius: 0.5rem;
            border: 1px solid #dce1e8;
            padding: 0.7rem 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .login-card .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        .login-card .input-group .form-control {
            border-right: none;
        }
        .login-card .input-group .input-group-text {
            background: #fff;
            border-left: none;
            border-radius: 0 0.5rem 0.5rem 0;
            cursor: pointer;
            color: #6c757d;
        }
        .login-card .btn-login {
            border-radius: 0.5rem;
            padding: 0.7rem;
            font-weight: 600;
            background: #0d6efd;
            border: none;
            transition: background 0.2s;
        }
        .login-card .btn-login:hover {
            background: #0b5ed7;
        }
        .login-card .alert {
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .login-card .footer-links {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: #6c757d;
        }
        .login-card .footer-links a {
            color: #0d6efd;
            text-decoration: none;
        }
        .login-card .footer-links a:hover {
            text-decoration: underline;
        }
        @media (max-width: 576px) {
            .login-card .card-body {
                padding: 1.5rem 1.25rem;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card login-card">
                <div class="card-header">
                    <div class="brand-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>ADSU Virtual Learning</h3>
                    <p class="text-muted">Sandwich Programme</p>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" novalidate>
                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i> Email Address
                            </label>
                            <input type="email" name="email" id="email" class="form-control" 
                                   placeholder="you@example.edu" required autofocus>
                        </div>

                        <!-- Password with toggle -->
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i> Password
                            </label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" 
                                       class="form-control" placeholder="Enter your password" required>
                                <span class="input-group-text" id="togglePassword" role="button" 
                                      aria-label="Toggle password visibility">
                                    <i class="fas fa-eye" id="passwordIcon"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Optional: Remember me (uncomment if needed) -->
                        <!--
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        -->

                        <button type="submit" class="btn btn-primary btn-login w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </button>
                    </form>

                    <div class="footer-links">
                        <a href="#">Forgot password?</a> &middot; 
                        <a href="#">Need help?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS (for toggles, etc.) -->
<script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.min.js"></script>
<!-- Password toggle script -->
<script>
    (function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const passwordIcon = document.getElementById('passwordIcon');

        if (togglePassword && passwordInput && passwordIcon) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                // Toggle icon
                passwordIcon.classList.toggle('fa-eye');
                passwordIcon.classList.toggle('fa-eye-slash');
            });
        }
    })();
</script>
</body>
</html>