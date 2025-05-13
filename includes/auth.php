<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: ../index.php?error=Access denied. Please log in.');
    exit();
}

// Ensure the user has the 'lecturer' role
if ($_SESSION['role'] !== 'lecturer' && $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php?error=Access denied. You are not authorized.');
    exit();
}


// Lecturer ID (retrieved from session)
$lecturer_id = $_SESSION['user_id'];
?>
