<?php

if (!defined('NGCMS')) {
    die('HAL');
}
function plugin_subscribe_comments_install($action)
{
    global $mysql;
    $install = true;
    $db_update = [
        [
            'table'  => 'subscribe_comments',
            'action' => 'cmodify',
            'key'    => 'primary key(id)',
            'fields' => [
                ['action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'],
                ['action' => 'cmodify', 'name' => 'user_email', 'type' => 'char(80)', 'params' => "default ''"],
                ['action' => 'cmodify', 'name' => 'news_id', 'type' => 'int', 'params' => 'not null'],
                ['action' => 'cmodify', 'name' => 'news_altname', 'type' => 'char(255)', 'params' => "default ''"],
            ],
        ],
        [
            'table'  => 'subscribe_comments_temp',
            'action' => 'cmodify',
            'key'    => 'primary key(id)',
            'fields' => [
                ['action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'],
                ['action' => 'cmodify', 'name' => 'com_id', 'type' => 'int', 'params' => 'not null'],
                ['action' => 'cmodify', 'name' => 'com_author', 'type' => 'char(100)', 'params' => "default ''"],
                ['action' => 'cmodify', 'name' => 'com_author_id', 'type' => 'int', 'params' => "default '0'"],
                ['action' => 'cmodify', 'name' => 'com_text', 'type' => 'text'],
                ['action' => 'cmodify', 'name' => 'news_title', 'type' => 'char(255)', 'params' => "default ''"],
                ['action' => 'cmodify', 'name' => 'news_id', 'type' => 'int', 'params' => 'not null'],
                ['action' => 'cmodify', 'name' => 'news_altname', 'type' => 'char(255)', 'params' => "default ''"],
                ['action' => 'cmodify', 'name' => 'user_email', 'type' => 'char(80)', 'params' => "default ''"],
            ],
        ],
    ];
    switch ($action) {
        case 'confirm':
            generate_install_page('subscribe_comments', 'Всё готово к установке.');
            break;
        case 'apply':
            if ($install) {
                if (fixdb_plugin_install('subscribe_comments', $db_update, 'install', ($action == 'autoapply') ? true : false)) {
                    plugin_mark_installed('subscribe_comments');
                } else {
                    return false;
                }
            } else {
                return false;
            }
            $params = [
                'admin_count'  => 10,
                'delayed_send' => 0,
            ];
            foreach ($params as $k => $v) {
                extra_set_param('subscribe_comments', $k, $v);
            }
            extra_commit_changes();
            break;
    }

    return true;
}
