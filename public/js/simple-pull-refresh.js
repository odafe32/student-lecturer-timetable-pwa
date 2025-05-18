/**
 * Simple Pull to Refresh
 * A lightweight implementation that works across most browsers and devices
 */
(function() {
    // Track touch positions
    let startY = 0;
    let distance = 0;
    
    // DOM elements
    let refresher;
    let refreshText;
    let refreshIcon;
    
    // States
    let isPulling = false;
    let isRefreshing = false;
    
    // Configuration
    const pullThreshold = 70; // How far to pull before refresh triggers
    
    // Create the pull to refresh UI elements
    function createRefreshElements() {
        // Create container
        refresher = document.createElement('div');
        refresher.className = 'pull-refresher';
        refresher.style.cssText = `
            position: fixed;
            left: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 60px;
            top: -60px;
            transition: transform 0.2s;
            z-index: 9999;
            pointer-events: none;
        `;
        
        // Create refresh icon
        refreshIcon = document.createElement('div');
        refreshIcon.className = 'refresh-icon';
        refreshIcon.style.cssText = `
            width: 20px;
            height: 20px;
            border: 2px solid #32B768;
            border-top-color: transparent;
            border-radius: 50%;
            margin-right: 10px;
        `;
        
        // Create text element
        refreshText = document.createElement('div');
        refreshText.className = 'refresh-text';
        refreshText.textContent = 'Pull to refresh';
        refreshText.style.cssText = `
            color: #32B768;
            font-size: 14px;
            font-weight: 500;
        `;
        
        // Assemble elements
        refresher.appendChild(refreshIcon);
        refresher.appendChild(refreshText);
        document.body.appendChild(refresher);
        
        // Add animation styles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
            .refreshing .refresh-icon {
                animation: spin 1s infinite linear;
            }
        `;
        document.head.appendChild(style);
    }
    
    // Initialize event listeners
    function init() {
        createRefreshElements();
        
        // Touch events for mobile
        document.addEventListener('touchstart', onTouchStart, { passive: true });
        document.addEventListener('touchmove', onTouchMove, { passive: false });
        document.addEventListener('touchend', onTouchEnd, { passive: true });
    }
    
    // Handle touch start
    function onTouchStart(e) {
        // Only trigger when at top of page
        if (window.scrollY <= 0) {
            startY = e.touches[0].clientY;
            isPulling = true;
        }
    }
    
    // Handle touch move
    function onTouchMove(e) {
        if (!isPulling || isRefreshing) return;
        
        // Calculate pull distance
        const touchY = e.touches[0].clientY;
        distance = touchY - startY;
        
        // Only activate when pulling down
        if (distance > 5 && window.scrollY <= 0) {
            // Prevent default scrolling
            e.preventDefault();
            
            // Apply resistance to make pull feel natural
            const pullDistance = Math.min(distance / 2, pullThreshold * 1.5);
            
            // Update UI based on pull distance
            refresher.style.transform = `translateY(${pullDistance}px)`;
            
            // Update text based on threshold
            if (pullDistance >= pullThreshold) {
                refreshText.textContent = 'Release to refresh';
            } else {
                refreshText.textContent = 'Pull to refresh';
            }
            
            // Rotate icon based on pull progress
            const rotation = (pullDistance / pullThreshold) * 180;
            refreshIcon.style.transform = `rotate(${rotation}deg)`;
        }
    }
    
    // Handle touch end
    function onTouchEnd() {
        if (!isPulling || isRefreshing) return;
        isPulling = false;
        
        // If pulled past threshold, trigger refresh
        if (distance > pullThreshold && window.scrollY <= 0) {
            doRefresh();
        } else {
            // Reset UI
            refresher.style.transform = 'translateY(0)';
        }
        
        // Reset distance
        distance = 0;
    }
    
    // Perform the refresh
    function doRefresh() {
        isRefreshing = true;
        
        // Update UI to show refreshing state
        refresher.classList.add('refreshing');
        refreshText.textContent = 'Refreshing...';
        refresher.style.transform = 'translateY(60px)';
        
        // Reload the page after animation
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();