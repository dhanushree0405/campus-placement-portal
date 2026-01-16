<?php
// apply.php  — call as apply.php?job_id=123

ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'config.php';// <-- critical

function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

// must be logged in
if (!isset($_SESSION['user_id'])) {
    // helpful debug: uncomment while testing
    // var_dump($_SESSION); exit;
    header('Location: login.php');
    exit;
}

// job id required
if (!isset($_GET['job_id'])) {
    die('Job ID missing');
}

$job_id = (int)$_GET['job_id'];
$uid = (int)$_SESSION['user_id'];

/* -------------------- check existing application -------------------- */
$c = $conn->prepare('SELECT id FROM applications WHERE student_id=? AND job_id=?');
if (!$c) {
    // prepare failed — show DB error for debugging
    die('Prepare failed (check SQL): ' . $conn->error);
}
$c->bind_param('ii', $uid, $job_id);

if (!$c->execute()) {
    die('Execute failed (check parameters): ' . $c->error);
}

$c->store_result(); // safe way to get num_rows without get_result()
if ($c->num_rows > 0) {
    // already applied — send back to dashboard (or show message)
    header('Location: dashboard.php?msg=already_applied');
    exit;
}
$c->free_result();
$c->close();

/* -------------------- insert application -------------------- */
$ins = $conn->prepare('INSERT INTO applications (student_id, job_id, status, applied_at) VALUES (?, ?, ?, NOW())');
if (!$ins) {
    die('Prepare failed (insert): ' . $conn->error);
}

$status = 'Pending';
$ins->bind_param('iis', $uid, $job_id, $status);

if ($ins->execute()) {

    if (function_exists('log_activity')) {
        // keep logging; optional 4th arg could be message
        log_activity($conn, 'student', $uid, 'apply_job', 'job_id='.$job_id);
    }

    header('Location: dashboard.php?msg=applied');
    exit;
} else {
    // show detailed SQL error for debugging (remove in production)
    die('Insert failed: ' . $ins->error . ' (errno: ' . $ins->errno . ')');
}
