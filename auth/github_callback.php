<?php
// auth/github_callback.php

// 1. INITIALIZATION
// ===================================================
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0, 
        'path' => '/', 
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', 
        'httponly' => true, 
        'samesite' => 'Lax'
    ]);
    session_start();
}

require_once __DIR__ . '/../config/github.php';
require_once __DIR__ . '/../config/db.php';

// 2. STATE & CODE VERIFICATION
// ===================================================
// Verify the state to prevent CSRF attacks
if (!isset($_GET['state']) || !isset($_SESSION['github_oauth_state']) || $_GET['state'] !== $_SESSION['github_oauth_state']) {
    unset($_SESSION['github_oauth_state']);
    header('Location: login.php?error=invalid_state');
    exit;
}
unset($_SESSION['github_oauth_state']); // Clean up the state

// Check if the code is provided
if (!isset($_GET['code'])) {
    header('Location: login.php?error=no_code');
    exit;
}

$code = $_GET['code'];

// 3. EXCHANGE CODE FOR ACCESS TOKEN
// ===================================================
$tokenUrl = 'https://github.com/login/oauth/access_token';
$postData = [
    'client_id' => $githubClientId,
    'client_secret' => $githubClientSecret,
    'code' => $code,
    'redirect_uri' => $githubRedirectUri,
];

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $tokenUrl,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($postData),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Accept: application/json'], // Ask for JSON response
    CURLOPT_USERAGENT => 'LevelUp-App' // GitHub requires a User-Agent
]);
$response = curl_exec($ch);
curl_close($ch);

$tokenData = json_decode($response, true);

if (!isset($tokenData['access_token'])) {
    header('Location: login.php?error=token_failed');
    exit;
}
$accessToken = $tokenData['access_token'];

// 4. FETCH USER INFORMATION FROM GITHUB
// ===================================================
function fetch_github_data($url, $token) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Accept: application/vnd.github.v3+json'
        ],
        CURLOPT_USERAGENT => 'LevelUp-App'
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Fetch user profile
$userInfo = fetch_github_data('https://api.github.com/user', $accessToken);

if (!isset($userInfo['id'])) {
    header('Location: login.php?error=user_info_failed');
    exit;
}

// Fetch user emails to find the primary one
$userEmails = fetch_github_data('https://api.github.com/user/emails', $accessToken);
$primaryEmail = '';
if (is_array($userEmails)) {
    foreach ($userEmails as $email) {
        if ($email['primary'] && $email['verified']) {
            $primaryEmail = $email['email'];
            break;
        }
    }
    // Fallback if no primary email is found
    if (empty($primaryEmail) && !empty($userEmails[0]['email'])) {
        $primaryEmail = $userEmails[0]['email'];
    }
}
// Final fallback to public email if available
if (empty($primaryEmail) && isset($userInfo['email'])) {
    $primaryEmail = $userInfo['email'];
}

if (empty($primaryEmail)) {
    header('Location: login.php?error=email_not_found');
    exit;
}

// 5. DATABASE & SESSION MANAGEMENT
// ===================================================
$githubId = (string)($userInfo['id'] ?? '');
$name = $userInfo['name'] ?? $userInfo['login'] ?? 'GitHub User';
$img = $userInfo['avatar_url'] ?? '';
$email = $primaryEmail;
$dbUserId = null;

if (isset($conn)) {
    try {
        // Check if user exists by github_id or email
        $stmt = $conn->prepare("SELECT id FROM users WHERE github_id = :github_id OR email = :email LIMIT 1");
        $stmt->execute([':github_id' => $githubId, ':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // User exists: Update their info
            $dbUserId = $user['id'];
            $updateStmt = $conn->prepare("UPDATE users SET github_id = :github_id, name = :name, img = :img WHERE id = :id");
            $updateStmt->execute([
                ':github_id' => $githubId,
                ':name' => $name,
                ':img' => $img,
                ':id' => $dbUserId
            ]);
        } else {
            // New user: Insert into database
            $insertStmt = $conn->prepare("INSERT INTO users (github_id, name, email, img) VALUES (:github_id, :name, :email, :img)");
            $insertStmt->execute([
                ':github_id' => $githubId,
                ':name' => $name,
                ':email' => $email,
                ':img' => $img
            ]);
            $dbUserId = $conn->lastInsertId();
        }

        // Check and create record in status_users if needed
        if (!empty($dbUserId)) {
            $statusCheck = $conn->prepare("SELECT id FROM status_users WHERE user_id = :user_id LIMIT 1");
            $statusCheck->execute([':user_id' => $dbUserId]);
            if (!$statusCheck->fetch()) {
                $insertStatus = $conn->prepare("INSERT INTO status_users (user_id, level, exp) VALUES (:user_id, 1, 0)");
                $insertStatus->execute([':user_id' => $dbUserId]);
            }
        }
    } catch (PDOException $e) {
        error_log("DB Error in GitHub Callback: " . $e->getMessage());
        header('Location: login.php?error=db_error');
        exit;
    }
}

// 6. LOG USER IN & REDIRECT
// ===================================================
$finalId = $dbUserId ?? $githubId;

$_SESSION['user_id'] = $finalId;
$_SESSION['user_login'] = [
    'id'       => $finalId,
    'name'     => $name,
    'email'    => $email,
    'img'      => $img,
    'level'    => 1, 
    'user_exp' => 0, 
    'streak'   => 1
];

session_write_close();
header('Location: ../index.php');
exit;
?>