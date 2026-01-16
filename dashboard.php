<?php
require '../config.php';
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
if(!isset($_SESSION['company_id'])) header('Location: ../company_login.php');

$cid = $_SESSION['company_id'];
$msg = '';
$err = '';

if($_SERVER['REQUEST_METHOD']==='POST'){
  $title = trim($_POST['title']);
  $location = trim($_POST['location']);
  $desc = trim($_POST['description']);

  $stmt = $conn->prepare('INSERT INTO jobs (title,company_id,location,description,posted_at) VALUES (?,?,?,?,NOW())');
  $stmt->bind_param('siss', $title, $cid, $location, $desc);

  if($stmt->execute()) {
    $msg='Job posted successfully!';
    log_activity($conn,'company',$cid,'post_job');
  } else {
    $err = 'Failed to post job.';
  }
}

$jobs = $conn->prepare('SELECT * FROM jobs WHERE company_id=? ORDER BY posted_at DESC');
$jobs->bind_param('i', $cid);
$jobs->execute();
$res = $jobs->get_result();
?>
<!doctype html>
<html>
<head>
<meta charset='utf-8'>
<meta name='viewport' content='width=device-width,initial-scale=1'>
<title>Company Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(to bottom right, #eef2ff, #e0f7ff, #fff);
    font-family: 'Poppins', sans-serif;
}

.dashboard-header {
    background: #f0f4ff;
    border-left: 6px solid #2563eb;
    color: #1e3a8a;
    padding: 28px;
    border-radius: 14px;
}


.card-modern {
    backdrop-filter: blur(15px);
    background: rgba(255,255,255,0.75);
    border-radius: 18px;
    padding: 25px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    border: 1px solid rgba(255,255,255,0.5);
}

.job-item {
    border-radius: 14px !important;
    padding: 18px !important;
    margin-bottom: 12px;
    transition: 0.3s;
}

.job-item:hover {
    background: #f1f5ff;
    transform: scale(1.02);
}

.btn-gradient {
    background: linear-gradient(135deg, #16a34a, #22c55e);
    border: none;
    padding: 10px 22px;
    font-weight: 600;
    color: white;
    border-radius: 12px;
}

.btn-gradient:hover {
    background: linear-gradient(135deg, #15803d, #16a34a);
}

label {
    font-weight: 600;
}

</style>
</head>

<body>

<div class="container py-4">

    <!-- HEADER -->
    <div class="dashboard-header mb-4">
        <h2 class="fw-bold">Welcome, <?php echo e($_SESSION['company_name']); ?></h2>
        <p style="opacity:0.9; font-size: 14px;">Manage job postings and view applications from talented students.</p>
    </div>

    <!-- JOB POST CARD -->
    <div class="card-modern mb-4">

        <h4 class="mb-3 fw-bold">Post a New Job</h4>

        <?php if($msg): ?>
            <div class="alert alert-success"><?php echo e($msg); ?></div>
        <?php endif; ?>

        <?php if($err): ?>
            <div class="alert alert-danger"><?php echo e($err); ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-2">
                <label>Job Title</label>
                <input class="form-control" name="title" required>
            </div>

            <div class="mb-2">
                <label>Location</label>
                <input class="form-control" name="location">
            </div>

            <div class="mb-2">
                <label>Description</label>
                <textarea class="form-control" name="description" rows="3"></textarea>
            </div>

            <button class="btn btn-gradient mt-2">Post Job</button>
        </form>
    </div>

    <!-- JOB LIST -->
    <div class="card-modern">
        <h4 class="fw-bold mb-3">Your Posted Jobs</h4>

        <?php while($j = $res->fetch_assoc()): ?>
            <div class="list-group-item job-item">
                <h6 class="fw-bold mb-1"><?php echo e($j['title']); ?></h6>
                <small class="text-muted">Posted on: <?php echo e($j['posted_at']); ?></small>

                <a class="btn btn-sm btn-outline-primary float-end"
                    href="../admin/view_applications.php?job_id=<?php echo $j['id']; ?>">
                    View Applications
                </a>
            </div>
        <?php endwhile; ?>
    </div>

</div>

</body>
</html>
