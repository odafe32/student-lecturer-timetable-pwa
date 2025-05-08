@extends('layout.auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Offline</div>

                    <div class="card-body">
                        <div class="alert alert-info">
                            It looks like you're currently offline. Please check your internet connection.
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <button id="install-button" style="display: none;">Install App</button>

    <script>
        let deferredPrompt;
        const installButton = document.getElementById('install-button');

        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent Chrome 67 and earlier from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later
            deferredPrompt = e;
            // Update UI to notify the user they can add to home screen
            installButton.style.display = 'block';

            installButton.addEventListener('click', () => {
                // Hide the app provided install promotion
                installButton.style.display = 'none';
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
        });
    </script>
@endsection
