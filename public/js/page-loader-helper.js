/**
 * Helper functions to manually control the page loader
 */

// Show the page loader manually
function showPageLoader() {
  if (window.showPageLoader) {
    window.showPageLoader();
  } else {
    const pageLoader = document.getElementById('pageLoader');
    if (pageLoader) {
      pageLoader.classList.add('show');
    }
  }
}

// Hide the page loader manually
function hidePageLoader() {
  if (window.hidePageLoader) {
    window.hidePageLoader();
  } else {
    const pageLoader = document.getElementById('pageLoader');
    if (pageLoader) {
      pageLoader.classList.remove('show');
    }
  }
}

// Add loader to specific elements manually
function addLoaderToElements(selector, eventType = 'click') {
  const elements = document.querySelectorAll(selector);
  
  elements.forEach(element => {
    element.addEventListener(eventType, function(e) {
      // Check if this is a navigation element
      const isNavigation = 
        (element.tagName === 'A' && !element.getAttribute('target')) || 
        (element.tagName === 'BUTTON' && !element.getAttribute('type'));
      
      if (isNavigation) {
        showPageLoader();
      }
    });
  });
}

// Example usage:
// document.addEventListener('DOMContentLoaded', function() {
//   // Add loader to all navigation menu items
//   addLoaderToElements('.nav-link');
//   
//   // Add loader to all buttons with class 'load-page'
//   addLoaderToElements('.load-page');
// });