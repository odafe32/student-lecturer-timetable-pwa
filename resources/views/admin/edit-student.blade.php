@extends('layouts.admin')

@section('content')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('css/admin-create-student.css?v=' . env('CACHE_VERSION')) }}">



    <div class="edit-student-container">
        <!-- Header Section -->
        <div class="header-section">
            <h1><i class="fas fa-user-plus me-3"></i>Edit New Student</h1>
            <p>Add a new student to the system</p>
        </div>

        <!-- Form Section -->
        <div class="form-section">
            <form id="editStudentForm">
                <!-- Personal Information Section -->
                <div class="section-title">
                    <i class="fas fa-user me-2"></i>Personal Information
                </div>

                <div class="row form-row">
                    <div class="col-md-6">
                        <label for="fullName" class="form-label">Full Name <span class="required">*</span></label>
                        <input type="text" class="form-control" id="fullName" name="fullName" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address <span class="required">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                </div>

                <div class="row form-row">
                    <div class="col-md-12">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter full address"></textarea>
                    </div>
                </div>

                <!-- Profile Picture Section -->
                <div class="section-title">
                    <i class="fas fa-camera me-2"></i>Profile Picture
                </div>

                <div class="form-row">
                    <div class="profile-upload" onclick="document.getElementById('profilePicture').click()">
                        <div class="profile-preview" id="profilePreviewContainer">
                            <i class="fas fa-camera"></i>
                            <img id="profilePreview" alt="Profile Preview">
                        </div>
                        <div class="profile-upload-text">
                            <strong>Click to upload profile picture</strong><br>
                            <small>JPG, PNG or GIF (Max 2MB)</small>
                        </div>
                        <input type="file" id="profilePicture" name="profilePicture" class="file-input" accept="image/*">
                    </div>
                </div>

                <!-- Academic Information Section -->
                <div class="section-title">
                    <i class="fas fa-graduation-cap me-2"></i>Academic Information
                </div>

                <div class="row form-row">
                    <div class="col-md-6">
                        <label for="faculty" class="form-label">Faculty <span class="required">*</span></label>
                        <select class="form-select" id="faculty" name="faculty" required onchange="updateDepartments()">
                            <option value="">Select Faculty</option>
                            <option value="engineering">Faculty of Engineering</option>
                            <option value="science">Faculty of Science</option>
                            <option value="arts">Faculty of Arts</option>
                            <option value="social_sciences">Faculty of Social Sciences</option>
                            <option value="medicine">Faculty of Medicine</option>
                            <option value="law">Faculty of Law</option>
                            <option value="agriculture">Faculty of Agriculture</option>
                            <option value="education">Faculty of Education</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="department" class="form-label">Department <span class="required">*</span></label>
                        <select class="form-select" id="department" name="department" required
                            onchange="generateMatricNumber()">
                            <option value="">Select Department</option>
                        </select>
                    </div>
                </div>

                <div class="row form-row">
                    <div class="col-md-6">
                        <label for="level" class="form-label">Level <span class="required">*</span></label>
                        <select class="form-select" id="level" name="level" required>
                            <option value="">Select Level</option>
                            <option value="100">100 Level</option>
                            <option value="200">200 Level</option>
                            <option value="300">300 Level</option>
                            <option value="400">400 Level</option>
                            < </select>
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status <span class="required">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                            <option value="graduated">Graduated</option>
                        </select>
                    </div>
                </div>

                <div class="row form-row">
                    <div class="col-md-6">
                        <label for="matricNumber" class="form-label">Matric Number <span
                                class="required">*</span></label>
                        <input type="text" class="form-control" id="matricNumber" name="matricNumber" readonly>
                        <div class="matric-preview" id="matricPreview">Auto-generated based on department and year</div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="row form-row mt-4">
                    <div class="col-12 text-end">
                        <button type="button" class="btn btn-cancel me-3" onclick="cancelForm()">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-submit text-white">
                            <i class="fas fa-save me-2"></i>Update Student
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Department mappings for each faculty
        const departmentMappings = {
            engineering: [{
                    value: 'computer_engineering',
                    text: 'Computer Engineering',
                    code: 'CPE'
                },
                {
                    value: 'electrical_engineering',
                    text: 'Electrical Engineering',
                    code: 'EEE'
                },
                {
                    value: 'mechanical_engineering',
                    text: 'Mechanical Engineering',
                    code: 'MEE'
                },
                {
                    value: 'civil_engineering',
                    text: 'Civil Engineering',
                    code: 'CVE'
                },
                {
                    value: 'chemical_engineering',
                    text: 'Chemical Engineering',
                    code: 'CHE'
                }
            ],
            science: [{
                    value: 'computer_science',
                    text: 'Computer Science',
                    code: 'CS'
                },
                {
                    value: 'mathematics',
                    text: 'Mathematics',
                    code: 'MAT'
                },
                {
                    value: 'physics',
                    text: 'Physics',
                    code: 'PHY'
                },
                {
                    value: 'chemistry',
                    text: 'Chemistry',
                    code: 'CHM'
                },
                {
                    value: 'biology',
                    text: 'Biology',
                    code: 'BIO'
                },
                {
                    value: 'statistics',
                    text: 'Statistics',
                    code: 'STA'
                }
            ],
            arts: [{
                    value: 'english',
                    text: 'English Language',
                    code: 'ENG'
                },
                {
                    value: 'history',
                    text: 'History',
                    code: 'HIS'
                },
                {
                    value: 'philosophy',
                    text: 'Philosophy',
                    code: 'PHI'
                },
                {
                    value: 'fine_arts',
                    text: 'Fine Arts',
                    code: 'ART'
                },
                {
                    value: 'music',
                    text: 'Music',
                    code: 'MUS'
                }
            ],
            social_sciences: [{
                    value: 'economics',
                    text: 'Economics',
                    code: 'ECO'
                },
                {
                    value: 'political_science',
                    text: 'Political Science',
                    code: 'POL'
                },
                {
                    value: 'sociology',
                    text: 'Sociology',
                    code: 'SOC'
                },
                {
                    value: 'psychology',
                    text: 'Psychology',
                    code: 'PSY'
                },
                {
                    value: 'geography',
                    text: 'Geography',
                    code: 'GEO'
                }
            ],
            medicine: [{
                    value: 'medicine_surgery',
                    text: 'Medicine & Surgery',
                    code: 'MED'
                },
                {
                    value: 'nursing',
                    text: 'Nursing',
                    code: 'NUR'
                },
                {
                    value: 'pharmacy',
                    text: 'Pharmacy',
                    code: 'PHM'
                },
                {
                    value: 'dentistry',
                    text: 'Dentistry',
                    code: 'DEN'
                }
            ],
            law: [{
                    value: 'common_law',
                    text: 'Common Law',
                    code: 'LAW'
                },
                {
                    value: 'islamic_law',
                    text: 'Islamic Law',
                    code: 'ISL'
                }
            ],
            agriculture: [{
                    value: 'crop_production',
                    text: 'Crop Production',
                    code: 'CRP'
                },
                {
                    value: 'animal_science',
                    text: 'Animal Science',
                    code: 'ANS'
                },
                {
                    value: 'soil_science',
                    text: 'Soil Science',
                    code: 'SOS'
                },
                {
                    value: 'agricultural_economics',
                    text: 'Agricultural Economics',
                    code: 'AGE'
                }
            ],
            education: [{
                    value: 'educational_admin',
                    text: 'Educational Administration',
                    code: 'EDA'
                },
                {
                    value: 'curriculum_instruction',
                    text: 'Curriculum & Instruction',
                    code: 'CIN'
                },
                {
                    value: 'guidance_counseling',
                    text: 'Guidance & Counseling',
                    code: 'GCO'
                }
            ]
        };

        // Update departments based on selected faculty
        function updateDepartments() {
            const facultySelect = document.getElementById('faculty');
            const departmentSelect = document.getElementById('department');
            const selectedFaculty = facultySelect.value;

            // Clear existing options
            departmentSelect.innerHTML = '<option value="">Select Department</option>';

            if (selectedFaculty && departmentMappings[selectedFaculty]) {
                departmentMappings[selectedFaculty].forEach(dept => {
                    const option = document.editElement('option');
                    option.value = dept.value;
                    option.textContent = dept.text;
                    option.dataset.code = dept.code;
                    departmentSelect.appendChild(option);
                });
            }

            // Clear matric number when faculty changes
            document.getElementById('matricNumber').value = '';
        }

        // Generate matric number based on department
        function generateMatricNumber() {
            const departmentSelect = document.getElementById('department');
            const matricNumberInput = document.getElementById('matricNumber');
            const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];

            if (selectedOption && selectedOption.dataset.code) {
                const deptCode = selectedOption.dataset.code;
                const currentYear = new Date().getFullYear();
                const randomNum = Math.floor(Math.random() * 900) + 100; // 3-digit random number
                const matricNumber = `${deptCode}/${currentYear}/${randomNum}`;
                matricNumberInput.value = matricNumber;
            }
        }

        // Handle profile picture upload
        document.getElementById('profilePicture').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) { // 2MB limit
                    alert('File size must be less than 2MB');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('profilePreview');
                    const container = document.getElementById('profilePreviewContainer');

                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    container.innerHTML = '<img id="profilePreview" src="' + e.target.result +
                        '" alt="Profile Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">';
                };
                reader.readAsDataURL(file);
            }
        });

        // Handle form submission
        document.getElementById('editStudentForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Get form data
            const formData = new FormData(this);
            const studentData = Object.fromEntries(formData.entries());

            // Basic validation
            const requiredFields = ['fullName', 'email', 'faculty', 'department', 'level', 'status'];
            const missingFields = requiredFields.filter(field => !studentData[field]);

            if (missingFields.length > 0) {
                alert('Please fill in all required fields: ' + missingFields.join(', '));
                return;
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(studentData.email)) {
                alert('Please enter a valid email address');
                return;
            }

            // Here you would typically send the data to your server
            console.log('Student Data:', studentData);

            // Show success message
            alert('Student created successfully!');

            // Optionally redirect or reset form
            // window.location.href = '/students';
        });

        // Cancel form
        function cancelForm() {
            if (confirm('Are you sure you want to cancel? All entered data will be lost.')) {
                // window.history.back();
                document.getElementById('editStudentForm').reset();
                document.getElementById('department').innerHTML = '<option value="">Select Department</option>';
                document.getElementById('matricNumber').value = '';
                document.getElementById('profilePreviewContainer').innerHTML = '<i class="fas fa-camera"></i>';
            }
        }

        // Initialize form
        document.addEventListener('DOMContentLoaded', function() {
            // Any initialization code can go here
        });
    </script>
@endsection
