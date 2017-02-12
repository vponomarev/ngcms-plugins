<?php
//
// Online Auto-Keys generator
//
function akeysGenerate($params) {

	global $userROW, $DSlist, $mysql, $twig;
	// Load library
	include_once(root . "/plugins/autokeys/lib/class.php");
	// Only registered users can use suggest
	if (!is_array($userROW))
		return array('status' => 0, 'errorCode' => 1, 'errorText' => 'Permission denied');
	// Check if suggest module is enabled
	if (pluginGetVariable('tags', 'suggestHelper')) {
		return array('status' => 0, 'errorCode' => 2, 'errorText' => 'Suggest helper is not enabled');
	}
	// Check if article is specified
	if ($params == '')
		return array('status' => 1, 'errorCode' => 0, 'data' => array($params, array()));
	// Generate keywords
	$words = akeysGetKeys(array('title' => iconv('UTF-8', 'windows-1251', $params['title']), 'content' => iconv('UTF-8', 'windows-1251', $params['content'])));

	// Return output
	return array('status' => 1, 'errorCode' => 0, 'data' => iconv('Windows-1251', 'UTF-8', $words));
}

rpcRegisterFunction('plugin.autokeys.generate', 'akeysGenerate');

