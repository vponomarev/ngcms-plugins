<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

add_act('index_post', 'plugin_multi_main');

function plugin_multi_main($params) {
	global $SYSTEM_FLAGS, $confArray, $CurrentHandler, $catmap, $userROW;

	// ** MAIN PAGE
	if (pluginGetVariable('multi_main', 'main') && isset($confArray['predefined']['REQUEST_URI']) && $confArray['predefined']['REQUEST_URI'] == '/'){
		$SYSTEM_FLAGS['template.main.name'] = pluginGetVariable('multi_main', 'main');
		return;
	}

	// ** NEWS CATEGORY PAGE
	if($CurrentHandler['pluginName'] == 'news' && $CurrentHandler['handlerName'] == 'by.category'){
		$catname = '';
		if (isset($CurrentHandler['params']['category']))
			$catname = $CurrentHandler['params']['category'];
		else if (isset($CurrentHandler['params']['catid']))
			$catname = $catmap[intval($CurrentHandler['params']['catid'])];
		$category = pluginGetVariable('multi_main', 'category');
		if (is_array($category) && array_key_exists($catname, $category)){
			$SYSTEM_FLAGS['template.main.name'] = $category[$catname];
			return;
		}
	}

	// ** SHOW SPECIFIC NEWS PAGE
	if (($CurrentHandler['pluginName'] == 'news') && ($CurrentHandler['handlerName'] == 'news')) {
		$calt = $SYSTEM_FLAGS['news']['currentCategory.alt'];
		$category = pluginGetVariable('multi_main', 'category');
		if (is_array($category) && array_key_exists($calt, $category)){
			$SYSTEM_FLAGS['template.main.name'] = $category[$calt];
		}
		return;
	}

	// ** DEFAULT ACTION
	if (!isset($SYSTEM_FLAGS['template.main.name'])){
		$main = '';
		if (!is_array($userROW)) $main = pluginGetVariable('multi_main', 'guest');
		else if ($userROW['status'] == 1) $main = pluginGetVariable('multi_main', 'admin');
		else if ($userROW['status'] == 2) $main = pluginGetVariable('multi_main', 'moder');
		else if ($userROW['status'] == 3) $main = pluginGetVariable('multi_main', 'journ');
		else if ($userROW['status'] == 4) $main = pluginGetVariable('multi_main', 'coment');
		if ($main) $SYSTEM_FLAGS['template.main.name'] = $main;
	}
}
