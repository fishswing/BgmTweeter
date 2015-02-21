<?php
/**
 * Properer Bangumi RSS - Sina Weibo Sender
 *
 * Code by Fishswing <me@swingworks.net>
 * http://www.swingworks.net
 */

/*
 * This file must be included by its upper directory
 */
require_once( 'weibo/config.php' );
require_once( 'weibo/saetv2.ex.class.php' );

function sendweibo($item, $id_name)
{
	global $weibo_pattern;
	$pattern = $weibo_pattern;
	
	$idfile_name = "weibo/weibo-$id_name.id";
	$date = $item['date'];
	$title = $item['title'];
	$desc = $item['desc_nolink'];		// use $desc_nolink since v0.8
	$link = $item['link'];
	$itemlink = $item['itemlink'];
	
	// set timestamp
	$timestamp = strtotime($date);
	@$idfile = file($idfile_name, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if (empty($idfile)) {
		file_put_contents($idfile_name, $timestamp . "\n$date\ninitialized");
	}
	elseif ($timestamp > $idfile[0]) {
		/*$desc = preg_replace('/<a\s.*?>(.*?)<\/a>/si', "$1", $desc);	no need since v0.8*/
		$pattern = preg_replace('/{{title}}/i', $title, $pattern);	// $limit==-1 by default
		$pattern = preg_replace('/{{content}}/i', $desc, $pattern);
		$pattern = preg_replace('/{{link}}/i', $link, $pattern, -1, $link_cnt);
		$pattern = preg_replace('/{{itemlink}}/i', $itemlink, $pattern, -1, $itemlink_cnt);
		
		// (v1.0a) check pattern length and cut content for weibo
		$link_len = mb_strlen($link, 'UTF-8');
		$itemlink_len = mb_strlen($itemlink, 'UTF-8');
		$pattern_len = mb_strlen($pattern, 'UTF-8') - ($link_len-11)*$link_cnt - ($itemlink_len-11)*$itemlink_cnt;
		if ($pattern_len > 140) {
			$desc_len = mb_strlen($desc, 'UTF-8') - $pattern_len + 140 - 2;
			$desc_len = ($desc_len < 0 ? 0 : $desc_len);
			$desc = mb_substr($desc, 0, $desc_len, 'UTF-8') . '...';
			$pattern = $weibo_pattern;
			$pattern = preg_replace('/{{title}}/i', $title, $pattern);	// $limit==-1 by default
			$pattern = preg_replace('/{{content}}/i', $desc, $pattern);
			$pattern = preg_replace('/{{link}}/i', $link, $pattern);
			$pattern = preg_replace('/{{itemlink}}/i', $itemlink, $pattern);
		}
				
		@ $last_key = json_decode(file_get_contents('weibo/weibo.oauth'), true);
		//@ $wb_handler = new WeiboClientV2(WB_AKEY, WB_SKEY, $_SESSION['token']['access_token']);
		@ $wb_handler = new WeiboClientV2(WB_AKEY, WB_SKEY, $last_key['access_token']);
		@ $wb_ret = $wb_handler->update($pattern);
		if ( isset($wb_ret['error_code']) && $wb_ret['error_code'] > 0 ) {
			$errmsg = "(" . $wb_ret['error_code'] . ") " . $wb_ret['error'];
			file_put_contents($idfile_name, $timestamp . "\n$date\nweibo failed: $errmsg");
			//file_put_contents($idfile_name, $timestamp . "\n$pattern\n\npattern_len=$pattern_len\nlink_cnt=$link_cnt,$itemlink_cnt\ndesc_len=$desc_len", FILE_APPEND);
		}
		else file_put_contents($idfile_name, $timestamp . "\n$date\nweibo ok");
	}
}
?>
