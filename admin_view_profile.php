<?php
session_start();

// Redirect if admin not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id']; // get logged-in admin ID

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "library_DB";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) die("Connection failed: " . mysqli_connect_error());

// Fetch current admin info
$admin_query = mysqli_query($conn, "SELECT * FROM admins WHERE admin_id='$admin_id'");
$admin = mysqli_fetch_assoc($admin_query);

// Handle email & mobile update
if (isset($_POST['update_contact'])) {
    $email  = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);

    $sql = "UPDATE admins SET email='$email', mobile='$mobile' WHERE admin_id='$admin_id'";
    if (mysqli_query($conn, $sql)) {
        $msg_contact = "✅ Contact info updated successfully!";
        $admin['email'] = $email;
        $admin['mobile'] = $mobile;
    } else {
        $msg_contact = "❌ Error: " . mysqli_error($conn);
    }
}

// Handle password update
if (isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password     = $_POST['new_password'];

    if ($current_password === $admin['password']) {
        $sql = "UPDATE admins SET password='$new_password' WHERE admin_id='$admin_id'";
        if (mysqli_query($conn, $sql)) {
            $msg_password = "✅ Password updated successfully!";
            $admin['password'] = $new_password;
        } else {
            $msg_password = "❌ Error: " . mysqli_error($conn);
        }
    } else {
        $msg_password = "❌ Current password is incorrect!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Profile | LMS</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<style>
body { font-family: Arial, sans-serif; background-color: #f4f6f9; }
.container { margin-top: 50px; max-width: 700px; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
h2, h3 { margin-bottom: 20px; }
label { font-weight: bold; }
input { margin-bottom: 15px; }
button { margin-top: 10px; }
.alert { margin-top: 15px; }
</style>
</head>
<body>
<div class="container">
    <h2>👤 Admin Profile</h2>

    <p><strong>Name:</strong> <?php echo htmlspecialchars($admin['name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($admin['email']); ?></p>
    <p><strong>Mobile:</strong> <?php echo htmlspecialchars($admin['mobile']); ?></p>

    <?php if(isset($msg_contact)) { echo "<div class='alert alert-info'>{$msg_contact}</div>"; } ?>
    <h3>✉️ Update Email & Mobile</h3>
    <form method="POST">
        <label>New Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" class="form-control" required>
        <label>New Mobile:</label>
        <input type="text" name="mobile" value="<?php echo htmlspecialchars($admin['mobile']); ?>" class="form-control" required>
        <button type="submit" name="update_contact" class="btn btn-primary">Update Contact</button>
    </form>

    <?php if(isset($msg_password)) { echo "<div class='alert alert-info'>{$msg_password}</div>"; } ?>
    <h3>🔒 Change Password</h3>
    <form method="POST">
        <label>Current Password:</label>
        <input type="password" name="current_password" class="form-control" required>
        <label>New Password:</label>
        <input type="password" name="new_password" class="form-control" required>
        <button type="submit" name="update_password" class="btn btn-warning">Update Password</button>
    </form>

    <a href="admin_dashboard.php" class="btn btn-success mt-3">⬅️ Back to Dashboard</a>
</div>
</body>
</html>


