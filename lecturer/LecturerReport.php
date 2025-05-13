<?php
class LecturerReport {
    private $db;
    private $conn;

    public function __construct($db) {
        $this->db = $db;
        $this->conn = $this->db->connect();
    }

    public function getAssignedCourses($lecturerId) {
        $query = "SELECT course_id, course_name FROM courses WHERE lecturer_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $lecturerId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function getAttendanceReport($courseId, $lecturerId) {
        $query = "
            SELECT 
                s.id AS student_id,
                s.fullname AS student_name,
                s.level AS student_level,
                c.course_name,
                c.course_code,
                e.attendance_percentage,
                e.eligibility_status
            FROM 
                students s
            JOIN 
                enrollments e ON s.id = e.student_id
            JOIN 
                courses c ON e.course_id = c.course_id
            WHERE 
                c.course_id = ? AND c.lecturer_id = ?
            ORDER BY 
                s.fullname
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $courseId, $lecturerId);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
