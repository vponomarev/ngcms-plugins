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
function plugin_feedback_install($action)
{
    global $lang;
    if ($action != 'autoapply') {
        loadPluginLang('feedback', 'install', '', '', ':');
    }
    // Fill DB_UPDATE configuration scheme
    $db_update = [
        [
            'table'  => 'feedback',
            'action' => 'cmodify',
            'key'    => 'primary key(id)',
            'fields' => [
                ['action' => 'cmodify', 'name' => 'id', 'type' => 'int', 'params' => 'not null auto_increment'],
                ['action' => 'cmodify', 'name' => 'active', 'type' => 'int', 'params' => 'default 0'],
                ['action' => 'cmodify', 'name' => 'regonly', 'type' => 'int', 'params' => 'default 0'],
                ['action' => 'cmodify', 'name' => 'flags', 'type' => 'char(20)'],
                ['action' => 'cmodify', 'name' => 'name', 'type' => 'char(40)'],
                ['action' => 'cmodify', 'name' => 'title', 'type' => 'char(80)'],
                ['action' => 'cmodify', 'name' => 'subj', 'type' => 'char(100)'],
                ['action' => 'cmodify', 'name' => 'description', 'type' => 'text'],
                ['action' => 'cmodify', 'name' => 'struct', 'type' => 'text'],
                ['action' => 'cmodify', 'name' => 'template', 'type' => 'char(50)'],
                ['action' => 'cmodify', 'name' => 'emails', 'type' => 'text'],
            ],
        ],
    ];
    // Apply requested action
    switch ($action) {
        case 'confirm':
            generate_install_page('feedback', $lang['feedback:description']);
            break;
        case 'autoapply':
        case 'apply':
            if (fixdb_plugin_install('feedback', $db_update, 'install', ($action == 'autoapply') ? true : false)) {
                plugin_mark_installed('feedback');
            } else {
                return false;
            }
            break;
    }

    return true;
}
