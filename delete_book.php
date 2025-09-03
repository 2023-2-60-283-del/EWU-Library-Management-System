<?php
session_start();

// ✅ Only admin can access
if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

// ✅ Database connection
$connection = mysqli_connect("localhost","root","","library_DB");
if(!$connection){
    die("Database connection failed: ".mysqli_connect_error());
}

// ✅ Validate and sanitize book_id
if(isset($_GET['id']) && is_numeric($_GET['id'])){
    $book_id = intval($_GET['id']);

    // 🔍 Check if the book exists
    $check_query = "SELECT * FROM books WHERE book_id = $book_id";
    $check_result = mysqli_query($connection, $check_query);

    if(mysqli_num_rows($check_result) > 0){

        // 🔍 Check if the book is issued
        $issued_check = "SELECT * FROM borrow_records 
                         WHERE book_id = $book_id 
                         AND (return_date IS NOT NULL)";
        $issued_result = mysqli_query($connection, $issued_check);

        if(mysqli_num_rows($issued_result) > 0){
            $_SESSION['success_message'] = "❌ Cannot delete! Book is currently issued.";
        } else {
            // ✅ Safe to delete
            $delete_query = "DELETE FROM books WHERE book_id = $book_id";
            if(mysqli_query($connection, $delete_query)){
                $_SESSION['success_message'] = "✅ Book deleted successfully!";
            } else {
                $_SESSION['success_message'] = "⚠️ Error deleting book: " . mysqli_error($connection);
            }
        }

    } else {
        $_SESSION['success_message'] = "⚠️ Book not found!";
    }

} else {
    $_SESSION['success_message'] = "⚠️ Invalid request!";
}

// ✅ Redirect back to dashboard
header("Location: admin_dashboard.php");
exit();
?>
