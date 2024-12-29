<?php
// Start the session


// Check if there's an error passed via the URL query string
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';

// Check if the user is already logged in and redirect based on their role
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header('Location: admin/dashboard.php');
            exit();
        case 'lecturer':
            header('Location: lecturer/dashboard.php');
            exit();
        case 'student':
            header('Location: student/dashboard.php');
            exit();
    }
}

include 'login_process.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="assets/logo/attnlg.jpg" rel="icon">
    <title>Login - Attendance System</title>
    <!-- Bootstrap CSS -->
    <link href="assets/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS for centering the login form -->
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
            background: url('assets/logo/umyu.jpeg') no-repeat center center/cover;
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
                    <h3 class="card-title text-center mb-4">Attendance System Login</h3>
                    <img src="assets/logo/12821518377_smalllogo.png" alt="Logo" style="width:100px; height:100px;">

                    <!-- Display error if available -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <!-- Login form -->
                    <form action="login_process.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="Enter your username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (Optional for interactivity) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>