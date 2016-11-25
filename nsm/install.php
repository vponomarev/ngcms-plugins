<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

//
// Install script for plugin.
// $action: possible action modes
//  confirm     - screen for installation confirmation
//  apply       - apply installation, with handy confirmation
//  autoapply       - apply installation in automatic mode [INSTALL script]
//
function plugin_nsm_install($action) {
    global $lang;

    // Apply requested action
    switch ($action) {
        case 'confirm':
            generate_install_page('nsm', "Установка NSM");
            break;
        case 'autoapply':
        case 'apply':
            
            $ULIB = new urlLibrary();
            $ULIB->loadConfig();
            
            $ULIB->registerCommand('nsm', '',
                array('descr' => array ('russian' => 'Список'))
            );
            
            $ULIB->registerCommand('nsm', 'add',
                array('descr' => array ('russian' => 'Добавление'))
            );
            
            $ULIB->registerCommand('nsm', 'edit',
                array('descr' => array ('russian' => 'Редактирование'))
            );
            
            $ULIB->registerCommand('nsm', 'del',
                array('descr' => array ('russian' => 'Удаление'))
            );
            
            $ULIB->saveConfig();
            
            plugin_mark_installed('nsm');
            break;
    }
    return true;
}
