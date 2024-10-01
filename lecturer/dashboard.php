<?php
session_start();

// Includes
require_once '../includes/db.php';
include '../includes/auth.php';  // Authentication check
include '../includes/header.php';  // Header

// Session expiration check
$session_lifetime = 30 * 60; // 30 minutes
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_lifetime) {
    session_unset();
    session_destroy();
    header('Location: ../index.php'); // Redirect to login page
    exit();
}
$_SESSION['last_activity'] = time();

// After successful login, set session variables
$_SESSION['user_id'] = $user['user_id']; // Set user ID
$_SESSION['username'] = $user['username']; // Set username
$_SESSION['email'] = $user['email']; // Set email

// Ensure the user is a lecturer
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    header('Location: ../index.php');
    exit();
}

$lecturer_id = $_SESSION['user_id']; // Lecturer's user ID
$lecturer_name = $_SESSION['username']; // Assuming username or name is stored in the session

// Fetch courses assigned to the lecturer
$stmt = $conn->prepare("SELECT * FROM courses WHERE lecturer_id = :lecturer_id");
$stmt->bindParam(':lecturer_id', $lecturer_id, PDO::PARAM_INT);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
$course_count = count($courses); // Count of courses taught

// Fetch the total number of students across all courses the lecturer teaches
$studentStmt = $conn->prepare("
    SELECT COUNT(DISTINCT student_id) AS total_students 
    FROM student_courses 
    WHERE course_id IN (SELECT course_id FROM courses WHERE lecturer_id = :lecturer_id)
");
$studentStmt->bindParam(':lecturer_id', $lecturer_id, PDO::PARAM_INT);
$studentStmt->execute();
$student_count = $studentStmt->fetch(PDO::FETCH_ASSOC)['total_students'];

// Fetch total attendance records for each course taught by the lecturer
$attendanceStmt = $conn->prepare("
    SELECT courses.course_name, COUNT(attendance.id) AS total_attendance 
    FROM attendance
    INNER JOIN courses ON attendance.course_id = courses.course_id
    WHERE courses.lecturer_id = :lecturer_id
    GROUP BY courses.course_name
");
$attendanceStmt->bindParam(':lecturer_id', $lecturer_id, PDO::PARAM_INT);
$attendanceStmt->execute();
$attendanceRecords = $attendanceStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
        }
        .sidebar a {
            color: white;
            padding: 10px;
            display: block;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .main-content {
            margin-left: 220px;
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <nav class="navbar navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="../assets/logo/download.jfif" width="30" height="30" class="d-inline-block align-top" alt=""> UMYUK
        </a>
        <span class="navbar-text">
            <img src="../assets/logo/user-icn.png" width="30" height="30" class="rounded-circle" alt=""> <?php echo htmlspecialchars($lecturer_name); ?>
        </span>
    </nav>

    <!-- Sidebar -->
    <div class="d-flex">
        <div class="sidebar p-3">
            <h4>Dashboard</h4>
            <a href="#">Home</a>
            <!-- <a href="#">Courses Taught</a> -->
            <a href="#">Mark Attendance</a>
            <!-- <a href="#">Attendance Reports</a> -->
            <a href="#">Generate XML Report</a>
            <a href="profile.php">Profile</a>
            <a href="../logout.php">Logout</a>
        </div>

        <!-- Main Content -->
        <div class="main-content container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <h5 class="card-title">Courses</h5>
                            <p class="card-text"><?php echo $course_count; ?> Courses</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <h5 class="card-title">Students</h5>
                            <p class="card-text"><?php echo $student_count; ?> Students</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card text-white bg-danger">
                        <div class="card-body">
                            <h5 class="card-title">Reports</h5>
                            <p class="card-text">4 Generated</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Courses Taught -->
            <h3>Courses Taught</h3>
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                                <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary">Mark Attendance</a>
                                    <a href="#" class="btn btn-sm btn-secondary">View Report</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No courses assigned</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Recent Attendance Activity -->
            <h3>Attendance Records</h3>
            <ul class="list-group">
                <?php if (!empty($attendanceRecords)): ?>
                    <?php foreach ($attendanceRecords as $record): ?>
                        <li class="list-group-item">
                            Marked attendance for <?php echo htmlspecialchars($record['course_name']); ?> - Total Attendance: <?php echo $record['total_attendance']; ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item">No attendance records found</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
include_once '../includes/footer.php';
footer::display();
?>
