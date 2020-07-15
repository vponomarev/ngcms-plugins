<?php

// Protect against hack attempts
if (!defined('NGCMS')) {
    die('HAL');
}
//
// Configuration file for plugin
//
plugins_load_config();
$db_update = [
    [
        'table'  => 'news',
        'action' => 'modify',
        'fields' => [
            ['action' => 'drop', 'name' => 'com'],
            ['action' => 'drop', 'name' => 'allow_com'],
        ],
    ],
    [
        'table'  => 'users',
        'action' => 'modify',
        'fields' => [
            ['action' => 'drop', 'name' => 'com'],
        ],
    ],
    [
        'table'  => 'comments',
        'action' => 'drop',
    ],
];
if ($_REQUEST['action'] == 'commit') {
    // If submit requested, do config save
    if (fixdb_plugin_install('comments', $db_update, 'deinstall')) {
        plugin_mark_deinstalled('comments');
    }
} else {
    $text = $lang['comments_desc_uninstall'];
    generate_install_page('comments', $text, 'deinstall');
}
