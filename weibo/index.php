<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>新浪微博授权 - BgmTweeter</title>
</head>

<body>

<?php
/**
 * BgmTweeter - Weibo OAuthV2
 */

if (!file_exists('config.php')) {
?>
	<p><strong>无法找到配置文件 config.php</strong></p>
	<p>
		请将 weibo/config-sample.php 重命名为 config.php ，<br />
		填入正确的应用配置信息，然后<a href="./">刷新本页面</a>。
	</p>
<?php
}
else {

session_start();
include_once( 'config.php' );
include_once( 'saetv2.ex.class.php' );

$o = new WeiboOAuthV2( WB_AKEY , WB_SKEY );
$aurl = $o->getAuthorizeURL( WB_CALLBACK_URL );

if (file_exists('weibo.oauth')) {
?>
	<!-- 授权文件存在 -->
	<p><strong>检测到新浪微博已被授权</strong><br />&nbsp;</p>
	<!-- 显示授权按钮 -->
<?php
		@ $last_key = json_decode(file_get_contents('weibo.oauth'), true);
		$t = $last_key['expires_in'] + $last_key['created_at'] - time();
		if ($t > 0) {
			// 显示友好时间
			$day = floor($t/86400);
			$hour = floor(($t-$day*86400)/3600);
			$min = floor(($t-$day*86400-$hour*3600)/60);
			$sec = $t-$day*86400-$hour*3600-$min*60;
			echo '<p>OAuth剩余有效期：'.(($day>0)?"${day}天":'')."${hour}小时${min}分${sec}秒。<br />";
			echo '由于微博API限制，请在到期前及时更新授权。</p>';
		}
		else echo '<p>OAuth当前已过期，请立即更新授权。</p>';
?>
	<p>
		BgmTweeter 是一个单用户项目。要供多用户使用，请部署多个 BgmTweeter 。<br />
		如需为其他用户授权，请先手工删除文件 weibo/weibo.oauth ，然后<a href="./">刷新本页面</a>。
	</p>
	<p>
		如需更新当前用户授权，请点击登录按钮：<br /><br />
    	<a href="<?php echo $aurl ?>"><img src="weibo_login.png" title="点击进入授权页面" alt="点击进入授权页面" border="0" /></a>
	</p>
	<p>--</p>
	<p>
		当前运行状态：<br /><pre>
<?php
		$idfile_list = glob('weibo-*.id');
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
?>
	<!-- 显示授权按钮 -->
    <p><a href="<?php echo $aurl ?>"><img src="weibo_login.png" title="点击进入授权页面" alt="点击进入授权页面" border="0" /></a></p>
<?php
}

}
?>
</body>
</html>
