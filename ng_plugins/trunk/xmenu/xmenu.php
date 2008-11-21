<?php

//
// Plugin core
//

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

add_act('index', 'xmenu_core');

function xmenu_core() {
	global $config, $mysql, $tpl, $template, $langMonths;

	// Check what menus are active
	$activate=extra_get_param('xmenu','activate');
	if (!is_array($activate)) $activate = array();

	for ($i = 1; $i <= 9; $i++) {
		if (!$activate[$i]) {
			$template['vars']['plugin_xmenu_'.$i] = '';
			continue;
		}

		// Check what template should be used
		$skin=extra_get_param('xmenu', 'skin');
		if (!is_array($skin)) $skin = array();

		// Check mode
		$mode=extra_get_param('xmenu', 'mode');
		if (!is_array($mode)) $mode = array();

		if (!$mode[$i]) {
			// Show pre-defined category list

		} else {
			// Show subcategories of current category
		}

		$template['vars']['plugin_xmenu_'.$i] = 'menu '.$i;

	}
}
