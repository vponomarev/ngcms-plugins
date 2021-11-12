<?php

// #====================================================================================#
// # Наименование плагина: nsched [ News SCHEDuller ]                                   #
// # Разрешено к использованию с: Next Generation CMS                                   #
// # Автор: Vitaly A Ponomarev, vp7@mail.ru                                             #
// #====================================================================================#
// #====================================================================================#
// # Инсталл скрипт плагина                                                             #
// #====================================================================================#

// Protect against hack attempts
if (! defined('NGCMS')) {
    die('HAL');
}

pluginsLoadConfig();
LoadPluginLang('nsched', 'deinstall', '', '', ':');

$db_update = [
    [
        'table' => 'news',
        'action' => 'modify',
        'fields' => [
            ['action' => 'drop', 'name' => 'nsched_activate'],
            ['action' => 'drop', 'name' => 'nsched_deactivate'],
        ],
    ],
];

if ($_REQUEST['action'] == 'commit') {
    // If submit requested, do config save
    if (fixdb_plugin_install('nsched', $db_update, 'deinstall')) {
        plugin_mark_deinstalled('nsched');
    }
} else {
    generate_install_page('nsched', $lang[$plugin.':description'], 'deinstall');
}
