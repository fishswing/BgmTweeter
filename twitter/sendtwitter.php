<?php
/**
 * BgmTweeter - Twitter Sender
 *
 * Code by Fishswing <me@swingworks.net>
 * http://www.swingworks.net
 */

/*
 * This file must be included by its upper directory
 */
require_once( 'twitter/config.php' );
require_once( 'twitter/twitteroauth/twitteroauth.php' );

function sendtwitter($item, $id_name)
{
	global $twitter_pattern;
	$pattern = $twitter_pattern;

	$idfile_name = "twitter/twitter-$id_name.id";
	$date = $item['date'];
	$rsstitle = $item['rsstitle'];
	$content = $item['content_nolink'];		// use $content_nolink since v0.8
	$link = $item['link'];
	$itemlink = $item['itemlink'];

	// set timestamp
	$timestamp = strtotime($date);
	@$idfile = file($idfile_name, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if (empty($idfile)) {
		file_put_contents($idfile_name, $timestamp . "\n$date\ninitialized");
	}
	elseif ($timestamp > $idfile[0]) {
		/*$content = preg_replace('/<a\s.*?>(.*?)<\/a>/si', "$1", $content);	no need since v0.8*/
		$pattern = preg_replace('/{{rsstitle}}/i', $rsstitle, $pattern);	// $limit==-1 by default
		$pattern = preg_replace('/{{content}}/i', $content, $pattern);
		$pattern = preg_replace('/{{link}}/i', $link, $pattern, -1, $link_cnt);
		$pattern = preg_replace('/{{itemlink}}/i', $itemlink, $pattern, -1, $itemlink_cnt);

		// (v1.0a) check pattern length and cut content for twitter
		$link_len = mb_strlen($link, 'UTF-8');
		$itemlink_len = mb_strlen($itemlink, 'UTF-8');
		$pattern_len = mb_strlen($pattern, 'UTF-8') - ($link_len-25)*$link_cnt - ($itemlink_len-25)*$itemlink_cnt;
		if ($pattern_len > 140) {
			$desc_len = mb_strlen($content, 'UTF-8') - $pattern_len + 140 - 3;
			$desc_len = ($desc_len < 0 ? 0 : $desc_len);
			$content = mb_substr($content, 0, $desc_len, 'UTF-8') . '...';
			$pattern = $twitter_pattern;
			$pattern = preg_replace('/{{rsstitle}}/i', $rsstitle, $pattern);	// $limit==-1 by default
			$pattern = preg_replace('/{{content}}/i', $content, $pattern);
			$pattern = preg_replace('/{{link}}/i', $link, $pattern);
			$pattern = preg_replace('/{{itemlink}}/i', $itemlink, $pattern);
		}

		@ $access_token = json_decode(file_get_contents('twitter/twitter.oauth'), true);
		@ $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
		// $content = $connection->get('account/verify_credentials');
		@ $response = $connection->post('statuses/update', array('status' => $pattern));
		if ($connection->http_code == '200' || $connection->http_code == '304') {
			file_put_contents($idfile_name, $timestamp . "\n$date\ntwitter ok");
		}
		else {
			$errmsg = print_r($response, TRUE);
			file_put_contents($idfile_name, $timestamp . "\n$date\ntwitter failed:\n$errmsg");
			//file_put_contents($idfile_name, $timestamp . "\n$pattern\n\npattern_len=$pattern_len\nlink_cnt=$link_cnt,$itemlink_cnt\ndesc_len=$desc_len", FILE_APPEND);
		}
	}
}
?>
