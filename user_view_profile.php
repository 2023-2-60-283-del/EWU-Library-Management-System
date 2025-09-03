<?php
session_start();
if(!isset($_SESSION['student_id'])){
    header("Location: index.php");
    exit();
}

$connection = mysqli_connect("localhost","root","","library_DB");
if(!$connection) die("Database connection failed: ".mysqli_connect_error());

$student_id = $_SESSION['student_id'];

// Fetch user info
$result = mysqli_query($connection, "SELECT * FROM users WHERE student_id='$student_id'");
$user = mysqli_fetch_assoc($result);

// Update email/mobile
if(isset($_POST['update_contact'])){
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $mobile = mysqli_real_escape_string($connection, $_POST['mobile']);
    mysqli_query($connection, "UPDATE users SET email='$email', mobile='$mobile' WHERE student_id='$student_id'");
    $_SESSION['success_message'] = "Contact info updated successfully!";
    header("Location: user_view_profile.php");
    exit();
}

// Update password
if(isset($_POST['update_password'])){
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    if($current_password === $user['password']){
        mysqli_query($connection, "UPDATE users SET password='$new_password' WHERE student_id='$student_id'");
        $_SESSION['success_message'] = "Password updated successfully!";
        header("Location: user_view_profile.php");
        exit();
    } else {
        $error_message = "Current password is incorrect.";
    }
}

// Success message
if(isset($_SESSION['success_message'])){
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Profile | LMS</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { background: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.container { max-width: 800px; margin-top: 50px; }
.card { border: none; border-radius: 16px; box-shadow: 0 6px 15px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s; }
.card:hover { transform: translateY(-6px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
.card-header { border-radius: 16px 16px 0 0; background: #007bff; color: white; font-weight: bold; font-size: 1.1rem; }
.btn-custom { border-radius: 25px; padding: 6px 18px; margin:2px; }
input { margin-bottom: 10px; }
.alert { margin-top: 15px; }
</style>
</head>
<body>

<div class="container">
    <div class="card bg-white p-4">
        <div class="card-header text-center"><i class="fas fa-user-circle"></i> My Profile</div>
        <div class="card-body">
            <?php if(isset($success_message)) { ?>
                <div class="alert alert-success text-center"><?php echo $success_message; ?></div>
            <?php } ?>
            <?php if(isset($error_message)) { ?>
                <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
            <?php } ?>

            <h4>ℹ️ Personal Information</h4>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Student ID:</strong> <?php echo htmlspecialchars($user['student_id']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address']); ?></p>


            <h4>🔒 Change Password</h4>
            <form method="POST">
                <input type="password" name="current_password" class="form-control" placeholder="Current Password" required>
                <input type="password" name="new_password" class="form-control" placeholder="New Password" required>
                <button type="submit" name="update_password" class="btn btn-warning btn-custom">Update Password</button>
            </form>

            <a href="user_dashboard.php" class="btn btn-info btn-custom mt-3"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>
