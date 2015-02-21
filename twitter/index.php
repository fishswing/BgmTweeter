<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Twitter OAuth - BgmTweeter</title>
</head>

<body>

<?php
/**
 * BgmTweeter - Twitter OAuth
 */

require_once('twitteroauth/autoload.php');
use Abraham\TwitterOAuth\TwitterOAuth;

//ini_set('display_errors', '1');

if (!file_exists('config.php')) {
?>
	<p><strong>无法找到配置文件 config.php</strong></p>
	<p>
		请将 twitter/config-sample.php 重命名为 config.php ，<br />
		填入正确的应用配置信息，然后<a href="./">刷新本页面</a>。
	</p>
<?php
}
else {

session_start();
require_once('config.php');

if (file_exists('twitter.oauth')) {
?>
	<!-- 授权文件存在 -->
	<p><strong>检测到 Twitter 已被授权</strong></p>
	<p>
		BgmTweeter 是一个单用户项目。要供多用户使用，请部署多个 BgmTweeter 。<br />
		如需重新授权，请先手工删除文件 twitter/twitter.oauth ，然后<a href="./">刷新本页面</a>。
	</p>
	<p>--</p>
	<p>
		当前运行状态：<br /><pre>
<?php
		$idfile_list = glob('twitter-*.id');
		foreach ($idfile_list as $idfile_name) {
			echo "$idfile_name:\n";
			$idfile = file_get_contents($idfile_name);
			echo "$idfile\n\n";
		}
?>
	</pre></p>
<?php
}
else {
	/* Build TwitterOAuth object with client credentials. */
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);

	/* Get temporary credentials. */
	$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));

	/* Save temporary credentials to session. */
	$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
	$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

	/* If last connection failed don't display authorization link. */
	switch ($connection->getLastHttpCode()) {
	  case 200:
		/* Build authorize URL and redirect user to Twitter. */
		$url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
		echo '<p><a href="'.$url.'"><img src="sign-in-with-twitter-gray.png" title="点击进入授权页面" alt="点击进入授权页面" border="0" /></a></p>';
		break;
	  default:
		/* Show notification if something went wrong. */
		echo '<p>连接到 Twitter 时遇到问题。请<a href="./">刷新</a>或过一会再试。</p>';
	}
	echo '<p>如果授权过程中反复遇到问题，可尝试<a href="clearsessions.php">刷新页面缓存</a>。</p>';
}

}
?>
</body>
</html>
