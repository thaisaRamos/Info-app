<?php
include('config/database.php');
$signed_request = $_REQUEST['signed_request'];

function base64_url_decode($input) {
	return base64_decode(strtr($input, '-_', '+/'));
}

list($encoded_sig, $payload) = explode('.', $signed_request, 2);

//decode data
$sig = base64_url_decode($encoded_sig);
$data = json_decode(base64_url_decode($payload), true);
$user_id = $data['user_id'];

if ($user_id) {
	try {
		$conn = new PDO('mysql:host=localhost;dbname=' . $database_table, $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$stmt = $conn->prepare('SELECT * FROM user WHERE fb_id = :fb_id');
		$stmt->execute(array('fb_id' => $user_id));

		$row = (bool) $stmt->fetchColumn();

		if ($row) {
			$stmt = $conn->prepare('UPDATE user SET active = :active WHERE fb_id = :fb_id');
			$stmt->execute(array(
				':fb_id'   	=> $user_id,
				':active' 	=> 0
			));
		}
	} catch(PDOException $e) {
		echo 'Error: ' . $e->getMessage();
	}
}