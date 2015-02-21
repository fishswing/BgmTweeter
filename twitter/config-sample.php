<?php
/**
 * BgmTweeter - Twitter Configuration
 */

/**
 * If you wanna use this feature, first create an app on https://apps.twitter.com ,
 * fill in your CONSUMER_KEY, CONSUMER_SECRET and OAUTH_CALLBACK below,
 * and then connect your Twitter account at http://<your_domain>/bgmtweeter/twitter/
 *
 * you will also need to enable this feature in config.php
 * by setting true to $twitter_enabled AND setting your $twitter_pattern .
 *
 * callback_url should look like http://<your_domain>/bgmtweeter/twitter/callback.php
 */

define('CONSUMER_KEY', 'CONSUMER_KEY_HERE');
define('CONSUMER_SECRET', 'CONSUMER_SECRET_HERE');
define('OAUTH_CALLBACK', 'http://<your_domain>/bgmtweeter/twitter/callback.php');
?>
