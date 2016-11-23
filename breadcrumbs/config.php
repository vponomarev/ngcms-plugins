<?php

/*
 * Configuration file for plugin "Breadcrumbs" for Next Generation CMS 0.9.3
 * Copyright (C) 2010-2011 Alexey N. Zhukov (http://digitalplace.ru)
 * web:    http://digitalplace.ru
 * e-mail: zhukov.alexei@gmail.com
 */

pluginsLoadConfig();

LoadPluginLang('breadcrumbs', 'config', '', 'bc', ':');

$cfg = array();
array_push($cfg, array('descr' => $lang['bc:description']));
array_push($cfg, array(
                    'name'    => 'block_full_path', 
                    'title'   => $lang['bc:block_full_path'], 
                    'type'    => 'select', 
                    'values'  => array(1 => $lang['yesa'], 0 => $lang['noa']), 
                    'value'   => pluginGetVariable($plugin, 'block_full_path')));

$cfgX = array();
array_push($cfgX, array(
                    'name'    => 'template_source', 
                    'title'   => $lang['bc:template_source_title'], 
                    'type'    => 'select', 
                    'values'  => array ( '0' => $lang['bc:template_source_site'], '1' => $lang['bc:template_source_plugin']), 
                    'value'   => intval(pluginGetVariable($plugin, 'template_source'))));
array_push($cfg, array(
                    'mode'    => 'group', 
                    'title'   => $lang['bc:template_source'], 
                    'entries' => $cfgX));
                        
if ($_REQUEST['action'] == 'commit') {
    commit_plugin_config_changes($plugin, $cfg);
    print_commit_complete($plugin);
} else {
    generate_config_page($plugin, $cfg);
}
