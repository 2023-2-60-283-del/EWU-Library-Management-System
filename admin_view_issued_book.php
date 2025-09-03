<?php
session_start();

// Redirect if admin not logged in
if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

$connection = mysqli_connect("localhost", "root", "", "library_DB");
if(!$connection){
    die("Connection failed: ".mysqli_connect_error());
}

// Fetch issued books with book, author, student, and admin info
$query = "
    SELECT br.record_id, b.title, a.name AS author_name, br.student_id,
           br.borrow_date, br.return_date, ad.name AS issued_by
    FROM borrow_records br
    LEFT JOIN books b ON br.book_id = b.book_id
    LEFT JOIN authors a ON b.author_id = a.author_id
    LEFT JOIN admins ad ON br.managed_by = ad.admin_id
    ORDER BY br.borrow_date DESC
";
$query_run = mysqli_query($connection, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Issued Books | LMS</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { background: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.navbar { background: #fff !important; border-bottom: 1px solid #ddd; }
.navbar-brand { font-weight: bold; color: #007bff !important; }
.table { background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 6px 15px rgba(0,0,0,0.1); }
.table thead { background: #007bff; color: #fff; }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="admin_dashboard.php"><i class="fas fa-book-reader"></i> LMS</a>
    <div class="ml-auto">
        <span class="mr-3"><strong>Welcome:</strong> <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
        <a class="btn btn-outline-danger btn-sm" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <h4 class="mb-4">Issued Books</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-hover text-center">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Student ID</th>
                    <th>Borrow Date</th>
                    <th>Return Date</th>
                    <th>Issued By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if(mysqli_num_rows($query_run) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($query_run)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['record_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['author_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                        <td><?php echo $row['borrow_date'] ?: 'N/A'; ?></td>
                        <td><?php echo $row['return_date'] ?: '<span class="text-muted">Not Set</span>'; ?></td>
                        <td><?php echo htmlspecialchars($row['issued_by'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="delete_issued_book.php?id=<?php echo $row['record_id']; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure?');">
                               <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-muted">No books issued yet.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <a href="admin_dashboard.php" class="btn btn-secondary mt-3"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
</div>

</body>
</html>
