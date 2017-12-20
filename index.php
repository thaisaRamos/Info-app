<?php
	include('config/app.php');
	
	if (!session_id()) {
		session_start();
	}

	if (isset($_SESSION['fb_access_token']) !== true || $_SESSION['fb_access_token'] == "") {
		echo "<p> Welcome to Info APP </p>";
		echo "<div id='msgs'></div>";
		echo "<fb:login-button scope='public_profile,email' onlogin='loginFB();'></fb:login-button>";
	} else {
		header('Location: profile.php');
		exit;
	}
?>
<script>
	function changeCallback(response) {
		if (response.status === 'connected') {
	      window.location.replace("fb-callback.php");
	    } else if (response.status === 'not_authorized') {
	      document.getElementById('msgs').innerHTML = 'To access, you must authorize this app.';
	    } else {
	      document.getElementById('msgs').innerHTML = 'To access the app, please login with facebook.';
	    }
	  }

	  function loginFB() {
	    FB.getLoginStatus(function(response) {
	      changeCallback(response);
	    });
	  }

	  window.fbAsyncInit = function() {
		  FB.init({
		    appId      : '<?php echo $app_id;?>',
		    cookie     : true,  
		    xfbml      : true,  
		    version    : 'v2.11'
		  });
	  };

	  (function(d, s, id) {
	    var js, fjs = d.getElementsByTagName(s)[0];
	    if (d.getElementById(id)) return;
	    js = d.createElement(s); js.id = id;
	    js.src = "//connect.facebook.net/en_US/sdk.js";
	    fjs.parentNode.insertBefore(js, fjs);
	  }(document, 'script', 'facebook-jssdk'));
</script>
