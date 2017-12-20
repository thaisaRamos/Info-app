<?php
require_once __DIR__ . '/vendor/autoload.php';
include('config/app.php');
include('config/database.php');

if (!session_id()) {
    session_start();
}

$fb = new Facebook\Facebook([
  "app_id" => "{$app_id}",
  "app_secret" => "{$app_secret}",
  "default_graph_version" => "v2.11",
  ]);

if (isset($_SESSION['fb_access_token'])) {
	$fb->setDefaultAccessToken($_SESSION['fb_access_token']);
	try {
		$request_picture = $fb->get('/me/picture?redirect=false&height=150'); 
		$request_profile = $fb->get('/me?fields=name,first_name,last_name,email');
		$picture = $request_picture->getGraphUser();
		$profile = $request_profile->getGraphUser();
	} catch(Facebook\Exceptions\FacebookResponseException $e) {
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(Facebook\Exceptions\FacebookSDKException $e) {
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
	
	echo "Hello " .  $profile["name"] . "</br>";
	echo "<img src='".$picture['url']."'/>";
	echo "</br></br>";
	echo "<a href='logout.php'>Logout</a>";

	try {
		$conn = new PDO('mysql:host=localhost;dbname=' . $database_table, $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$stmt = $conn->prepare('SELECT * FROM user WHERE fb_id = :fb_id');
		$stmt->execute(array('fb_id' => $profile["id"]));

		$row = $stmt->fetch();

		if (!$row) {
			$stmt = $conn->prepare('INSERT INTO user (first_name, last_name, email, token, fb_id) VALUES (:first_name, :last_name, :email,:token, :fb_id)');
			$stmt->execute(array(
				':first_name' => $profile["first_name"],
		    	':last_name' => $profile["last_name"],
		    	':email' => $profile["email"],
		    	':token' => $_SESSION['fb_access_token'],
		    	':fb_id' => $profile["id"],
		    	));
		} else {
			if (!$row["active"]) {
				$stmt = $conn->prepare('UPDATE user SET active = :active WHERE fb_id = :fb_id');
				$stmt->execute(array(
					':fb_id'   	=> $profile["id"],
					':active' 	=> 1
					));
			}
		}
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
} else {
	header('Location: index.php');
}
?>