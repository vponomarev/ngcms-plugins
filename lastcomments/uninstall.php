<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
$ULIB = new urlLibrary();
$ULIB->loadConfig();
$ULIB->removeCommand('lastcomments', '');
$ULIB->removeCommand('lastcomments', 'rss');
if ($_REQUEST['action'] == 'commit') {
	plugin_mark_deinstalled('lastcomments');
	$ULIB->saveConfig();
} else {
	generate_install_page('lastcomments', "Bye-Bye!", 'deinstall');
}