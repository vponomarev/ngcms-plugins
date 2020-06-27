<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
function plugin_lastcomments_install($action) {

	global $config, $lang;
	$ULIB = new urlLibrary();
	$ULIB->loadConfig();
	$ULIB->registerCommand('lastcomments', '',
		array(
			'vars'  =>
				array(),
			'descr' => array('russian' => 'РЎС‚СЂР°РЅРёС†Р° СЃ РїРѕСЃР»РµРґРЅРёРјРё РєРѕРјРјРµРЅС‚Р°СЂРёСЏРјРё'),
		)
	);
	$ULIB->registerCommand('lastcomments', 'rss',
		array(
			'vars'  =>
				array(),
			'descr' => array('russian' => 'Rss Р»РµРЅС‚Р° РїРѕСЃР»РµРґРЅРёС… РєРѕРјРјРµРЅС‚Р°СЂРёРµРІ'),
		)
	);
	// Apply requested action
	switch ($action) {
		case 'confirm':
			generate_install_page('lastcomments', "GO GO GO");
			break;
		case 'autoapply':
		case 'apply':
			if (fixdb_plugin_install('lastcomments', array(), 'install', ($action == 'autoapply') ? true : false)) {
				plugin_mark_installed('lastcomments');
			} else {
				return false;
			}
			$ULIB->saveConfig();
			break;
	}

	return true;
}






