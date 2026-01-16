<?php 
require 'config.php';
if(!isset($_SESSION['user_id'])) header('Location: login.php');
$uid = $_SESSION['user_id'];

$user = $conn->query("SELECT * FROM students WHERE id=$uid")->fetch_assoc();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    .profile-pic {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 0 12px rgba(0,0,0,0.3);
    }
    .card-custom {
        border-radius: 15px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    }
</style>
</head>

<body class="bg-light">

<div class="container py-4">
    <div class="card card-custom p-4">

        <h3 class="mb-3">Your Profile</h3>

        <!-- PROFILE PHOTO -->
        <div class="text-center mb-4">
            <?php if(!empty($user['avatar'])) { ?>
                <img src="<?php echo $user['avatar']; ?>" class="profile-pic">
            <?php } else { ?>
                <img src="default_user.png" class="profile-pic">
            <?php } ?>
        </div>

        <!-- Photo + Resume View Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Current Photo</h5>
                <?php if($user['avatar']) { ?>
                    <a class="btn btn-danger btn-sm" href="delete_photo.php">Delete Photo</a>
                <?php } else { ?>
                    <p>No photo uploaded.</p>
                <?php } ?>
            </div>

            <div class="col-md-6">
                <h5>Current Resume</h5>
                <?php if($user['resume']) { ?>
                    <a class="btn btn-success btn-sm" target="_blank" href="<?php echo $user['resume']; ?>">Download Resume</a>
                    <a class="btn btn-danger btn-sm" href="delete_resume.php">Delete</a>
                <?php } else { ?>
                    <p>No resume uploaded.</p>
                <?php } ?>
            </div>
        </div>

        <!-- Resume Preview -->
        <?php if($user['resume']) { ?>
            <h5>Resume Preview</h5>
            <iframe src="<?php echo $user['resume']; ?>" width="100%" height="400" style="border:1px solid #ccc;"></iframe>
            <hr>
        <?php } ?>

        <!-- UPDATE FORM -->
        <h4>Update Profile</h4>
        <form method="post" enctype="multipart/form-data" action="profile_update.php">

            <div class="mb-3">
                <label>Name</label>
                <input class="form-control" name="name" value="<?php echo $user['name']; ?>">
            </div>

            <div class="mb-3">
                <label>Upload New Photo</label>
                <input type="file" name="avatar" class="form-control">
            </div>

            <div class="mb-3">
                <label>Upload New Resume (PDF)</label>
                <input type="file" name="resume" accept="application/pdf" class="form-control">
            </div>

            <button class="btn btn-primary">Save Changes</button>
        </form>

        <hr class="my-4">

        <!-- CHANGE PASSWORD -->
        <h4 class="mb-3">Change Password</h4>
        <form method="post" action="change_password.php">
            <div class="mb-3">
                <label>Current Password</label>
                <input type="password" name="current_pass" class="form-control">
            </div>
            <div class="mb-3">
                <label>New Password</label>
                <input type="password" name="new_pass" class="form-control">
            </div>
            <div class="mb-3">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_pass" class="form-control">
            </div>
            <button class="btn btn-warning">Update Password</button>
        </form>

    </div>
</div>

</body>
</html>
