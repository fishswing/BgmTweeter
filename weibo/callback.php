<?php
/**
 * Properer Bangumi RSS - Sina Weibo OAuthV2 Callback
 * Code by Fishswing <me@swingworks.net>
 */

session_start();
include_once( 'config.php' );
include_once( 'saetv2.ex.class.php' );

$o = new WeiboOAuthV2( WB_AKEY , WB_SKEY );

if (isset($_REQUEST['code'])) {
	$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = WB_CALLBACK_URL;
	try {
		$cur_key = $o->getAccessToken( 'code', $keys ) ;
	} catch (WeiboOAuthException $e) {
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>新浪微博授权 - Properer Bangumi RSS</title>
</head>
<body>

<?php
if ($cur_key) {
	$banned = false;
	if (file_exists('weibo.oauth')) {
		@ $last_key = json_decode(file_get_contents('weibo.oauth'), true);
		if ($last_key['uid'] != $cur_key['uid']) {
?>
			<p><strong>检测到授权用户不一致，授权被禁止</strong></p>
			<p>
				propbgmrss 是一个单用户项目。要供多用户使用，请部署多个 propbgmrss 。<br />
				如需为其他用户授权，请先删除 weibo 目录下的 weibo.oauth 文件，然后<a href="index.php">重新授权</a>。
			</p>
<?php
			$banned = true;
		}
	}
	if (!$banned) {
		$cur_key['created_at'] = time();
		@file_put_contents('weibo.oauth', json_encode($cur_key));
		echo '<p><strong>授权完成。现在您可以关闭本页面</strong></p>';
		// 显示友好时间
		$t = $cur_key['expires_in'];
		$day = floor($t/86400);
		$hour = floor(($t-$day*86400)/3600);
		$min = floor(($t-$day*86400-$hour*3600)/60);
		$sec = $t-$day*86400-$hour*3600-$min*60;
		echo '<p>微博OAuth剩余有效期：'.(($day>0)?"${day}天":'')."${hour}小时${min}分${sec}秒<br />。";
		echo '请在有效期内及时更新授权。</p>';
	}
}
else {
	echo '<p>授权失败。请<a href="index.php">重试</a></p>';
}
//$_SESSION['cur_key'] = $cur_key;
?>

</body>
</html>
