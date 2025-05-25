/**
 * Login Form Validation with SweetAlert
 * This script handles client-side validation for the login form
 * and displays SweetAlert messages for errors and success
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get the login form
    const loginForm = document.querySelector('form[action*="login"]');
    
    // Password visibility toggle
    const passwordField = document.getElementById('password');
    const passwordVisibility = document.getElementById('password-visibility');
    
    if (passwordVisibility) {
        // Initially hide the eye-slash icon
       
    
    }
    
    // Form validation
    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Get form inputs
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            
            // Validate email
            if (!email) {
                showErrorAlert('Email is required');
                return;
            }
            
            if (!isValidEmail(email)) {
                showErrorAlert('Please enter a valid email address');
                return;
            }
            
            // Validate password
            if (!password) {
                showErrorAlert('Password is required');
                return;
            }
            
            // If validation passes, show loading state and submit the form
            Swal.fire({
                title: 'Logging in...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                    loginForm.submit();
                }
            });
        });
    }
    
    /**
     * Show error alert using SweetAlert
     * @param {string} message - The error message to display
     */
    function showErrorAlert(message) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: message,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    }
    
    /**
     * Validate email format
     * @param {string} email - The email to validate
     * @returns {boolean} - Whether the email is valid
     */
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    /**
     * Check for flash messages from the server and display them
     */
    function checkForFlashMessages() {
        // Check for success message in URL params (e.g., after redirect)
        const urlParams = new URLSearchParams(window.location.search);
        const successMessage = urlParams.get('success');
        const errorMessage = urlParams.get('error');
        
        if (successMessage) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: decodeURIComponent(successMessage),
                confirmButtonColor: '#3085d6'
            });
        } else if (errorMessage) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: decodeURIComponent(errorMessage),
                confirmButtonColor: '#3085d6'
            });
        }
        
        // Check for Laravel error messages
        const errorElements = document.querySelectorAll('.invalid-feedback');
        if (errorElements.length > 0) {
            // Get the first error message
            const firstError = errorElements[0].textContent.trim();
            if (firstError) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: firstError,
                    confirmButtonColor: '#3085d6'
                });
            }
        }
    }
    
    // Check for flash messages when the page loads
    checkForFlashMessages();
});
