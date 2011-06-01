<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

if (!defined('LINKFEED_USER')){
	define('LINKFEED_USER', pluginGetVariable('ads_linkfeed', 'linkfeed_user'));
	include_once(root."/plugins/ads_linkfeed/inc/linkfeed.php");
}


add_act('index_post', 'plugin_ads_linkfeed');

function plugin_ads_linkfeed() {
	global $template;

	$count = abs(intval(pluginGetVariable('ads_linkfeed', 'bcount')));
	if (!$count) $count = 1;

	// Check if plugin should be activated on this domain
	if (trim(pluginGetVariable('ads_linkfeed', 'domains'))) {
		$found = false;
		$list = split("\n", pluginGetVariable('ads_linkfeed', 'domains'));
		foreach ($list as $dn) {
			if (trim($_SERVER['HTTP_HOST']) == trim($dn)) {
				$found = true;
				break;
			}
		}

		// Not found. Don't activate plugin
		if (!$found) {
			for ($i = 1; $i <= $count; $i++) {
				$template['vars']['plugin_ads_linkfeed_'.$i] = '';
			}
			return;
		}
	}

	$blen = array();
	foreach (split(",", pluginGetVariable('ads_linkfeed', 'blength')) as $br) {
		$blen []= intval(trim($br));
	}

	$flag_final   = false;
	$blen[$count] = 0;

	$linkfeed = new LinkfeedClient(array(
		'multi_site' => pluginGetVariable('ads_linkfeed', 'multisite')?true:false,
		'db_dir' => get_plugcache_dir('ads_linkfeed'),
	));

	for ($i = 1; $i <= $count; $i++) {
		if ($flag_final) {
			$template['vars']['plugin_ads_linkfeed_'.$i] = '';
		} else {
			if (!$blen[$i-1]) {
				$template['vars']['plugin_ads_linkfeed_'.$i] = $linkfeed->return_links($blen[$i-1]);
			} else {
				$flag_final = true;
				$template['vars']['plugin_ads_linkfeed_'.$i] = $linkfeed->return_links();
			}
		}
	}
}
