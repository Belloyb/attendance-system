<?php
session_start();

// admin/dashboard.php
require_once '../includes/db.php';
include '../includes/auth.php';  // Authentication check
include '../includes/header.php';  // Header
require '../models/User.php';      // User model
require '../models/Course.php';    // Course model
require '../models/Attendance.php';// Attendance model

// Check if session is too old (e.g., session expires after 30 minutes of inactivity)
$session_lifetime = 30 * 60; // 30 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_lifetime) {
    session_unset();     // Unset all session variables
    session_destroy();   // Destroy the session
    header('Location: ../index.php'); // Redirect to login page
    exit();
}
$_SESSION['last_activity'] = time(); // Update last activity time
// Check if session is too old (e.g., session expires after 30 minutes of inactivity)
$session_lifetime = 30 * 60; // 30 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_lifetime) {
    session_unset();     // Unset all session variables
    session_destroy();   // Destroy the session
    header('Location: ../index.php'); // Redirect to login page
    exit();
}
$_SESSION['last_activity'] = time(); // Update last activity time


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

$role = $_SESSION['role'];
require_once('../includes/header.php');
pageHeader::display($role);

$course = new Course($conn); // Pass the database connection to the Course class
$courses = $course->getAllCourses();


// Instantiate models
$userModel = new User();
$courseModel = new Course($conn);
$attendanceModel = new Attendance();

// Fetch counts for dashboard display
$totalUsers = $userModel->getTotalUsers();
$totalCourses = $courseModel->getTotalCourses();
$totalAttendanceRecords = $attendanceModel->getTotalAttendanceRecords();

?>

<div class="container mt-5">
    <h1>Admin Dashboard</h1>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text"><?php echo $totalUsers; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Courses</h5>
                    <p class="card-text"><?php echo $totalCourses; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Attendance Records</h5>
                    <p class="card-text"><?php echo $totalAttendanceRecords; ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; 

Footer::display();
?>
