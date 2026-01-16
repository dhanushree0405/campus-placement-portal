<?php
require 'config.php';
function e($str) { return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); }

$err = '';

if($_SERVER['REQUEST_METHOD']==='POST'){
  $email = trim($_POST['email']); 
  $pass = $_POST['password'];

  $stmt = $conn->prepare('SELECT id,password,name FROM companies WHERE email=?');
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $r = $stmt->get_result()->fetch_assoc();

  if($r && password_verify($pass, $r['password'])){
      $_SESSION['company_id'] = $r['id'];
      $_SESSION['company_name'] = $r['name'];
      log_activity($conn,'company',$r['id'],'login');
      header('Location: company/dashboard.php');
      exit;
  } else {
      $err = 'Invalid email or password.';
  }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Company Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
/* ========= PAGE BACKGROUND ========= */
body {
    font-family: "Inter", system-ui, sans-serif;
    background: #f5f7fa;
    margin: 0;
    padding: 0;
}

/* Brand Top Bar */
.top-bar {
    width: 100%;
    padding: 18px 30px;
    background: #1f3a60;
    color: #fff;
    font-size: 22px;
    font-weight: 600;
    letter-spacing: 0.5px;
}

/* Center Layout */
.page-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: calc(100vh - 80px);
    padding: 20px;
}

/* ========= LOGIN CARD ========= */
.login-card {
    background: #ffffff;
    border-radius: 14px;
    padding: 40px 35px;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 8px 18px rgba(0,0,0,0.06);
    border: 1px solid #e5e7eb;
    animation: fadeIn 0.35s ease;
}

.login-card h2 {
    font-size: 26px;
    font-weight: 700;
    color: #1f2d3d;
    margin-bottom: 18px;
}

.login-card p {
    color: #4b5563;
    margin-bottom: 28px;
}

/* ========= FORM ELEMENTS ========= */
.form-label {
    font-weight: 600;
    color: #374151;
}

.form-control {
    height: 48px;
    border-radius: 10px;
    border: 1px solid #c6d3e1;
    transition: 0.25s;
}

.form-control:focus {
    border-color: #1d72b8;
    box-shadow: 0 0 0 2px rgba(29,114,184,0.2);
}

/* ========= BUTTON ========= */
.btn-login {
    width: 100%;
    background: #1d72b8;
    padding: 12px;
    border: none;
    font-size: 17px;
    font-weight: 600;
    border-radius: 10px;
    transition: 0.2s;
}

.btn-login:hover {
    background: #165b90;
    transform: translateY(-2px);
}

.forgot-link {
    display: block;
    margin-top: 10px;
    text-align: right;
    color: #1d72b8;
    font-size: 14px;
    text-decoration: none;
}

/* Animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

</head>
<body>

<!-- Brand Bar -->
<div class="top-bar">
    Company Portal
</div>

<!-- Page Content -->
<div class="page-container">

    <div class="login-card">

        <h2>Welcome Back</h2>
        <p>Login to manage your company job postings and applicants.</p>

        <?php 
        if($err) echo "<div class='alert alert-danger rounded-3'>".e($err)."</div>"; 
        ?>

        <form method="post">

            <label class="form-label">Email Address</label>
            <input class="form-control mb-3" type="email" name="email" required placeholder="Enter your email">

            <label class="form-label">Password</label>
            <input class="form-control mb-3" type="password" name="password" required placeholder="Enter password">

            <button class="btn btn-login">Login</button>

            
        </form>

    </div>

</div>

</body>
</html>
