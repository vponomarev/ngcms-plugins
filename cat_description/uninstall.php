<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
if ($_REQUEST['action'] == 'commit') {
	plugin_mark_deinstalled('cat_description');
} else {
	$text = 'CРµР№С‡Р°СЃ РїР»Р°РіРёРЅ Р±СѓРґРµС‚ СѓРґР°Р»РµРЅ';
	generate_install_page('cat_description', $text, 'deinstall');
}