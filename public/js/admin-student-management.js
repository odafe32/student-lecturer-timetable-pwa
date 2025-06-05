/**
 * Student Management JavaScript
 * Handles dynamic functionality for student forms
 */

// Handle profile picture upload
function handleProfilePictureUpload() {
    document.getElementById('profilePicture').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) { // 2MB limit
                alert('File size must be less than 2MB');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const container = document.getElementById('profilePreviewContainer');
                container.innerHTML = '<img id="profilePreview" src="' + e.target.result +
                    '" alt="Profile Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">';
            };
            reader.readAsDataURL(file);
        }
    });
}

// Load departments based on selected faculty
function setupFacultyDepartmentRelationship(getDepartmentsUrl, currentDepartmentId = null) {
    document.getElementById('faculty').addEventListener('change', function() {
        const facultyId = this.value;
        const departmentSelect = document.getElementById('department');
        
        // Clear existing options
        departmentSelect.innerHTML = '<option value="">Select Department</option>';
        
        if (facultyId) {
            // Fetch departments via AJAX
            fetch(`${getDepartmentsUrl}?faculty_id=${facultyId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(dept => {
                        const option = document.createElement('option');
                        option.value = dept.id;
                        option.textContent = dept.name;
                        option.dataset.code = dept.code;
                        
                        // Select the current department if provided
                        if (currentDepartmentId && dept.id === currentDepartmentId) {
                            option.selected = true;
                        }
                        
                        departmentSelect.appendChild(option);
                    });
                    
                    // If there's a department value in the old input, select it
                    const oldDepartment = departmentSelect.getAttribute('data-old-value');
                    if (oldDepartment) {
                        departmentSelect.value = oldDepartment;
                    }
                    
                    // Trigger change event on department select to update any dependent fields
                    departmentSelect.dispatchEvent(new Event('change'));
                })
                .catch(error => {
                    console.error('Error fetching departments:', error);
                });
        }
    });
}


// Cancel form with confirmation
function cancelForm(redirectUrl) {
    if (confirm('Are you sure you want to cancel? All changes will be lost.')) {
        window.location.href = redirectUrl;
    }
}

// Initialize all student management functionality
function initStudentManagement(options = {}) {
    document.addEventListener('DOMContentLoaded', function() {
        // Setup profile picture upload
        handleProfilePictureUpload();
        
        // Setup faculty-department relationship if needed
        if (options.getDepartmentsUrl) {
            setupFacultyDepartmentRelationship(
                options.getDepartmentsUrl, 
                options.currentDepartmentId
            );
            
            // Trigger faculty change event on page load to populate departments
            const facultySelect = document.getElementById('faculty');
            if (facultySelect && facultySelect.value) {
                facultySelect.dispatchEvent(new Event('change'));
            }
        }
        
        // Setup student search if on the listing page
        setupStudentSearch();
    });
}