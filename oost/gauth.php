<?php
session_start();

require_once('settings.php');
require_once('google-login-api.php');

// Google passes a parameter 'code' in the Redirect Url
if(isset($_GET['code'])) {
	try {
		$gapi = new GoogleLoginApi();
		
		// Get the access token 
		$data = $gapi->GetAccessToken(CLIENT_ID, CLIENT_REDIRECT_URL, CLIENT_SECRET, $_GET['code']);
		
		// Get user information
		$user_info = $gapi->GetUserProfileInfo($data['access_token']);

		//echo '<pre>';print_r($user_info); echo '</pre>';
		$_SESSION['FB_userID'] = $user_info['id']; // actual google id
		$_SESSION['name'] = $user_info['displayName'];
		setcookie("userImgLink", $user_info['image']['url']);
		//echo $user_info['displayName'];
		// Now that the user is logged in you may want to start some session variables
		$_SESSION['logged_in'] = 1;

		// You may now want to redirect the user to the home page of your website
		header('Location: index.php');
	}
	catch(Exception $e) {
		echo $e->getMessage();
		exit();
	}
}

?>