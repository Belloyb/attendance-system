<?php
// models/Course.php

class Course {
    private $db;
    private $table = 'courses';

    public function __construct($db) {
        $this->db = $db;
    }

    // Method to get total courses
    public function getTotalCourses() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Method to get courses by lecturer
    public function getCoursesByLecturer($lecturer_id) {
        $query = "
            SELECT course_id, course_name
            FROM " . $this->table . "
            WHERE lecturer_id = :lecturer_id
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':lecturer_id', $lecturer_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all courses
    public function getAllCourses() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Add new course
    public function addCourse($courseName, $department) {
        $query = "INSERT INTO " . $this->table . " (course_name, department) VALUES (:courseName, :department)";
        $stmt = $this->db->prepare($query);

        // Bind data
        $stmt->bindParam(':courseName', $courseName);
        $stmt->bindParam(':department', $department);

        return $stmt->execute();
    }

    // Get single course by ID
    public function getCourseById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE course_id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update course
    public function updateCourse($id, $courseName, $department) {
        $query = "UPDATE " . $this->table . " SET course_name = :courseName, department = :department WHERE course_id = :id";
        $stmt = $this->db->prepare($query);

        // Bind data
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':courseName', $courseName);
        $stmt->bindParam(':department', $department);

        return $stmt->execute();
    }

    // Delete course
    public function deleteCourse($id) {
        $query = "DELETE FROM " . $this->table . " WHERE course_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}
