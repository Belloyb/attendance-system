<?php
session_start();
require_once '../includes/db.php'; // Database connection

// Check if the course ID is provided via GET request
if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id']; // Get course ID

    // Prepare the SQL query
    $sql = "DELETE FROM courses WHERE course_id = :id";

    // Prepare the statement
    $stmt = $conn->prepare($sql);

    // Bind the course ID to the prepared statement
    $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);

    // Execute the query
    if ($stmt->execute()) {
        // If the deletion is successful, redirect to the courses page
        header('Location: manage_courses.php');
        exit();
    } else {
        // Handle any errors during execution
        echo "Error deleting the course.";
    }
} else {
    // If no course ID is provided, redirect to the courses page
    header('Location: manage_courses.php');
    exit();
}
