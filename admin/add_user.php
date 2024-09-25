<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/header.php';

$role = $_SESSION['role'];
pageHeader::display($role);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the user into the database
    $query = "INSERT INTO users (full_name, username, email, role, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt->execute([$full_name, $username, $email, $role, $hashed_password])) {
        // Redirect to manage users page after successful insertion
        header("Location: manage_user.php");
        exit();
    } else {
        echo "Error adding user.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User</title>

    <!-- Link to Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body, html {
            height: 100%;
        }
        .login-container {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Add New User</h2>
    <form action="add_user.php" method="POST">
    <div class="form-group">
        <label for="full_name">Full Name:</label>
        <input type="text" name="full_name" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" name="username" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="role">Role:</label>
        <select name="role" class="form-control" required>
            <option value="admin">Admin</option>
            <option value="lecturer">Lecturer</option>
        </select>
    </div>
    <!-- Add the Password Field -->
    <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Add User</button>
</form>

</div>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
