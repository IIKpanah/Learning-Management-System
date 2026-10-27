<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Debug: Starting...<br>";

require_once 'includes/config.php';
echo "Debug: config.php loaded.<br>";

require_once 'includes/auth.php';
echo "Debug: auth.php loaded.<br>";

echo "Debug: Script completed.<br>";
?>