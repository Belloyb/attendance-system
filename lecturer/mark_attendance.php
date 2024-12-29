<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';



// Check if lecturer is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'lecturer') {
    die("Access denied. Lecturer not logged in.");
}

$lecturer_id = $_SESSION['user_id'];
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : null;

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch courses assigned to the lecturer
    $stmt = $conn->prepare("SELECT course_id, course_name FROM courses WHERE lecturer_id = :lecturer_id");
    $stmt->bindParam(':lecturer_id', $lecturer_id, PDO::PARAM_INT);
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch students for the selected course
    if ($course_id) {
        $stmt = $conn->prepare("
            SELECT s.id AS student_id, s.fullname 
            FROM students s 
            JOIN student_courses sc ON s.id = sc.student_id 
            WHERE sc.course_id = :course_id
        ");
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Handle attendance submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST['attendance'] as $student_id => $status) {
                $stmt = $conn->prepare("
                    INSERT INTO attendance (student_id, course_id, status, lecturer_id, attendance_date) 
                    VALUES (:student_id, :course_id, :status, :lecturer_id, NOW())
                    ON DUPLICATE KEY UPDATE status = :status
                ");
                $stmt->execute([
                    ':student_id' => $student_id,
                    ':course_id' => $course_id,
                    ':status' => $status,
                    ':lecturer_id' => $lecturer_id,
                ]);
            }
            echo "Attendance marked successfully.";
        }
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Mark Attendance</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        h1 {
            color: #343a40;
            font-weight: bold;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #216bbb;
            color: #fff;
            border-radius: 10px 10px 0 0;
            font-weight: bold;
        }
        .table th {
            background-color: #216bbb;
            color: white;
        }
        .btn {
            font-size: 1.1rem;
        }
        select.form-select {
            border: 2px solid rgb(30, 80, 134);
        }
        .submit-btn {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
            border-radius: 30px;
        }
        .submit-btn:hover {
            background-color: #218838;
        }
        .form-check-input:checked {
            background-color: #007bff;
            border-color: #007bff;
        }
        .radio-label {
            font-size: 1rem;
            font-weight: bold;
            color: #495057;
        }
        .no-courses {
            color: #6c757d;
            text-align: center;
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

    <div class="container mt-5">
        <h1 class="text-center mb-4">Mark Attendance</h1>

        <!-- Course Selection -->
        <div class="card mb-4">
            <div class="card-header text-center">Select a Course</div>
            <div class="card-body">
                <form action="" method="get">
                    <div class="mb-3">
                        <select name="course_id" id="course" class="form-select form-select-lg" onchange="this.form.submit()">
                            <option value="">-- Select a Course --</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo $course['course_id']; ?>" 
                                    <?php if ($course['course_id'] == $course_id) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($course['course_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- Students List for Attendance -->
        <?php if (isset($students)): ?>
            <div class="card">
                <div class="card-header text-center">Attendance for Selected Course</div>
                <div class="card-body">
                    <form action="" method="post">
                        <table class="table table-bordered table-hover">
                            <thead class="text-center">
                            <tr>
                                <th>Student Name</th>
                                <th>Present</th>
                                <th>Absent</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['fullname']); ?></td>
                                    <td class="text-center">
                                        <input type="radio" 
                                               class="form-check-input" 
                                               name="attendance[<?php echo $student['student_id']; ?>]" 
                                               value="present" 
                                               required>
                                    </td>
                                    <td class="text-center">
                                        <input type="radio" 
                                               class="form-check-input" 
                                               name="attendance[<?php echo $student['student_id']; ?>]" 
                                               value="absent" 
                                               required>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="d-grid">
                            <button type="submit" class="btn submit-btn">Submit Attendance</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php elseif ($course_id): ?>
            <p class="no-courses">No students are enrolled in the selected course.</p>
        <?php endif; ?>
    </div>

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include '../includes/footer.php'; 
Footer::display();
?>