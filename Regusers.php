<?php
session_start();

// Redirect if admin not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$connection = mysqli_connect("localhost", "root", "", "library_DB");
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Delete user if requested
if (isset($_GET['delete'])) {
    $student_id = mysqli_real_escape_string($connection, $_GET['delete']);
    mysqli_query($connection, "DELETE FROM users WHERE student_id='$student_id'");
    header("Location: Regusers.php");
    exit();
}

// Fetch all users with the admin who added them
$query = "
    SELECT u.student_id, u.name, u.email, u.mobile, u.address, a.name AS added_by_name
    FROM users u
    LEFT JOIN admins a ON u.added_by = a.admin_id
    ORDER BY u.student_id ASC
";
$result = mysqli_query($connection, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Registered Users | LMS</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { background: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.container { margin-top: 40px; }
.card { border-radius: 16px; box-shadow: 0 6px 15px rgba(0,0,0,0.1); }
.card-header { border-radius: 16px 16px 0 0; background: #007bff; color: white; font-weight: bold; }
.table th, .table td { vertical-align: middle !important; }
.action-btns a { margin-right: 5px; }
</style>
</head>
<body>

<div class="container">
    <div class="card">
        <div class="card-header"><i class="fas fa-users"></i> Registered Users</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Address</th>
                            <th>Added By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['student_id']); ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['mobile']); ?></td>
                            <td><?php echo htmlspecialchars($user['address']); ?></td>
                            <td><?php echo htmlspecialchars($user['added_by_name'] ?? 'N/A'); ?></td>
                            <td class="action-btns">
                                <a href="edit_user.php?student_id=<?php echo $user['student_id']; ?>" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="Regusers.php?delete=<?php echo $user['student_id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this user?');">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if(mysqli_num_rows($result) == 0) { ?>
                        <tr><td colspan="7" class="text-center text-muted">No users found.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>
