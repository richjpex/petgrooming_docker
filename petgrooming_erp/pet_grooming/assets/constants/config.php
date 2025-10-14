<?php
// Database settings
$servername = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
$username = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME');
$password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD');
$dbname = $_ENV['DB_NAME'] ?? getenv('DB_NAME');

// Application settings
$currency = $_ENV['APP_CURRENCY'] ?? getenv('APP_CURRENCY')
$timezone = $_ENV['APP_TIMEZONE'] ?? getenv('APP_TIMEZONE');

date_default_timezone_set($timezone);
?>