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
LoadPluginLang($plugin, 'install', '', '', ':');

$db_update = [
    [
        'table' => 'news',
        'action' => 'modify',
        'fields' => [
            ['action' => 'cmodify', 'name' => 'nsched_activate', 'type' => 'datetime'],
            ['action' => 'cmodify', 'name' => 'nsched_deactivate', 'type' => 'datetime'],
        ],
    ],
];

if ($_REQUEST['action'] == 'commit') {
    // If submit requested, do config save
    if (fixdb_plugin_install($plugin, $db_update)) {
        plugin_mark_installed($plugin);
    }
} else {
    generate_install_page($plugin, $lang[$plugin.':description']);
}
