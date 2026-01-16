<?php
require '../config.php';
function e($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

if($_SERVER['REQUEST_METHOD']==='POST'){
  $email = trim($_POST['email']);
  $pass = $_POST['password'];

  $stmt = $conn->prepare('SELECT id,password,name FROM admins WHERE email=?');
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $r = $stmt->get_result()->fetch_assoc();

  if($r && password_verify($pass, $r['password'])){
      $_SESSION['admin_id']=$r['id'];
      $_SESSION['admin_name']=$r['name'];
      header('Location: dashboard.php');
      exit;
  } else {
      $err = 'Invalid credentials';
  }
}
?>
<!doctype html>
<html>
<head>
<meta charset='utf-8'>
<meta name='viewport' content='width=device-width,initial-scale=1'>
<title>Admin Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #eef2f7;
    font-family: 'Segoe UI', sans-serif;
}

.login-wrapper {
    max-width: 400px;
    margin: 80px auto;
}

.login-card {
    background: #ffffff;
    border-radius: 18px;
    padding: 35px 30px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    border: 1px solid #e3e6ea;
}

.login-title {
    font-weight: 700;
    font-size: 26px;
    text-align: center;
    margin-bottom: 5px;
    color: #1e293b;
}

.login-sub {
    text-align: center;
    color: #64748b;
    margin-bottom: 25px;
    font-size: 14px;
}

.form-control {
    border-radius: 10px;
    padding: 12px;
    border: 1px solid #cbd5e1;
}

.form-control:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 0.1rem rgba(99,102,241,0.25);
}

.btn-login {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    font-weight: 600;
    background: #f59e0b;
    border: none;
    transition: 0.2s;
}

.btn-login:hover {
    background: #d97706;
}
</style>

</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <h2 class="login-title">Admin Login</h2>
        <p class="login-sub">Access the admin control panel</p>

        <?php if(isset($err)): ?>
            <div class="alert alert-danger py-2"><?php echo e($err); ?></div>
        <?php endif; ?>

        <form method="post">

            <div class="mb-3">
                <input class="form-control" name="email" placeholder="Email" required>
            </div>

            <div class="mb-3">
                <input class="form-control" name="password" type="password" placeholder="Password" required>
            </div>

            <button class="btn btn-login">Login</button>

        </form>
    </div>
</div>

</body>
</html>
