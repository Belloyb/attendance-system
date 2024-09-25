<?php
session_start();
// admin/reports.php
require_once '../includes/db.php'; //Data base
include '../includes/auth.php';  // Authentication check
include '../includes/header.php';  // Header
require '../models/Report.php';    // Report model

$role = $_SESSION['role'] ?? null;
pageHeader::display($role); // Display the header with role-based links

$reportModel = new Report();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reportType = $_POST['report_type'];
    if ($reportType == 'attendance') {
        $reportModel->generateAttendanceReport();  // Generate attendance report
    } elseif ($reportType == 'user') {
        $reportModel->generateUserReport();  // Generate user report
    } elseif ($reportType == 'course') {
        $reportModel->generateCourseReport();  // Generate course report
    }
}
?>

<div class="container mt-5">
    <h1>Generate Reports</h1>
    <form method="POST">
        <div class="form-group">
            <label for="report_type">Select Report Type:</label>
            <select class="form-control" id="report_type" name="report_type">
                <option value="attendance">Attendance Report</option>
                <option value="user">User Report</option>
                <option value="course">Course Report</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Generate Report</button>
    </form>
</div>

<?php include '../includes/footer.php'; 
Footer::display(); // Assuming your footer has a display method
?>

