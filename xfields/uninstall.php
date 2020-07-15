<?php

// Protect against hack attempts
if (!defined('NGCMS')) {
    die('HAL');
}
//
// Configuration file for plugin
//
plugins_load_config();
LoadPluginLang('xfields', 'config');
$db_update = [
    [
        'table'  => 'news',
        'action' => 'modify',
        'fields' => [
            ['action' => 'drop', 'name' => 'xfields', 'type' => 'text'],
        ],
    ],
    [
        'table'  => 'category',
        'action' => 'modify',
        'fields' => [
            ['action' => 'drop', 'name' => 'xf_group', 'type' => 'text'],
        ],
    ],
    [
        'table'  => 'users',
        'action' => 'modify',
        'fields' => [
            ['action' => 'drop', 'name' => 'xfields', 'type' => 'text'],
        ],
    ],
];
if ($_REQUEST['action'] == 'commit') {
    // If submit requested, do config save
    if (fixdb_plugin_install('xfields', $db_update, 'deinstall')) {
        plugin_mark_deinstalled('xfields');
    }
} else {
    $text = $lang['xfields_desc_uninstall'];
    generate_install_page('xfields', $text, 'deinstall');
}
