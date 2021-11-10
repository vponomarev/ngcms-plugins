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
LoadPluginLang('nsched', 'install');

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
    if (fixdb_plugin_install('nsched', $db_update)) {
        plugin_mark_installed('nsched');
    }
} else {
    $text = 'Плагин <b>nsched</b> позволяет публиковать/снимать с публикации новости по расписанию.<br><br>';
    generate_install_page('nsched', $text);
}
