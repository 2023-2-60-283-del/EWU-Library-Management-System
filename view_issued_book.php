<?php
session_start();

// Make sure the user is logged in
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

// Fetch issued books for the user (not returned)
$issued_query = mysqli_query($connection, "
    SELECT br.record_id, b.title, a.name AS author_name, c.name AS category_name, br.borrow_date, br.return_date
    FROM borrow_records br
    LEFT JOIN books b ON br.book_id = b.book_id
    LEFT JOIN authors a ON b.author_id = a.author_id
    LEFT JOIN categories c ON b.category_id = c.category_id
    WHERE br.student_id='$student_id' AND br.return_date IS NOT NULL
    ORDER BY br.borrow_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Issued Books | LMS</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
.table thead { background: #007bff; color: #fff; }
.table-hover tbody tr:hover { background-color: #e2e6ea; }
.navbar { background: #fff !important; border-bottom: 1px solid #ddd; }
.navbar-brand { color: #007bff !important; font-weight: bold; }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="user_dashboard.php"><i class="fas fa-book-reader"></i> LMS</a>
        <div class="ml-auto text-right">
            Welcome, <strong><?php echo htmlspecialchars($user_info['name']); ?></strong> |
            <span><?php echo htmlspecialchars($user_info['email']); ?></span>
            <a href="logout.php" class="btn btn-danger btn-sm ml-2"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="mb-4"><i class="fas fa-book-open"></i> My Issued Books</h2>

    <?php if(mysqli_num_rows($issued_query) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Borrow Date</th>
                        <th>Return Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; while($book = mysqli_fetch_assoc($issued_query)): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author_name']); ?></td>
                            <td><?php echo htmlspecialchars($book['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($book['borrow_date']); ?></td>
                            <td><?php echo $book['return_date'] ? htmlspecialchars($book['return_date']) : 'Not returned'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="alert alert-info">You currently have no issued books.</p>
    <?php endif; ?>

    <a href="user_dashboard.php" class="btn btn-primary mt-3"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</div>

</body>
</html>
