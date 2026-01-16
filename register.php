<?php
require 'config.php';
require 'functions.php';

$error=''; 
$success='';

if($_SERVER['REQUEST_METHOD']==='POST'){

  $name      = trim($_POST['name']);
  $usn       = trim($_POST['usn']);
  $semester  = trim($_POST['semester']);
  $cgpa      = trim($_POST['cgpa']);

  // convert CGPA to percentage
  $percentage = $cgpa * 9.5;

  $sslc      = trim($_POST['sslc']);
  $puc       = trim($_POST['puc']);
  $email     = trim($_POST['email']);
  $contact   = trim($_POST['contact']);
  $pass      = $_POST['password'];

  // NEW FIELDS
  $department = trim($_POST['department']);
  $mentor     = trim($_POST['mentor']);
$section   = trim($_POST['section']);

  $avatar_path = null;
  $resume_path = null;

  /* ------------------- PHOTO UPLOAD -------------------- */
  if(!empty($_FILES['photo']['name'])){
    $allowed_img = ['image/jpeg','image/png','image/jpg'];
    if($_FILES['photo']['size'] > 2*1024*1024){
        $error = "Photo too large (max 2MB)";
    } elseif(!in_array($_FILES['photo']['type'], $allowed_img)){
        $error = "Photo must be JPG or PNG";
    } else {
        $dest = 'uploads/photos/'; 
        @mkdir($dest,0755,true);
        $fn = time()."_".basename($_FILES['photo']['name']);
        if(move_uploaded_file($_FILES['photo']['tmp_name'], $dest.$fn)) {
            $avatar_path = $dest.$fn;
        }
    }
  }

  /* ------------------- RESUME UPLOAD -------------------- */
  if(!$error && !empty($_FILES['resume']['name'])){
    $allowed = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
    ];

    if($_FILES['resume']['size'] > 2*1024*1024){
        $error = 'Resume too large (max 2MB)';
    } elseif(!in_array($_FILES['resume']['type'], $allowed)){
        $error = 'Resume must be PDF or DOC/DOCX';
    } else {
      $dest = 'uploads/resumes/'; 
      @mkdir($dest,0755,true);
      $fn = time()."_".basename($_FILES['resume']['name']);
      if(move_uploaded_file($_FILES['resume']['tmp_name'], $dest.$fn)) {
         $resume_path = $dest.$fn;
      }
    }
  }

  /* ------------------- INSERT INTO DATABASE -------------------- */
  if(!$error){

    $hash = password_hash($pass, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO students 
    (name, email, password, avatar, resume, usn, semester, cgpa, contact, sslc_percentage, puc_percentage, cgpa_percentage, department, mentor,section)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)");

$stmt->bind_param("ssssssidddddsss",
    $name,
    $email,
    $hash,
    $avatar_path,
    $resume_path,
    $usn,
    $semester,
    $cgpa,
    $contact,
    $sslc,
    $puc,
    $percentage,
    $department,
    $mentor,
    $section
);

    if($stmt->execute()){
      $success = "Registered successfully! Please login.";
      log_activity($conn,'student', $stmt->insert_id, 'register');
    } else {
      $error = "Registration failed — email or USN may already exist.";
    }
  }
}
?>
<!doctype html>
<html>
<head>
<meta charset='utf-8'>
<meta name='viewport' content='width=device-width,initial-scale=1'>
<title>Student Registration</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
    body {
        background: #eef1f7;
        font-family: 'Segoe UI', sans-serif;
    }
    .register-wrapper {
        max-width: 520px;
        margin: 60px auto;
    }
    .register-card {
        border-radius: 20px;
        padding: 35px;
        background: #fff;
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        animation: fadeIn 0.5s ease;
    }
    .register-card h3 {
        font-weight: 800;
        color: #3156e3;
    }
    .form-control {
        border-radius: 12px;
        padding: 12px 14px;
    }
    .btn-primary {
        border-radius: 12px;
        padding: 12px 16px;
        font-weight: 600;
        width: 100%;
    }
