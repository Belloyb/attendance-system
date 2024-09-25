<?php
class pageHeader {
    public static function display($role = null) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Attendance System</title>
            <!-- Bootstrap CSS -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <!-- Custom CSS -->
            <link rel="stylesheet" href="../assets/css/styles.css">
        </head>
        <body>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="#">Attendance System</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <?php
                        // Display common links (for all roles)
                        ?>
                        

                        <?php
                        // Display role-specific links
                        if ($role === 'admin') {
                            ?>
                            <li class="nav-item">
                                <a class="nav-link" href="dashboard.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="../admin/manage_user.php">Manage Users</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="../admin/manage_courses.php">Manage Courses</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="../admin/report.php">Reports</a>
                            </li>
                            <?php
                        } elseif ($role === 'lecturer') {
                            ?>
                            <li class="nav-item">
                            <a class="nav-link" href="../lecturer/dashboard.php">Home</a>
                        </li>
                            <li class="nav-item">
                                <a class="nav-link" href="../lecturer/mark_attendance.php">Mark Attendance</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="../lecturer/view_students.php">View Students</a>
                            </li>
                            <?php
                        } elseif ($role === 'student') {
                            ?>
                            <li class="nav-item">
                            <a class="nav-link" href="../student/dashboard.php">Home</a>
                        </li>
                            <li class="nav-item">
                                <a class="nav-link" href="../student/view_attendance.php">View Attendance</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="../student/eligibility_status.php">Eligibility Status</a>
                            </li>
                            <?php
                        }
                        ?>

                        <?php
                        // Logout link (visible to all logged-in users)
                        if ($role !== null) {
                            ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../logout.php">Logout</a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </nav>
        <?php
    }
}
?>
