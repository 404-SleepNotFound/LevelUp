<?php
// เช็คไฟล์ autoload หากมีก็โหลด ถ้าไม่มีก็ข้ามได้เลย (ไม่ต้องใช้ Composer)
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// กำหนดค่าต่างๆ จาก Google Console
$clientID = '391621497018-cbigmvo3gcaiqjk89rij042m30cqtmks.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'https://levelup.quizchainat.com/auth/callback.php'; // ต้องตรงกับที่ตั้งค่าไว้ในหน้าเว็บ Google

// สร้าง URL สำหรับ Redirect ไปหน้า Login ของ Google (ใช้ Pure PHP ไม่พึ่ง Composer)
$googleParams = [
    'client_id'     => $clientID,
    'redirect_uri'  => $redirectUri,
    'response_type' => 'code',
    'scope'         => 'openid email profile',
    'access_type'   => 'online',
    'prompt'        => 'select_account'
];
$googleAuthUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($googleParams);

// สร้าง Class จำลองไว้รองรับกรณีที่ไฟล์อื่นเรียกใช้ $client->createAuthUrl()
if (!class_exists('Google\Client')) {
    class PureGoogleClient {
        private $clientId;
        private $clientSecret;
        private $redirectUri;
        private $scopes = [];

        public function setClientId($id) { $this->clientId = $id; }
        public function setClientSecret($secret) { $this->clientSecret = $secret; }
        public function setRedirectUri($uri) { $this->redirectUri = $uri; }
        public function addScope($scope) { $this->scopes[] = $scope; }
        public function createAuthUrl() {
            global $googleAuthUrl;
            return $googleAuthUrl;
        }
    }
    class_alias('PureGoogleClient', 'Google\Client');
}

// สร้าง Object ของ Google Client
$client = new Google\Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);

// บอก Google ว่าเราขอแค่ อีเมล และ โปรไฟล์
$client->addScope("email");
$client->addScope("profile");

// หากกดเข้าไฟล์ google.php โดยตรง ให้ Redirect ไปหน้า Login ของ Google ทันที
if (basename($_SERVER['PHP_SELF']) === 'google.php') {
    header('Location: ' . $googleAuthUrl);
    exit;
}