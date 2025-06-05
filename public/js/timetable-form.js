// Timetable Form Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Faculty-Department relationship handling
    const facultySelect = document.getElementById('faculty_id');
    const departmentSelect = document.getElementById('department_id');
    const courseSelect = document.getElementById('course_id');
    const levelSelect = document.getElementById('level');
    
    // Initialize faculty-department relationship
    if (facultySelect && departmentSelect) {
        console.log('Faculty and department selects found');

        facultySelect.addEventListener('change', function() {
            const facultyId = this.value;
            console.log('Faculty changed to:', facultyId);

            // Reset dependent dropdowns
            departmentSelect.innerHTML = '<option value="">Select Department</option>';
            if (courseSelect) courseSelect.innerHTML = '<option value="">Select Course</option>';

            if (!facultyId) {
                console.log('No faculty selected, returning');
                return;
            }

            // Show loading indicator
            departmentSelect.disabled = true;
            departmentSelect.innerHTML = '<option value="">Loading departments...</option>';

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            console.log('CSRF token:', csrfToken ? 'Found' : 'Not found');

            // Fetch departments for the selected faculty
            const url = `/lecturer/time-table/departments/${facultyId}`;
            console.log('Fetching from URL:', url);

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response received:', response);
                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                return response.text(); // Get as text first to see raw response
            })
            .then(text => {
                console.log('Raw response text:', text);

                // Try to parse as JSON
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('Failed to parse JSON:', e);
                    throw new Error('Invalid JSON response');
                }

                console.log('Parsed data:', data);
                console.log('Data type:', typeof data);
                console.log('Is array:', Array.isArray(data));

                // Reset and re-enable the select
                departmentSelect.innerHTML = '<option value="">Select Department</option>';
                departmentSelect.disabled = false;

                if (!data || !Array.isArray(data) || data.length === 0) {
                    departmentSelect.innerHTML = '<option value="">No departments found</option>';
                    console.log('No departments found for faculty:', facultyId);
                    return;
                }

                // Add the departments to the select
                data.forEach((department, index) => {
                    console.log(`Adding department ${index}:`, department);
                    console.log('Department ID:', department.id);
                    console.log('Department name:', department.name);

                    const option = document.createElement('option');
                    option.value = department.id;
                    option.textContent = department.name;
                    departmentSelect.appendChild(option);
                });

                console.log(`Successfully loaded ${data.length} departments`);
                console.log('Final department select HTML:', departmentSelect.innerHTML);

                // If there's only one department, select it automatically
                if (data.length === 1) {
                    departmentSelect.value = data[0].id;
                    // Trigger change event to load any dependent dropdowns
                    departmentSelect.dispatchEvent(new Event('change'));
                }
            })
            .catch(error => {
                console.error('Error loading departments:', error);
                departmentSelect.innerHTML = '<option value="">Error loading departments</option>';
                departmentSelect.disabled = false;

                // Show user-friendly error
                if (typeof Snackbar !== 'undefined') {
                    Snackbar.show({
                        text: 'Failed to load departments. Please try again.',
                        pos: 'bottom-center',
                        showAction: false,
                        actionText: "Dismiss",
                        duration: 3000,
                        textColor: '#fff',
                        backgroundColor: '#dc3545'
                    });
                } else {
                    alert('Failed to load departments. Please try again or contact support.');
                }
            });
        });

        // Trigger faculty change if a value is already selected (for edit forms)
        if (facultySelect.value) {
            console.log('Triggering faculty change for existing value:', facultySelect.value);
            facultySelect.dispatchEvent(new Event('change'));
        }
    } else {
        console.error('Faculty or department select not found');
    }

    // Department change event - load courses
    if (departmentSelect && courseSelect) {
        departmentSelect.addEventListener('change', function() {
            const departmentId = this.value;
            
            // Reset course dropdown
            courseSelect.innerHTML = '<option value="">Select Course</option>';
            
            if (!departmentId) return;

            // Show loading indicator
            courseSelect.disabled = true;
            courseSelect.innerHTML = '<option value="">Loading courses...</option>';

            // Get the selected level (if any)
            const level = levelSelect ? levelSelect.value : '';
            
            // Include level in the request if selected
            let url = `/lecturer/time-table/courses/${departmentId}`;
            if (level) {
                url += `?level=${level}`;
            }

            // Get the CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Reset and re-enable the select
                courseSelect.innerHTML = '<option value="">Select Course</option>';
                courseSelect.disabled = false;
            
                if (data.length === 0) {
                    courseSelect.innerHTML = '<option value="">No courses available</option>';
                    console.log('No courses found for this department/level');
                    return;
                }
                
                // Add the courses to the select
                data.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.id;
                    option.textContent = `${course.course_code} - ${course.course_title}`;
                    courseSelect.appendChild(option);
                });
                
                console.log(`Loaded ${data.length} courses`);
                
                // If there's only one course, select it automatically
                if (data.length === 1) {
                    courseSelect.value = data[0].id;
                }
            })
            .catch(error => {
                console.error('Error loading courses:', error);
                courseSelect.innerHTML = '<option value="">Error loading courses</option>';
                courseSelect.disabled = false;
                
                // Show error notification if available
                if (typeof Snackbar !== 'undefined') {
                    Snackbar.show({
                        text: 'Failed to load courses. Please try again.',
                        pos: 'bottom-center',
                        showAction: false,
                        actionText: "Dismiss",
                        duration: 3000,
                        textColor: '#fff',
                        backgroundColor: '#dc3545'
                    });
                }
            });
        });
    }

    // Level change event - reload courses if department is selected
    if (levelSelect) {
        levelSelect.addEventListener('change', function() {
            if (departmentSelect && departmentSelect.value) {
                // Trigger the department change event to reload courses
                departmentSelect.dispatchEvent(new Event('change'));
            }
        });
    }
    
    // Trigger faculty change if a value is already selected (for edit forms)
    if (facultySelect && facultySelect.value) {
        facultySelect.dispatchEvent(new Event('change'));
    }

    // Check for scheduling conflicts
    function checkConflict() {
        const dayOfWeek = document.getElementById('day_of_week')?.value;
        const startTime = document.getElementById('start_time')?.value;
        const endTime = document.getElementById('end_time')?.value;
        const venue = document.getElementById('venue')?.value;
        const effectiveDate = document.getElementById('effective_date')?.value;
        const timetableId = document.getElementById('timetable_id')?.value;

        if (!dayOfWeek || !startTime || !endTime) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            console.error('CSRF token not found');
            return;
        }
        
        const requestData = {
            day_of_week: dayOfWeek,
            start_time: startTime,
            end_time: endTime,
            venue: venue,
            effective_date: effectiveDate
        };
        
        // Add exclude_id if editing an existing timetable
        if (timetableId) {
            requestData.exclude_id = timetableId;
        }

        fetch('/lecturer/time-table/check-conflict', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(requestData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const conflictAlert = document.getElementById('conflict-alert');
            const conflictDetails = document.getElementById('conflict-details');
            
            if (data.has_conflict) {
                let detailsHtml = 'There is already a class scheduled at this time.';
                
                if (data.conflict_details) {
                    detailsHtml = `
                        <p class="mb-0">Course: ${data.conflict_details.course}</p>
                        <p class="mb-0">Lecturer: ${data.conflict_details.lecturer}</p>
                        <p class="mb-0">Time: ${data.conflict_details.time}</p>
                        <p class="mb-0">Venue: ${data.conflict_details.venue || 'Not specified'}</p>
                    `;
                }
                
                conflictDetails.innerHTML = detailsHtml;
                conflictAlert.classList.remove('d-none');
            } else {
                conflictAlert.classList.add('d-none');
            }
        })
        .catch(error => {
            console.error('Error checking conflicts:', error);
            
            // Show error notification if available
            if (typeof Snackbar !== 'undefined') {
                Snackbar.show({
                    text: 'Failed to check for scheduling conflicts.',
                    pos: 'bottom-center',
                    showAction: false,
                    actionText: "Dismiss",
                    duration: 3000,
                    textColor: '#fff',
                    backgroundColor: '#dc3545'
                });
            }
        });
    }

    // Add event listeners for conflict checking
    const dayOfWeekSelect = document.getElementById('day_of_week');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const venueInput = document.getElementById('venue');
    const effectiveDateInput = document.getElementById('effective_date');
    
    if (dayOfWeekSelect) dayOfWeekSelect.addEventListener('change', checkConflict);
    if (startTimeInput) startTimeInput.addEventListener('change', checkConflict);
    if (endTimeInput) endTimeInput.addEventListener('change', checkConflict);
    if (venueInput) venueInput.addEventListener('change', checkConflict);
    if (effectiveDateInput) effectiveDateInput.addEventListener('change', checkConflict);
    
    // Time validation
    if (startTimeInput && endTimeInput) {
        endTimeInput.addEventListener('change', function() {
            const startTime = startTimeInput.value;
            const endTime = this.value;
            if (startTime && endTime && startTime >= endTime) {
                alert('End time must be after start time');
                this.value = '';
            }
        });
    }
});

