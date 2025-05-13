
<?php
session_start();
// Include database connection
require_once('../includes/db.php');

// Initialize error and success message
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $req_no = $_POST['req_no'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $level = $_POST['level'];

    // Validate input
    if (empty($fullname) || empty($req_no) || empty($password) || empty($level)) {
        $error = 'All fields are required.';
    } else {
        // Insert new student into the database
        $query = "INSERT INTO students (fullname, req_no, password, level) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $fullname);
        $stmt->bindParam(2, $req_no);
        $stmt->bindParam(3, $password);
        $stmt->bindParam(4, $level);

        if ($stmt->execute()) {
            $success = 'Registration successful! You can now log in.';
        } else {
            $error = 'Error in registration. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css"> <!-- Add your CSS file here -->
</head>
<body>
    <div class="container">
        <h2 class="text-center">Student Registration</h2>

        <!-- Display error or success message -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <!-- Registration Form -->
        <form action="register.php" method="POST">
            <!-- Full Name -->
            <div class="form-group">
                <label for="fullname">Full Name:</label>
                <input type="text" name="fullname" class="form-control" required>
            </div>

            <!-- Request Number -->
            <div class="form-group">
                <label for="req_no">Registration Number:</label>
                <input type="text" name="req_no" class="form-control" required>
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <!-- Level -->
            <div class="form-group">
                <label for="level">Level:</label>
                <select name="level" class="form-control" required>
                    <option value="">Select Level</option>
                    <option value="100">100</option>
                    <option value="200">200</option>
                    <option value="300">300</option>
                    <option value="400">400</option>
                </select>
            </div>

            <!-- Register Button -->
            <button type="submit" class="btn btn-success">Register</button>
        </form>

        <!-- Back to Login -->
        <div class="mt-3">
            <p>Already have an account? <a href="login.php" class="btn btn-secondary">Login Here</a></p>
        </div>
    </div>
</body>
</html>
