<?php

// Protect against hack attempts
if (!defined('NGCMS')) {
    die('HAL');
}
//
// Configuration file for plugin
//
plugins_load_config();
loadPluginLang('feedback', 'config', '', '', ':');
$db_update = [
    [
        'table'  => 'feedback',
        'action' => 'drop',
    ],
];
if ($_REQUEST['action'] == 'commit') {
    if (fixdb_plugin_install($plugin, $db_update, 'deinstall')) {
        plugin_mark_deinstalled($plugin);
    }
} else {
    generate_install_page($plugin, $lang['feedback:text.deinstall'], 'deinstall');
}
