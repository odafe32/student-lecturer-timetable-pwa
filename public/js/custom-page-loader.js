// Combined Page Transition Loader
// This script implements both a horizontal progress bar and a dot animation loader
// While preventing Chrome's default loading indicator

// Track initialization to prevent duplicate event listeners
let isInitialized = false;

// Create the custom dot animation loader element
function createDotAnimationLoader() {
  // Check if loader already exists
  if (document.getElementById('pageLoader')) {
    return;
  }
  
  // Create the main loader container
  const loaderContainer = document.createElement('div');
  loaderContainer.id = 'pageLoader';
  loaderContainer.className = 'page-loader';
  
  // Create the loader animation element
  const loaderAnimation = document.createElement('div');
  loaderAnimation.className = 'loader-animation';
  
  // Create multiple animation dots for a nice effect
  for (let i = 0; i < 5; i++) {
    const dot = document.createElement('div');
    dot.className = 'loader-dot';
    loaderAnimation.appendChild(dot);
  }
  
  // Append animation to container
  loaderContainer.appendChild(loaderAnimation);
  
  // Add the container to the body
  document.body.appendChild(loaderContainer);
}

// Create the horizontal progress loader element
function createHorizontalProgressLoader() {
  // Check if loader already exists
  if (document.getElementById('pageProgressLoader')) {
    return;
  }
  
  // Create the progress bar container
  const loaderContainer = document.createElement('div');
  loaderContainer.id = 'pageProgressLoader';
  loaderContainer.className = 'page-progress-container';
  
  // Create the actual progress bar element
  const progressBar = document.createElement('div');
  progressBar.className = 'progress-bar';
  
  // Append progress bar to container
  loaderContainer.appendChild(progressBar);
  
  // Add the container to the body
  document.body.appendChild(loaderContainer);
}

// Add necessary styles for both loaders
function addLoaderStyles() {
  // Check if styles already exist
  if (document.getElementById('pageLoaderStyles')) {
    return;
  }
  
  const styleElement = document.createElement('style');
  styleElement.id = 'pageLoaderStyles';
  styleElement.textContent = `
    /* Dot Animation Loader Styles */
    .page-loader {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0, 0.7);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999999;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.2s ease, visibility 0.2s ease;
    }
    
    .page-loader.show {
      opacity: 1;
      visibility: visible;
    }
    
    .loader-animation {
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .loader-dot {
      width: 12px;
      height: 12px;
      margin: 0 5px;
      border-radius: 50%;
      background-color: #024ef3;
      animation: loader-bounce 1.4s infinite ease-in-out both;
    }
    
    .loader-dot:nth-child(1) {
      animation-delay: -0.32s;
    }
    
    .loader-dot:nth-child(2) {
      animation-delay: -0.24s;
    }
    
    .loader-dot:nth-child(3) {
      animation-delay: -0.16s;
    }
    
    .loader-dot:nth-child(4) {
      animation-delay: -0.08s;
    }
    
    @keyframes loader-bounce {
      0%, 80%, 100% {
        transform: scale(0);
      }
      40% {
        transform: scale(1);
      }
    }
    
    /* Horizontal Progress Bar Styles */
    .page-progress-container {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 6px;
      background-color: rgba(0, 0, 0, 0.1);
      z-index: 9999999;
      pointer-events: none;
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.2s ease, visibility 0.2s ease;
    }
    
    .page-progress-container.show {
      opacity: 1;
      visibility: visible;
    }
    
    .progress-bar {
      height: 100%;
      width: 0%;
      background: linear-gradient(to right, #024ef3, #3b82f6, #60a5fa);
      box-shadow: 0 0 10px rgba(2, 78, 243, 0.7);
      transition: width 0.1s ease;
      position: relative;
      overflow: hidden;
    }
    
    .progress-bar::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      bottom: 0;
      right: 0;
      background: linear-gradient(
        to right,
        rgba(255, 255, 255, 0.1),
        rgba(255, 255, 255, 0.2),
        rgba(255, 255, 255, 0.3),
        rgba(255, 255, 255, 0.1)
      );
      transform: translateX(-100%);
      animation: shimmer 1.5s infinite;
    }
    
    @keyframes shimmer {
      100% {
        transform: translateX(100%);
      }
    }
    
    /* Hidden iframe for AJAX navigation */
    .navigation-frame {
      display: none;
      width: 0;
      height: 0;
      border: 0;
    }
  `;
  
  document.head.appendChild(styleElement);
}

