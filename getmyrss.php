<?php
/**
 * BgmTweeter - Main functions
 */

require_once('feedcreator/feedcreator.class.php');

// echo '<!DOCTYPE html><html><head><meta charset="utf-8" /></head><body>';  // DEBUG
function getmyrss()
{
	global $nickname, $bgm_id, $bgm_type;
	global $rss_title, $name_pattern;
	global $rss_enabled, $rss_filename, $rss_redirect;

	// check user settings
	if ($bgm_type != 'progress' && $bgm_type != 'subject' && $bgm_type != 'all')
		die ("[ERROR] This type of Bangumi is not supported.\n");

	if ($bgm_type != 'all') {
		$feed_url = "http://bgm.tv/feed/user/$bgm_id/timeline?type=$bgm_type";
		$file_url = "http://bgm.tv/user/$bgm_id/timeline?type=$bgm_type";
	}
	else {
		$feed_url = "http://bgm.tv/feed/user/$bgm_id/timeline";
		$file_url = "http://bgm.tv/user/$bgm_id/timeline";
	}
	$link_url = "http://bgm.tv/user/$bgm_id/timeline";
	$feed = file_get_contents($feed_url);
	$file = file_get_contents($file_url);
	if (empty($feed) || empty($file))
		die ("[ERROR] Cannot fetch timeline or feed. Bangumi seems down.\n");

	if ($rss_enabled) {
		// define rss channel
		$myrss = new UniversalFeedCreator();
		$myrss->useCached();
		$myrss->title = $rss_title;
		$myrss->language = "zh-cn";
		$myrss->description = "$rss_title, the Bangumi feed generated by BgmTweeter";
		$myrss->link = $file_url;
		$myrss->ttl = 720;
		$myrss->copyright = "2015 (c) Bangumi. Powered by BgmTweeter.";

		// define rss channel image
		$myrss_image = new FeedImage();
		$myrss_image->title = "Bangumi 番组计划";
		$myrss_image->url = "http://chii.in/img/logo_rc1.png";
		$myrss_image->link = "http://bgm.tv";
		$myrss->image = $myrss_image;
	}

	$feedoffset = 0; $fileoffset = 0;
	$is_first = 1; $ret = array();
	// using original feed as offset base point
	while (preg_match('/<item>.+?<\/item>/si', $feed, $m, PREG_OFFSET_CAPTURE, $feedoffset)) {
		// get original feed info
		$feeditem = $m[0][0]; $feedoffset = $m[0][1] + strlen($feeditem);
		preg_match('/<title>(.+?)<\/title>/i', $feeditem, $m); $feedtitle = $m[1];
		if (preg_match('/<pubDate>(.+?)(\+0000|GMT)<\/pubDate>/i', $feeditem, $m))
			$date = $m[1] . 'GMT';
		else die("pubDate does not match GMT rule.\n");
		preg_match('/<guid\s+.*?>(.+?)<\/guid>/i', $feeditem, $m); $guid = $m[1];

		// get html file info
		if (!preg_match('/class="clearit tml_item">.+?<\/li>/si',
						$file, $m, PREG_OFFSET_CAPTURE, $fileoffset)) break;
		$fileitem = $m[0][0]; $fileoffset = $m[0][1] + strlen($fileitem);
		$is_multi_episodes = 0;
		preg_match('/<span class="info_full clearit">\s*(<a\s+.*?<\/a>\s*)?(.+?\s*)<a\s+href="(.*?)".*?>(.*?)<\/a>/i',
					$fileitem, $m);
		$title1 = $m[2]; $title2 = $m[4]; $link1 = $m[3]; $filetitle = $title1 . $title2;
		if (preg_match('/<\/a>(\s*.*?([\d\.]+)\s*(of\s*[\d\.\?]+\s*)?话)\s*<p/i', $fileitem, $m)) {
			// pre-parse multiple completed episode (including books)
			$filetitle .= $m[1]; $watched_to = $m[2]; $is_multi_episodes = 1;
		}
		//echo "feedtitle: [$feedtitle]<br />filetitle: [$filetitle]<br />";  // DEBUG

		// check and generate new feed item
		if ($feedtitle == $filetitle) {
			$title = ""; $content = "";
			if ($is_multi_episodes == 1) {
				// multiple completed episode
				// eg: 完成了 偽物語 9 of 12 话 => 完成了 偽物語 第9话
				// eg: 读过 FAIRY TAIL 第277话
				$title2 = getitemname($name_pattern, $link1, $title2);
				$title = $title1 . $title2 . " 第${watched_to}话";
				$content = $title1 . "<a href=\"$link1\">" . $title2 . "</a> 第${watched_to}话";
				$content_nolink = $title;
				$itemlink = $link1;
			}
			elseif (preg_match('/.+<a\s+href="(.*?)"\s+class="tip">(.+?)<\/a>/i', $fileitem, $m)) {
				// single completed episode
				// eg: 看过 ep.9 つきひフェニックス 其ノ貳 => 看过 偽物語 第9话：つきひフェニックス 其ノ貳
				$link2 = $m[1]; $title3 = $m[2];
				preg_match('/ep\.([\d\.]+)\s*(.*?)\s*$/', $title2, $m);
				$title4 = " 第$m[1]话";
				$title5 = " 第$m[1]话：<a href=\"$link1\">$m[2]</a>";
				$title6 = " 第$m[1]话：$m[2]";
				$title3 = getitemname($name_pattern, $link1, $title3);
				$title = $title1 . $title3 . $title4;
				$content = $title1 . "<a href=\"$link2\">" . $title3 . "</a>" . $title5;
				$content_nolink = $title1 . $title3 . $title6;
				$itemlink = $link2;
			}
			elseif (preg_match('/<div class="quote"><q>(.+?)<\/q>/i', $fileitem, $m)) {
				// favorites (subjects) with comment
				$title2 = getitemname($name_pattern, $link1, $title2);
				$title = $title1 . $title2;
				$content = $title1 . "<a href=\"$link1\">" . $title2 . "</a>：$m[1]";
				$content_nolink = $title1 . $title2 . "：$m[1]";
				$itemlink = $link1;
			}
			elseif ($bgm_type == 'subject' || $bgm_type == 'all') {
				// favorites (subjects) with no comment
				$title2 = getitemname($name_pattern, $link1, $title2);
				$title = $title1 . $title2;
				$content = $title1 . "<a href=\"$link1\">" . $title2 . "</a>";
				$content_nolink = $title;
				$itemlink = $link1;
			}
			// echo "title: [$title]<br />content: [$content]<br />";  // DEBUG

			// add feed item
			if ($rss_enabled) {
				$myrss_item = new FeedItem();
				$myrss_item->title = $title;
				$myrss_item->link = $link_url;
				$myrss_item->guid = $guid;
				$myrss_item->date = $date;
				$myrss_item->description = $content;
				$myrss_item->source = "http://bgm.tv";
				$myrss_item->author = $nickname;
				$myrss_item->authorURL = "http://bgm.tv/user/$bgm_id";
				$myrss_item->descriptionHtmlSyndicated = true;
				$myrss->addItem($myrss_item);
			}

			// set return array
			if ($is_first == 1) {
				$ret['date'] = $date; $ret['title'] = $title;
				$ret['content'] = $content; $ret['content_nolink'] = $content_nolink;
				$ret['link'] = $link_url; $ret['itemlink'] = $itemlink;
				$ret['rsstitle'] = $rss_title;
				$is_first = 0;
			}
		}
		//else echo "[ERROR] A dismatch in title has been found.<br /><br />";
	}

	// if using cron, setting $rss_redirect to false is recommended
	if ($rss_enabled)
		$myrss->saveFeed("RSS2.0", $rss_filename, $rss_redirect);

	return $ret;
}

