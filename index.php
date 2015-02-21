<?php
/**
 * Properer Bangumi RSS - Caller
 * Code by Fishswing <me@swingworks.net>
 */
 
require_once("getmyrss.php");
require_once("weibo/sendweibo.php");
require_once("twitter/sendtwitter.php");

/**
 * NOTICE:		Please make sure that there is ONLY ONE SINGLE CALL in a caller file!
 *				If you wanna generate feeds for both "progress" and "subject",
 *				copy "config.php & index.php" into "config2.php & index2.php" and do
 *				pair modifications.
 */

require_once("config.php");

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
