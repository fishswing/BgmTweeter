<?php
/**
 * Properer Bangumi RSS - Cron Launcher
 * Code by Fishswing <me@swingworks.net>
 */

/* Fill in with the complete url(s) here, then create a cron work in php panel:
php /home/<username>/domains/<yourdomain>/public_html/propbgmrss/cron.php >/dev/null 2>&1
*/
$urls = array(
'http://<yourdomain>/propbgmrss/index.php'
//,'http://<yourdomain>/propbgmrss/another_url_if_needed_here.php'
);


/* Do not modify the following lines */

if (empty($urls)) {
	die('Please configure cron.php first.\n');
}

foreach ($urls as $url) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_exec($ch);
	curl_close($ch);
}
?>
