<?php
// functions.php

function connect_db() {
    $connection = mysqli_connect("localhost", "root", "", "library_DB");
    if (!$connection) {
        die("Database connection failed: " . mysqli_connect_error());
    }
    return $connection;
}

// Get the number of books issued by a user
function get_user_issue_book_count($student_id) {
    $connection = connect_db();
    $student_id_safe = mysqli_real_escape_string($connection, $student_id);

    $query = "SELECT COUNT(*) AS count FROM borrow_records WHERE student_id = '$student_id_safe'";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] ?? 0;
}

// Get total number of books in library
function get_total_books_count() {
    $connection = connect_db();
    $query = "SELECT COUNT(*) AS count FROM books";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['count'] ?? 0;
}

// Get user info by student_id
function get_user_info($student_id) {
    $connection = connect_db();
    $student_id_safe = mysqli_real_escape_string($connection, $student_id);

    $query = "SELECT * FROM users WHERE student_id = '$student_id_safe'";
    $result = mysqli_query($connection, $query);
    return mysqli_fetch_assoc($result);
}
?>
