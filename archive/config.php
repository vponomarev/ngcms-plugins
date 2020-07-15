<?php

// Protect against hack attempts
if (!defined('NGCMS')) {
    die('HAL');
}
//
// Configuration file for plugin
//
// Preload config file
plugins_load_config();
LoadPluginLang('archive', 'config', '', '', ':');
// Fill configuration parameters
$cfg = [];
$cfgX = [];
array_push($cfg, ['descr' => $lang['archive:description']]);
array_push($cfgX, ['name' => 'maxnum', 'title' => $lang['archive:maxnum'], 'descr' => $lang['archive:maxnum#desc'], 'type' => 'input', 'value' => intval(pluginGetVariable($plugin, 'maxnum')) ? pluginGetVariable($plugin, 'maxnum') : '12']);
array_push($cfgX, ['name' => 'counter', 'title' => $lang['archive:counter'], 'descr' => $lang['archive:counter#desc'], 'type' => 'select', 'values' => ['0' => 'Нет', '1' => 'Да'], 'value' => intval(pluginGetVariable($plugin, 'counter'))]);
array_push($cfgX, ['name' => 'tcounter', 'title' => $lang['archive:tcounter'], 'descr' => $lang['archive:tcounter#desc'], 'type' => 'select', 'values' => ['0' => 'Нет', '1' => 'Да'], 'value' => intval(pluginGetVariable($plugin, 'tcounter'))]);
array_push($cfg, ['mode' => 'group', 'title' => $lang['archive:group.config'], 'entries' => $cfgX]);
$cfgX = [];
array_push($cfgX, ['name' => 'mode', 'title' => 'В каком режиме генерируется вывод плагина<br /><small><b>Автоматически</b> - при включении плагина автоматически генерируется блок {plugin_comments}<br /><b>TWIG</b> - вывод плагина генерируется только через TWIG функцию <b>callPlugin()</b></small>', 'type' => 'select', 'values' => ['0' => 'Автоматически', '1' => 'TWIG'], 'value' => intval(pluginGetVariable($plugin, 'mode'))]);
array_push($cfg, ['mode' => 'group', 'title' => '<b>Режим запуска</b>', 'entries' => $cfgX]);
$cfgX = [];
array_push($cfgX, ['name' => 'localsource', 'title' => $lang['archive:localsource'], 'descr' => $lang['archive:localsource#desc'], 'type' => 'select', 'values' => ['0' => 'Шаблон сайта', '1' => 'Плагин'], 'value' => intval(pluginGetVariable($plugin, 'localsource'))]);
array_push($cfg, ['mode' => 'group', 'title' => $lang['archive:group.source'], 'entries' => $cfgX]);
$cfgX = [];
array_push($cfgX, ['name' => 'cache', 'title' => $lang['archive:cache'], $lang['archive:cache#desc'], 'type' => 'select', 'values' => ['1' => 'Да', '0' => 'Нет'], 'value' => intval(pluginGetVariable($plugin, 'cache'))]);
array_push($cfgX, ['name' => 'cacheExpire', 'title' => $lang['archive:cacheExpire'], 'descr' => $lang['archive:cacheExpire#desc'], 'type' => 'input', 'value' => intval(pluginGetVariable($plugin, 'cacheExpire')) ? pluginGetVariable($plugin, 'cacheExpire') : '60']);
array_push($cfg, ['mode' => 'group', 'title' => $lang['archive:group.cache'], 'entries' => $cfgX]);
// RUN
if ($_REQUEST['action'] == 'commit') {
    // If submit requested, do config save
    commit_plugin_config_changes($plugin, $cfg);
    print_commit_complete($plugin);
} else {
    generate_config_page($plugin, $cfg);
}
