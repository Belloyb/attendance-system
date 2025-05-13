<?php
session_start();
require_once('../includes/header.php');
require_once('../includes/db.php');

// Assume the student is logged in, and we have the student_id in the session
$student_id = $_SESSION['student_id']; // You must have student_id in session

// Fetch registered courses for the student
$query = "SELECT c.course_id, c.course_code, c.course_name 
          FROM student_courses sc
          JOIN courses c ON sc.course_id = c.course_id
          WHERE sc.student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $student_id);
$stmt->execute();
$registered_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to fetch attendance records for a specific course
function fetchAttendanceRecords($conn, $student_id, $course_id) {
    $query = "SELECT attendance_date, status 
              FROM attendance 
              WHERE student_id = ? AND course_id = ? 
              ORDER BY attendance_date ASC";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(1, $student_id);
    $stmt->bindParam(2, $course_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
    <!-- Include Bootstrap CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Your Attendance Records</h2>

    <!-- Navigation button to go back to dashboard -->
    <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

    <?php if (count($registered_courses) > 0): ?>
        <div class="accordion" id="attendanceAccordion">
            <?php foreach ($registered_courses as $course): ?>
                <?php 
                    $attendance_records = fetchAttendanceRecords($conn, $student_id, $course['course_id']);
                ?>
                <div class="card">
                    <div class="card-header" id="heading<?php echo $course['course_id']; ?>">
                        <h5 class="mb-0">
                            <button class="btn btn-link" type="button" data-toggle="collapse" 
                                    data-target="#collapse<?php echo $course['course_id']; ?>" 
                                    aria-expanded="true" aria-controls="collapse<?php echo $course['course_id']; ?>">
                                <?php echo $course['course_code'] . ' - ' . $course['course_name']; ?>
                            </button>
                        </h5>
                    </div>

                    <div id="collapse<?php echo $course['course_id']; ?>" class="collapse" 
                         aria-labelledby="heading<?php echo $course['course_id']; ?>" 
                         data-parent="#attendanceAccordion">
                        <div class="card-body">
                            <?php if (count($attendance_records) > 0): ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($attendance_records as $record): ?>
                                            <tr>
                                                <td><?php echo date('d M Y', strtotime($record['attendance_date'])); ?></td>
                                                <td><?php echo ucfirst($record['status']); ?></td> <!-- Present/Absent -->
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p>No attendance records available for this course.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>You are not registered for any courses.</p>
    <?php endif; ?>
</div>

<!-- Include Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="../assets/bootstrap-5.3.3-dist/js/bootstrap.min.js"></script>
</body>
</html>

<?php
require_once('../includes/footer.php');
Footer::display();
?>

