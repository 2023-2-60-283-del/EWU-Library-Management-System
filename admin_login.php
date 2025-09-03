<?php
session_start();
$error = "";

if(isset($_POST['admin_login'])){
    $connection = mysqli_connect("localhost","root","","library_DB");

    if(!$connection){
        die("Database connection failed: " . mysqli_connect_error());
    }

    $email = mysqli_real_escape_string($connection, $_POST['email']);
    $password = mysqli_real_escape_string($connection, $_POST['password']);

    // Plain text password check
    $query = "SELECT * FROM admins WHERE email='$email' AND password='$password' LIMIT 1";
    $query_run = mysqli_query($connection, $query);

    if(mysqli_num_rows($query_run) > 0){
        $row = mysqli_fetch_assoc($query_run);
        $_SESSION['admin_id'] = $row['admin_id'];
        $_SESSION['admin_name'] = $row['name'];
        $_SESSION['admin_email'] = $row['email'];

        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>LMS | Admin Login</title>
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

        .container { max-width: 1200px; }

        #side_bar {
            background: rgba(245, 245, 245, 0.95);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
            height: fit-content;
        }

        h5 { color: #007bff; margin-top: 20px; font-weight: bold; }
        h5:first-child { margin-top: 0; }

        blockquote { font-style: italic; border-left: 3px solid #007bff; padding-left: 15px; margin: 15px 0; }
        ul { padding-left: 20px; } li { margin-bottom: 8px; }

        .login-box {
            background: rgba(245, 245, 245, 0.95);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
            max-width: 450px;
            margin: 0 auto;
        }

        .login-box h3 {
            text-align: center;
            margin-bottom: 25px;
            color: #007bff;
            font-weight: bold;
        }

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
                    <h3>Admin Login</h3>
                    <form action="" method="post">
                        <div class="form-group">
                            <label>Email ID:</label>
                            <input type="text" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password:</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" name="admin_login" class="btn btn-primary mt-3">Login</button>
                    </form>

                    <?php if(!empty($error)) echo '<div class="alert alert-danger text-center mt-3">'.$error.'</div>'; ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

