<?php

// Protect against hack attempts
if (!defined('NGCMS')) {
    die('HAL');
}
//
// Configuration file for plugin
//
//
// Install script for plugin.
// $action: possible action modes
// 	confirm		- screen for installation confirmation
//	apply		- apply installation, with handy confirmation
//	autoapply       - apply installation in automatic mode [INSTALL script]
//
function plugin_comments_install($action)
{
    global $lang;
    if ($action != 'autoapply') {
        loadPluginLang('comments', 'config', '', '', ':');
    }
    // Fill DB_UPDATE configuration scheme
    $db_update = [
        [
            'table'  => 'news',
            'action' => 'cmodify',
            'fields' => [
                ['action' => 'cmodify', 'name' => 'allow_com', 'type' => 'tinyint(1)', 'params' => "default '2'"],
                ['action' => 'cmodify', 'name' => 'com', 'type' => 'int', 'params' => "default '0'"],
            ],
        ],
        [
            'table'  => 'category',
            'action' => 'cmodify',
            'fields' => [
                ['action' => 'cmodify', 'name' => 'allow_com', 'type' => 'tinyint(1)', 'params' => "default '2'"],
            ],
        ],
        [
            'table'  => 'comments',
            'action' => 'cmodify',
            'key'    => 'primary key(id), KEY `c_post` (`post`)',
            'fields' => [
                ['action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'],
                ['action' => 'cmodify', 'name' => 'postdate', 'type' => 'int', 'params' => "default '0'"],
                ['action' => 'cmodify', 'name' => 'post', 'type' => 'int', 'params' => "default '0'"],
                ['action' => 'cmodify', 'name' => 'name', 'type' => 'char(100)', 'params' => "default ''"],
                ['action' => 'cmodify', 'name' => 'author', 'type' => 'char(100)', 'params' => "default ''"],
                ['action' => 'cmodify', 'name' => 'author_id', 'type' => 'int', 'params' => "default '0'"],
                ['action' => 'cmodify', 'name' => 'mail', 'type' => 'char(100)', 'params' => "default ''"],
                ['action' => 'cmodify', 'name' => 'text', 'type' => 'text'],
                ['action' => 'cmodify', 'name' => 'answer', 'type' => 'text'],
                ['action' => 'cmodify', 'name' => 'ip', 'type' => 'char(15)', 'params' => "default ''"],
                ['action' => 'cmodify', 'name' => 'reg', 'type' => 'tinyint(1)', 'params' => "default '0'"],
            ],
        ],
        [
            'table'  => 'users',
            'action' => 'modify',
            'fields' => [
                ['action' => 'cmodify', 'name' => 'com', 'type' => 'int', 'params' => "default '0'"],
            ],
        ],
    ];
    // Apply requested action
    switch ($action) {
        case 'confirm':
            generate_install_page('comments', $lang['comments:desc_install']);
            break;
        case 'autoapply':
        case 'apply':
            if (fixdb_plugin_install('comments', $db_update, 'install', ($action == 'autoapply') ? true : false)) {
                plugin_mark_installed('comments');
            } else {
                return false;
            }
            // Now we need to set some default params
            $params = [
                'regonly'            => 0,
                'backorder'          => 0,
                'maxlen'             => 500,
                'maxwlen'            => 50,
                'multi'              => 1,
                'author_multi'       => 1,
                'timestamp'          => 'j.m.Y - H:i',
                'multipage'          => 1,
                'multi_mcount'       => 10,
                'multi_scount'       => 10,
                'inform_author'      => 0,
                'inform_admin'       => 0,
                'global_default'     => 1,
                'default_news'       => 2,
                'default_categories' => 2,
            ];
            foreach ($params as $k => $v) {
                extra_set_param('comments', $k, $v);
            }
            pluginsSaveConfig();
            break;
    }

    return true;
}
