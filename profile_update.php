<?php
require 'config.php';
if(!isset($_SESSION['user_id'])) header("Location: login.php");
$uid = $_SESSION['user_id'];

$name = $_POST['name'];
$conn->query("UPDATE students SET name='$name' WHERE id=$uid");

// Photo upload
if(!empty($_FILES['avatar']['name'])){
    $dest="uploads/avatars/";
    @mkdir($dest,0755,true);
    $file = $dest.time()."_".basename($_FILES['avatar']['name']);
    if(move_uploaded_file($_FILES['avatar']['tmp_name'],$file)){
        $conn->query("UPDATE students SET avatar='$file' WHERE id=$uid");
    }
}

// Resume upload
if(!empty($_FILES['resume']['name'])){
    $dest="uploads/resumes/";
    @mkdir($dest,0755,true);
    $file = $dest.time()."_".basename($_FILES['resume']['name']);
    if(move_uploaded_file($_FILES['resume']['tmp_name'],$file)){
        $conn->query("UPDATE students SET resume='$file' WHERE id=$uid");
    }
}

header("Location: profile.php?updated=1");
exit;
?>
