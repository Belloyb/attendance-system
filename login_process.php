<?php
session_start();
require_once 'includes/db.php';  // Connect to your database

// Initialize an error variable to hold error messages
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Basic input validation (you may add more validation if needed)
    if (empty($username) || empty($password)) {
        header('Location: index.php?error=Please fill out all fields');
        exit();
    }

    // Query the database to check if the user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array

    // Check if the user was found
    if ($user) {
        // Verify the provided password with the hashed password in the database
        if (password_verify($password, $user['password'])) {
            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);

            // Set session variables for successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect to the appropriate dashboard based on the user's role
            switch ($user['role']) {
                case 'admin':
                    header('Location: admin/dashboard.php');
                    exit();
                case 'lecturer':
                    header('Location: lecturer/dashboard.php');
                    exit();
                case 'student':
                    header('Location: student/dashboard.php');
                    exit();
                default:
                    // In case the role is unexpected
                    header('Location: index.php?error=Invalid user role');
                    exit();
            }
        } else {
            // Invalid password
            header('Location: index.php?error=Invalid username or password');
            exit();
        }
    } else {
        // Invalid username
        header('Location: index.php?error=Invalid username or password');
        exit();
    }
}
