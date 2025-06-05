// Logout notification handler
document.addEventListener('DOMContentLoaded', function() {
    // Check for logout message in URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const logoutMessage = urlParams.get('logout');
    
    if (logoutMessage === 'success') {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'You have been successfully logged out.',
            confirmButtonColor: '#3085d6'
        });
    }
    
    // Debug check for SweetAlert
    console.log('Logout notification script loaded');
    console.log('SweetAlert available:', typeof Swal !== 'undefined');
});