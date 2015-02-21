<?php
/**
 * Properer Bangumi RSS (v1.1a)
 *
 * Code by Fishswing <me@swingworks.net>
 * http://www.swingworks.net
 */
 
require_once('feedcreator/feedcreator.class.php');

//echo '<!DOCTYPE html><html><head><meta charset="utf-8" /></head><body>';
function getmyrss()
{
	global $nickname, $bgm_id, $bgm_type, $rss_filename, $is_redirect;
	
	if ($bgm_type != 'progress' && $bgm_type != 'subject')
		die ("[ERROR] This type of Bangumi is not supported at this moment. We pray for updates.\n");
	
	$feed = file_get_contents("http://bgm.tv/feed/user/$bgm_id/timeline?type=$bgm_type");
	$file = file_get_contents("http://bgm.tv/user/$bgm_id/timeline?type=$bgm_type");
	if (empty($feed) || empty($file))
		die ("[ERROR] Cannot fetch timeline or feed. Bangumi is down.\n");
	
	switch ($bgm_type) {
		case 'progress':	$typetext = '进度'; break;
		case 'subject':		$typetext = '收藏'; break;
		default:			$typetext = '';
	}
	
	// define channel
	$myrss = new UniversalFeedCreator();
	$myrss->useCached();
	$myrss->title = "$nickname 的 Bangumi $typetext";
	$myrss->language = "zh-cn";
	$myrss->description = "$nickname 的 Bangumi 时间胶囊 ($typetext)";
	$myrss->link = "http://bgm.tv/user/$bgm_id/timeline?type=$bgm_type";
	$myrss->ttl = 720;
	$myrss->copyright = "2012 (c) Bangumi. Powered by propbgmrss.";
	
	// define channel image
	$myrss_image = new FeedImage();
	$myrss_image->title = "Bangumi 番组计划";
	$myrss_image->url = "http://chii.in/img/logo_rc1.png";
	$myrss_image->link = "http://bgm.tv";
	$myrss->image = $myrss_image;
	
	$feedoffset = 0; $fileoffset = 0;
	$is_first = 1; $ret = array();
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
		$is_multi = 0; 
		preg_match('/<span class="info_full clearit">\s*(<a\s+.*?<\/a>\s*)?(.+?\s*)<a\s+href="(.*?)".*?>(.*?)<\/a>/i',
					$fileitem, $m);
		$title1 = $m[2]; $title2 = $m[4]; $link1 = $m[3]; $filetitle = $title1 . $title2;
		if (preg_match('/<\/a>(\s*.*?([\d\.]+)\s*(of\s*[\d\.\?]+\s*)?话)\s*<p/i', $fileitem, $m)) {
			// pre-parse multiple completed episode (including books)
			$filetitle .= $m[1]; $watched_to = $m[2]; $is_multi = 1;
		}
		//echo "feedtitle: [$feedtitle]<br />filetitle: [$filetitle]<br />";
		
		// check and generate new feed item
		if ($feedtitle == $filetitle) {
			$title = ""; $desc = "";
			if ($is_multi == 1) {
				// multiple completed episode
				// eg: 完成了 偽物語 9 of 12 话 => 完成了 偽物語 第9话
				// eg: 读过 FAIRY TAIL 第277话
				$title = $title1 . $title2 . " 第${watched_to}话";
				$title2 .= getcntitle($link1, $title2);
				$desc = $title1 . "<a href=\"$link1\">" . $title2 . "</a> 第${watched_to}话";
				$desc_nolink = $title1 . $title2 . " 第${watched_to}话";
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
				$title = $title1 . $title3 . $title4;
				$title3 .= getcntitle($link1, $title3);
				$desc = $title1 . "<a href=\"$link2\">" . $title3 . "</a>" . $title5;
				$desc_nolink = $title1 . $title3 . $title6;
				$itemlink = $link2;
			}
			elseif (preg_match('/<div class="quote"><q>(.+?)<\/q>/i', $fileitem, $m)) {
				// favorites (subjects) with comment
				$title = $feedtitle;
				//$desc = $title1 . $title2 . "：$m[1]";
				$title2 .= getcntitle($link1, $title2);
				$desc = $title1 . "<a href=\"$link1\">" . $title2 . "</a>：$m[1]";
				$desc_nolink = $title1 . $title2 . "：$m[1]";
				$itemlink = $link1;
			}
			elseif ($bgm_type == 'subject') {
				// favorites (subjects) with no comment
				$title = $feedtitle;
				//$desc = $title1 . $title2;
				$title2 .= getcntitle($link1, $title2);
				$desc = $title1 . "<a href=\"$link1\">" . $title2 . "</a>";
				$desc_nolink = $title1 . $title2;
				$itemlink = $link1;
			}
			//echo "title: [$title]<br />descr: [$desc]<br />";
			
			// add feed item
			$link = "http://bgm.tv/user/$bgm_id/timeline";
			$myrss_item = new FeedItem();
			$myrss_item->title = $title;
			$myrss_item->link = $link;
			$myrss_item->guid = $guid;
			$myrss_item->date = $date;
			$myrss_item->description = $desc;
			$myrss_item->source = "http://bgm.tv";
			$myrss_item->author = $nickname;
			$myrss_item->authorURL = "http://bgm.tv/user/$bgm_id";
			$myrss_item->descriptionHtmlSyndicated = true;
			$myrss->addItem($myrss_item);
			
			// set return array
			if ($is_first == 1) {
				$ret['date'] = $date; $ret['title'] = $title;
				$ret['desc'] = $desc; $ret['desc_nolink'] = $desc_nolink;
				$ret['link'] = $link; $ret['itemlink'] = $itemlink;
				$is_first = 0;
			}
		}
		//else echo "[ERROR] A dismatch in title is found.<br /><br />";
	}
	
	// if using cron, setting $is_redirect to false is recommended
	$myrss->saveFeed("RSS2.0", $rss_filename, $is_redirect);
	
	return $ret;
}

function getcntitle($link, $jptitle)
{
	static $cachefile = '';
	if (empty($cachefile)) @ $cachefile = file_get_contents('titlecache.id');
	$slashedjptitle = preg_quote($jptitle, '/-');	// pre-process for regex
	if (preg_match("/###$slashedjptitle###(.*?)###\n/i", $cachefile, $m)) $cntitle = $m[1];
	else {
		$file = file_get_contents($link, NULL, NULL, -1, 10240);	// fetch 10kB
		if (empty($file)) return '';
		if (preg_match('/<h1 class=\"nameSingle\">\s*<a\s.+?title=\"(.+?)\".*?>.+?<\/a>/i', $file, $m)) {
			$cntitle = $m[1];
			// ignore if all Chinese characters
			if (preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $jptitle) || ($jptitle==$cntitle)) $cntitle = '';
			$cachefile .= "###$jptitle###$cntitle###\n";
			file_put_contents('titlecache.id', $cachefile);
		}
	}
	if (!empty($cntitle)) $cntitle = '(' . $cntitle . ')';
	return $cntitle;
}

//echo '</body></html>';
?>
