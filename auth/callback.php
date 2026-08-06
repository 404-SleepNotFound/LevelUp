<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

require_once __DIR__ . '/../config/google.php';
require_once __DIR__ . '/../config/db.php';

if (isset($_GET['state']) && isset($_SESSION['oauth2state'])) {
    if ($_GET['state'] !== $_SESSION['oauth2state']) {
        unset($_SESSION['oauth2state']);
        exit('Invalid OAuth State Parameter');
    }
    unset($_SESSION['oauth2state']);
}

if (isset($_GET['code'])) {
    $code = $_GET['code'];

    $tokenUrl = 'https://oauth2.googleapis.com/token';
    $postData = [
        'code'          => $code,
        'client_id'     => $clientID,
        'client_secret' => $clientSecret,
        'redirect_uri'  => $redirectUri,
        'grant_type'    => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $tokenUrl,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($postData),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    
    $response  = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        exit('cURL Error: ' . htmlspecialchars($curlError));
    }

    $tokenData = json_decode($response, true);

    if (isset($tokenData['access_token'])) {
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
        
        $ch2 = curl_init();
        curl_setopt_array($ch2, [
            CURLOPT_URL            => $userInfoUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $tokenData['access_token']],
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        $userResponse = curl_exec($ch2);
        curl_close($ch2);

        $userInfo = json_decode($userResponse, true, 512, JSON_BIGINT_AS_STRING);

        if (isset($userInfo['email'])) {
            $googleId = (string)($userInfo['id'] ?? '');
            $email    = $userInfo['email'];
            $name     = $userInfo['name'] ?? ($email ?? 'Google User');
            $picture  = $userInfo['picture'] ?? '';
            
            $dbUserId = null;

            $stmt = $pdo->prepare("SELECT id FROM users WHERE google_id = :google_id OR email = :email LIMIT 1");
            $stmt->execute([':google_id' => $googleId, ':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $dbUserId = $user['id'];
            } else {
                $insertStmt = $pdo->prepare("INSERT INTO users (google_id, email, username, name, picture) VALUES (:google_id, :email, :username, :name, :picture)");
                $insertStmt->execute([
                    ':google_id' => $googleId,
                    ':email'     => $email,
                    ':username'  => $name,
                    ':name'      => $name,
                    ':picture'   => $picture
                ]);
                
                $dbUserId = $pdo->lastInsertId();
            }

            $finalId = $dbUserId ?? $googleId;

            $_SESSION['user_data'] = $userInfo;
            $_SESSION['user_id']   = $finalId;
            
            $_SESSION['user_login'] = [
                'id'       => $finalId,
                'name'     => $name,
                'username' => $name,
                'email'    => $email,
                'picture'  => $picture,
                'img'      => $picture,
                'avatar'   => $picture,
                'level'    => 1,
                'user_exp' => 0,
                'streak'   => 1
            ];

            session_write_close();
            
            header('Location: ../index.php');
            exit;
        }
    } else {
        echo "<div style='padding: 20px; font-family: sans-serif;'>";
        echo "<h3 style='color: red;'>❌ การล็อกอินด้วย Google ล้มเหลว!</h3>";
        echo "<pre style='background: #f4f4f4; padding: 15px; border-radius: 5px;'>";
        echo htmlspecialchars(print_r($tokenData, true));
        echo "</pre>";
        echo "<a href='../auth/login.php'>กลับหน้าล็อกอิน</a>";
        echo "</div>";
        exit;
    }
}

header('Location: ../index.php');
exit;