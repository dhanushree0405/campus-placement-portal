<?php
require '../config.php';

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

if(!isset($_SESSION['admin_id'])) header('Location: login.php');

// ---------------- DELETE JOB ----------------
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $stmt = $conn->prepare("DELETE FROM jobs WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: manage_jobs.php?msg=deleted");
    exit;
}

$jobs = $conn->query('SELECT j.*, c.name as company_name FROM jobs j LEFT JOIN companies c ON j.company_id=c.id ORDER BY posted_at DESC');
?>
<!doctype html>
<html>
<head>
<meta charset='utf-8'>
<meta name='viewport' content='width=device-width,initial-scale=1'>
<title>Manage Jobs</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="top-bar text-center bg-primary text-white p-3 fs-4 fw-bold">
    Manage Job Postings
</div>

<div class='container py-4'>
    <div class='card card-custom p-4 shadow'>

        <h4 class='mb-3 fw-bold'>Job Listings</h4>

        <?php if(isset($_GET['msg']) && $_GET['msg']=="deleted"): ?>
            <div class="alert alert-success">Job deleted successfully.</div>
        <?php endif; ?>

        <table class='table table-bordered table-striped'>
            <thead>
                <tr>
                    <th width="60">ID</th>
                    <th>Title</th>
                    <th>Company</th>
                    <th width="160">Posted On</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php while($j = $jobs->fetch_assoc()): ?>
                <tr>
                    <td><?php echo e($j['id']); ?></td>
                    <td><?php echo e($j['title']); ?></td>
                    <td><?php echo e($j['company_name']); ?></td>
                    <td><?php echo e($j['posted_at']); ?></td>

                    <td>
                        <a href="manage_jobs.php?delete=<?php echo $j['id']; ?>" 
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Are you sure you want to delete this job?');">
                            Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

    </div>
</div>

</body>
</html>
