<?php

// Includes database connection
require_once('../includes/db.php');

// Check if level and semester are provided in the GET request
if (isset($_GET['level']) && isset($_GET['semester'])) {
    $level = $_GET['level'];
    $semester = $_GET['semester'];

    // Prepare and execute the query
    $query = "SELECT id, course_name FROM courses WHERE level = ? AND semester = ?";
    $stmt = $conn->prepare($query);
    $stmt->bindValue('ii', $level, $semester);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch courses and prepare for JSON response
    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = [
            'id' => $row['id'], 
            'name' => $row['course_name']
        ];
    }

    // Set the header for JSON response
    header('Content-Type: application/json');
    echo json_encode($courses);
}
?>
