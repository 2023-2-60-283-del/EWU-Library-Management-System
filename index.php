<?php
session_start();

$error = "";

if (isset($_POST['login'])) {
    $student_id = $_POST['student_id'];
    $password   = $_POST['password'];

    // DB connection
    $connection = mysqli_connect("localhost", "root", "", "library_DB");
    if (!$connection) {
        die("Database connection failed: " . mysqli_connect_error());
    }

    $student_id_safe = mysqli_real_escape_string($connection, $student_id);

    $result = mysqli_query($connection, "SELECT * FROM users WHERE student_id='$student_id_safe'");

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        if ($password === $user['password']) { // plain text password
            // Set session variables
            $_SESSION['student_id'] = $user['student_id'];
            $_SESSION['user_name']  = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            header("Location: user_dashboard.php");
            exit();
        } else {
            $error = "Incorrect password!";
        }
    } else {
        $error = "Student ID not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>LMS | User Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('IMG_20250818_191345_603.jpg') no-repeat center center fixed;
            background-size: cover;
            background-position: center 60%;
            padding-top: 40px;
        }

        nav.navbar {
            background: rgba(0,0,0,0.7) !important;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        nav.navbar .navbar-brand, nav.navbar .nav-link {
            color: #fff !important;
        }

        nav.navbar .nav-link:hover {
            color: #007bff !important;
        }

        .overlay {
            background: rgba(0,0,0,0.6);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }

        #side_bar, .login-box {
            background: rgba(245, 245, 245, 0.95);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        #side_bar h5 { color: #007bff; margin-top: 20px; font-weight: bold; }
        #side_bar h5:first-child { margin-top: 0; }
        #side_bar blockquote { font-style: italic; border-left: 3px solid #007bff; padding-left: 15px; margin: 15px 0; }

        .login-box { max-width: 450px; margin: 0 auto; }
        .login-box h3 { text-align: center; margin-bottom: 25px; color: #007bff; font-weight: bold; }
        .login-box .btn { width: 100%; }
        .alert { margin-top: 15px; }

        @media (min-width: 992px) {
            .col-info { width: 45%; }
            .col-form { width: 55%; }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="index.php">East West University Library Management System</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">User Login</a></li>
            <li class="nav-item"><a class="nav-link" href="admin_login.php">Admin Login</a></li>
        </ul>
    </div>
</nav>

<div class="overlay">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-5 col-info">
                <div id="side_bar">
           <div class="text-center mb-3">
           <img src="OIP.webp" alt="East West University Logo" style="max-width: 100%; max-height: 210px; height: auto;">

          </div>

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

            <!-- Login Form -->
            <div class="col-md-7 col-form">
                <div class="login-box">
                    <h3>User Login</h3>
                    <form method="post">
                        <div class="form-group">
                            <label>Student ID:</label>
                            <input type="text" name="student_id" class="form-control" required placeholder="e.g. 2023-2-60-246">
                        </div>
                        <div class="form-group">
                            <label>Password:</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary mt-3">Login</button>
                    </form>
                    <?php if (!empty($error)) echo '<div class="alert alert-danger text-center mt-3">' . $error . '</div>'; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
