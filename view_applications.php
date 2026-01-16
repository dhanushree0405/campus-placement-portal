<?php
require '../config.php'; 
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

if(!isset($_SESSION['admin_id'])) header('Location: login.php');

$job_id = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action'])){
    $id = (int)$_POST['id']; 
    $action = $_POST['action'];

    $u = $conn->prepare('UPDATE applications SET status=? WHERE id=?');
    $u->bind_param('si', $action, $id); 
    $u->execute();
}

$sql = 'SELECT a.*, s.name as student_name, j.title 
        FROM applications a 
        JOIN students s ON a.student_id=s.id 
        JOIN jobs j ON a.job_id=j.id';

if($job_id) $sql .= ' WHERE a.job_id='.$job_id;

$sql .= ' ORDER BY a.applied_at DESC';
$apps = $conn->query($sql);
?>
<!doctype html>
<html>
<head>
<meta charset='utf-8'>
<meta name='viewport' content='width=device-width,initial-scale=1'>
<title>Applications</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Poppins Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #eef2ff, #e0f7ff);
        min-height: 100vh;
    }

    .top-bar {
        background: #1e3a8a;
        color: white;
        padding: 15px 25px;
        font-size: 22px;
        font-weight: 600;
        text-align: center;
        border-radius: 0 0 15px 15px;
        box-shadow: 0 3px 12px rgba(0,0,0,0.15);
    }

    .card-custom {
        border-radius: 18px;
        padding: 25px;
        border: none;
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    }

    table {
        border-radius: 12px;
        overflow: hidden;
    }

    thead {
        background: #1e3a8a;
        color: white;
        font-weight: 500;
    }

    tbody tr:hover {
        background: #eef4ff;
        transition: 0.25s;
    }

    .btn-update {
        border-radius: 8px;
        padding: 4px 10px;
    }

    .form-select {
        border-radius: 8px;
    }
</style>

</head>
<body>

<!-- Header -->
<div class="top-bar">
    Manage Applications
</div>

<div class="container py-4">
    <div class="card card-custom">

        <h4 class="fw-bold mb-3">Applications</h4>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="60">ID</th>
                    <th>Student</th>
                    <th>Job Title</th>
                    <th width="130">Status</th>
                    <th width="200">Update Status</th>
                </tr>
            </thead>

            <tbody>
            <?php while($r = $apps->fetch_assoc()): ?>
                <tr>
                    <td><?php echo e($r['id']); ?></td>
                    <td><?php echo e($r['student_name']); ?></td>
                    <td><?php echo e($r['title']); ?></td>
                    <td>
                        <span class="badge 
                            <?php 
                                echo $r['status'] === 'Approved' ? 'bg-success' : 
                                ($r['status'] === 'Rejected' ? 'bg-danger' : 'bg-secondary');
                            ?>">
                            <?php echo e($r['status']); ?>
                        </span>
                    </td>

                    <td>
                        <form method="post" class="d-flex align-items-center gap-2">
                            <input type="hidden" name="id" value="<?php echo e($r['id']); ?>">
                            <select name="action" class="form-select form-select-sm w-auto">
                                <option <?php if($r['status']=='Pending') echo 'selected'; ?>>Pending</option>
                                <option <?php if($r['status']=='Approved') echo 'selected'; ?>>Approved</option>
                                <option <?php if($r['status']=='Rejected') echo 'selected'; ?>>Rejected</option>
                            </select>
                            <button class="btn btn-primary btn-sm btn-update">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

    </div>
</div>

</body>
</html>
