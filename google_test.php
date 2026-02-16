<?php
// Load your .env variables (assuming you have a loader or manual define)
$client_id = "972521484162-og5cvgodnbkhno5b3l7jusqrkj9ldkdn.apps.googleusercontent.com";
$redirect_uri = "http://localhost:8000/google/callback";

// The "Scope" for Google Calendar
$scope = "https://www.googleapis.com/auth/calendar.events";

// Generate the Login URL
$auth_url = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
    'client_id' => $client_id,
    'redirect_uri' => $redirect_uri,
    'response_type' => 'code',
    'scope' => $scope,
    'access_type' => 'offline',
    'prompt' => 'consent'
]);

echo "<h1>Google Connection Test</h1>";
echo "<p>If your settings are correct, clicking the link below will take you to the Google Login screen.</p>";
echo "<a href='$auth_url' style='padding:10px 20px; background: #4285F4; color:white; text-decoration:none; border-radius:5px;'>Test Google Login</a>";
?>