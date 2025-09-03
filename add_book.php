<?php
session_start();
require("functions.php"); // optional helper functions

// Redirect if admin not logged in
if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

$connection = mysqli_connect("localhost","root","","library_DB");
if(!$connection){
    die("Database connection failed: ".mysqli_connect_error());
}

// Get logged-in admin ID
$admin_id = $_SESSION['admin_id'];

// Fetch authors and categories for the dropdown
$authors = mysqli_query($connection, "SELECT * FROM authors ORDER BY name");
$categories = mysqli_query($connection, "SELECT * FROM categories ORDER BY name");

// Handle form submission
if(isset($_POST['add_book'])){
    $title       = mysqli_real_escape_string($connection, $_POST['title']);
    $author_id   = mysqli_real_escape_string($connection, $_POST['author_id']);
    $category_id = mysqli_real_escape_string($connection, $_POST['category_id']);
    $isbn        = mysqli_real_escape_string($connection, $_POST['isbn']);

    // Insert book with added_by field
    $insert = mysqli_query($connection, "
        INSERT INTO books (title, author_id, category_id, isbn, added_by)
        VALUES ('$title', '$author_id', '$category_id', '$isbn', '$admin_id')
    ");

    if($insert){
        $success_message = "Book added successfully!";
    } else {
        $error_message = "Error adding book: ".mysqli_error($connection);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Book | LMS</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<style>
.container { max-width: 600px; margin-top: 50px; }
.btn-custom { border-radius: 25px; padding: 6px 18px; }
</style>
</head>
<body>
<div class="container bg-white p-4 rounded shadow">
    <h3 class="mb-4 text-center"><i class="fas fa-plus"></i> Add Book</h3>

    <?php
    if(isset($success_message)){
        echo "<div class='alert alert-success'>{$success_message}</div>";
    }
    if(isset($error_message)){
        echo "<div class='alert alert-danger'>{$error_message}</div>";
    }
    ?>

    <form method="post">
        <div class="form-group">
            <label>Title:</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Author:</label>
            <select name="author_id" class="form-control" required>
                <option value="">--Select Author--</option>
                <?php while($a = mysqli_fetch_assoc($authors)){ ?>
                    <option value="<?php echo $a['author_id']; ?>"><?php echo htmlspecialchars($a['name']); ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Category:</label>
            <select name="category_id" class="form-control" required>
                <option value="">--Select Category--</option>
                <?php mysqli_data_seek($categories,0); while($c = mysqli_fetch_assoc($categories)){ ?>
                    <option value="<?php echo $c['category_id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>ISBN:</label>
            <input type="text" name="isbn" class="form-control">
        </div>

        <button type="submit" name="add_book" class="btn btn-success btn-block btn-custom"><i class="fas fa-plus"></i> Add Book</button>
        <a href="admin_dashboard.php" class="btn btn-secondary btn-block btn-custom">Back to Dashboard</a>
    </form>
</div>
</body>
</html>

