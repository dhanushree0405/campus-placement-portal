<?php
require 'config.php';
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$msg=''; 
$err='';

if($_SERVER['REQUEST_METHOD']==='POST'){
  $name = trim($_POST['name']); 
  $email = trim($_POST['email']); 
  $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $stmt = $conn->prepare('INSERT INTO companies (name,email,password,created_at) VALUES (?,?,?,NOW())');
  $stmt->bind_param('sss', $name, $email, $pass);

  if($stmt->execute()){ 
      $msg='Company registered successfully. Please log in.'; 
  } else {
      $err='Registration failed — email already exists.';
  }
}
?>
<!doctype html>
<html>
<head>
<meta charset='utf-8'>
<meta name='viewport' content='width=device-width, initial-scale=1'>
<title>Company Signup</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    font-family: "Inter", system-ui, sans-serif;
    background: #f0f4f8;
    min-height: 100vh;
    display: flex;
    align-items: center;
    padding: 20px;
}

.signup-wrapper {
    max-width: 450px;
    margin: auto;
}

.card-modern {
    background: #fff;
    border-radius: 14px;
    padding: 35px;
    border: 1px solid #e5e9f0;
    box-shadow: 0px 4px 16px rgba(0,0,0,0.05);
}

.card-modern h3 {
    font-weight: 600;
    color: #1d3557;
    margin-bottom: 25px;
    text-align: center;
}

.form-label {
    font-weight: 500;
    margin-bottom: 6px;
    color: #34495e;
}

.form-control {
    height: 48px;
    border-radius: 10px;
    border: 1px solid #cfd8e3;
}

.form-control:focus {
    border-color: #1d72b8;
    box-shadow: 0 0 0 2px rgba(29, 114, 184, 0.2);
}

.btn-primary-custom {
    background: #1d72b8;
    border: none;
    padding: 12px;
    font-size: 16px;
    border-radius: 10px;
    width: 100%;
}

.btn-primary-custom:hover {
    background: #155a8a;
}

.alert {
    border-radius: 10px;
}
</style>

</head>
<body>

<div class="signup-wrapper">
    <div class="card-modern">

        <h3>Company Registration</h3>

        <?php 
            if($err) echo "<div class='alert alert-danger'>".e($err)."</div>"; 
            if($msg) echo "<div class='alert alert-success'>".e($msg)."</div>"; 
        ?>

        <form method="post">

            <label class="form-label">Company Name</label>
            <input class="form-control mb-3" name="name" required placeholder="Enter company name">

            <label class="form-label">Email Address</label>
            <input class="form-control mb-3" name="email" type="email" required placeholder="Enter email">

            <label class="form-label">Password</label>
            <input class="form-control mb-4" name="password" type="password" required placeholder="Create password">

            <button class="btn btn-primary-custom">Create Account</button>
        </form>

    </div>
</div>

</body>
</html>
