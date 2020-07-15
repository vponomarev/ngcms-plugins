<?php

if (!defined('NGCMS')) {
    die('HAL');
}
function plugin_auth_basic_install($action)
{
    global $lang;
    if ($action != 'autoapply') {
        loadPluginLang('auth_basic', 'config', '', '', ':');
    }
    $db_update = [
        [
            'table'  => 'users_sessions',
            'action' => 'cmodify',
            'key'    => 'KEY `userUpdate` (`userID`, `authcookie`), KEY `users_auth` (`authcookie`)',
            'fields' => [
                ['action' => 'cmodify', 'name' => 'userID', 'type' => 'int(10)', 'params' => 'NOT NULL'],
                ['action' => 'cmodify', 'name' => 'ip', 'type' => 'varchar(15)', 'params' => 'NOT NULL default "0"'],
                ['action' => 'cmodify', 'name' => 'last', 'type' => 'int(10)', 'params' => 'NOT NULL default "0"'],
                ['action' => 'cmodify', 'name' => 'authcookie', 'type' => 'varchar(50)', 'params' => 'default NULL'],
            ],
        ],
    ];
    // Apply requested action
    switch ($action) {
        case 'confirm':
            generate_install_page('auth_basic', $lang['auth_basic:description']);
            break;
        case 'autoapply':
        case 'apply':
            if (fixdb_plugin_install('auth_basic', $db_update, 'install', ($action == 'autoapply') ? true : false)) {
                plugin_mark_installed('auth_basic');
            } else {
                return false;
            }
            break;
    }

    return true;
}
