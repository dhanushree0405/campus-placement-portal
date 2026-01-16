<?php 
require '../config.php'; 
if(!isset($_SESSION['admin_id'])) header('Location: login.php');

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

$logs = $conn->query('SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 200');
?>
<!doctype html>
<html>
<head>
<meta charset='utf-8'>
<meta name='viewport' content='width=device-width,initial-scale=1'>
<title>Activity Logs</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(120deg, #eef2ff, #e0faff);
        min-height: 100vh;
        margin: 0;
        padding: 0;
    }

    /* Top Bar */
    .top-bar {
        background: #1e3a8a;
        color: white;
        text-align: center;
        padding: 15px 25px;
        font-size: 22px;
        font-weight: 600;
        border-radius: 0 0 15px 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    /* Card */
    .card-custom {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        border-radius: 18px;
        padding: 25px;
        border: none;
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    }

    /* Table */
    table {
        border-radius: 12px;
        overflow: hidden;
    }

    thead {
        background: #1e3a8a;
        color: white;
        font-size: 15px;
    }

    tbody tr:hover {
        background: #eef4ff;
        transition: 0.25s;
    }

    /* User type badge */
    .badge-admin {
        background: #4f46e5;
        color: white;
    }

    .badge-student {
        background: #16a34a;
        color: white;
    }

    .badge-company {
        background: #2563eb;
        color: white;
    }

    .badge-unknown {
        background: #6b7280;
        color: white;
    }
</style>
</head>

<body>

<!-- Header -->
<div class="top-bar">
    Activity Logs
</div>

<div class="container py-4">
    <div class="card card-custom">

        <h4 class="fw-bold mb-3">Recent Activity Logs</h4>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="170">Timestamp</th>
                    <th width="130">User</th>
                    <th width="120">Action</th>
                    <th>Details</th>
                </tr>
            </thead>

            <tbody>
            <?php while($l = $logs->fetch_assoc()): ?>

                <?php 
                    $type = strtolower($l['user_type']);
                    $badgeClass = 
                        ($type == 'admin' ? "badge-admin" :
                        ($type == 'student' ? "badge-student" :
                        ($type == 'company' ? "badge-company" : "badge-unknown")));
                ?>

                <tr>
                    <td><?php echo e($l['created_at']); ?></td>

                    <td>
                        <span class="badge <?php echo $badgeClass; ?> p-2">
                            <?php echo e(ucfirst($l['user_type'])); ?> #<?php echo e($l['user_id']); ?>
                        </span>
                    </td>

                    <td><strong><?php echo e($l['action']); ?></strong></td>

                    <td><?php echo e($l['meta']); ?></td>
                </tr>

            <?php endwhile; ?>
            </tbody>
        </table>

    </div>
</div>

</body>
</html>