</style>
</head>
<body>

<div class="register-wrapper">
    <div class="register-card">
        <h3 class="text-center mb-4"><i class="fa fa-user-graduate"></i> Student Registration</h3>

        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo e($error); ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo e($success); ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">

    <div class='mb-3'>
        <label>Full Name</label>
        <input class='form-control' name='name' required>
    </div>

    <div class='mb-3'>
        <label>USN</label>
        <input class='form-control' name='usn' required placeholder='e.g., 1AB23CS001'>
    </div>

    <!-- NEW: DEPARTMENT -->
<div class='mb-3'>
    <label>Department</label>
    <select class='form-control' name='department' required>
        <option value="">Select Department</option>
        <option value="CSE">Computer Science (CSE)</option>
        <option value="ISE">Information Science (ISE)</option>
        <option value="ECE">Electronics & Communication (ECE)</option>
        <option value="EEE">Electrical & Electronics (EEE)</option>
        <option value="ME">Mechanical Engineering (ME)</option>
        <option value="CIVIL">Civil Engineering</option>
        <option value="AIML">Artificial Intelligence & Machine Learning (AIML)</option>
        <option value="AI">Artificial Intelligence (AI)</option>
        <option value="DS">Data Science</option>
        <option value="IOT">Internet of Things (IoT)</option>
    </select>
</div>

<!-- NEW: MENTOR (no dummy values) -->
<div class='mb-3'>
    <label>Mentor</label>
    <input class='form-control' name='mentor' type='mentor' required>
    </select>
</div>

<!-- NEW: SECTION -->
<div class='mb-3'>
    <label>Section</label>
    <select class='form-control' name='section' required>
        <option value="">Select Section</option>
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="C">C</option>
        <option value="D">D</option>
        <option value="E">E</option>
    </select>
</div>

    <div class='mb-3'>
        <label>Semester</label>
        <input class='form-control' name='semester' type='number' min='1' max='8' required>
    </div>

    <div class='mb-3'>
    <label>CGPA</label>
    <input class='form-control' name='cgpa' id='cgpa' type='number' step='0.01' min='0' max='10' required>
    <small id="cgpa_percentage_display" style="font-weight:600; color:#3156e3;">
        Percentage: -
    </small>
</div>

    <div class='mb-3'>
        <label>SSLC Percentage</label>
        <input class='form-control' name='sslc' type='number' step='0.01' min='0' max='100' required>
    </div>

    <div class='mb-3'>
        <label>PUC Percentage</label>
        <input class='form-control' name='puc' type='number' step='0.01' min='0' max='100' required>
    </div>

    <div class='mb-3'>
        <label>Email Address</label>
        <input class='form-control' name='email' type='email' required>
    </div>

    <div class='mb-3'>
        <label>Contact Number</label>
        <input class='form-control' name='contact' type='text' required>
    </div>

    <div class='mb-3'>
        <label>Password</label>
        <input class='form-control' name='password' type='password' required>
    </div>

    <div class='mb-3'>
        <label>Photo (JPG/PNG, max 2MB)</label>
        <input class='form-control' type='file' name='photo' required>
    </div>

    <div class='mb-3'>
        <label>Resume (PDF/DOCX, max 2MB)</label>
        <input class='form-control' type='file' name='resume'>
    </div>

    <button class='btn btn-primary'>Create Account</button>
</form>

    </div>
</div>

<script>
document.getElementById("cgpa").addEventListener("input", function() {
    let cgpa = parseFloat(this.value);
    if (!isNaN(cgpa)) {
        let percentage = (cgpa * 9.5).toFixed(2);
        document.getElementById("cgpa_percentage_display").innerHTML = "Percentage: " + percentage + "%";
    } else {
        document.getElementById("cgpa_percentage_display").innerHTML = "Percentage: -";
    }
});
</script>

</body>
</html>
