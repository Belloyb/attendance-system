<?php
session_start();
// admin/manage_users.php
require_once '../includes/db.php';
include '../includes/auth.php';  // Authentication check
include '../includes/header.php';  // Header
require '../models/User.php';      // User model

$role = $_SESSION['role'] ?? null;
pageHeader::display($role); // Display the header with role-based links

$userModel = new User();
$users = $userModel->getAllUsers();  // Fetch all users

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_user'])) {
        $userModel->addUser($_POST);  // Add user
    } elseif (isset($_POST['update_user'])) {
        $userModel->updateUser($_POST);  // Update user
    }
    header('Location: manage_user.php');
}
?>

<?php
require_once '../includes/db.php';

// Fetch all users using PDO
$query = "SELECT user_id, full_name, username, email, role FROM users";
$stmt = $conn->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <a href="add_user.php" class="btn btn-primary">Add New User</a> <!-- Ensure this is not duplicated -->
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php $counter = 1; ?>
    <?php foreach ($users as $user): ?>
        <tr>
            <!-- Display the counter instead of user_id -->
            <td><?php echo $counter++; ?></td>
            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['role']); ?></td>
            <td>
                <!-- Use user_id for editing and deleting actions -->
                <a href="edit_user.php?user_id=<?php echo $user['user_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="delete_user.php?user_id=<?php echo $user['user_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>
    </table>
</div>



<?php include '../includes/footer.php'; 

Footer::display(); // Assuming your footer has a display method
?>

