<?php
session_start();
require_once 'includes/db.php';  // Connect to your database

// Initialize an error variable to hold error messages
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query the database to check if the user exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the result as an associative array

    // Check if the user was found
    if ($result) {
        // Check the password against the stored hash
        if (password_verify($password, $result['password'])) {
            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id();

            // Start a session or handle successful login
            $_SESSION['username'] = $result['username'];
            $_SESSION['role'] = $result['role'];

            // Redirect to the dashboard based on the user's role
            if ($result['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } elseif ($result['role'] === 'lecturer') {
                header('Location: lecturer/dashboard.php');
            } elseif ($result['role'] === 'student') {
                header('Location: student/dashboard.php');
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Invalid username!";
    }
}
