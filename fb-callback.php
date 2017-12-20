<?php
require_once __DIR__ . '/vendor/autoload.php';
include('config/app.php');

if (!session_id()) {
	session_start();
}

$fb = new Facebook\Facebook([
	"app_id" => "{$app_id}",
	"app_secret" => "{$app_secret}",
	"default_graph_version" => "v2.11",
	]);

$helper = $fb->getJavaScriptHelper();

try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (!isset($accessToken)) {
  echo 'No cookie set or no OAuth data could be obtained from cookie.';
  exit;
}

$oAuth2Client = $fb->getOAuth2Client();

$tokenMetadata = $oAuth2Client->debugToken($accessToken);
$tokenMetadata->validateAppId("{$app_id}");
$tokenMetadata->validateExpiration();

if (!$accessToken->isLongLived()) {
	try {
		$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
	} catch (Facebook\Exceptions\FacebookSDKException $e) {
		echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
		exit;
	}
}

$_SESSION['fb_access_token'] = (string) $accessToken;

header('Location: profile.php');

?>