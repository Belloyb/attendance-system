<?php
// Example in LoginController.php
session_start();

// Assuming you’ve verified the user credentials and fetched user details
if ($userIsValid) {
    // Set session variables based on the user type
    $_SESSION['role'] = $user['role']; // 'admin', 'lecturer', or 'student'
    $_SESSION['user_id'] = $user['id']; // User ID for future reference

    // Redirect based on role
    if ($user['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } elseif ($user['role'] === 'lecturer') {
        header('Location: lecturer/dashboard.php');
    } elseif ($user['role'] === 'student') {
        header('Location: student/dashboard.php');
    }
    exit();
}

