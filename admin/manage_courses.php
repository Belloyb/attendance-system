<?php
session_start();
// admin/manage_courses.php
require_once '../includes/db.php';
include '../includes/auth.php';  // Authentication check
include '../includes/header.php';  // Header
require '../models/Course.php';    // Course model
require_once '../controllers/CourseController.php';


$role = $_SESSION['role'] ?? null;
pageHeader::display($role); // Display the header with role-based links

$courseController = new CourseController($conn);
$courses = $courseController->getCourses();


// Fetch all courses from the database
$query = "SELECT * FROM courses";
$stmt = $conn->prepare($query);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all courses as associative array     
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Manage Courses</h2>
    
    <!-- Add New Course Button -->
    <a href="add_course.php" class="btn btn-primary mb-3">Add New Course</a>

    <!-- Display Courses in Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Course Name</th>
                <th>Lecturer</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Check if any courses are available
if (count($courses) > 0) {
    foreach ($courses as $row) {
        echo "<tr>";
        echo "<td>" . $row['course_id'] . "</td>";
        echo "<td>" . $row['course_name'] . "</td>";
        echo "<td>" . $row['lecturer_id'] . "</td>";
        echo "<td>
                <a href='edit_course.php?id=" . $row['course_id'] . "' class='btn btn-warning btn-sm'>Edit</a>
                <a href='delete_course.php?id=" . $row['course_id'] . "' class='btn btn-danger btn-sm'>Delete</a>
              </td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No courses found.</td></tr>";
}
            ?>
        </tbody>
    </table>
</div>

<?php
// Include footer
require_once('../includes/footer.php');
Footer::display()

?>
