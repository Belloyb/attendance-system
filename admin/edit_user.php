<?php
session_start();
require_once '../includes/db.php';

if (isset($_GET['user_id'])) {
    $id = $_GET['user_id'];
    
    // Fetch user data using PDO
    $query = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user data is found
    if (!$user) {
        echo "User not found!";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $full_name = $_POST['full_name'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $role = $_POST['role'];

        // Update user details using PDO
        $updateQuery = "UPDATE users SET full_name = ?, username = ?, email = ?, role = ? WHERE user_id = ?";
        $updateStmt = $conn->prepare($updateQuery);

        if ($updateStmt->execute([$full_name, $username, $email, $role, $id])) {
            // Redirect back to manage users page after successful update
            header("Location: manage_user.php");
            exit();
        } else {
            echo "Error updating user.";
        }
    }
} else {
    echo "User ID not provided!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>

    <!-- Link to Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    
</head>

<!-- Edit User Form -->
<div class="container mt-5">
    <h2>Edit User</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="role">Role:</label>
            <select name="role" class="form-control" required>
                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                <option value="lecturer" <?php echo $user['role'] === 'lecturer' ? 'selected' : ''; ?>>Lecturer</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Update User</button>
    </form>
</div>


<!-- Include Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</html>