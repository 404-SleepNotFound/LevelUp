<?php
// auth/github_login.php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the GitHub config file
require_once __DIR__ . '/../config/github.php';

// Construct the GitHub authorization URL
$authUrl = 'https://github.com/login/oauth/authorize';
$params = [
    'client_id' => $githubClientId,
    'redirect_uri' => $githubRedirectUri,
    'scope' => 'read:user user:email', // Request access to user's profile and email
    'state' => bin2hex(random_bytes(16)) // A random string to prevent CSRF attacks
];

// Store the state in session to verify it in the callback
$_SESSION['github_oauth_state'] = $params['state'];

$loginUrl = $authUrl . '?' . http_build_query($params);

// Redirect the user to the GitHub authorization page
header('Location: ' . $loginUrl);
exit;
?>