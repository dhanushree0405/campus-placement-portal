<?php
require 'config.php';
if(!isset($_SESSION['user_id'])) header("Location: login.php");

$uid = $_SESSION['user_id'];
$current = $_POST['current_pass'];
$new = $_POST['new_pass'];
$confirm = $_POST['confirm_pass'];

$user = $conn->query("SELECT password FROM students WHERE id=$uid")->fetch_assoc();

if($new !== $confirm){
    die("Passwords do not match");
}

if(!password_verify($current, $user['password'])){
    die("Current password wrong");
}

$hashed = password_hash($new, PASSWORD_DEFAULT);
$conn->query("UPDATE students SET password='$hashed' WHERE id=$uid");

header("Location: profile.php?pass_updated=1");
?>
