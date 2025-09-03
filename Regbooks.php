<?php
session_start();
require("functions.php");

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

$connection = mysqli_connect("localhost","root","","library_DB");
if(!$connection){
    die("Database connection failed: ".mysqli_connect_error());
}

// Fetch all books with author, category, and added_by admin
$books_query = "
    SELECT b.book_id, b.title, a.name AS author_name, c.name AS category_name, b.isbn, adm.name AS added_by_name
    FROM books b
    LEFT JOIN authors a ON b.author_id = a.author_id
    LEFT JOIN categories c ON b.category_id = c.category_id
    LEFT JOIN admins adm ON b.added_by = adm.admin_id
    ORDER BY b.book_id ASC
";
$books_result = mysqli_query($connection, $books_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Books | LMS</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<style>
.container { margin-top: 50px; }
</style>
</head>
<body>
<div class="container">
    <h3 class="mb-4">All Books</h3>
    <a href="add_book.php" class="btn btn-success mb-3"><i class="fas fa-plus"></i> Add New Book</a>
    <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Book ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>ISBN</th>
                    <th>Added By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($books_result) > 0) { ?>
                    <?php while($book = mysqli_fetch_assoc($books_result)){ ?>
                    <tr>
                        <td><?php echo $book['book_id']; ?></td>
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo htmlspecialchars($book['author_name']); ?></td>
                        <td><?php echo htmlspecialchars($book['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                        <td><?php echo htmlspecialchars($book['added_by_name'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="edit_book.php?id=<?php echo $book['book_id']; ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                            <a href="delete_book.php?id=<?php echo $book['book_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i> Delete</a>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="7" class="text-center text-muted">No books found.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
