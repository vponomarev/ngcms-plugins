<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

class WPingerNewsfilter extends NewsFilter {

	function addNewsNotify(&$tvars, $SQL, $newsid) {

		if ($SQL['approve'])
			plugin_wpinger_servicePing();

		return 1;
	}

	function editNewsNotify($newsID, $SQLnews, &$SQLnew, &$tvars) {

		if ($SQLnew['approve'] && !$SQLnews['approve'])
			plugin_wpinger_servicePing();

		return 1;
	}

	// Mass news modify
	function massModifyNewsNotify($idList, $setValue, $currentData) {

		if (isset($setValue['approve']) && $setValue['approve'])
			plugin_wpinger_servicePing();

		return 1;
	}
}

register_filter('news', 'wpinger', new WPingerNewsFilter);
function plugin_wpinger_servicePing() {

	global $config;
	// Determine SITEMAP URL
	$smapURL = generateLink('core', 'plugin', array('plugin' => 'rss_export'), array(), false, true);
	// Determine servicing mode:
	// * DIRECT
	// * VIA PROXY
	$proxyMode = pluginGetVariable('wpinger', 'proxy');
	// Generate a list of services that should be pinged
	$serviceList = array();
	foreach (explode("\n", pluginGetVariable('wpinger', 'urls')) as $serviceURL) {
		if (preg_match('#^http\:\/\/#', $serviceURL, $null)) {
			$serviceList[] = str_replace(array("\n", "\r"), '', $serviceURL);
		}
	}
	// Do not do anything if service list is empty
	if (!count($serviceList))
		return;
	// Load and init HTTP GETTER library
	@include_once root . 'includes/inc/httpget.inc.php';
	$req = new http_get();
	$content = '<?xml version="1.0" encoding="UTF-8"?>';
	// Decide calling mode - via proxy or directly
	if (pluginGetVariable('wpinger', 'proxy')) {
		$content .= '<methodCall><methodName>services.weblog.pingerProxy</methodName>' .
			'<params>' .
			'<param><value><string>' . $config['home_title'] . '</string></value></param>' .
			'<param><value><string>' . $smapURL . '</string></value></param>' .
			'<param><value><array><data>';
		foreach ($serviceList as $url) {
			$content .= '<value><string>' . $url . '</string></value>';
		}
		$content .= '</data></array></value></param>' .
			'</params>' .
			'</methodCall>';
		$RPC_Service_URL = 'http://ngcms.ru/services/RPC/01/';
		$vms = $req->request('POST', $RPC_Service_URL, $content, 5);
	} else {
		// Generate XML content
		$content = '<?xml version="1.0" encoding="UTF-8"?>' .
			'<methodCall><methodName>weblogUpdates.ping</methodName>' .
			'<params>' .
			'<param><value>' . $config['home_title'] . '</value></param>' .
			'<param><value>' . $smapURL . '</value></param>' .
			'</params>' .
			'</methodCall>';
		foreach ($serviceList as $url) {
			// Send POST request
			$vms = $req->request('POST', $url, $content, 5);
		}
	}
}