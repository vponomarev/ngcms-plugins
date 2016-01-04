<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

plugins_load_config();

// Load library
include_once(root."/plugins/gsmg/lib/common.php");

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
function plugin_gsmg_install($action) {
    
    // Apply requested action
    switch ($action) {
        case 'confirm':
            generate_install_page('gsmg', '');
            break;
        case 'autoapply':
        case 'apply':
            create_gsmg_urls();
            plugin_mark_installed('gsmg');
            $url = home."/engine/admin.php?mod=extras";
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: {$url}");
            break;
    }
    return true;
}
