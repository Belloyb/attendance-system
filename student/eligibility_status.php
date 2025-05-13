
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

// Function to calculate attendance percentage
function calculateAttendance($conn, $student_id, $course_id) {
    // Total number of classes conducted for the course
    $query = "SELECT COUNT(*) AS total_classes FROM attendance WHERE course_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(1, $course_id);
    $stmt->execute();
    $total_classes = $stmt->fetch(PDO::FETCH_ASSOC)['total_classes'];

    // Total number of classes the student attended for this course
    $query = "SELECT COUNT(*) AS attended_classes 
              FROM attendance 
              WHERE course_id = ? AND student_id = ? AND status = 'present'";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(1, $course_id);
    $stmt->bindParam(2, $student_id);
    $stmt->execute();
    $attended_classes = $stmt->fetch(PDO::FETCH_ASSOC)['attended_classes'];

    // Calculate attendance percentage
    if ($total_classes > 0) {
        return ($attended_classes / $total_classes) * 100;
    } else {
        return 0; // No classes conducted yet
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Eligibility Status</title>
    <!-- Include Bootstrap CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Exam Eligibility Status</h2>

    <!-- Navigation button to go back to dashboard -->
    <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

    <div class="card">
        <div class="card-header">Your Eligibility Status</div>
        <div class="card-body">
            <?php if (count($registered_courses) > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Attendance Percentage</th>
                            <th>Eligibility Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registered_courses as $course): ?>
                            <?php
                                $attendance_percentage = calculateAttendance($conn, $student_id, $course['course_id']);
                                $eligibility_status = ($attendance_percentage >= 75) ? 'Eligible' : 'Not Eligible';
                            ?>
                            <tr>
                                <td><?php echo $course['course_code']; ?></td>
                                <td><?php echo $course['course_name']; ?></td>
                                <td><?php echo number_format($attendance_percentage, 2) . '%'; ?></td>
                                <td>
                                    <?php if ($eligibility_status === 'Eligible'): ?>
                                        <span class="badge badge-success">Eligible</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Not Eligible</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You are not registered for any courses.</p>
            <?php endif; ?>
        </div>
    </div>
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

