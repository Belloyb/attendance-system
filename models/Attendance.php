<?php
class Attendance {
    private $conn;

    public function __construct() {
        // Database connection using MySQLi
        $this->conn = new mysqli('localhost', 'root', '', 'attendance_db');

        // Check for a connection error
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    // Method to get total attendance records
    public function getTotalAttendanceRecords() {
        // SQL query to count attendance records
        $sql = "SELECT COUNT(*) AS total FROM attendance_records";
        $result = $this->conn->query($sql);

        // Check if the query was successful
        if (!$result) {
            die("Query Failed: " . $this->conn->error);
        }

        $row = $result->fetch_assoc();  // Fetch result
        return $row['total'];  // Return the total count of attendance records
    }

    // Method to get total attendance records for a specific lecturer
    public function getTotalAttendanceForLecturer($lecturer_id) {
        // SQL query to get total attendance for lecturer's courses
        $sql = "
            SELECT COUNT(*) as total
            FROM attendance a
            JOIN courses c ON a.course_id = c.course_id
            WHERE c.lecturer_id = ?
        ";

        // Prepare the SQL statement
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $lecturer_id);  // Bind the lecturer ID as an integer
        $stmt->execute();
        $result = $stmt->get_result();  // Get the result set
        $row = $result->fetch_assoc();  // Fetch the result

        return $row['total'];  // Return the total attendance count
    }
}
