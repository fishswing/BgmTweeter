<?php
/**
 * BgmTweeter v1.2 (aka propbgmrss)
 *
 * Author: Fishswing <me@swingworks.net>
 * http://www.swingworks.net
 */

require_once("getmyrss.php");
require_once("weibo/sendweibo.php");
require_once("twitter/sendtwitter.php");

/**
 * NOTICE:
 * If you wanna setup for both "progress" and "subject",
 * copy "config.php & index.php" out to "config2.php & index2.php"
 * and setup them separately.
 *
 * In this file you only need to change this file name accordingly:
 */
require_once("config.php");

// Make sure there is ONLY ONE SINGLE CALL in one caller file!
if ($is_enabled) {
	// generate feed
	$item = getmyrss();

	// send weibo/twitter
	if ($weibo_enabled || $twitter_enabled) {
		// get caller.php name (eg.: index)
		$slash = strrpos($_SERVER['PHP_SELF'], '/');
		if ($slash === false) $selfname = $_SERVER['PHP_SELF'];
		else $selfname = substr($_SERVER['PHP_SELF'], $slash + 1);
		$selfname = substr($selfname, 0, -4);
		// send it
		if ($weibo_enabled) sendweibo($item, $selfname);
		if ($twitter_enabled) sendtwitter($item, $selfname);
	}
}
?>
