<?php
session_start();

// Ensure user is logged in
if(!isset($_SESSION['student_id'])){
    header("Location: index.php");
    exit();
}

$connection = mysqli_connect("localhost","root","","library_DB");
if(!$connection){
    die("Database connection failed: ".mysqli_connect_error());
}

// Fetch user info for navbar
$student_id = $_SESSION['student_id'];
$user_query = mysqli_query($connection, "SELECT * FROM users WHERE student_id='$student_id'");
$user_info = mysqli_fetch_assoc($user_query);

// Fetch all books with author and category
$books_query = "
    SELECT b.book_id, b.title, b.isbn, a.name AS author_name, c.name AS category_name
    FROM books b
    LEFT JOIN authors a ON b.author_id = a.author_id
    LEFT JOIN categories c ON b.category_id = c.category_id
    ORDER BY b.book_id ASC
";
$books_result = mysqli_query($connection, $books_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Library Books | LMS</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
.navbar { background: #fff !important; border-bottom: 1px solid #ddd; }
.navbar-brand { color: #007bff !important; font-weight: bold; }
.card { border-radius: 16px; box-shadow: 0 6px 15px rgba(0,0,0,0.1); transition: 0.2s; }
.card:hover { transform: translateY(-6px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
.card-header { background: #007bff; color: #fff; font-weight: bold; border-radius: 16px 16px 0 0; }
.table th, .table td { vertical-align: middle !important; }
.btn-custom { border-radius: 25px; padding: 6px 18px; margin:2px; }
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
    <div class="card">
        <div class="card-header text-center"><i class="fas fa-book"></i> Library Books</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Book ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>ISBN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($books_result) > 0): ?>
                            <?php while($book = mysqli_fetch_assoc($books_result)): ?>
                            <tr>
                                <td><?php echo $book['book_id']; ?></td>
                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                <td><?php echo htmlspecialchars($book['author_name']); ?></td>
                                <td><?php echo htmlspecialchars($book['category_name']); ?></td>
                                <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">No books found in the library.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <a href="user_dashboard.php" class="btn btn-info btn-custom mt-3"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>
