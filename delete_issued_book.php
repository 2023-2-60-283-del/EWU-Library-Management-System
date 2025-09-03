<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$connection = mysqli_connect("localhost", "root", "", "library_DB");
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Validate and sanitize record ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $record_id = intval($_GET['id']);

    // Prepare and execute delete query
    $stmt = $connection->prepare("DELETE FROM borrow_records WHERE record_id = ?");
    $stmt->bind_param("i", $record_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Issued book record deleted successfully!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting record: " . $connection->error;
        $_SESSION['msg_type'] = "danger";
    }

    $stmt->close();
} else {
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['msg_type'] = "warning";
}

// Redirect back to issued books page
header("Location: admin_view_issued_book.php");
exit();
