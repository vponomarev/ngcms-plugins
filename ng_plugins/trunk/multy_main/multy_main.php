<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

add_act('index_post', 'plugin_multy_main');

function plugin_multy_main($params)
{
	global $SYSTEM_FLAGS, $confArray, $CurrentHandler, $catmap, $userROW;

	if (pluginGetVariable('multy_main', 'main') && isset($confArray['predefined']['REQUEST_URI']) && $confArray['predefined']['REQUEST_URI'] == '/'){
		$SYSTEM_FLAGS['template.main.name'] = pluginGetVariable('multy_main', 'main');
		return;
	}
	if($CurrentHandler['pluginName'] == 'news' && $CurrentHandler['handlerName'] == 'by.category'){
		$catname = '';
		if (isset($CurrentHandler['params']['category']))
			$catname = $CurrentHandler['params']['category'];
		else if (isset($CurrentHandler['params']['catid']))
			$catname = $catmap[intval($CurrentHandler['params']['catid'])];
		$category = pluginGetVariable('multy_main', 'category');
		if (array_key_exists($catname, $category)){
			$SYSTEM_FLAGS['template.main.name'] = $category[$catname];
			return;
		}
	}
	$main = '';
	if (!is_array($userROW)) $main = pluginGetVariable('multy_main', 'guest');
	else if ($userROW['status'] == 1) $main = pluginGetVariable('multy_main', 'admin');
	else if ($userROW['status'] == 2) $main = pluginGetVariable('multy_main', 'moder');
	else if ($userROW['status'] == 3) $main = pluginGetVariable('multy_main', 'journ');
	else if ($userROW['status'] == 4) $main = pluginGetVariable('multy_main', 'coment');
	if ($main) $SYSTEM_FLAGS['template.main.name'] = $main;
}
