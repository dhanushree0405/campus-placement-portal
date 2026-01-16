<?php
// config.php
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'campus_placement_v2';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

session_start();

// simple helper for activity logging
function log_activity($conn, $user_type, $user_id, $action, $meta='') {
  $stmt = $conn->prepare('INSERT INTO activity_logs (user_type,user_id,action,meta,created_at) VALUES (?,?,?,?,NOW())');
  $stmt->bind_param('siss', $user_type, $user_id, $action, $meta);
  $stmt->execute();
}
