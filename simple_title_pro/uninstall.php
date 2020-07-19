<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
pluginsLoadConfig();
if ($_REQUEST['action'] == 'commit') {
	plugin_mark_deinstalled($plugin);
} else {
	generate_install_page($plugin, 'Удаление плагина', 'deinstall');
}