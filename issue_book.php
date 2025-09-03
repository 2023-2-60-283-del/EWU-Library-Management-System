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

// Handle form submission
if(isset($_POST['issue_book'])){
    $student_id  = mysqli_real_escape_string($connection, $_POST['student_id']);
    $book_id     = mysqli_real_escape_string($connection, $_POST['book_id']);
    $borrow_date = mysqli_real_escape_string($connection, $_POST['borrow_date']);
    $return_date = mysqli_real_escape_string($connection, $_POST['return_date']);
    $admin_id    = $_SESSION['admin_id'];

    // Optional: Ensure return date is after borrow date
    if(strtotime($return_date) < strtotime($borrow_date)){
        $error_message = "Return date cannot be before borrow date!";
    } else {
        // Check if book is already issued
        $check_book = mysqli_query($connection, "SELECT * FROM borrow_records WHERE book_id='$book_id' AND return_date IS NULL");
        if(mysqli_num_rows($check_book) > 0){
            $error_message = "This book is currently issued to another student!";
        } else {
            // Insert into borrow_records
            $insert = mysqli_query($connection, "
                INSERT INTO borrow_records (student_id, book_id, borrow_date, return_date, managed_by)
                VALUES ('$student_id', '$book_id', '$borrow_date', '$return_date', '$admin_id')
            ");
            if($insert){
                $_SESSION['success_message'] = "Book issued successfully!";
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error_message = "Error issuing book: ".mysqli_error($connection);
            }
        }
    }
}

// Fetch students
$students = mysqli_query($connection, "SELECT student_id, name FROM users ORDER BY name");

// Fetch books that are not currently issued
$books = mysqli_query($connection, "
    SELECT b.book_id, b.title 
    FROM books b
    LEFT JOIN borrow_records br ON b.book_id = br.book_id AND br.return_date IS NULL
    WHERE br.book_id IS NULL
    ORDER BY b.title
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Issue Book | LMS</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<style>
body { background: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.container { margin-top: 50px; max-width: 600px; }
.btn-custom { border-radius: 25px; padding: 6px 18px; margin-top:10px; }
</style>
</head>
<body>

<div class="container bg-white p-4 rounded shadow">
    <h3 class="mb-4 text-center">Issue Book</h3>

    <?php if(isset($error_message)){ echo "<div class='alert alert-danger'>{$error_message}</div>"; } ?>

    <form method="post">
        <input type="hidden" name="issue_book" value="1">

        <div class="form-group">
            <label>Select Book:</label>
            <select name="book_id" class="form-control" required>
                <option value="">--Select Book--</option>
                <?php 
                if(mysqli_num_rows($books) > 0){
                    while($b = mysqli_fetch_assoc($books)){
                        echo "<option value='{$b['book_id']}'>".htmlspecialchars($b['title'])."</option>";
                    }
                } else {
                    echo "<option value=''>No books available</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Select Student:</label>
            <select name="student_id" class="form-control" required>
                <option value="">--Select Student--</option>
                <?php while($s = mysqli_fetch_assoc($students)){
                    echo "<option value='{$s['student_id']}'>".htmlspecialchars($s['name'])."</option>";
                } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Issue Date:</label>
            <input type="date" name="borrow_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <div class="form-group">
            <label>Return Date:</label>
            <input type="date" name="return_date" class="form-control" value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" required>
        </div>

        <button type="submit" class="btn btn-info btn-block btn-custom">Issue Book</button>
        <a href="admin_dashboard.php" class="btn btn-secondary btn-block btn-custom">Back to Dashboard</a>
    </form>
</div>

</body>
</html>

