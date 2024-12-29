<?php
session_start();

// Include header and db connection
require_once('../includes/header.php');
require_once('../includes/db.php');

// Get student information from session
$student_id = $_SESSION['student_id'];
$student_name = $_SESSION['full_name'];

// Fetch student information from the database
$query = "SELECT * FROM students WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $student_id);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <!-- Welcome Message -->
        <div class="text-center mb-4">
            <h2>Welcome, <?php echo htmlspecialchars($student_name); ?>!</h2>
        </div>

        <!-- Row of Cards -->
        <div class="row">
            <!-- View Attendance -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white text-center">View Attendance</div>
                    <div class="card-body text-center">
                        <a href="view_attendance.php" class="btn btn-primary">View Attendance</a>
                    </div>
                </div>
            </div>

            <!-- Check Eligibility Status -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white text-center">Eligibility Status</div>
                    <div class="card-body text-center">
                        <a href="eligibility_status.php" class="btn btn-success">Check Status</a>
                    </div>
                </div>
            </div>

            <!-- Register Courses -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark text-center">Register Courses</div>
                    <div class="card-body text-center">
                        <a href="register_courses.php" class="btn btn-warning">Register Courses</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logout Button - Centered -->
        <div class="row justify-content-center">
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white text-center">Logout</div>
                    <div class="card-body text-center">
                        <a href="../views/logout.php" class="btn btn-danger">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Include footer
require_once('../includes/footer.php');
Footer::display();
?>
