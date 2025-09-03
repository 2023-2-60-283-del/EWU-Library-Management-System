<?php
session_start();

// Redirect if admin not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$connection = mysqli_connect("localhost", "root", "", "library_DB");
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get user details
if (isset($_GET['student_id'])) {
    $student_id = mysqli_real_escape_string($connection, $_GET['student_id']);
    $query = "SELECT * FROM users WHERE student_id = '$student_id'";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
    } else {
        echo "User not found!";
        exit();
    }
} else {
    header("Location: Regusers.php");
    exit();
}

// Update user details
if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($connection, $_POST['name']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $mobile = mysqli_real_escape_string($connection, $_POST['mobile']);
    $address = mysqli_real_escape_string($connection, $_POST['address']);

    $update_query = "UPDATE users 
                     SET name='$name', email='$email', mobile='$mobile', address='$address' 
                     WHERE student_id='$student_id'";

    if (mysqli_query($connection, $update_query)) {
        header("Location: Regusers.php");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($connection);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit User | LMS</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow rounded">
        <div class="card-header bg-primary text-white">
            <h4>Edit User - <?php echo htmlspecialchars($user['student_id']); ?></h4>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label><strong>Name:</strong></label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>
                <div class="form-group">
                    <label><strong>Email:</strong></label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label><strong>Mobile:</strong></label>
                    <input type="text" name="mobile" class="form-control" value="<?php echo htmlspecialchars($user['mobile']); ?>" required>
                </div>
                <div class="form-group">
                    <label><strong>Address:</strong></label>
                    <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                </div>
                <button type="submit" name="update" class="btn btn-success">Update</button>
                <a href="Regusers.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
