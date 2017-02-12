<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
if (!defined('_SAPE_USER')) {
	define('_SAPE_USER', pluginGetVariable('ads_sape', 'sape_user'));
	include_once(root . "/plugins/ads_sape/inc/sape.php");
}
add_act('index_post', 'plugin_ads_sape');
function plugin_ads_sape() {

	global $template;
	$count = abs(intval(pluginGetVariable('ads_sape', 'bcount')));
	if (!$count) $count = 1;
	// Check if plugin should be activated on this domain
	if (trim(pluginGetVariable('ads_sape', 'domains'))) {
		$found = false;
		$list = explode("\n", pluginGetVariable('ads_sape', 'domains'));
		foreach ($list as $dn) {
			if (trim($_SERVER['HTTP_HOST']) == trim($dn)) {
				$found = true;
				break;
			}
		}
		// Not found. Don't activate plugin
		if (!$found) {
			for ($i = 1; $i <= $count; $i++) {
				$template['vars']['plugin_ads_sape_' . $i] = '';
			}

			return;
		}
	}
	$blen = array();
	foreach (explode(",", pluginGetVariable('ads_sape', 'blength')) as $br) {
		$blen [] = intval(trim($br));
	}
	$flag_final = false;
	$blen[$count] = 0;
	$sape = new SAPE_client(array(
		'multi_site' => pluginGetVariable('ads_sape', 'multisite') ? true : false,
		'db_dir'     => get_plugcache_dir('ads_sape'),
	));
	for ($i = 1; $i <= $count; $i++) {
		if ($flag_final) {
			$template['vars']['plugin_ads_sape_' . $i] = '';
		} else {
			if ($blen[$i - 1]) {
				$template['vars']['plugin_ads_sape_' . $i] = $sape->return_links($blen[$i - 1]);
			} else {
				$flag_final = true;
				$template['vars']['plugin_ads_sape_' . $i] = $sape->return_links();
			}
		}
	}
}
