<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// กำหนดค่าต่างๆ จาก Google Console
$clientID     = '391621497018-cbigmvo3gcaiqjk89rij042m30cqtmks.apps.googleusercontent.com';
$clientSecret = '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';

// กำหนด Redirect URI (หากใช้ callback.php ให้เปลี่ยนเป็น https://levelup.quizchainat.com/auth/callback.php)
$redirectUri  = 'https://levelup.quizchainat.com/index.php';

// สร้าง Class จำลองรองรับกรณีที่ไม่ได้ติดตั้ง Composer SDK
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
            $params = [
                'client_id'     => $this->clientId,
                'redirect_uri'  => $this->redirectUri,
                'response_type' => 'code',
                'scope'         => implode(' ', $this->scopes) ?: 'openid email profile',
                'access_type'   => 'online',
                'prompt'        => 'select_account'
            ];
            return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        }
    }
    class_alias('PureGoogleClient', 'Google\Client');
}

// สร้าง Object $client ส่งต่อไปยัง auth/login.php
$client = new Google\Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

// หากมีการส่ง code กลับมาจาก Google ให้ทำการแลก Token และดึงข้อมูลผู้ใช้
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
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $tokenData = json_decode($response, true);

    if (isset($tokenData['access_token'])) {
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $tokenData['access_token'];
        $userInfoJson = file_get_contents($userInfoUrl);
        $userInfo = json_decode($userInfoJson, true);

        $_SESSION['user_data'] = $userInfo;

        header('Location: /index.php');
        exit;
    }
}