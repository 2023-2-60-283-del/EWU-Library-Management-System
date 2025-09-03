<?php
session_start();
$error = "";
$success = "";

// Database connection
$conn = new mysqli("localhost", "root", "", "library_DB"); // Adjust credentials
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

if (isset($_POST['register'])) {
    $student_id = $conn->real_escape_string($_POST['student_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password']; 
    $mobile = $_POST['mobile'];
    $address = $conn->real_escape_string($_POST['address']);

    // Check if student_id or email already exists
    $check = $conn->query("SELECT * FROM users WHERE student_id='$student_id' OR email='$email'");
    if ($check->num_rows > 0) {
        $error = "Student ID or Email already registered!";
    } else {
        $conn->query("INSERT INTO users (student_id, name, email, password, mobile, address) 
                      VALUES ('$student_id','$name','$email','$password','$mobile','$address')");
        $success = "Registration successful! <a href='index.php'>Login here</a>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>LMS | Signup</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

    <style>
        body { margin:0; padding:0; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('IMG_20250818_191345_603.jpg') no-repeat center center fixed;
            background-size: cover; background-position: center 60%; padding-top:40px; }
        nav.navbar { background: rgba(0,0,0,0.7)!important; position: fixed; top:0; width:100%; z-index:1000; }
        nav.navbar .navbar-brand, nav.navbar .nav-link { color:#fff !important; }
        nav.navbar .nav-link:hover { color:#007bff !important; }
        .overlay { background: rgba(0,0,0,0.6); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px 0; }
        .container { max-width:1200px; }
        #main_content, #side_bar { background: rgba(245,245,245,0.95); padding:30px; border-radius:10px; box-shadow:0 6px 20px rgba(0,0,0,0.3); margin-bottom:20px; }
        #side_bar { height: fit-content; }
        h5 { color:#007bff; margin-top:20px; font-weight:bold; }
        h5:first-child { margin-top:0; }
        blockquote { font-style:italic; border-left:3px solid #007bff; padding-left:15px; margin:15px 0; }
        ul { padding-left:20px; }
        li { margin-bottom:8px; }
        .signup-box { background: rgba(245,245,245,0.95); border-radius:10px; padding:30px; box-shadow:0 6px 20px rgba(0,0,0,0.3); max-width:500px; margin:0 auto; }
        .signup-box h3 { text-align:center; margin-bottom:25px; color:#007bff; font-weight:bold; }
        .signup-box .btn { width:100%; }
        .alert { margin-top:15px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="index.php">Library Management System</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">User Login</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_login.php">Admin Login</a></li>
            <li class="nav-item"><a class="nav-link" href="signup.php">Signup</a></li>
        </ul>
    </div>
</nav>

<div class="overlay">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-5 col-info">
                <div id="side_bar">
                    <h5>Today's Quote</h5>
                    <blockquote>“There is more treasure in books than in all the pirate's loot on Treasure Island.”</blockquote>
                    <footer>~ Walt Disney</footer>
                    <h5>Library Timing</h5>
                    <ul>
                        <li>Opening: 9:00 AM</li>
                        <li>Closing: 6:00 PM</li>
                    </ul>
                    <h5>What We Provide?</h5>
                    <ul>
                        <li>AC Rooms</li>
                        <li>Free Wi-Fi</li>
                        <li>Learning Environment</li>
                        <li>Discussion Room</li>
                        <li>Free Electricity</li>
                    </ul>
                </div>
            </div>

            <!-- Signup Form -->
            <div class="col-md-7 col-form">
                <div class="signup-box">
                    <h3>User Registration</h3>
                    <form action="" method="post">
                        <div class="form-group">
                            <label>Student ID:</label>
                            <input type="text" name="student_id" class="form-control" required placeholder="e.g. 2023-2-60-246">
                        </div>
                        <div class="form-group">
                            <label>Full Name:</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email ID:</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password:</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Mobile:</label>
                            <input type="text" name="mobile" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Address:</label>
                            <textarea name="address" class="form-control" required></textarea>
                        </div>
                        <button type="submit" name="register" class="btn btn-primary mt-3">Register</button>
                    </form>

                    <?php
                    if(!empty($error)) echo '<div class="alert alert-danger text-center">'.$error.'</div>';
                    if(!empty($success)) echo '<div class="alert alert-success text-center">'.$success.'</div>';
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>


