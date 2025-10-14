<?php
// Database settings
$servername = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
$username = $_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME');
$password = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD');
$dbname = $_ENV['DB_NAME'] ?? getenv('DB_NAME');

// Application settings
$currency = "INR";
date_default_timezone_set('Asia/Kolkata');

date_default_timezone_set($timezone);