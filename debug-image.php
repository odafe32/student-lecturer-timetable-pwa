<?php
// Add this code temporarily to your view to debug
// Replace with your actual image path from the database
$imagePath = 'admin_images/' . $adminProfile->profile_image;
$fullStoragePath = storage_path('app/public/' . $imagePath);
$publicPath = public_path('storage/' . $imagePath);

echo "Image filename: " . $adminProfile->profile_image . "<br>";
echo "Full storage path: " . $fullStoragePath . "<br>";
echo "Public path: " . $publicPath . "<br>";
echo "Storage file exists: " . (file_exists($fullStoragePath) ? 'Yes' : 'No') . "<br>";
echo "Public file exists: " . (file_exists($publicPath) ? 'Yes' : 'No') . "<br>";
?>