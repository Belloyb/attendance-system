<?php
session_start();
// Include header and db connection
require_once('../includes/header.php');
require_once('../includes/db.php');
$role = $_SESSION['role'];
pageHeader::display($role);

// Initialize the $error variable
$error = '';

// Get the course ID from the URL
$course_id = $_GET['id'] ?? null;

if (!$course_id) {
    // If no course ID is provided, redirect or display an error
    header("Location: manage_courses.php");
    exit();
}

// Fetch the existing course details
$query = "SELECT * FROM courses WHERE course_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    // If course not found, redirect or display an error
    header("Location: manage_courses.php");
    exit();
}

// Fetch lecturers for the dropdown
$query = "SELECT user_id, full_name FROM users WHERE role = 'lecturer'";
$stmt = $conn->prepare($query);
$stmt->execute();
$lecturers = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_name = $_POST['course_name'];
    $level = $_POST['level'];
    $semester = $_POST['semester'];
    $lecturer_id = $_POST['lecturer'];

    // Validate input
    if (empty($course_name) || empty($level) || empty($semester) || empty($lecturer_id)) {
        $error = 'All fields are required.';
    } else {
        // Update the course in the database
        $query = "UPDATE courses SET course_name = ?, level = ?, semester = ?, lecturer_id = ? WHERE course_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $course_name);
        $stmt->bindParam(2, $level);
        $stmt->bindParam(3, $semester);
        $stmt->bindParam(4, $lecturer_id);
        $stmt->bindParam(5, $course_id);

        if ($stmt->execute()) {
            // Redirect to manage_courses.php on success
            header("Location: manage_courses.php");
            exit();
        } else {
            // Fetch error info from PDO
            $errorInfo = $stmt->errorInfo();
            $error = "Error: " . $errorInfo[2]; // Display the SQL error message
        }
    }
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Edit Course</h2>

    <!-- Display error message if exists -->
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="edit_course.php?id=<?php echo $course_id; ?>" method="POST">
        <!-- Course Name Field -->
        <div class="form-group">
            <label for="course_name">Course Name:</label>
            <input type="text" class="form-control" name="course_name" value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
        </div>

        <!-- Level Dropdown -->
        <div class="form-group">
            <label for="level">Level:</label>
            <select name="level" id="level" class="form-control" required>
                <option value="">Select Level</option>
                <option value="100" <?php if ($course['level'] == '100') echo 'selected'; ?>>100</option>
                <option value="200" <?php if ($course['level'] == '200') echo 'selected'; ?>>200</option>
                <option value="300" <?php if ($course['level'] == '300') echo 'selected'; ?>>300</option>
                <option value="400" <?php if ($course['level'] == '400') echo 'selected'; ?>>400</option>
            </select>
        </div>

        <!-- Semester Dropdown -->
        <div class="form-group">
            <label for="semester">Semester:</label>
            <select name="semester" id="semester" class="form-control" required>
                <option value="">Select Semester</option>
                <option value="1" <?php if ($course['semester'] == '1') echo 'selected'; ?>>First Semester</option>
                <option value="2" <?php if ($course['semester'] == '2') echo 'selected'; ?>>Second Semester</option>
            </select>
        </div>

        <!-- Lecturer Dropdown -->
        <div class="form-group">
            <label for="lecturer">Assign Lecturer:</label>
            <select name="lecturer" id="lecturer" class="form-control" required>
                <option value="">Select Lecturer</option>
                <?php foreach ($lecturers as $lecturer) : ?>
                    <option value="<?php echo $lecturer['user_id']; ?>" <?php if ($lecturer['user_id'] == $course['lecturer_id']) echo 'selected'; ?>><?php echo htmlspecialchars($lecturer['full_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-success mt-3">Update Course</button>
    </form>
</div>

<?php
// Include footer
require_once('../includes/footer.php');
Footer::display();
?>
