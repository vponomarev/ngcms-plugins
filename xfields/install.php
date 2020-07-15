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
            ['action' => 'cmodify', 'name' => 'xfields', 'type' => 'text', 'params' => 'default null'],
        ],
    ],
    [
        'table'  => 'category',
        'action' => 'modify',
        'fields' => [
            ['action' => 'cmodify', 'name' => 'xf_group', 'type' => 'char(40)', 'params' => 'default 0'],
        ],
    ],
    [
        'table'  => 'users',
        'action' => 'modify',
        'fields' => [
            ['action' => 'cmodify', 'name' => 'xfields', 'type' => 'text', 'params' => 'default null'],
        ],
    ],
    [
        'table'  => 'xfields',
        'action' => 'cmodify',
        'key'    => 'primary key(id)',
        'fields' => [
            ['action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'],
            ['action' => 'cmodify', 'name' => 'linked_ds', 'type' => 'int', 'params' => 'default 0'],
            ['action' => 'cmodify', 'name' => 'linked_id', 'type' => 'int', 'params' => 'default 0'],
            ['action' => 'cmodify', 'name' => 'xfields', 'type' => 'text', 'params' => 'default null'],
        ],
    ],
];
if ($_REQUEST['action'] == 'commit') {
    // If submit requested, do config save
    if (fixdb_plugin_install('xfields', $db_update)) {
        plugin_mark_installed('xfields');
    }
} else {
    $text = $lang['xfields_desc_install'];
    generate_install_page('xfields', $text);
}
