<?php
// ตั้งค่า Cookie Path ให้ครอบคลุมทุกโฟลเดอร์ในเว็บ
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

// กำหนดค่าต่างๆ จาก Google Console
$clientID     = '391621497018-cbigmvo3gcaiqjk89rij042m30cqtmks.apps.googleusercontent.com';
$clientSecret = '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';

// กำหนด Redirect URI ให้ชี้ไปที่ callback.php
$redirectUri = 'https://levelup.quizchainat.com/auth/callback.php';

// สร้าง Class จำลองรองรับกรณีที่ไม่ได้ติดตั้ง Composer SDK
if (!class_exists('Google\Client')) {
    class PureGoogleClient {
        private $clientId;
        private $clientSecret;
        private $redirectUri;
        private $scopes = array();

        public function setClientId($id) { $this->clientId = $id; }
        public function setClientSecret($secret) { $this->clientSecret = $secret; }
        public function setRedirectUri($uri) { $this->redirectUri = $uri; }
        public function addScope($scope) { $this->scopes[] = $scope; }
        
        public function createAuthUrl() {
            $params = array(
                'client_id'     => $this->clientId,
                'redirect_uri'  => $this->redirectUri,
                'response_type' => 'code',
                'scope'         => !empty($this->scopes) ? implode(' ', $this->scopes) : 'openid email profile',
                'access_type'   => 'online',
                'prompt'        => 'select_account'
            );
            return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        }
    }
    class_alias('PureGoogleClient', 'Google\Client');
}

// สร้าง Object $client
$client = new Google\Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");