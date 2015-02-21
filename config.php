<?php
/**
 * BgmTweeter - Configuration
 */

/**
 * NOTICE:
 * If you wanna setup for both "progress" and "subject",
 * copy "config.php & index.php" out to "config2.php & index2.php"
 * and setup them separately.
 */


///////////////////////////////////////////////////////////////////////////////
// Global Settings
///////////////////////////////////////////////////////////////////////////////

/* is_enabled:
 * If set to false, the whole system will pause.
 */
$is_enabled = true;

/* nickname:
 * It can be anything except html tags.
 */
$nickname = '鱼尾Swing';

/* bgm_id:
 * Your bangumi id. It can be found in url "http://bgm.tv/user/xxxxxxx" <- here
 */
$bgm_id = 'fishswing';

/* bgm_type:
 * At this moment we only support "progress" & "subject".
 */
$bgm_type = 'progress';


///////////////////////////////////////////////////////////////////////////////
// General Publishing Settings
///////////////////////////////////////////////////////////////////////////////

/* rss_title:
 * This will be applied to both RSS channel title & {{rsstitle}} tag.
 */
$rss_title = "$nickname 的 Bangumi";

/* name_pattern:
 * The pattern in which the name of bangumi will be showed.
 * You can use the following tags inline:
 * {{name}}     - original name, usually showed in Japanese
 * {{chname}}   - translated name in simplified Chinese
 * {{(chname)}} - clever translated name embraced by '()', auto removed if
 *                1) not available, 2) same as original name, or
 *                3) original name is all Chinese characters
 */
$name_pattern = '{{name}}{{(chname)}}';


///////////////////////////////////////////////////////////////////////////////
// Weibo & Twitter Settings
///////////////////////////////////////////////////////////////////////////////

/* weibo_enabled / twitter_enabled:
 * Set to false if you don't want to publish.
 */
$weibo_enabled = false;
$twitter_enabled = false;

/* weibo_pattern / twitter_pattern:
 * The pattern to publish.
 * You can use the following flags inline:
 * {{rsstitle}} - the value of $rss_title as defined above
 * {{content}}  - content text (url links will be removed)
 * {{link}}     - link url to your bangumi timeline
 * {{itemlink}} - link url to current bangumi item
 */
$weibo_pattern = '【{{rsstitle}}】 {{content}} {{link}} #bangumi#';
$twitter_pattern = '【{{rsstitle}}】 {{content}} {{link}} #bangumi';


///////////////////////////////////////////////////////////////////////////////
// RSS Update Support
///////////////////////////////////////////////////////////////////////////////

/* rss_enabled:
 * Set to false if you do not want to update your RSS feed.
 */
$rss_enabled = true;

/* rss_filename:
 * Specify your output rss file name here.
 * The file will be generated under the same directory of BgmTweeter.
 */
$rss_filename = 'rss.xml';

/* rss_redirect (default: true):
 * If cron job is working, you can set it to false,
 * in this case the call of index.php will generate no rss output,
 * and the feed url should be set to the $rss_filename defined above.
 *
 * NOTE: Do not modify if you don't know what it is!
 */
$rss_redirect = true;

?>
