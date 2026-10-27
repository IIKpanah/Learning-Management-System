<?php
/**
 * Dashboard Redirector
 * 
 * This script determines the user's role and redirects them to the appropriate
 * dashboard module. It also displays a professional loading page to enhance
 * user experience before the redirect occurs.
 */

// Enable error reporting for development (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load configuration and authentication helpers
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Ensure the user is logged in; if not, they are redirected to login
requireLogin();

// Determine the target dashboard based on user role
$role = $_SESSION['role'] ?? '';
$redirectUrl = '';

switch ($role) {
    case 'admin':
        $redirectUrl = 'modules/admin/dashboard.php';
        break;
    case 'lecturer':
        $redirectUrl = 'modules/lecturer/dashboard.php';
        break;
    case 'student':
        $redirectUrl = 'modules/student/dashboard.php';
        break;
    default:
        // Fallback to index if role is unknown
        $redirectUrl = 'index.php';
        break;
}

// Ensure the redirect URL is absolute to avoid issues
$redirectUrl = BASE_URL . '/' . ltrim($redirectUrl, '/');

// We will show a smart loading page and then redirect using JavaScript/meta refresh.
// The PHP header redirect is kept as a fallback (will be overridden by HTML output).
// To prevent "headers already sent" errors, we output the HTML and use client-side redirect.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADSU LMS – Redirecting</title>
    <!-- Bootstrap 5 (minimal) -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/bootstrap.min.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom styles (inline for performance) -->
    <style>
        body {
            background: linear-gradient(145deg, #f0f4f8 0%, #d9e2ec 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        .redirect-card {
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            padding: 2.5rem 2rem;
            max-width: 480px;
            width: 100%;
            text-align: center;
            transition: transform 0.2s;
        }
        .redirect-card .brand-icon {
            font-size: 3.5rem;
            color: #0d6efd;
            margin-bottom: 0.75rem;
        }
        .redirect-card h2 {
            font-weight: 700;
            color: #1a2a3a;
            letter-spacing: -0.5px;
            margin-bottom: 0.25rem;
        }
        .redirect-card .subtitle {
            color: #6c757d;
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }
        .redirect-card .spinner-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 1.5rem 0 1rem;
        }
        .redirect-card .spinner-border {
            width: 3.5rem;
            height: 3.5rem;
            color: #0d6efd;
            border-width: 0.3rem;
        }
        .redirect-card .loading-text {
            color: #2d3e50;
            font-weight: 500;
            margin-top: 0.5rem;
        }
        .redirect-card .redirect-note {
            font-size: 0.85rem;
            color: #8a9aa8;
            margin-top: 1.5rem;
        }
        .redirect-card .redirect-note a {
            color: #0d6efd;
            text-decoration: none;
        }
        .redirect-card .redirect-note a:hover {
            text-decoration: underline;
        }
        @media (max-width: 576px) {
            .redirect-card {
                padding: 2rem 1.25rem;
                margin: 0 1rem;
            }
        }
    </style>
    <!-- Meta refresh fallback: redirect after 3 seconds -->
    <meta http-equiv="refresh" content="3;url=<?= htmlspecialchars($redirectUrl) ?>">
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="redirect-card">
                <!-- Branding -->
                <div class="brand-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h2>ADSU Virtual Learning</h2>
                <p class="subtitle">Sandwich Programme</p>

                <!-- Spinner -->
                <div class="spinner-container">
                    <div class="spinner-border" role="status" aria-hidden="true"></div>
                </div>
                <div class="loading-text">
                    <i class="fas fa-arrow-right me-1"></i> Redirecting to your dashboard...
                </div>

                <!-- Fallback link in case redirect doesn't happen -->
                <div class="redirect-note">
                    If you are not redirected automatically, 
                    <a href="<?= htmlspecialchars($redirectUrl) ?>">click here</a>.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS (optional for any toggles) -->
<script src="<?= BASE_URL ?>/assets/js/bootstrap.bundle.min.js"></script>
<!-- JavaScript redirect as primary method (smooth and immediate) -->
<script>
    (function() {
        // Redirect after a short delay (1.5 seconds) to let the loading page show
        setTimeout(function() {
            window.location.href = "<?= htmlspecialchars($redirectUrl, ENT_QUOTES) ?>";
        }, 1500);
    })();
</script>

</body>
</html>