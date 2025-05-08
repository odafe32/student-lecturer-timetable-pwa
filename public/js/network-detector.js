// Internet Connection Status Detector
// This script monitors the user's internet connection and displays notifications

// Create the connection status elements
function createNetworkStatusElements() {
  // Create the main container
  const statusContainer = document.createElement('div');
  statusContainer.id = 'internetStatus';
  statusContainer.className = 'internet-status-container';
  
  // Create the online status element
  const onlineStatus = document.createElement('div');
  onlineStatus.id = 'onlineStatusWrapper';
  onlineStatus.className = 'internet-status bg-success';
  onlineStatus.innerHTML = `
    <div class="content">
      <i class="bi bi-wifi"></i>
      <span>Your internet connection was restored.</span>
    </div>
    <button class="close-btn" id="dismissOnlineBtn"><i class="bi bi-x"></i></button>
  `;
  onlineStatus.style.display = 'none';
  
  // Create the offline status element
  const offlineStatus = document.createElement('div');
  offlineStatus.id = 'offlineStatusWrapper';
  offlineStatus.className = 'internet-status bg-danger';
  offlineStatus.innerHTML = `
    <div class="content">
      <i class="bi bi-wifi-off"></i>
      <span>No internet connection detected.</span>
    </div>
    <button class="close-btn" id="dismissOfflineBtn"><i class="bi bi-x"></i></button>
  `;
  offlineStatus.style.display = 'none';
  
  // Append to container
  statusContainer.appendChild(onlineStatus);
  statusContainer.appendChild(offlineStatus);
  
  // Add the container to the body
  document.body.appendChild(statusContainer);
  
  // Add styles
  addNetworkStatusStyles();
  
  // Add event listeners for dismiss buttons
  document.getElementById('dismissOnlineBtn').addEventListener('click', function() {
    document.getElementById('onlineStatusWrapper').style.display = 'none';
  });
  
  document.getElementById('dismissOfflineBtn').addEventListener('click', function() {
    document.getElementById('offlineStatusWrapper').style.display = 'none';
  });
}

// Add necessary styles for the connection status elements
function addNetworkStatusStyles() {
  const styleElement = document.createElement('style');
  styleElement.textContent = `
    .internet-status-container {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 999999;
    }
    
    .internet-status {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 15px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
      color: #ffffff;
      font-size: 14px;
      transition: all 0.3s ease;
      font-family: 'Poppins', sans-serif;
    }
    
    .internet-status .content {
      display: flex;
      align-items: center;
    }
    
    .internet-status i {
      margin-right: 8px;
      font-size: 18px;
    }
    
    .bg-success {
      background-color: #32B768;
    }
    
    .bg-danger {
      background-color: #EA4335;
    }
    
    .close-btn {
      background: none;
      border: none;
      color: #ffffff;
      cursor: pointer;
      font-size: 16px;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 24px;
      height: 24px;
    }
    
    /* Add Bootstrap Icons */
    @import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css");
  `;
  
  document.head.appendChild(styleElement);
}

// Initialize the network status monitoring
function initNetworkStatusMonitoring() {
  // Create the UI elements first
  createNetworkStatusElements();
  
  const onlineStatusWrapper = document.getElementById('onlineStatusWrapper');
  const offlineStatusWrapper = document.getElementById('offlineStatusWrapper');
  
  // Variables to track status
  let isOnline = navigator.onLine;
  let timeoutId = null;
  
  // Function to handle online status
  function handleOnlineStatus() {
    if (!isOnline) {
      isOnline = true;
      
      // Show the online notification
      onlineStatusWrapper.style.display = 'flex';
      offlineStatusWrapper.style.display = 'none';
      
      // Auto-hide after 3 seconds
      clearTimeout(timeoutId);
      timeoutId = setTimeout(() => {
        onlineStatusWrapper.style.display = 'none';
      }, 3000);
    }
  }
  
  // Function to handle offline status
  function handleOfflineStatus() {
    isOnline = false;
    
    // Show the offline notification
    offlineStatusWrapper.style.display = 'flex';
    onlineStatusWrapper.style.display = 'none';
    
    // Don't auto-hide the offline notification
    clearTimeout(timeoutId);
  }
  
  // Add event listeners for online/offline events
  window.addEventListener('online', handleOnlineStatus);
  window.addEventListener('offline', handleOfflineStatus);
  
  // Additional check: Periodically ping a reliable server to verify connection
  setInterval(() => {
    const pingStart = Date.now();
    
    fetch('https://www.google.com/generate_204', { 
      mode: 'no-cors',
      cache: 'no-store'
    })
    .then(() => {
      const pingTime = Date.now() - pingStart;
      if (pingTime < 3000) { // If ping is successful within 3 seconds
        handleOnlineStatus();
      }
    })
    .catch(() => {
      handleOfflineStatus();
    });
  }, 10000); // Check every 10 seconds
}

// Run the network status monitoring when the DOM is fully loaded
document.addEventListener('DOMContentLoaded', initNetworkStatusMonitoring);

// Initialize immediately if the document is already loaded
if (document.readyState === 'complete' || document.readyState === 'interactive') {
  initNetworkStatusMonitoring();
}