<?php
require 'config.php';
require 'functions.php';

$q = isset($_GET['q']) ? "%".$conn->real_escape_string($_GET['q'])."%" : '%';
$location = isset($_GET['location']) ? $conn->real_escape_string($_GET['location']) : '';

$sql = "SELECT j.*, c.name AS company_name
        FROM jobs j
        LEFT JOIN companies c ON j.company_id = c.id
        WHERE (j.title LIKE ? OR j.description LIKE ? OR c.name LIKE ?)";
if ($location) $sql .= " AND j.location LIKE ?";
$sql .= " ORDER BY j.posted_at DESC";

$stmt = $conn->prepare($sql);
if($location) $stmt->bind_param('ssss', $q, $q, $q, $location);
else $stmt->bind_param('sss',$q, $q, $q);
$stmt->execute();
$jobs = $stmt->get_result();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Campus Placement Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
    body {
        background: #eef1f7;
        font-family: 'Segoe UI', sans-serif;
    }
    .hero-section {
        background: linear-gradient(135deg, #4e73df, #1cc88a);
        color: white;
        padding: 70px 20px;
        border-radius: 0 0 35px 35px;
        text-align: center;
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    .hero-section h1 {
        font-size: 2.8rem;
        font-weight: 800;
    }
    .hero-section p {
        font-size: 1.2rem;
        opacity: 0.9;
    }

    .search-container {
        margin-top: -40px;
        background: white;
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.10);
    }

    .job-card {
        border-radius: 20px;
        padding: 20px;
        background: white;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: 0.25s;
    }
    .job-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    }

    .company-tag {
        background: #e9efff;
        color: #3156e3;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .side-card {
        border-radius: 20px;
        background: white;
        padding: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .quick-link-btn {
        border-radius: 10px;
        padding: 10px;
        font-size: 0.95rem;
        font-weight: 600;
    }

    .apply-btn {
        border-radius: 10px;
        padding: 10px 18px;
        font-weight: 600;
    }
</style>
</head>
<body>

<!-- HERO HEADER -->
<div class="hero-section">
    <h1>Find Your Dream Placement</h1>
    <p>Search jobs, companies and apply instantly — simple and beautiful.</p>
</div>

<!-- SEARCH BAR -->
<div class="container search-container">
    <form class="row g-3" method="get" action="index.php">
        <div class="col-md-5">
            <input class="form-control form-control-lg" name="q" placeholder="Search jobs, roles or companies...">
        </div>
        <div class="col-md-4">
            <input class="form-control form-control-lg" name="location" placeholder="Location">
        </div>
        <div class="col-md-3 d-grid">
            <button class="btn btn-primary btn-lg" type="submit"><i class="fa fa-search"></i> Search</button>
        </div>
    </form>
</div>

<br>
<div class="container py-4">
  <div class="row g-4">

    <!-- JOB LIST -->
    <div class="col-md-8">
      <?php while($job = $jobs->fetch_assoc()): ?>
        <div class="job-card mb-4">
            <h4 class="fw-bold text-dark mb-2"><?php echo e($job['title']); ?></h4>
            <span class="company-tag mb-2 d-inline-block"><i class="fa fa-building"></i> <?php echo e($job['company_name'] ?: 'Unknown'); ?></span>

            <div class="text-muted small mb-3">
                <i class="fa fa-map-marker-alt"></i> <?php echo e($job['location']); ?> &nbsp; | &nbsp;
                <i class="fa fa-clock"></i> <?php echo e($job['posted_at']); ?>
            </div>

            <p><?php echo nl2br(e(substr($job['description'],0,250))); ?><?php if(strlen($job['description'])>250) echo '...'; ?></p>

            <a class="btn btn-primary apply-btn" 
   href="apply.php?job_id=<?php echo $job['id']; ?>">
   Apply Now
</a>

        </div>
      <?php endwhile; ?>
    </div>

    <!-- SIDE PANEL -->
    <div class="col-md-4">
      <div class="side-card">
        <h5 class="fw-bold mb-3">Quick Links</h5>

        <?php if(is_logged_in()): ?>
          <a href="dashboard.php" class="btn btn-outline-primary quick-link-btn w-100 mb-2">My Dashboard</a>
          <a href="profile.php" class="btn btn-outline-secondary quick-link-btn w-100 mb-2">Profile</a>
          <a href="logout.php" class="btn btn-outline-primary quick-link-btn w-100 mb-2">Logout</a>
        <?php else: ?>
          <a href="register.php" class="btn btn-primary quick-link-btn w-100 mb-2">Register</a>
          <a href="login.php" class="btn btn-secondary quick-link-btn w-100 mb-2">Login</a>
        <?php endif; ?>

        <hr>

        <a href="company_register.php" class="btn btn-success quick-link-btn w-100 mb-2">Company Signup</a>
        <a href="company_login.php" class="btn btn-outline-success quick-link-btn w-100 mb-2">Company Login</a>
        <a href="admin/login.php" class="btn btn-warning quick-link-btn w-100">Admin Panel</a>
      </div>
    </div>

  </div>
</div>

</body>
</html>