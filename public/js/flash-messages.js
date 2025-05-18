// Flash Message Handler
document.addEventListener('DOMContentLoaded', function() {
    // Set up close buttons for alerts
    const closeButtons = document.querySelectorAll('.alert .btn-close');
    closeButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const alert = this.closest('.alert');
            fadeOut(alert);
        });
    });
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            fadeOut(alert);
        });
    }, 5000);
    
    // Helper function to fade out elements
    function fadeOut(element) {
        element.style.opacity = '1';
        
        (function fade() {
            if ((element.style.opacity -= 0.1) < 0) {
                element.style.display = 'none';
            } else {
                requestAnimationFrame(fade);
            }
        })();
    }
});