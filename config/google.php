<?php
// เช็คไฟล์ autoload หากมีก็โหลด ถ้าไม่มีก็ข้ามได้เลย (ไม่ต้องใช้ Composer)[cite: 3]
$autoloadPath = __DIR__ . '/../vendor/autoload.php';[cite: 3]
if (file_exists($autoloadPath)) {[cite: 3]
    require_once $autoloadPath;[cite: 3]
}

if (session_status() === PHP_SESSION_NONE) {[cite: 3]
    session_start();[cite: 3]
}

// หากมีเซสชันการล็อกอินอยู่แล้ว ให้เด้งไปที่หน้า index.php ทันที
if (isset($_SESSION['user_data'])) {
    header('Location: /index.php');
    exit;
}

// กำหนดค่าต่างๆ จาก Google Console[cite: 3]
$clientID     = '391621497018-cbigmvo3gcaiqjk89rij042m30cqtmks.apps.googleusercontent.com';[cite: 3]
$clientSecret = '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';[cite: 3]
$redirectUri  = 'https://levelup.quizchainat.com/auth/callback.php'; // ต้องตรงกับที่ตั้งค่าไว้ในหน้าเว็บ Google[cite: 3]
$indexUrl     = 'https://levelup.quizchainat.com/index.php'; // URL หน้าหลัก index.php

// สร้าง URL สำหรับ Redirect ไปหน้า Login ของ Google (ใช้ Pure PHP ไม่พึ่ง Composer)[cite: 3]
$googleParams = [[cite: 3]
    'client_id'     => $clientID,[cite: 3]
    'redirect_uri'  => $redirectUri,[cite: 3]
    'response_type' => 'code',[cite: 3]
    'scope'         => 'openid email profile',[cite: 3]
    'access_type'   => 'online',[cite: 3]
    'prompt'        => 'select_account'[cite: 3]
];
$googleAuthUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($googleParams);[cite: 3]

// สร้าง Class จำลองไว้รองรับกรณีที่ไฟล์อื่นเรียกใช้ $client->createAuthUrl()[cite: 3]
if (!class_exists('Google\Client')) {[cite: 3]
    class PureGoogleClient {[cite: 3]
        private $clientId;[cite: 3]
        private $clientSecret;[cite: 3]
        private $redirectUri;[cite: 3]
        private $scopes = [];[cite: 3]

        public function setClientId($id) { $this->clientId = $id; }[cite: 3]
        public function setClientSecret($secret) { $this->clientSecret = $secret; }[cite: 3]
        public function setRedirectUri($uri) { $this->redirectUri = $uri; }[cite: 3]
        public function addScope($scope) { $this->scopes[] = $scope; }[cite: 3]
        public function createAuthUrl() {[cite: 3]
            global $googleAuthUrl;[cite: 3]
            return $googleAuthUrl;[cite: 3]
        }
    }
    class_alias('PureGoogleClient', 'Google\Client');[cite: 3]
}

// สร้าง Object ของ Google Client[cite: 3]
$client = new Google\Client();[cite: 3]
$client->setClientId($clientID);[cite: 3]
$client->setClientSecret($clientSecret);[cite: 3]
$client->setRedirectUri($redirectUri);[cite: 3]

// บอก Google ว่าเราขอแค่ อีเมล และ โปรไฟล์[cite: 3]
$client->addScope("email");[cite: 3]
$client->addScope("profile");[cite: 3]

// หากกดเข้าไฟล์ google.php โดยตรง ให้ Redirect ไปหน้า Login ของ Google ทันที[cite: 3]
if (basename($_SERVER['PHP_SELF']) === 'google.php') {[cite: 3]
    header('Location: ' . $googleAuthUrl);[cite: 3]
    exit;[cite: 3]
}