<?php
require_once '../includes/auth.php'; // Checks for lecturer role
require_once '../includes/db.php';  // Connect to the database



// Fetch the courses assigned to this lecturer
$lecturer_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM courses WHERE lecturer_id = :lecturer_id");
$stmt->bindParam(':lecturer_id', $lecturer_id, PDO::PARAM_INT);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch attendance records for a course if selected
$attendance = [];
$search_query = '';
if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
    $search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

    $sql = "
        SELECT students.fullname, attendance.attendance_date, attendance.status 
        FROM attendance
        JOIN students ON attendance.student_id = students.id
        WHERE attendance.course_id = :course_id
    ";

    // Add search filter if a search query exists
    if (!empty($search_query)) {
        $sql .= " AND students.fullname LIKE :search_query";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);

    if (!empty($search_query)) {
        $search_param = "%" . $search_query . "%";
        $stmt->bindParam(':search_query', $search_param, PDO::PARAM_STR);
    }

    $stmt->execute();
    $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 30px;
        }
        h1 {
            color: #007bff;
            font-weight: bold;
        }
        .welcome-message {
            font-size: 1.2rem;
            color: #495057;
            margin-bottom: 20px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color:#0f5bad;
            color: white;
            font-weight: bold;
            border-radius: 10px 10px 0 0;
        }
        .table th {
            background-color: #0f5bad;
            color: white;
            text-align: center;
        }
        .table td {
            text-align: center;
        }
        .course-list a {
            text-decoration: none;
            font-weight: bold;
            color: #0f5bad;
        }
        .course-list a:hover {
            text-decoration: underline;
        }
        .no-data {
            text-align: center;
            color: #6c757d;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Attendance System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Page Header -->
        <h1 class="text-center mt-4">View Attendance</h1>
        <p class="welcome-message">Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>! Below are your courses and their respective attendance records.</p>

        <!-- Courses List -->
        <div class="card mb-4">
            <div class="card-header text-center">Your Courses</div>
            <div class="card-body">
                <?php if (!empty($courses)): ?>
                    <form action="" method="get">
                        <div class="mb-3">
                            <label for="course" class="form-label">Select a Course:</label>
                            <select name="course_id" id="course" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Select Course --</option>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?php echo $course['course_id']; ?>" <?php if (isset($_GET['course_id']) && $_GET['course_id'] == $course['course_id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($course['course_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                <?php else: ?>
                    <p class="no-data">You are not assigned to any courses.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Search Feature -->
        <?php if (!empty($attendance) || isset($_GET['course_id'])): ?>
            <div class="mb-4">
                <form action="" method="get">
                    <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($_GET['course_id']); ?>">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search student by name" value="<?php echo htmlspecialchars($search_query); ?>">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Attendance Records -->
        <?php if (!empty($attendance)): ?>
            <div class="card">
                <div class="card-header text-center">Attendance Records</div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendance as $record): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($record['fullname']); ?></td>
                                    <td><?php echo htmlspecialchars($record['attendance_date']); ?></td>
                                    <td>
                                        <?php if ($record['status'] === 'present'): ?>
                                            <span class="badge bg-success">Present</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Absent</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif (isset($_GET['course_id'])): ?>
            <p class="no-data">No attendance records found for the selected course.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


<?php include '../includes/footer.php'; 
Footer::display();
?>
