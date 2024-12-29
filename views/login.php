
<?php
session_start();
// Include database connection
require_once('../includes/db.php');

// Initialize error message variable
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $req_no = $_POST['req_no'];
    $password = $_POST['password'];

    // Check if inputs are empty
    if (empty($req_no) || empty($password)) {
        $error = 'Request number and password are required.';
    } else {
        // Fetch student data from database
        $query = "SELECT * FROM students WHERE req_no = ?";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(1, $req_no);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if ($student && password_verify($password, $student['password'])) {
            // Set session variables
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['full_name'] = $student['fullname'];

            // Redirect to student dashboard
            header('Location: ../student/dashboard.php');
            exit();
        } else {
            $error = 'Invalid request number or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
        }
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('../assets/logo/umyu.jpeg') no-repeat center center/cover;
            filter: blur(5px);
            z-index: -1;
        }
        .login-container {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
        }
        .card img {
            display: block;
            margin: 0 auto 20px;
        }
    </style>
</head>
<body>

<div class="background"></div>
    <div class="login-container">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Student Login</h3>
                    <img src="../assets/logo/12821518377_smalllogo.png" alt="Logo" style="width:100px; height:100px;">
    

        <!-- Display error message if exists -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Login Form -->
        <form action="login.php" method="POST">
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

            <!-- Login Button -->
            <button type="submit" class="btn btn-primary">Login</button>
        </form>

        <!-- Register Button -->
        <div class="mt-3">
            <p>Don't have an account? <a href="register.php" class="btn btn-secondary">Register Here</a></p>
        </div>
    </div>
</body>
</html>
