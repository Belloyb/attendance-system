<?php
// controllers/CourseController.php

require_once '../includes/db.php';
require_once '../models/Course.php';

class CourseController {
    private $course;

    public function __construct($db) {
        $this->course = new Course($db);
    }

    // Fetch all courses
    public function getCourses() {
        return $this->course->getAllCourses();
    }

    // Handle adding a new course
    public function addCourse($courseName, $department) {
        if (empty($courseName) || empty($department)) {
            return "Course name and department are required.";
        }

        $result = $this->course->addCourse($courseName, $department);
        if ($result) {
            return "Course added successfully!";
        } else {
            return "Failed to add course.";
        }
    }

    // Handle updating a course
    public function updateCourse($id, $courseName, $department) {
        if (empty($courseName) || empty($department)) {
            return "Course name and department are required.";
        }

        $result = $this->course->updateCourse($id, $courseName, $department);
        if ($result) {
            return "Course updated successfully!";
        } else {
            return "Failed to update course.";
        }
    }

    // Handle deleting a course
    public function deleteCourse($id) {
        $result = $this->course->deleteCourse($id);
        if ($result) {
            return "Course deleted successfully!";
        } else {
            return "Failed to delete course.";
        }
    }

    // Fetch a single course for editing
    public function getCourseById($id) {
        return $this->course->getCourseById($id);
    }
}
?>
