<?php
// admin/search.php
require '../config.php';
require '../functions.php';

// Simple helper (if not present)
if (!function_exists('e')) {
    function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
}

// Optional debug flag (set to true while debugging)
$DEBUG = false;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Search Students</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{ background:#f0f2f5; font-family:Segoe UI,Arial; padding:20px; }
    .panel{ background:#fff; border-radius:10px; padding:20px; box-shadow:0 3px 12px rgba(0,0,0,.06); }
    label{ font-weight:600; color:#2d3e50; }
    .heading{ font-weight:700; color:#233; margin-bottom:18px; }
    .btn-primary{ background:#3f51b5; border:none; }
    .table th{ background:#eef3ff; }
    .small-muted{ color:#6b7280; font-size:.9rem; }
  </style>
</head>
<body>

<div class="container">
  <div class="heading">🔎 Search Students</div>

  <div class="panel mb-4">
    <form method="GET" class="row g-3">

      <div class="col-md-4">
        <label>Search Name / USN</label>
        <input type="text" name="q" class="form-control" placeholder="Name or USN" value="<?php echo e($_GET['q'] ?? ''); ?>">
      </div>

      <div class="col-md-2">
        <label>SSLC %</label>
        <input type="number" step="0.01" name="sslc" class="form-control" value="<?php echo e($_GET['sslc'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>&nbsp;</label>
        <select name="sslc_filter" class="form-control">
          <option value="above" <?php if(($_GET['sslc_filter'] ?? '')==='above') echo 'selected'; ?>>Above</option>
          <option value="below" <?php if(($_GET['sslc_filter'] ?? '')==='below') echo 'selected'; ?>>Below</option>
        </select>
      </div>

      <div class="col-md-2">
        <label>PUC %</label>
        <input type="number" step="0.01" name="puc" class="form-control" value="<?php echo e($_GET['puc'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>&nbsp;</label>
        <select name="puc_filter" class="form-control">
          <option value="above" <?php if(($_GET['puc_filter'] ?? '')==='above') echo 'selected'; ?>>Above</option>
          <option value="below" <?php if(($_GET['puc_filter'] ?? '')==='below') echo 'selected'; ?>>Below</option>
        </select>
      </div>

      <div class="col-md-2">
        <label>CGPA %</label>
        <input type="number" step="0.01" name="cgpa" class="form-control" value="<?php echo e($_GET['cgpa'] ?? ''); ?>">
      </div>
      <div class="col-md-2">
        <label>&nbsp;</label>
        <select name="cgpa_filter" class="form-control">
          <option value="above" <?php if(($_GET['cgpa_filter'] ?? '')==='above') echo 'selected'; ?>>Above</option>
          <option value="below" <?php if(($_GET['cgpa_filter'] ?? '')==='below') echo 'selected'; ?>>Below</option>
        </select>
      </div>

      <div class="col-md-4">
        <label>Department</label>
        <select name="department" class="form-control">
          <option value="">All Departments</option>
          <?php
            $depts = ['CSE','ISE','ECE','EEE','ME','CIVIL','AIML','AI','Data Science','IOT'];
            foreach($depts as $d){
              $sel = (isset($_GET['department']) && $_GET['department']===$d) ? 'selected' : '';
              echo "<option value=\"" . e($d) . "\" $sel>" . e($d) . "</option>";
            }
          ?>
        </select>
      </div>

      <div class="col-md-4">
        <label>Semester</label>
        <select name="semester" class="form-control">
          <option value="">All Semesters</option>
          <?php for($i=1;$i<=8;$i++): $sel = (isset($_GET['semester']) && (int)$_GET['semester'] === $i) ? 'selected' : ''; ?>
            <option value="<?php echo $i; ?>" <?php echo $sel; ?>><?php echo $i; ?> Sem</option>
          <?php endfor; ?>
        </select>
      </div>

      <div class="col-md-4">
        <label>Mentor</label>
        <input type="text" name="mentor" class="form-control" placeholder="Enter mentor name" value="<?php echo e($_GET['mentor'] ?? ''); ?>">
      </div>

      <div class="col-md-4">
    <label>Section</label>
    <select name="section" class="form-control">
        <option value="">All Sections</option>
        <option value="A" <?php if(($_GET['section'] ?? '')==='A') echo 'selected'; ?>>A</option>
        <option value="B" <?php if(($_GET['section'] ?? '')==='B') echo 'selected'; ?>>B</option>
        <option value="C" <?php if(($_GET['section'] ?? '')==='C') echo 'selected'; ?>>C</option>
        <option value="D" <?php if(($_GET['section'] ?? '')==='D') echo 'selected'; ?>>D</option>
        <option value="E" <?php if(($_GET['section'] ?? '')==='E') echo 'selected'; ?>>E</option>
    </select>
</div>


      <div class="col-12 text-end">
        <button type="submit" class="btn btn-primary px-4">Search</button>
      </div>

    </form>
  </div>

<?php
// Build dynamic WHERE and parameters (prepared)
$whereParts = [];
$types = '';
$params = [];

// name / usn
if (!empty($_GET['q'])) {
    $whereParts[] = "(name LIKE ? OR usn LIKE ?)";
    $types .= 'ss';
    $params[] = '%' . $_GET['q'] . '%';
    $params[] = '%' . $_GET['q'] . '%';
}

// SSLC (students table column assumed: sslc_percentage)
if (isset($_GET['sslc']) && $_GET['sslc'] !== '') {
    $val = (float) $_GET['sslc'];
    if (($_GET['sslc_filter'] ?? 'above') === 'below') {
        $whereParts[] = "sslc_percentage <= ?";
    } else {
        $whereParts[] = "sslc_percentage >= ?";
    }
    $types .= 'd';
    $params[] = $val;
}

// PUC
if (isset($_GET['puc']) && $_GET['puc'] !== '') {
    $val = (float) $_GET['puc'];
    if (($_GET['puc_filter'] ?? 'above') === 'below') {
        $whereParts[] = "puc_percentage <= ?";
    } else {
        $whereParts[] = "puc_percentage >= ?";
    }
    $types .= 'd';
    $params[] = $val;
}

// CGPA (column: cgpa_percentage)
if (isset($_GET['cgpa']) && $_GET['cgpa'] !== '') {
    $val = (float) $_GET['cgpa'];
    if (($_GET['cgpa_filter'] ?? 'above') === 'below') {
        $whereParts[] = "cgpa_percentage <= ?";
    } else {
        $whereParts[] = "cgpa_percentage >= ?";
    }
    $types .= 'd';
    $params[] = $val;
}

// Department exact match
if (!empty($_GET['department'])) {
    $whereParts[] = "department = ?";
    $types .= 's';
    $params[] = $_GET['department'];
}

// Semester integer match
if (!empty($_GET['semester'])) {
    $whereParts[] = "semester = ?";
    $types .= 'i';
    $params[] = (int)$_GET['semester'];
}

// Mentor LIKE (search inside students.mentor column)
if (!empty($_GET['mentor'])) {
    $whereParts[] = "mentor LIKE ?";
    $types .= 's';
    $params[] = '%' . $_GET['mentor'] . '%';
}

// Section exact match
if (!empty($_GET['section'])) {
    $whereParts[] = "section = ?";
    $types .= 's';
    $params[] = $_GET['section'];
}


// Build final SQL
$whereSQL = count($whereParts) ? ('WHERE ' . implode(' AND ', $whereParts)) : '';
$sql = "SELECT * FROM students $whereSQL ORDER BY name ASC";

if ($DEBUG) {
    echo "<div class='panel mb-3'><strong>DEBUG:</strong><pre>SQL: " . e($sql) . "\nTYPES: $types\nPARAMS: " . e(json_encode($params)) . "</pre></div>";
}

// Prepare + bind dynamically
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo "<div class='panel text-danger'><strong>DB prepare error:</strong> " . e($conn->error) . "</div>";
} else {
    if ($types !== '') {
        // mysqli bind_param requires references
        $bind_names[] = $types;
        for ($i=0; $i<count($params); $i++) {
            $bind_name = 'bind' . $i;
            $$bind_name = $params[$i];
            $bind_names[] = &$$bind_name;
        }
        call_user_func_array([$stmt, 'bind_param'], $bind_names);
    }

    if (!$stmt->execute()) {
        echo "<div class='panel text-danger'><strong>DB execute error:</strong> " . e($stmt->error) . "</div>";
    } else {
        $result = $stmt->get_result();
        $total = $result ? $result->num_rows : 0;
    }
}
?>

<?php if (!empty($result) && $total > 0): ?>
  <div class="panel">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div><strong>Results:</strong> <?php echo (int)$total; ?> student(s) found</div>
      <div class="small-muted">You can combine filters for precise results</div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>Name</th>
            <th>USN</th>
            <th>Dept</th>
            <th>Sem</th>
            <th>Email</th>
            <th>Contact</th>
            <th>SSLC</th>
            <th>PUC</th>
            <th>CGPA</th>
            <th>Mentor</th>
            <th>Section</th>
            <th>Resume</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($s = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo e($s['name']); ?></td>
              <td><?php echo e($s['usn']); ?></td>
              <td><?php echo e($s['department']); ?></td>
              <td><?php echo e($s['semester']); ?></td>
              <td><?php echo e($s['email']); ?></td>
              <td><?php echo e($s['contact']); ?></td>
              <td><?php echo e($s['sslc_percentage']); ?></td>
              <td><?php echo e($s['puc_percentage']); ?></td>
              <td><?php echo e($s['cgpa_percentage']); ?></td>
              <td><?php echo e($s['mentor']); ?></td>
              <td><?php echo e($s['section']); ?></td>
              <td>
                <?php if (!empty($s['resume'])): ?>
                  <a class="btn btn-info btn-sm text-white" href="../<?php echo e($s['resume']); ?>" target="_blank">View</a>
                <?php else: ?>
                  <span class="text-muted">N/A</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php elseif (isset($result)): ?>
  <div class="panel">
    <div class="text-muted">No students found for the given filters.</div>
  </div>
<?php endif; ?>

</div>
</body>
</html>
