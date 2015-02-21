<?php
/**
 * BgmTweeter - Cron Launcher
 */

/* Fill in with the complete url(s) here, then create a cron work in php panel:
 * php /home/<username>/<your_domain_files_path>/bgmtweeter/cron.php >/dev/null 2>&1
 *
 * e.g.:
 * (For Virtualmin manager:)
 * php /home/<username>/public_html/bgmtweeter/cron.php >/dev/null 2>&1
 * (For DA Panel:)
 * php /home/<username>/domains/<your_domain>/public_html/bgmtweeter/cron.php >/dev/null 2>&1
*/

$urls = array(
'http://<your_domain>/bgmtweeter/index.php'
//,'http://<your_domain>/bgmtweeter/another_one_if_needed_here.php'
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
