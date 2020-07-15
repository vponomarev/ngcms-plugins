<?php

// Protect against hack attempts
if (!defined('NGCMS')) {
    die('HAL');
}
//
// Voting plugin installer
//
plugins_load_config();
function plugin_voting_install($action)
{
    global $lang;
    if ($action != 'autoapply') {
        loadPluginLang('voting', 'config', '', '', ':');
    }
    $db_update = [
        [
            'table'  => 'vote',
            'action' => 'cmodify',
            'key'    => 'primary key(id)',
            'fields' => [
                ['action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'],
                ['action' => 'cmodify', 'name' => 'newsid', 'type' => 'int', 'params' => 'default 0'],
                ['action' => 'cmodify', 'name' => 'name', 'type' => 'char(50)'],
                ['action' => 'cmodify', 'name' => 'descr', 'type' => 'text'],
                ['action' => 'cmodify', 'name' => 'active', 'type' => 'int', 'params' => 'default 0'],
                ['action' => 'cmodify', 'name' => 'closed', 'type' => 'int', 'params' => 'default 0'],
                ['action' => 'cmodify', 'name' => 'regonly', 'type' => 'int', 'params' => 'default 0'],
            ],
        ],
        [
            'table'  => 'voteline',
            'action' => 'cmodify',
            'key'    => 'primary key(id)',
            'fields' => [
                ['action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'],
                ['action' => 'cmodify', 'name' => 'voteid', 'type' => 'int', 'params' => 'default 0'],
                ['action' => 'cmodify', 'name' => 'position', 'type' => 'int', 'params' => 'default 0'],
                ['action' => 'cmodify', 'name' => 'name', 'type' => 'char(50)'],
                ['action' => 'cmodify', 'name' => 'cnt', 'type' => 'int', 'params' => 'default 0'],
                ['action' => 'cmodify', 'name' => 'active', 'type' => 'int', 'params' => 'default 1'],
            ],
        ],
        [
            'table'  => 'votestat',
            'action' => 'cmodify',
            'key'    => 'primary key(id)',
            'fields' => [
                ['action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'],
                ['action' => 'cmodify', 'name' => 'userid', 'type' => 'int', 'params' => 'default 0'],
                ['action' => 'cmodify', 'name' => 'voteid', 'type' => 'int', 'params' => 'default 0'],
                ['action' => 'cmodify', 'name' => 'voteline', 'type' => 'int', 'params' => 'default 0'],
                ['action' => 'cmodify', 'name' => 'ip', 'type' => 'char(15)'],
                ['action' => 'cmodify', 'name' => 'dt', 'type' => 'datetime'],
            ],
        ],
    ];
    // Apply requested action
    switch ($action) {
        case 'confirm':
            generate_install_page('voting', $lang['voting:install#desc']);
            break;
        case 'autoapply':
        case 'apply':
            if (fixdb_plugin_install('voting', $db_update, 'install', ($action == 'autoapply') ? true : false)) {
                plugin_mark_installed('voting');
            }
            // Now we need to set some default params
            $params = [
                'access'     => 1,
                'refresh'    => 30,
                'history'    => 30,
                'rate_limit' => 0,
                'maxidle'    => 0,
                'maxwlen'    => 40,
                'maxlen'     => 500,
            ];
            foreach ($params as $k => $v) {
                extra_set_param('voting', $k, $v);
            }
            extra_commit_changes();
            break;
    }

    return true;
}
