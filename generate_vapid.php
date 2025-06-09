<?php

require_once 'vendor/autoload.php';

use Minishlink\WebPush\VAPID;

try {
    echo "Generating VAPID keys...\n\n";
    
    // Generate VAPID keys
    $keys = VAPID::createVapidKeys();
    
    echo "✅ VAPID keys generated successfully!\n\n";
    echo "Copy these to your .env file:\n";
    echo "=====================================\n";
    echo "VAPID_PUBLIC_KEY=" . $keys['publicKey'] . "\n";
    echo "VAPID_PRIVATE_KEY=" . $keys['privateKey'] . "\n";
    echo "=====================================\n\n";
    
    echo "Public Key Length: " . strlen($keys['publicKey']) . " characters\n";
    echo "Private Key Length: " . strlen($keys['privateKey']) . " characters\n\n";
    
    echo "✅ Keys are ready to use!\n";
    
} catch (Exception $e) {
    echo "❌ Error generating VAPID keys: " . $e->getMessage() . "\n";
    echo "\nTry installing web-push library first:\n";
    echo "composer require minishlink/web-push\n";
}