<style>
    /* Container for the install prompt */
    #install-container {
        display: none;
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        max-width: 400px;
        background: #0066ff;
        /* Bright blue matching the app */
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
        z-index: 9999;
        overflow: hidden;
        border: 2px solid #ffbb00;
        /* Gold/yellow accent */
        animation: pulse 2s infinite;
    }

    /* Animation for attention */
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 187, 0, 0.7);
        }

        70% {
            box-shadow: 0 0 0 15px rgba(255, 187, 0, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(255, 187, 0, 0);
        }
    }

    /* Content styling */
    .install-content {
        padding: 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Icon and text container */
    .install-info {
        display: flex;
        align-items: center;
        flex: 1;
    }

    /* App icon */
    .app-icon {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        margin-right: 12px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .app-icon svg {
        width: 35px;
        height: 35px;
        fill: #0066ff;
    }

    /* Text styling */
    .install-text {
        color: #ffffff;
    }

    .install-text h3 {
        margin: 0 0 4px 0;
        font-size: 14px;
        font-weight: 600;
        color: white
    }

    .install-text p {
        margin: 0;
        font-size: 13px;
        color: white;
    }

    /* Button styling */
    #install-button {
        background: #ffbb00;
        /* Flat yellow matching the app button */
        color: #000000;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 14px;
        white-space: nowrap;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    #install-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
    }

    /* Close button */
    #close-install {
        position: absolute;
        top: 8px;
        right: 8px;
        background: transparent;
        border: none;
        color: #ffffff;
        font-size: 20px;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s;
    }

    #close-install:hover {
        opacity: 1;
    }
</style>

<div id="install-container">
    <button id="close-install">&times;</button>
    <div class="install-content">
        <div class="install-info">
            <div class="app-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path
                        d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14h-2v-4h-2V9h2V7h2v2h2v4h-2v4z" />
                </svg>
            </div>
            <div class="install-text">
                <h3>Affan Timetables</h3>
                <p>Install for offline access & faster experience</p>
            </div>
        </div>
        <button id="install-button">Install Now</button>
    </div>
</div>

<script>
    let deferredPrompt;
    const installContainer = document.getElementById('install-container');
    const installButton = document.getElementById('install-button');
    const closeButton = document.getElementById('close-install');

    // Show the install prompt when available
    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent Chrome 67 and earlier from automatically showing the prompt
        e.preventDefault();
        // Stash the event so it can be triggered later
        deferredPrompt = e;
        // Update UI to notify the user they can add to home screen
        installContainer.style.display = 'block';
    });

    // Handle install button click
    installButton.addEventListener('click', () => {
        // Hide the app provided install promotion
        installContainer.style.display = 'none';
        // Show the install prompt
        deferredPrompt.prompt();
        // Wait for the user to respond to the prompt
        deferredPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
                console.log('User accepted the install prompt');
            } else {
                console.log('User dismissed the install prompt');
            }
            deferredPrompt = null;
        });
    });

    // Handle close button click
    closeButton.addEventListener('click', () => {
        installContainer.style.display = 'none';
        // Maybe set a cookie/localStorage item to remember user dismissed it
        localStorage.setItem('installPromptDismissed', 'true');
    });

    // Check if user previously dismissed the prompt
    window.addEventListener('DOMContentLoaded', () => {
        const dismissed = localStorage.getItem('installPromptDismissed');
        if (dismissed === 'true') {
            // Don't show the prompt if previously dismissed
            installContainer.style.display = 'none';
        }

        // Reset dismissed status after 3 days
        const resetPrompt = () => {
            localStorage.removeItem('installPromptDismissed');
        };
        setTimeout(resetPrompt, 3 * 24 * 60 * 60 * 1000); // 3 days in milliseconds
    });
</script>
