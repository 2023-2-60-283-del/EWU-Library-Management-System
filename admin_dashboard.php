<?php
session_start();
require("functions.php");

// Redirect if admin not logged in
if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

$connection = mysqli_connect("localhost","root","","library_DB");
if(!$connection){
    die("Database connection failed: ".mysqli_connect_error());
}

// Quick stats
$user_count   = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) AS c FROM users"))['c'];
$book_count   = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) AS c FROM books"))['c'];
$issued_count = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) AS c FROM borrow_records WHERE return_date IS NOT NULL"))['c'];

// Success message
if(isset($_SESSION['success_message'])){
    $success_issue = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Dashboard | LMS</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body { background: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.navbar { background: linear-gradient(90deg, #007bff, #0056b3); }
.navbar-brand { font-weight: bold; color: #fff !important; }
.nav-link { color: #fff !important; margin-right: 12px; font-weight: 500; }
.nav-link:hover { color: #dcdcdc !important; }
.welcome-text { margin-left: auto; margin-right: 20px; color: #fff; font-size: 0.95rem; }

.card {
    border: none;
    border-radius: 18px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease-in-out;
    overflow: hidden;
}
.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.15);
}

.card-header {
    border: none;
    background: none;
    font-weight: bold;
    font-size: 1rem;
    margin-bottom: 10px;
}

.card-body h4 {
    font-size: 2.5rem;
    font-weight: bold;
}

.icon-circle {
    width: 60px; height: 60px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem;
    margin: 0 auto 15px auto;
    color: white;
}
.icon-users { background: linear-gradient(135deg, #42a5f5, #1e88e5); }
.icon-books { background: linear-gradient(135deg, #66bb6a, #388e3c); }
.icon-issued { background: linear-gradient(135deg, #ffb74d, #f57c00); }

.btn-custom { border-radius: 25px; padding: 6px 18px; margin: 4px; font-weight: 500; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
<div class="container-fluid">
    <a class="navbar-brand" href="admin_dashboard.php"><i class="fas fa-book-reader"></i> EWU Library System</a>
    <div class="welcome-text">
        Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_name']); ?></strong> |
        <span><?php echo htmlspecialchars($_SESSION['admin_email']); ?></span>
    </div>
    <ul class="navbar-nav">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown"><i class="fas fa-user-circle"></i> My Profile</a>
            <div class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="admin_view_profile.php"><i class="fas fa-id-card"></i> View Profile</a>
            </div>
        </li>
        <li class="nav-item"><a class="nav-link text-warning" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>
</nav>

<!-- Success message -->
<div class="container mt-4">
<?php if(isset($success_issue)) { ?>
    <div class="alert alert-success text-center shadow-sm"><?php echo $success_issue; ?></div>
<?php } ?>
</div>

<!-- Dashboard -->
<div class="container mt-4">
<div class="row justify-content-center">

    <!-- Users Card -->
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="icon-circle icon-users"><i class="fas fa-users"></i></div>
                <div class="card-header">Registered Users</div>
                <h4><?php echo $user_count; ?></h4>
                <p class="text-muted">Total Users</p>
                <a class="btn btn-primary btn-custom" href="add_user.php"><i class="fas fa-user-plus"></i> Add User</a>
                <a class="btn btn-outline-primary btn-custom" href="Regusers.php">View Users</a>
            </div>
        </div>
    </div>

    <!-- Books Card -->
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="icon-circle icon-books"><i class="fas fa-book"></i></div>
                <div class="card-header">Total Books</div>
                <h4><?php echo $book_count; ?></h4>
                <p class="text-muted">Books Available</p>
                <a class="btn btn-success btn-custom" href="add_book.php"><i class="fas fa-plus"></i> Add Book</a>
                <a class="btn btn-outline-success btn-custom" href="Regbooks.php">View Books</a>
            </div>
        </div>
    </div>

    <!-- Issued Books Card -->
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="icon-circle icon-issued"><i class="fas fa-book-reader"></i></div>
                <div class="card-header">Books Issued</div>
                <h4><?php echo $issued_count; ?></h4>
                <p class="text-muted">Issued to Students</p>
                <a class="btn btn-warning btn-custom text-white" href="issue_book.php"><i class="fas fa-plus"></i> Issue Book</a>
                <a class="btn btn-outline-warning btn-custom" href="admin_view_issued_book.php">View Issued</a>
            </div>
        </div>
    </div>

</div>
</div>

</body>
</html>

