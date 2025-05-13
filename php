SELECT 
    students.fullname, 
    (SUM(CASE WHEN attendance.status = 'present' THEN 1 ELSE 0 END) / COUNT(attendance.id)) * 100 AS attendance_percentage,
    CASE 
        WHEN (SUM(CASE WHEN attendance.status = 'present' THEN 1 ELSE 0 END) / COUNT(attendance.id)) * 100 >= 75 THEN 'Eligible'
        ELSE 'Not Eligible'
    END AS eligibility_status
FROM attendance
JOIN students ON attendance.student_id = students.id
WHERE attendance.course_id = :course_id
GROUP BY students.id
ORDER BY students.fullname;