function getitemname($pattern, $link, $jptitle)
{
	static $cachefile = '';
	if (empty($cachefile)) @ $cachefile = file_get_contents('namecache.id');
	$slashedjptitle = preg_quote($jptitle, '/-');	// pre-process for regex
	if (preg_match("/###$slashedjptitle###(.*?)###\n/i", $cachefile, $m)) $chtitle = $m[1];
	else {
		$file = file_get_contents($link, NULL, NULL, -1, 10240);	// fetch 10kB
		if (empty($file)) return '';
		if (preg_match('/<h1 class=\"nameSingle\">\s*<a\s.+?title=\"(.+?)\".*?>.+?<\/a>/i', $file, $m)) {
			$chtitle = $m[1];
			$cachefile .= "###$jptitle###$chtitle###\n";
			file_put_contents('titlecache.id', $cachefile);
		}
	}
	// ignore if same as jptitle or all Chinese characters
	if (preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $jptitle) || ($jptitle==$chtitle))
		$pattern = preg_replace('/{{\(chname\)}}/i', "", $pattern);
	else
		$pattern = preg_replace('/{{\(chname\)}}/i', " ($chtitle)", $pattern);
	$pattern = preg_replace('/{{name}}/i', $jptitle, $pattern);
	$pattern = preg_replace('/{{chname}}/i', $chtitle, $pattern);
	return $pattern;
}

// echo '</body></html>';  // DEBUG
?>
