<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// กำหนดค่าต่างๆ จาก Google Console
$clientID     = '391621497018-cbigmvo3gcaiqjk89rij042m30cqtmks.apps.googleusercontent.com';
$clientSecret = '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';

// กำหนด Redirect URI ให้ตรงกับ Google Console
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

// หากมีการส่ง code กลับมาจาก Google ให้ทำการแลก Token และดึงข้อมูลผู้ใช้
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // 1. ส่ง Code ไปแลก Access Token ด้วย cURL
    $tokenUrl = 'https://oauth2.googleapis.com/token';
    $postData = array(
        'code'          => $code,
        'client_id'     => $clientID,
        'client_secret' => $clientSecret,
        'redirect_uri'  => $redirectUri,
        'grant_type'    => 'authorization_code'
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ป้องกันปัญหา SSL บนเซิร์ฟเวอร์
    $response = curl_exec($ch);
    curl_close($ch);

    $tokenData = json_decode($response, true);

    if (isset($tokenData['access_token'])) {
        // 2. ดึงข้อมูลโปรไฟล์ผู้ใช้ด้วย cURL (เสถียรกว่า file_get_contents)
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
        
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $userInfoUrl);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $tokenData['access_token']
        ));
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false); // ป้องกันปัญหา SSL บนเซิร์ฟเวอร์
        $userResponse = curl_exec($ch2);
        curl_close($ch2);

        $userInfo = json_decode($userResponse, true);

        // 3. ถ้าได้ข้อมูลผู้ใช้มาจริง ให้บันทึกลง Session
        if (isset($userInfo['email'])) {
            $_SESSION['user_data'] = $userInfo;

            // รีเฟรชหน้าเพื่อลบ ?code= ออกจาก URL
            header('Location: /index.php');
            exit;
        }
    }
}