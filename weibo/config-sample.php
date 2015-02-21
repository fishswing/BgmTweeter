<?php
/**
 * BgmTweeter - Weibo Configuration
 */

/**
 * If you wanna use this feature, first create an app on http://open.weibo.com ,
 * fill in your app_key, app_secret and callback_url below,
 * and then connect your weibo account at http://<your_domain>/bgmtweeter/weibo/
 *
 * you will also need to enable this feature in config.php
 * by setting true to $weibo_enabled AND setting your $weibo_pattern .
 *
 * callback_url should look like http://<your_domain>/bgmtweeter/weibo/callback.php
 */

$weibo_app_key = '1111111111';
$weibo_app_secret = '00000000000000000000000000000000';
$weibo_callback_url = 'http://<yourdomain>/bgmtweeter/weibo/callback.php';

/* The following lines should not be modified. */
define( "WB_AKEY" , $weibo_app_key );
define( "WB_SKEY" , $weibo_app_secret );
define( "WB_CALLBACK_URL" , $weibo_callback_url );
?>
