<?php
session_start();

// Ensure user logged in
if(!isset($_SESSION['student_id'])){
    header("Location: index.php");
    exit();
}

$connection = mysqli_connect("localhost","root","","library_DB");
if(!$connection){
    die("Database connection failed: ".mysqli_connect_error());
}

// Fetch user info
$student_id = $_SESSION['student_id'];
$user_query = mysqli_query($connection, "SELECT * FROM users WHERE student_id='$student_id'");
$user_info = mysqli_fetch_assoc($user_query);

// Functions
function get_user_issue_book_count($student_id){
    global $connection;
    $result = mysqli_query($connection, "SELECT COUNT(*) AS c FROM borrow_records WHERE student_id='$student_id' AND return_date IS NOT NULL");
    $row = mysqli_fetch_assoc($result);
    return $row['c'];
}

function get_total_books_count(){
    global $connection;
    $result = mysqli_query($connection, "SELECT COUNT(*) AS c FROM books");
    $row = mysqli_fetch_assoc($result);
    return $row['c'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard | LMS</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
.navbar { background: linear-gradient(90deg, #007bff, #0056b3); }
.navbar-brand { color: #fff !important; font-weight: bold; }
.navbar .ml-auto { color: #fff; font-size: 0.95rem; }

.card {
    border: none;
    border-radius: 18px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease-in-out;
    text-align: center;
    overflow: hidden;
}
.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.15);
}

.card-body h4 { font-size: 2rem; font-weight: bold; }

.icon-circle {
    width: 70px; height: 70px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.8rem;
    margin: 0 auto 15px auto;
    color: white;
}
.icon-issued { background: linear-gradient(135deg, #42a5f5, #1e88e5); }
.icon-books  { background: linear-gradient(135deg, #66bb6a, #388e3c); }
.icon-user   { background: linear-gradient(135deg, #ffb74d, #f57c00); }

.btn-custom { border-radius: 25px; padding: 6px 18px; font-weight: 500; margin-top: 8px; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="user_dashboard.php"><i class="fas fa-book-reader"></i> EWU Library System</a>
        <div class="ml-auto text-right">
            Welcome, <strong><?php echo htmlspecialchars($user_info['name']); ?></strong> |
            <span><?php echo htmlspecialchars($user_info['email']); ?></span>
            <a href="logout.php" class="btn btn-danger btn-sm ml-2"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</nav>

<!-- Dashboard Cards -->
<div class="container mt-5">
    <div class="row">

        <!-- Books Issued -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="icon-circle icon-issued"><i class="fas fa-book-open"></i></div>
                    <h4><?php echo get_user_issue_book_count($user_info['student_id']); ?></h4>
                    <p class="text-muted">Books Issued</p>
                    <a href="view_issued_book.php" class="btn btn-primary btn-custom">View Issued Books</a>
                </div>
            </div>
        </div>

        <!-- Total Books -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="icon-circle icon-books"><i class="fas fa-layer-group"></i></div>
                    <h4><?php echo get_total_books_count(); ?></h4>
                    <p class="text-muted">Books in Library</p>
                    <a href="user_regbook.php" class="btn btn-success btn-custom">View All Books</a>
                </div>
            </div>
        </div>

        <!-- Profile -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="icon-circle icon-user"><i class="fas fa-user-circle"></i></div>
                    <h4><?php echo htmlspecialchars($user_info['name']); ?></h4>
                    <p class="text-muted">User Profile</p>
                    <a href="user_view_profile.php" class="btn btn-warning btn-custom text-white">View Profile</a>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>



