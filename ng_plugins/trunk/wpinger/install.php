<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Install script for plugin.
// $action: possible action modes
// 	confirm		- screen for installation confirmation
//	apply		- apply installation, with handy confirmation
//	autoapply       - apply installation in automatic mode [INSTALL script]
//
plugins_load_config();
function plugin_wpinger_install($action) {
	global $lang;

	// Apply requested action
	switch ($action) {
		case 'confirm':
			generate_install_page('wpinger', "Плагин позволяет информировать внешние системы (обычно - поисковые сервера) об обновлениях на вашем сайте.<br/><br/>");
			break;
		case 'autoapply':
		case 'apply':
			// Now we need to set some default params
			$params = array(
				'proxy'		=> 1,
				'urls'		=> "http://ping.blogs.yandex.ru/RPC2\nhttp://blogsearch.google.ru/ping/RPC2",
			);

			foreach ($params as $k => $v) {
				extra_set_param('wpinger', $k, $v);
			}
			
			if (fixdb_plugin_install('wpinger', array())) {
				plugin_mark_installed('wpinger');
				extra_commit_changes();
			}

			break;
	}
	return true;
}
