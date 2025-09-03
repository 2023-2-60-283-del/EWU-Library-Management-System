<?php
session_start();

// Redirect if admin not logged in
if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

// DB connection
$connection = mysqli_connect("localhost","root","","library_DB");
if(!$connection){
    die("Database connection failed: ".mysqli_connect_error());
}

// Initialize variables
$book_id = $title = $author_id = $category_id = $isbn = "";
$error = "";
$success = "";

// Get book ID from URL
if(isset($_GET['id'])){
    $book_id = intval($_GET['id']);

    // Fetch book details
    $book_query = "SELECT * FROM books WHERE book_id=$book_id";
    $book_result = mysqli_query($connection, $book_query);

    if(mysqli_num_rows($book_result) == 1){
        $book = mysqli_fetch_assoc($book_result);
        $title = $book['title'];
        $author_id = $book['author_id'];
        $category_id = $book['category_id'];
        $isbn = $book['isbn'];
    } else {
        $error = "Book not found!";
    }
} else {
    $error = "Invalid book ID!";
}

// Handle form submission
if(isset($_POST['update'])){
    $title = mysqli_real_escape_string($connection, $_POST['title']);
    $author_id = intval($_POST['author_id']);
    $category_id = intval($_POST['category_id']);
    $isbn = mysqli_real_escape_string($connection, $_POST['isbn']);

    if(empty($title)){
        $error = "Title cannot be empty!";
    } else {
        $update_query = "UPDATE books SET 
                            title='$title',
                            author_id=".($author_id > 0 ? $author_id : "NULL").",
                            category_id=".($category_id > 0 ? $category_id : "NULL").",
                            isbn='$isbn'
                         WHERE book_id=$book_id";

        if(mysqli_query($connection, $update_query)){
            $success = "Book updated successfully!";
        } else {
            $error = "Error updating book: " . mysqli_error($connection);
        }
    }
}

// Fetch authors and categories for dropdowns
$authors_result = mysqli_query($connection, "SELECT author_id, name FROM authors ORDER BY name ASC");
$categories_result = mysqli_query($connection, "SELECT category_id, name FROM categories ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Edit Book | LMS</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { background: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.container { margin-top: 50px; max-width: 700px; }
.card { border-radius: 16px; box-shadow: 0 6px 15px rgba(0,0,0,0.1); }
.card-header { border-radius: 16px 16px 0 0; background: #007bff; color: white; font-weight: bold; }
</style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header"><i class="fas fa-edit"></i> Edit Book</div>
        <div class="card-body">

            <?php if($error){ ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php } ?>

            <?php if($success){ ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php } ?>

            <form method="post">
                <div class="form-group">
                    <label for="title">Book Title</label>
                    <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($title); ?>" required>
                </div>

                <div class="form-group">
                    <label for="author_id">Author</label>
                    <select name="author_id" id="author_id" class="form-control">
                        <option value="">-- Select Author --</option>
                        <?php while($author = mysqli_fetch_assoc($authors_result)){ ?>
                            <option value="<?php echo $author['author_id']; ?>" <?php if($author_id == $author['author_id']) echo "selected"; ?>>
                                <?php echo htmlspecialchars($author['name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select name="category_id" id="category_id" class="form-control">
                        <option value="">-- Select Category --</option>
                        <?php while($category = mysqli_fetch_assoc($categories_result)){ ?>
                            <option value="<?php echo $category['category_id']; ?>" <?php if($category_id == $category['category_id']) echo "selected"; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="isbn">ISBN</label>
                    <input type="text" name="isbn" id="isbn" class="form-control" value="<?php echo htmlspecialchars($isbn); ?>">
                </div>

                <button type="submit" name="update" class="btn btn-primary"><i class="fas fa-save"></i> Update Book</button>
                <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            </form>

        </div>
    </div>
</div>

</body>
</html>
