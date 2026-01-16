<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../config.php';

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

/* ------------------------------
    DELETE STUDENT LOGIC
------------------------------ */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    // Get resume file
    $stmt = $conn->prepare("SELECT resume FROM students WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    // Delete resume file
    if ($res && $res['resume']) {
        $filePath = "../" . $res['resume'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Delete student row
    $del = $conn->prepare("DELETE FROM students WHERE id=?");
    $del->bind_param("i", $id);
    $del->execute();

    header("Location: manage_students.php?msg=deleted");
    exit;
}

$students = $conn->query("SELECT * FROM students ORDER BY created_at DESC");
?>
<!doctype html>
<html>
<head>
<meta charset='utf-8'>
<meta name='viewport' content='width=device-width,initial-scale=1'>
<title>Manage Students</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<div class="top-bar text-center p-3 bg-dark text-white fw-bold fs-4">
    Manage Students
</div>

<div class='container py-4'>
    <div class='card p-4 shadow'>

        <h4 class='mb-3 fw-bold'>Student Records</h4>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="alert alert-success">Student deleted successfully.</div>
        <?php endif; ?>

        <table class='table table-bordered table-striped'>
            <thead>
                <tr>
                    <th width="60">ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th width="120">Resume</th>
                    <th width="100">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($s = $students->fetch_assoc()): ?>
                <tr>
                    <td><?php echo e($s['id']); ?></td>
                    <td><?php echo e($s['name']); ?></td>
                    <td><?php echo e($s['email']); ?></td>

                    <td>
                        <?php if ($s['resume']): ?>
                            <a class="btn btn-info btn-sm text-white" href="../<?php echo e($s['resume']); ?>" target="_blank">View</a>
                        <?php else: ?>
                            <span class="text-muted">No file</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a href="manage_students.php?delete=<?php echo $s['id']; ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Delete this student?');">
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
