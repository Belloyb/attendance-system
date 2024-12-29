<?php
session_start();
require_once('../includes/header.php');
require_once('../includes/db.php');

// Check if session for level and semester is set, or set default values
if (!isset($_SESSION['level'])) {
    $_SESSION['level'] = '';  // Set default level, or empty if no session value
}
if (!isset($_SESSION['semester'])) {
    $_SESSION['semester'] = '1';  // Set default semester
}

$student_level = $_SESSION['level'];
$student_semester = $_SESSION['semester'];
$student_id = $_SESSION['student_id']; // Assuming student_id is stored in the session

// Fetch available levels for the dropdown (assuming levels are predefined)
$query = "SELECT DISTINCT level FROM courses ORDER BY level ASC";
$stmt = $conn->prepare($query);
$stmt->execute();
$levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch registered courses for the student
$query = "SELECT c.course_code, c.course_name 
          FROM student_courses sc
          JOIN courses c ON sc.course_id = c.course_id
          WHERE sc.student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bindParam(1, $student_id);
$stmt->execute();
$registered_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch courses based on the selected level
if (!empty($student_level)) {
    $query = "SELECT course_id, course_code, course_name 
              FROM courses 
              WHERE level = ? AND semester = ?";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(1, $student_level);
    $stmt->bindParam(2, $student_semester);
    $stmt->execute();
    $available_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $available_courses = [];
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['course_id'])) {
        $selected_course_id = $_POST['course_id'];

        // Check if the course has already been registered
        $query = "SELECT * FROM student_courses WHERE student_id = ? AND course_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $student_id);
        $stmt->bindParam(2, $selected_course_id);
        $stmt->execute();
        $duplicate = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($duplicate) {
            $error = 'You have already registered for this course.';
        } else {
            // Register the course
            $query = "INSERT INTO student_courses (student_id, course_id, enrollment_date) VALUES (?, ?, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(1, $student_id);
            $stmt->bindParam(2, $selected_course_id);

            if ($stmt->execute()) {
                $success = 'Course registered successfully!';
                header("Refresh:0"); // Refresh the page to show updated registered courses
            } else {
                $error = 'Error registering the course.';
            }
        }
    } elseif (isset($_POST['level'])) {
        // Set selected level in session
        $_SESSION['level'] = $_POST['level'];
        header("Location: register_courses.php"); // Reload the page to reflect changes
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Courses</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Register for Courses</h2>

    <!-- Navigation button to go back to dashboard -->
    <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>

    <!-- Already Registered Courses -->
    <div class="card mb-4">
        <div class="card-header">Already Registered Courses</div>
        <div class="card-body">
            <?php if (count($registered_courses) > 0): ?>
                <ul>
                    <?php foreach ($registered_courses as $course): ?>
                        <li><?php echo $course['course_code'] . ' - ' . $course['course_name']; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No courses registered yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Display error or success message -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Level Selection Form -->
    <form action="register_courses.php" method="POST">
        <div class="form-group">
            <label for="level">Select Level:</label>
            <select name="level" id="level" class="form-control" onchange="this.form.submit()" required>
                <option value="">-- Select Level --</option>
                <?php foreach ($levels as $level): ?>
                    <option value="<?php echo $level['level']; ?>" 
                        <?php echo ($level['level'] == $student_level) ? 'selected' : ''; ?>>
                        Level <?php echo $level['level']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <!-- Course Registration Form -->
    <?php if (!empty($student_level)): ?>
    <form action="register_courses.php" method="POST" onsubmit="return confirmRegistration()">
        <div class="form-group">
            <label for="course_id">Select Course to Register:</label>
            <select name="course_id" id="course_id" class="form-control" required>
                <option value="">-- Select Course --</option>
                <?php foreach ($available_courses as $course): ?>
                    <option value="<?php echo $course['course_id']; ?>">
                        <?php echo $course['course_code'] . ' - ' . $course['course_name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Register Course</button>
    </form>
    <?php else: ?>
        <p>Please select a level to view available courses.</p>
    <?php endif; ?>
</div>

<!-- Include Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Confirmation Dialog Script -->
<script>
    function confirmRegistration() {
        return confirm("Are you sure you want to register for this course?");
    }
</script>
</body>
</html>

<?php
require_once('../includes/footer.php');
Footer::display();
?>
