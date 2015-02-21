<?php
/**
 * BgmTweeter - Twitter OAuth Callback
 */

session_start();
require_once('twitteroauth/autoload.php');
require_once('config.php');
use Abraham\TwitterOAuth\TwitterOAuth;

/* If the oauth_token is old redirect to the connect page. */
if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
	$_SESSION['oauth_status'] = 'oldtoken';
	header('Location: ./clearsessions.php');
}

/* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

/* Request access tokens from twitter */
$access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier']));

/* Save the access tokens. Normally these would be saved in a database for future use. */
//$_SESSION['access_token'] = $access_token;

/* Remove no longer needed request tokens */
unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Twitter OAuth - BgmTweeter</title>
</head>
<body>

<?php
if ($access_token && 200 == $connection->getLastHttpCode()) {
	@file_put_contents('twitter.oauth', json_encode($access_token));
	echo '<p>授权完成。现在您可以关闭本页面。</p>';
}
else {
	echo '<p>授权失败。请<a href="clearsessions.php">重试</a></p>';
}
?>

</body>
</html>
