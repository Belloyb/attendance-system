<?php
session_start();
require_once '../includes/db.php'; // Include your database connection

if (isset($_GET['user_id'])) { // Check if user_id is passed
    $id = $_GET['user_id']; // Get the user_id from the URL
    
    // Prepare the DELETE query
    $query = "DELETE FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt->execute([$id])) { // Execute the query and check if it was successful
        // Redirect back to the manage users page after successful deletion
        header("Location: manage_user.php"); // Ensure this file path is correct
        exit();
    } else {
        echo "Error deleting user.";
    }
} else {
    echo "User ID not provided!";
}
