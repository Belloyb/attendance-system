document.addEventListener('DOMContentLoaded', function() {
    const levelSelect = document.querySelector('select[name="level"]');
    const semesterSelect = document.querySelector('select[name="semester"]');
    const courseSelect = document.querySelector('select[name="course"]');

    // When the level or semester changes, make an AJAX request
    levelSelect.addEventListener('change', loadCourses);
    semesterSelect.addEventListener('change', loadCourses);

    function loadCourses() {
        const level = levelSelect.value;
        const semester = semesterSelect.value;

        // Only proceed if both level and semester have a valid value
        if (level && semester) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'get_courses.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            // Send the AJAX request
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Parse the returned JSON data
                    const courses = JSON.parse(xhr.responseText);

                    // Clear the current options in the course select
                    courseSelect.innerHTML = '';

                    // Add new options to the course select based on the response
                    courses.forEach(function(course) {
                        const option = document.createElement('option');
                        option.value = course.id;
                        option.textContent = course.course_name;
                        courseSelect.appendChild(option);
                    });
                } else {
                    console.error('Error loading courses');
                }
            };

            xhr.send(`level=${level}&semester=${semester}`);
        }
    }
});
