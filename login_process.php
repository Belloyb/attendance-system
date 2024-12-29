<?php
session_start();
require_once 'includes/db.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($username) || empty($password)) {
        header('Location: index.php?error=Please fill out all fields');
        exit();
    }

    // Check if the user exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Regenerate session ID
        session_regenerate_id(true);

        // Set session variables
        $_SESSION['user_id'] = $user['user_id']; // Unique user ID
        $_SESSION['role'] = $user['role']; // User role (e.g., 'lecturer', 'admin')
        $_SESSION['username'] = $user['username'];

       


        // Redirect based on role
        if ($user['role'] === 'lecturer') {
            header('Location: lecturer/dashboard.php');
        } elseif ($user['role'] === 'admin') {
            header('Location: admin/dashboard.php');
        } elseif ($user['role'] === 'student') {
            header('Location: student/dashboard.php');
        } else {
            header('Location: index.php?error=Invalid role');
        }
        exit();
    } else {
        header('Location: index.php?error=Invalid username or password');
        exit();
    }
}
