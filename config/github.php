<?php
// config/github.php

// GitHub OAuth App Credentials
$githubClientId = 'Ov23liga1mfFfmSF1K7d';
$githubClientSecret = 'dd12ed43aa74ab351ada389a805d7266991173ce';

// Authorization callback URL
// Make sure this matches the one you set in your GitHub OAuth App settings
$githubRedirectUri = 'https://levelup.quizchainat.com/auth/github_callback.php';

// If you are testing on a local server, you might use this instead:
// $githubRedirectUri = 'http://localhost/LevelUp/auth/github_callback.php';
?>