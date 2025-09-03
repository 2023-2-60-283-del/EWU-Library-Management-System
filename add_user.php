<?php
session_start();

// Redirect if admin not logged in
if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

$connection = mysqli_connect("localhost","root","","library_DB");
if(!$connection){
    die("Database connection failed: ".mysqli_connect_error());
}

$admin_id = $_SESSION['admin_id']; // Logged-in admin

// Initialize variables
$student_id = $name = $email = $mobile = $address = $password = "";
$error = $success = "";

// Handle form submission
if(isset($_POST['submit'])){
    $student_id = mysqli_real_escape_string($connection, $_POST['student_id']);
    $name       = mysqli_real_escape_string($connection, $_POST['name']);
    $email      = mysqli_real_escape_string($connection, $_POST['email']);
    $mobile     = mysqli_real_escape_string($connection, $_POST['mobile']);
    $address    = mysqli_real_escape_string($connection, $_POST['address']);
    $password   = mysqli_real_escape_string($connection, $_POST['password']);

    if(empty($student_id) || empty($name) || empty($email) || empty($mobile) || empty($address) || empty($password)){
        $error = "All fields are required!";
    } else {
        // Check for existing student ID or email
        $check_query = "SELECT * FROM users WHERE student_id='$student_id' OR email='$email'";
        $check_result = mysqli_query($connection, $check_query);

        if(mysqli_num_rows($check_result) > 0){
            $error = "Student ID or Email already exists!";
        } else {
            // Insert new user with added_by
            $insert_query = "INSERT INTO users 
                (student_id, name, email, password, mobile, address, added_by)
                VALUES 
                ('$student_id','$name','$email','$password','$mobile','$address','$admin_id')";

            if(mysqli_query($connection, $insert_query)){
                $success = "User added successfully!";
                $student_id = $name = $email = $mobile = $address = $password = "";
            } else {
                $error = "Error adding user: " . mysqli_error($connection);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Add User | LMS</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { background: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.container { margin-top: 50px; max-width: 600px; }
.card { border-radius: 16px; box-shadow: 0 6px 15px rgba(0,0,0,0.1); }
.card-header { border-radius: 16px 16px 0 0; background: #007bff; color: white; font-weight: bold; }
</style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header"><i class="fas fa-user-plus"></i> Add New User</div>
        <div class="card-body">

            <?php if($error) { ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>

            <?php if($success) { ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php } ?>

            <form method="post" action="">
                <div class="form-group">
                    <label for="student_id">Student ID</label>
                    <input type="text" name="student_id" id="student_id" class="form-control" value="<?php echo htmlspecialchars($student_id); ?>" required>
                </div>
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="form-group">
                    <label for="mobile">Mobile</label>
                    <input type="text" name="mobile" id="mobile" class="form-control" value="<?php echo htmlspecialchars($mobile); ?>" required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea name="address" id="address" class="form-control" required><?php echo htmlspecialchars($address); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="password">Password (plain text)</label>
                    <input type="text" name="password" id="password" class="form-control" value="<?php echo htmlspecialchars($password); ?>" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary"><i class="fas fa-save"></i> Add User</button>
                <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            </form>

        </div>
    </div>
</div>

</body>
</html>
