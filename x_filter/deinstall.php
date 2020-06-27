<?php

/*
 * Configuration file for plugin
 */

// Protect against hack attempts.
if (! defined('NGCMS')) {
    die('HAL');
}

// Load lang files
loadPluginLang($plugin, 'backend', '', $plugin, ':');
    
// Duplicate var.
$plugin = 'x_filter';

// RUN
if ('commit' == $action) {
    if ($mysql->record("SHOW INDEX FROM " .prefix."_news WHERE Key_name = 'search'")) {
        if ($mysql->query("ALTER TABLE ".prefix."_news DROP INDEX `search`")) {
            msg([
                'text' => $lang[$plugin.':msg.deinstalled'],
                'info' => $lang[$plugin.':msg.back'],
            ]);
        }
    }
    
    plugin_mark_deinstalled($plugin);
} else {
    generate_install_page($plugin, $lang[$plugin.':desc_deinstall'], 'deinstall');
}