// Create a hidden iframe for AJAX navigation
function createNavigationFrame() {
  if (document.getElementById('navigationFrame')) {
    return;
  }
  
  const iframe = document.createElement('iframe');
  iframe.id = 'navigationFrame';
  iframe.className = 'navigation-frame';
  iframe.setAttribute('aria-hidden', 'true');
  iframe.tabIndex = -1;
  document.body.appendChild(iframe);
  
  return iframe;
}

// Store event listeners for cleanup
const eventListeners = {
  click: null,
  submit: null,
  popstate: null
};

// Initialize both loaders and intercept navigation
function initPageLoaders() {
  // Prevent duplicate initialization
  if (isInitialized) {
    return;
  }
  
  isInitialized = true;
  
  // Create both loader elements
  createDotAnimationLoader();
  createHorizontalProgressLoader();
  
  // Add styles for both loaders
  addLoaderStyles();
  
  // Create navigation frame for AJAX
  const navigationFrame = createNavigationFrame();
  
  // Get loader elements
  const dotLoader = document.getElementById('pageLoader');
  const progressContainer = document.getElementById('pageProgressLoader');
  const progressBar = progressContainer.querySelector('.progress-bar');
  
  let progressInterval;
  let currentProgress = 0;
  let navigationInProgress = false;
  let navigationTimeout;
  
  // Track active navigation requests to prevent duplicate loaders
  let activeNavigationRequests = 0;
  
  // Function to show dot loader
  function showDotLoader() {
    navigationInProgress = true;
    dotLoader.classList.add('show');
  }
  
  // Function to hide dot loader
  function hideDotLoader() {
    navigationInProgress = false;
    dotLoader.classList.remove('show');
  }
  
  // Function to animate progress
  function animateProgress() {
    // Clear any existing interval
    clearInterval(progressInterval);
    
    // Reset progress
    currentProgress = 0;
    progressBar.style.width = '0%';
    
    // Show the progress container
    progressContainer.classList.add('show');
    
    // Set up the animation
    progressInterval = setInterval(() => {
      // Calculate next progress increment
      // Progress gets slower as it approaches 90%
      let increment;
      
      if (currentProgress < 30) {
        increment = Math.random() * 10 + 5; // Fast at start (5-15%)
      } else if (currentProgress < 60) {
        increment = Math.random() * 5 + 2; // Medium speed (2-7%)
      } else if (currentProgress < 85) {
        increment = Math.random() * 2 + 0.5; // Slow (0.5-2.5%)
      } else {
        increment = 0.1; // Very slow near the end
      }
      
      // Ensure we don't exceed 90% (save the last 10% for actual completion)
      currentProgress = Math.min(currentProgress + increment, 90);
      progressBar.style.width = currentProgress + '%';
      
      // If we've reached 90%, stop the animation
      if (currentProgress >= 90) {
        clearInterval(progressInterval);
      }
    }, 200); // Update every 200ms
  }
  
  // Function to complete progress and hide
  function completeProgress() {
    clearInterval(progressInterval);
    
    // Quickly complete to 100%
    currentProgress = 100;
    progressBar.style.width = '100%';
    
    // Hide after a short delay
    setTimeout(() => {
      progressContainer.classList.remove('show');
      setTimeout(() => {
        progressBar.style.width = '0%';
      }, 300);
    }, 200);
  }
  
  // Function to show both loaders
  function showLoaders() {
    // Only show loaders if there are no active requests
    if (activeNavigationRequests === 0) {
      // Clear any existing timeout
      if (navigationTimeout) {
        clearTimeout(navigationTimeout);
      }
      
      showDotLoader();
      animateProgress();
      
      // Set a timeout to hide loaders if navigation doesn't happen
      navigationTimeout = setTimeout(() => {
        if (navigationInProgress) {
          hideLoaders();
        }
      }, 8000); // 8 seconds timeout
    }
    
    // Increment active requests counter
    activeNavigationRequests++;
  }
  
  // Function to hide both loaders
  function hideLoaders() {
    // Decrement active requests counter
    activeNavigationRequests = Math.max(0, activeNavigationRequests - 1);
    
    // Only hide loaders if there are no more active requests
    if (activeNavigationRequests === 0) {
      // Clear any existing timeout
      if (navigationTimeout) {
        clearTimeout(navigationTimeout);
        navigationTimeout = null;
      }
      
      hideDotLoader();
      completeProgress();
    }
  }
  
  // Function to check if a URL is likely to be a navigation request
  function isNavigationRequest(url) {
    if (!url || typeof url !== 'string') {
      return false;
    }
    
    // Skip API endpoints, static resources, and AJAX-specific endpoints
    const skipPatterns = [
      '/api/',
      '.json',
      '.xml',
      '.jpg', '.jpeg', '.png', '.gif', '.svg', '.webp',
      '.css', '.js',
      '.woff', '.woff2', '.ttf', '.eot',
      '.mp3', '.mp4', '.webm', '.ogg',
      '.pdf', '.doc', '.docx', '.xls', '.xlsx',
      'livewire/livewire.js', // Skip Livewire requests
      'livewire/update', // Skip Livewire updates
      '_debugbar', // Skip Laravel Debugbar requests
      'sanctum/csrf-cookie' // Skip Laravel Sanctum requests
    ];
    
    return !skipPatterns.some(pattern => url.includes(pattern));
  }
  
  // Function to handle link navigation without Chrome's loader
  function handleLinkNavigation(url) {
    showLoaders();
    
    // Use fetch to get the page content
    fetch(url, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'text/html',
        'X-Page-Navigation': 'true' // Custom header to identify navigation requests
      },
      credentials: 'same-origin'
    })
    .then(response => {
      if (!response.ok) {
        // If there's an error, just do a normal navigation
        window.location.href = url;
        return;
      }
      return response.text();
    })
    .then(html => {
      if (html) {
        try {
          // Push the new URL to history
          window.history.pushState({}, '', url);
          
          // Parse the HTML
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, 'text/html');
          
          // Update the document title
          document.title = doc.title;
          
          // Get the content to replace
          const newContent = doc.querySelector('body');
          if (newContent) {
            // Clean up event listeners before replacing content
            cleanupEventListeners();
            
            // Replace the content
            document.body.innerHTML = newContent.innerHTML;
            
            // Re-initialize the loaders
            isInitialized = false; // Reset initialization flag
            initPageLoaders();
            
            // Execute any scripts in the new content
            const scripts = Array.from(newContent.querySelectorAll('script'));
            scripts.forEach(script => {
              // Skip inline event handlers and loader script
              if (script.textContent.includes('custom-page-loader.js') || 
                  script.src.includes('custom-page-loader.js')) {
                return;
              }
              
              const newScript = document.createElement('script');
              Array.from(script.attributes).forEach(attr => {
                newScript.setAttribute(attr.name, attr.value);
              });
              newScript.textContent = script.textContent;
              document.body.appendChild(newScript);
            });
            
            // Scroll to top
            window.scrollTo(0, 0);
            
            // Dispatch a custom event
            window.dispatchEvent(new CustomEvent('navigationComplete', { detail: { url } }));
          } else {
            // If we couldn't find the content, do a normal navigation
            window.location.href = url;
          }
        } catch (error) {
          console.error('Error processing navigation:', error);
          window.location.href = url;
        }
      } else {
        // If there's no HTML, do a normal navigation
        window.location.href = url;
      }
      
      hideLoaders();
    })
    .catch(error => {
      console.error('Navigation error:', error);
      // If there's an error, do a normal navigation
      window.location.href = url;
      hideLoaders();
    });
  }
  
  // Function to handle form submission without Chrome's loader
  function handleFormSubmission(form) {
    showLoaders();
    
    // Get form data
    const formData = new FormData(form);
    const method = (form.method || 'GET').toUpperCase();
    const action = form.action || window.location.href;
    
    // For GET requests, build the URL with query parameters
    let url = action;
    if (method === 'GET') {
      const params = new URLSearchParams(formData).toString();
      url = action + (action.includes('?') ? '&' : '?') + params;
      
      // Use the link navigation handler for GET forms
      handleLinkNavigation(url);
      return;
    }
    
    // For POST and other methods, use fetch
    fetch(action, {
      method: method,
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'text/html',
        'X-Page-Navigation': 'true' // Custom header to identify navigation requests
      },
      credentials: 'same-origin'
    })
    .then(response => {
      if (!response.ok) {
        // If there's an error, submit the form normally
        form.submit();
        return;
      }
      return response.text();
    })
    .then(html => {
      if (html) {
        try {
          // Parse the HTML
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, 'text/html');
          
          // Update the document title
          document.title = doc.title;
          
          // Push the new URL to history
          window.history.pushState({}, '', response.url || action);
          
          // Get the content to replace
          const newContent = doc.querySelector('body');
          if (newContent) {
            // Clean up event listeners before replacing content
            cleanupEventListeners();
            
            // Replace the content
            document.body.innerHTML = newContent.innerHTML;
            
            // Re-initialize the loaders
            isInitialized = false; // Reset initialization flag
            initPageLoaders();
            
            // Execute any scripts in the new content
            const scripts = Array.from(newContent.querySelectorAll('script'));
            scripts.forEach(script => {
              // Skip inline event handlers and loader script
              if (script.textContent.includes('custom-page-loader.js') || 
                  script.src.includes('custom-page-loader.js')) {
                return;
              }
              
              const newScript = document.createElement('script');
              Array.from(script.attributes).forEach(attr => {
                newScript.setAttribute(attr.name, attr.value);
              });
              newScript.textContent = script.textContent;
              document.body.appendChild(newScript);
            });
            
            // Scroll to top
            window.scrollTo(0, 0);
            
            // Dispatch a custom event
            window.dispatchEvent(new CustomEvent('navigationComplete', { detail: { url: response.url || action } }));
          } else {
            // If we couldn't find the content, submit the form normally
            form.submit();
          }
        } catch (error) {
          console.error('Error processing form submission:', error);
          form.submit();
        }
      } else {
        // If there's no HTML, submit the form normally
        form.submit();
      }
      
      hideLoaders();
    })
    .catch(error => {
      console.error('Form submission error:', error);
      // If there's an error, submit the form normally
      form.submit();
      hideLoaders();
    });
  }
  
  // Function to clean up event listeners
  function cleanupEventListeners() {
    // Remove click event listener
    if (eventListeners.click) {
      document.removeEventListener('click', eventListeners.click, true);
    }
    
    // Remove submit event listener
    if (eventListeners.submit) {
      document.removeEventListener('submit', eventListeners.submit, true);
    }
    
    // Remove popstate event listener
    if (eventListeners.popstate) {
      window.removeEventListener('popstate', eventListeners.popstate);
    }
  }
  
  // Intercept all link clicks with capture phase to ensure it runs before other handlers
  eventListeners.click = function(event) {
    // Find closest anchor tag if the clicked element is inside one
    const link = event.target.closest('a');
    
    if (link && link.href) {
      // Skip if it's an external link, has a target attribute, or is a download
      if (
        link.getAttribute('target') === '_blank' ||
        link.getAttribute('download') !== null ||
        link.getAttribute('href').startsWith('#') ||
        link.getAttribute('href').startsWith('javascript:') ||
        link.getAttribute('href').startsWith('tel:') ||
        link.getAttribute('href').startsWith('mailto:') ||
        (link.origin !== window.location.origin) ||
        link.classList.contains('no-loader') || // Skip links with no-loader class
        link.hasAttribute('data-no-loader') // Skip links with data-no-loader attribute
      ) {
        return;
      }
      
      // Prevent default navigation to stop Chrome's loader
      event.preventDefault();
      
      // Handle navigation with our custom loader
      handleLinkNavigation(link.href);
    }
  };
  document.addEventListener('click', eventListeners.click, true);
  
  // Intercept form submissions
  eventListeners.submit = function(event) {
    const form = event.target;
    
    // Skip if the form has a target attribute set to _blank or has no-loader class
    if (
      form.getAttribute('target') === '_blank' ||
      form.classList.contains('no-loader') ||
      form.hasAttribute('data-no-loader')
    ) {
      return;
    }
    
    // Prevent default submission to stop Chrome's loader
    event.preventDefault();
    
    // Handle form submission with our custom loader
    handleFormSubmission(form);
  };
  document.addEventListener('submit', eventListeners.submit, true);
  
  // For single page applications that use History API
  const originalPushState = history.pushState;
  history.pushState = function() {
    showLoaders();
    const result = originalPushState.apply(this, arguments);
    
    // Hide loaders after a short delay to allow the new page content to load
    setTimeout(() => {
      hideLoaders();
    }, 500);
    
    return result;
  };
  
  // Handle popstate events (back/forward navigation)
  eventListeners.popstate = function(event) {
    // Show loaders
    showLoaders();
    
    // Get the current URL
    const currentUrl = window.location.href;
    
    // Handle navigation with our custom loader
    handleLinkNavigation(currentUrl);
  };
  window.addEventListener('popstate', eventListeners.popstate);
  
  // For Ajax requests with fetch API
  const originalFetch = window.fetch;
  window.fetch = function() {
    const request = arguments[0];
    const options = arguments[1] || {};
    
    // Extract URL from request
    const url = typeof request === 'string' ? request : request.url;
    
    // Only show loaders for GET requests that might be navigation
    // and don't have our custom navigation header (to avoid recursion)
    if (
      (!options.method || options.method === 'GET') && 
      (!options.headers || !options.headers['X-Page-Navigation']) &&
      isNavigationRequest(url)
    ) {
      showLoaders();
      
      // Add a custom property to track this request
      const originalPromise = originalFetch.apply(this, arguments);
      
      // Return a new promise that hides the loader when done
      return originalPromise.finally(function() {
        // Small delay to ensure content is rendered before hiding loaders
        setTimeout(() => {
          hideLoaders();
        }, 200);
      });
    }
    
    // For non-navigation requests, just pass through
    return originalFetch.apply(this, arguments);
  };
  
  // For XMLHttpRequest
  const originalOpen = XMLHttpRequest.prototype.open;
  XMLHttpRequest.prototype.open = function() {
    const method = arguments[0];
    const url = arguments[1];
    
    // Only show loaders for GET requests that might be navigation
    if (method.toLowerCase() === 'get' && isNavigationRequest(url)) {
      let isNavigationRequest = true;
      
      // Add loadstart and loadend event listeners
      this.addEventListener('loadstart', function() {
        if (isNavigationRequest) {
          showLoaders();
        }
      });
      
      this.addEventListener('loadend', function() {
        if (isNavigationRequest) {
          // Small delay to ensure content is rendered before hiding loaders
          setTimeout(() => {
            hideLoaders();
          }, 200);
        }
      });
      
      // Override setRequestHeader to detect if this is a navigation request
      const originalSetRequestHeader = this.setRequestHeader;
      this.setRequestHeader = function(name, value) {
        // If this is an AJAX request or has our navigation header, don't show loader
        if (
          (name.toLowerCase() === 'x-requested-with' && value === 'XMLHttpRequest') ||
          (name.toLowerCase() === 'x-page-navigation')
        ) {
          isNavigationRequest = false;
        }
        return originalSetRequestHeader.apply(this, arguments);
      };
    }
    
    originalOpen.apply(this, arguments);
  };
  
  // Hide loaders when page is fully loaded
  window.addEventListener('load', function() {
    hideLoaders();
    
    // Hide the initial preloader if it exists
    const initialPreloader = document.getElementById('preloader');
    if (initialPreloader) {
      initialPreloader.style.display = 'none';
    }
  });
  
  // Add global functions to manually control the loaders
  window.showPageLoaders = showLoaders;
  window.hidePageLoaders = hideLoaders;
  window.showDotLoader = showDotLoader;
  window.hideDotLoader = hideDotLoader;
  window.animateProgressBar = animateProgress;
  window.completeProgressBar = completeProgress;
}

// Run when the DOM is ready
document.addEventListener('DOMContentLoaded', function() {
  // Hide the initial preloader if it exists
  const initialPreloader = document.getElementById('preloader');
  if (initialPreloader) {
    initialPreloader.style.display = 'none';
  }
  
  initPageLoaders();
});

// Initialize immediately if the document is already loaded
if (document.readyState === 'complete' || document.readyState === 'interactive') {
  initPageLoaders();
}