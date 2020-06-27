<?php

/*
 * Configuration file for plugin
 */

// Protect against hack attempts.
if (! defined('NGCMS')) {
    die('HAL');
}

// Duplicate var.
$plugin = 'x_filter';

// Load lang files
LoadPluginLang($plugin, 'backend', '', $plugin, ':');

function plugin_x_filter_install($action)
{
    global $mysql, $lang, $plugin;

    // Apply requested action
    switch ($action) {
        case 'confirm':
            generate_install_page($plugin, $lang[$plugin.':desc_install']);
            break;
        case 'autoapply':
        case 'apply':
            if (!$mysql->record("SHOW INDEX FROM " .prefix."_news WHERE Key_name = 'search'")) {
                if (!$mysql->query("ALTER TABLE ".prefix."_news ADD FULLTEXT search(title, content)")) {
                    // code ...
                    return false;
                }
            }

            if (plugin_mark_installed($plugin) and 'apply' == $action) {
                msg([
                    'text' => $lang[$plugin.':msg.installed'],
                    'info' => $lang[$plugin.':msg.back'],
                ]);
            }

            break;
    }
    return true;
}
