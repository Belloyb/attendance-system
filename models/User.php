<?php
class User {
    private $conn;

    public function __construct() {
        // Database connection
        $this->conn = new mysqli('localhost', 'root', '', 'attendance_db');
    }

    public function getAllUsers() {
        $result = $this->conn->query("SELECT * FROM users");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTotalUsers() {
        $result = $this->conn->query("SELECT COUNT(*) AS total FROM users");
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function addUser($data) {
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, role) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $data['username'], $data['email'], $data['role']);
        $stmt->execute();
    }

    public function updateUser($data) {
        $stmt = $this->conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
        $stmt->bind_param('sssi', $data['username'], $data['email'], $data['role'], $data['id']);
        $stmt->execute();
    }
}

