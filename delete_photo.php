<?php
require 'config.php';
if(!isset($_SESSION['user_id'])) exit;

$uid = $_SESSION['user_id'];
$conn->query("UPDATE students SET avatar='' WHERE id=$uid");
header("Location: profile.php");
?>
