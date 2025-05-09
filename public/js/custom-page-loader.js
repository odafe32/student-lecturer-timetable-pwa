// Combined Page Transition Loader
// This script implements both a horizontal progress bar and a dot animation loader

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
    
    /* Hide browser's built-in loading indicator */
    html.nprogress-busy body {
      cursor: default !important;
    }
    
    html.nprogress-busy #nprogress {
      display: none !important;
    }
    
    /* Hide Chrome's loading spinner in the tab */
    @keyframes disableAnimation {
      0% { opacity: 0; }
      100% { opacity: 0; }
    }
    
    /* This targets Chrome's throbber specifically */
    html:not([data-preloader]) .preloader-container,
    html:not([data-preloader]) .preloader-icon,
    html:not([data-preloader]) .spinner-container,
    html:not([data-preloader]) .spinner {
      display: none !important;
      animation: disableAnimation 0.001s infinite !important;
      opacity: 0 !important;
      z-index: -9999 !important;
      pointer-events: none !important;
    }
  `;
  
  document.head.appendChild(styleElement);
}

// Function to disable browser's built-in loading indicator
function disableBrowserLoadingIndicator() {
   const metaTags = [
    { name: 'theme-color', content: '#ffffff' },
    { name: 'mobile-web-app-capable', content: 'yes' },
    { name: 'apple-mobile-web-app-capable', content: 'yes' }
  ];
  
  metaTags.forEach(tag => {
    const meta = document.createElement('meta');
    meta.name = tag.name;
    meta.content = tag.content;
    document.head.appendChild(meta);
  });
  
  // Add class to HTML element
  document.documentElement.classList.add('custom-loader-active');
  
  // Use CSS to hide Chrome's throbber (spinner in tab)
  const style = document.createElement('style');
  style.textContent = `
    @keyframes disableAnimation {
      0% { opacity: 0; }
      100% { opacity: 0; }
    }
    
    /* Target Chrome's throbber in the tab */
    html.custom-loader-active::-webkit-progress-bar,
    html.custom-loader-active::-webkit-progress-value,
    html.custom-loader-active::-webkit-progress {
      display: none !important;
      opacity: 0 !important;
      animation: disableAnimation 0.001s infinite !important;
    }
  `;
  document.head.appendChild(style);
  // Add a meta tag to help disable Chrome's loading indicator
  const meta = document.createElement('meta');
  meta.name = 'theme-color';
  meta.content = '#ffffff';
  document.head.appendChild(meta);
  
  // Add a class to the HTML element
  document.documentElement.classList.add('custom-loader-active');
  
  // Create a hidden iframe to intercept navigation and prevent browser loading indicator
  const iframe = document.createElement('iframe');
  iframe.id = 'navigation-interceptor';
  iframe.style.display = 'none';
  iframe.setAttribute('aria-hidden', 'true');
  document.body.appendChild(iframe);
  
  // Disable browser's default behavior for links
  document.addEventListener('click', function(event) {
    const link = event.target.closest('a');
    if (link && link.href && !link.getAttribute('target') && link.origin === window.location.origin) {
      // Prevent default only for internal links
      if (!link.getAttribute('href').startsWith('#') && 
          !link.getAttribute('href').startsWith('javascript:') &&
          !link.getAttribute('href').startsWith('tel:') &&
          !link.getAttribute('href').startsWith('mailto:')) {
        event.preventDefault();
      }
    }
  }, true);
}

// Initialize both loaders and intercept navigation
function initPageLoaders() {
  // Create both loader elements
  createDotAnimationLoader();
  createHorizontalProgressLoader();
  
  // Add styles for both loaders
  addLoaderStyles();
  
  // Disable browser's built-in loading indicator
  disableBrowserLoadingIndicator();
  
  // Get loader elements
  const dotLoader = document.getElementById('pageLoader');
  const progressContainer = document.getElementById('pageProgressLoader');
  const progressBar = progressContainer.querySelector('.progress-bar');
  
  let progressInterval;
  let currentProgress = 0;
  let navigationInProgress = false;
  let loaderTimeoutId = null; // Track the timeout ID
  let pendingRequests = 0; // Track number of pending AJAX requests
  let isUserInitiatedNavigation = false; // Flag for user-initiated navigation
  let isPageReloading = false; // Flag for page reload
  
  // Function to show dot loader
  function showDotLoader() {
    navigationInProgress = true;
    dotLoader.classList.add('show');
  }
  
  // Function to hide dot loader
  function hideDotLoader() {
    navigationInProgress = false;
    dotLoader.classList.remove('show');
    
    // Clear the timeout if it exists
    if (loaderTimeoutId) {
      clearTimeout(loaderTimeoutId);
      loaderTimeoutId = null;
    }
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
    // If loaders are already shown, don't start a new timeout
    if (navigationInProgress) {
      return;
    }
    
    showDotLoader();
    animateProgress();
    
    // Clear any existing timeout
    if (loaderTimeoutId) {
      clearTimeout(loaderTimeoutId);
    }
    
    // Set a timeout to hide loaders if navigation doesn't happen
    loaderTimeoutId = setTimeout(() => {
      if (navigationInProgress) {
        hideLoaders();
      }
      loaderTimeoutId = null;
    }, 5000); // 5 seconds timeout (reduced from 8)
  }
  
  // Function to hide both loaders
  function hideLoaders() {
    // Only hide if there are no pending requests and page is not reloading
    if (pendingRequests <= 0 && !isPageReloading) {
      hideDotLoader();
      completeProgress();
      pendingRequests = 0; // Reset counter to ensure it doesn't go negative
    }
  }
  
  // Helper function to check if a URL is likely to be a navigation request
  function isNavigationRequest(url) {
    if (!url || typeof url !== 'string') return false;
    
    // Exclude common resource types and API endpoints
    const nonNavigationPatterns = [
      '/api/',
      '.json',
      '.xml',
      '.js',
      '.css',
      '.png',
      '.jpg',
      '.jpeg',
      '.gif',
      '.svg',
      '.woff',
      '.woff2',
      '.ttf',
      '.eot',
      '/socket.io/',
      'livewire'
    ];
    
    return !nonNavigationPatterns.some(pattern => url.includes(pattern));
  }
  
  // Function to handle navigation to a new URL
  function navigateTo(url) {
    // Show loaders
    showLoaders();
    
    // Set a timeout to actually navigate
    setTimeout(() => {
      window.location.href = url;
    }, 50); // Small delay to ensure loader is visible before navigation
  }
  
  // Function to handle page reload
  function reloadPage() {
    isPageReloading = true;
    showLoaders();
    
    // Set a timeout to actually reload
    setTimeout(() => {
      window.location.reload();
    }, 50); // Small delay to ensure loader is visible before reload
  }
  
  // Intercept all link clicks with capture phase to ensure it runs before other handlers
  document.addEventListener('click', function(event) {
    // Find closest anchor tag if the clicked element is inside one
    const link = event.target.closest('a');
    
    if (link && link.href) {
      // Check if it's a reload link (same URL as current page)
      if (link.href === window.location.href) {
        event.preventDefault();
        reloadPage();
        return;
      }
      
      // Skip if it's an external link, has a target attribute, or is a download
      if (
        link.getAttribute('target') === '_blank' ||
        link.getAttribute('download') !== null ||
        link.getAttribute('href').startsWith('#') ||
        link.getAttribute('href').startsWith('javascript:') ||
        link.getAttribute('href').startsWith('tel:') ||
        link.getAttribute('href').startsWith('mailto:') ||
        (link.origin !== window.location.origin)
      ) {
        return;
      }
      
      // Prevent default browser navigation
      event.preventDefault();
      
      // Mark as user-initiated navigation
      isUserInitiatedNavigation = true;
      
      // Show loaders before navigation
      showLoaders();
      
      // Navigate to the URL after a small delay
      setTimeout(() => {
        window.location.href = link.href;
      }, 50);
      
      // Reset the flag after a short delay
      setTimeout(() => {
        isUserInitiatedNavigation = false;
      }, 500);
    }
  }, true); // Use capture phase
  
  // Intercept form submissions
  document.addEventListener('submit', function(event) {
    const form = event.target;
    
    // Skip if the form has a target attribute set to _blank
    if (form.getAttribute('target') === '_blank') {
      return;
    }
    
    // Prevent default form submission
    event.preventDefault();
    
    // Mark as user-initiated navigation
    isUserInitiatedNavigation = true;
    
    // Show loaders before form submission
    showLoaders();
    
    // Submit the form programmatically after a small delay
    setTimeout(() => {
      // Create a hidden input for the submit button if it was clicked
      if (event.submitter && event.submitter.name) {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = event.submitter.name;
        hiddenInput.value = event.submitter.value || '';
        form.appendChild(hiddenInput);
      }
      
      form.submit();
    }, 50);
    
    // Reset the flag after a short delay
    setTimeout(() => {
      isUserInitiatedNavigation = false;
    }, 500);
  }, true); // Use capture phase
  
  // Add event listeners for page load/unload events
  window.addEventListener('beforeunload', function() {
    // Always show loaders on page unload, regardless of how it was triggered
    isPageReloading = true;
    showLoaders();
    
    // Make sure the initial preloader is visible
    const initialPreloader = document.getElementById('preloader');
    if (initialPreloader) {
      initialPreloader.style.display = 'block';
    }
  });
  
  // Detect page reload using the performance API
  if (window.performance && performance.navigation) {
    if (performance.navigation.type === 1) { // 1 is TYPE_RELOAD
      isPageReloading = true;
      showLoaders();
      
      // Make sure the initial preloader is visible
      const initialPreloader = document.getElementById('preloader');
      if (initialPreloader) {
        initialPreloader.style.display = 'block';
      }
    }
  }
  
  // Alternative reload detection for newer browsers
  if (window.performance && performance.getEntriesByType) {
    const navEntries = performance.getEntriesByType('navigation');
    if (navEntries.length > 0 && navEntries[0].type === 'reload') {
      isPageReloading = true;
      showLoaders();
      
      // Make sure the initial preloader is visible
      const initialPreloader = document.getElementById('preloader');
      if (initialPreloader) {
        initialPreloader.style.display = 'block';
      }
    }
  }
  
  window.addEventListener('pageshow', function(event) {
    // Only hide loaders if not reloading
    if (!isPageReloading) {
      hideLoaders();
    }
    
    // For back-forward cache
    if (event.persisted) {
      isPageReloading = false;
      hideLoaders();
    }
    
    // Don't hide the initial preloader if we're reloading
    if (!isPageReloading) {
      const initialPreloader = document.getElementById('preloader');
      if (initialPreloader) {
        initialPreloader.style.display = 'none';
      }
    }
  });
  
  // For single page applications that use History API
  const originalPushState = history.pushState;
  history.pushState = function() {
    // Only show loaders for user-initiated navigation
    if (isUserInitiatedNavigation) {
      showLoaders();
    }
    
    const result = originalPushState.apply(this, arguments);
    
    // Hide loaders after a short delay to allow the new page content to load
    setTimeout(() => {
      hideLoaders();
    }, 500);
    
    return result;
  };
  
  window.addEventListener('popstate', function() {
    // Mark as user-initiated navigation
    isUserInitiatedNavigation = true;
    
    showLoaders();
    
    // Hide loaders after a short delay to allow the new page content to load
    setTimeout(() => {
      hideLoaders();
      
      // Reset the flag
      isUserInitiatedNavigation = false;
    }, 500);
  });
  
  // Intercept F5 key and browser reload button
  window.addEventListener('keydown', function(event) {
    // F5 key or Ctrl+R
    if (event.key === 'F5' || (event.ctrlKey && event.key === 'r')) {
      isPageReloading = true;
      showLoaders();
      
      // Make sure the initial preloader is visible
      const initialPreloader = document.getElementById('preloader');
      if (initialPreloader) {
        initialPreloader.style.display = 'block';
      }
    }
  });
  
  // For Ajax requests with fetch API
  const originalFetch = window.fetch;
  window.fetch = function() {
    const request = arguments[0];
    const options = arguments[1] || {};
    const url = typeof request === 'string' ? request : request.url;
    
    // Only show loaders for GET requests that might be navigation
    // and only if they're user-initiated or explicitly marked
    const shouldShowLoader = 
      (isUserInitiatedNavigation || options._showLoader) && 
      (!options.method || options.method === 'GET') && 
      isNavigationRequest(url);
    
    if (shouldShowLoader) {
      pendingRequests++;
      showLoaders();
    }
    
    return originalFetch.apply(this, arguments)
      .then(response => {
        if (shouldShowLoader) {
          pendingRequests--;
          if (pendingRequests <= 0 && !isPageReloading) {
            setTimeout(() => hideLoaders(), 200);
          }
        }
        return response;
      })
      .catch(error => {
        if (shouldShowLoader) {
          pendingRequests--;
          if (pendingRequests <= 0 && !isPageReloading) {
            setTimeout(() => hideLoaders(), 200);
          }
        }
        throw error;
      });
  };
  
  // For XMLHttpRequest
  const originalOpen = XMLHttpRequest.prototype.open;
  const originalSend = XMLHttpRequest.prototype.send;
  
  XMLHttpRequest.prototype.open = function() {
    const method = arguments[0];
    const url = arguments[1];
    
    // Store the URL and method for later use
    this._url = url;
    this._method = method;
    
    originalOpen.apply(this, arguments);
  };
  
  XMLHttpRequest.prototype.send = function() {
    // Only show loaders for GET requests that might be navigation
    // and only if they're user-initiated
    const shouldShowLoader = 
      isUserInitiatedNavigation && 
      this._method && 
      this._method.toLowerCase() === 'get' && 
      isNavigationRequest(this._url);
    
    if (shouldShowLoader) {
      pendingRequests++;
      showLoaders();
      
      // Add event listeners to hide loader when request completes
      this.addEventListener('load', function() {
        pendingRequests--;
        if (pendingRequests <= 0 && !isPageReloading) {
          setTimeout(() => hideLoaders(), 200);
        }
      });
      
      this.addEventListener('error', function() {
        pendingRequests--;
        if (pendingRequests <= 0 && !isPageReloading) {
          setTimeout(() => hideLoaders(), 200);
        }
      });
      
      this.addEventListener('abort', function() {
        pendingRequests--;
        if (pendingRequests <= 0 && !isPageReloading) {
          setTimeout(() => hideLoaders(), 200);
        }
      });
    }
    
    originalSend.apply(this, arguments);
  };
  
  // Handle automatic AJAX calls that might happen on page load
  let initialLoadHandled = false;
  
  // Hide loaders when page is fully loaded
  window.addEventListener('load', function() {
    initialLoadHandled = true;
    
    // Only hide loaders if not reloading
    if (!isPageReloading) {
      hideLoaders();
      
      // Hide the initial preloader if it exists and we're not reloading
      const initialPreloader = document.getElementById('preloader');
      if (initialPreloader) {
        initialPreloader.style.display = 'none';
      }
    }
    
    // Reset the reload flag after the page has fully loaded
    setTimeout(() => {
      isPageReloading = false;
    }, 1000);
  });
  
  // Add global functions to manually control the loaders
  window.showPageLoaders = function() {
    isUserInitiatedNavigation = true;
    showLoaders();
    setTimeout(() => {
      isUserInitiatedNavigation = false;
    }, 500);
  };
  
  window.hidePageLoaders = hideLoaders;
  window.showDotLoader = showDotLoader;
  window.hideDotLoader = hideDotLoader;
  window.animateProgressBar = animateProgress;
  window.completeProgressBar = completeProgress;
  window.navigateTo = navigateTo;
  
  // Add a new function to reload the page with loaders
  window.reloadPageWithLoaders = reloadPage;
  
  // Prevent unwanted loader activations after initial page load
  setTimeout(() => {
    if (!initialLoadHandled && !isPageReloading) {
      initialLoadHandled = true;
      hideLoaders();
    }
  }, 1000);
  
  // Force hide loaders if they get stuck
  setInterval(() => {
    // If there are no pending requests but the loader is still showing,
    // it might be stuck, so force hide it (but not during reload)
    if (pendingRequests <= 0 && navigationInProgress && !isPageReloading) {
      console.log('Force hiding stuck loaders');
      hideLoaders();
    }
  }, 10000); // Check every 10 seconds
}

// Run when the DOM is ready
document.addEventListener('DOMContentLoaded', function() {
  // Don't hide the initial preloader immediately - let the page loader logic handle it
  initPageLoaders();
});

// Initialize immediately if the document is already loaded
if (document.readyState === 'complete' || document.readyState === 'interactive') {
  initPageLoaders();
}
