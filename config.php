<?php
/**
 * Properer Bangumi RSS - Configuration
 * Code by Fishswing <me@swingworks.net>
 */
 
/**
 * NOTICE:		If you wanna generate feeds for both "progress" and "subject",
 *				copy "config.php & index.php" into "config2.php & index2.php"
 *              and do pair modifications.
 */


///////////////////////////////////////////////////////////////////////////////

/* is_enabled:
 * if set to false, the whole system will pause its work. */
$is_enabled = true;

/* nickname:
 * can be anything but html tags */
$nickname = '鱼尾Swing';

/* bgm_id:
 * your bangumi id. can be found in url "http://bgm.tv/user/xxxxxxx" <- here */
$bgm_id = 'fishswing';

/* bgm_type:
 * at this moment we only support "progress" and "subject" */
$bgm_type = 'progress';

/* rss_filename:
 * specify your output rss file name here */
$rss_filename = 'rss.xml';

/* is_redirect: (true by default)
 * if set to false, the call of index.php itself will generate no rss output,
 * and the feed url should be set to url of $rss_filename.
 * which is recommended WHEN & ONLY WHEN cron is enabled. */
$is_redirect = true;


///////////////////////////////////////////////////////////////////////////////

/* Sina Weibo & Twitter Updating Support */

/* weibo_enabled/twitter_enabled:
 * set to false if you don't have weibo/twitter or wanna stop publishing. */
$weibo_enabled = false;
$twitter_enabled = false;

/* weibo_pattern/twitter_pattern:
 * publishing pattern. you can use the following flags embedded:
 * {{title}}    - feed title text
 * {{content}}  - feed content text (with no links)
 * {{link}}     - link url to your bangumi timeline
 * {{itemlink}} - link url to current bangumi item
 */
$weibo_pattern = '#鱼尾de♂补番计画# {{content}} {{link}} #bangumi#';
$twitter_pattern = '#鱼尾de补番计画 {{content}} {{link}} #bangumi';

?>
