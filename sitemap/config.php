<?php
pluginsLoadConfig();
loadPluginLang('sitemap', 'config', '', '', ':');
$cfg = array();
array_push($cfg, array('name' => 's_cache', 'title' => $lang['sitemap:cache'], 'type' => 'select', 'values' => array('1' => $lang['sitemap:label_yes'], '0' => $lang['sitemap:label_no']), 'value' => intval(pluginGetVariable('sitemap', 's_cache'))));
array_push($cfg, array('name' => 's_cacheExpire', 'title' => $lang['sitemap:period_cache'], 'type' => 'input', 'value' => intval(pluginGetVariable('sitemap', 's_cacheExpire')) ? pluginGetVariable('sitemap', 's_cacheExpire') : '86400'));
array_push($cfg, array('name' => 'news_per_page', 'title' => $lang['sitemap:news_per_page'], 'type' => 'input', 'html_flags' => 'style="width: 370px;"', 'value' => pluginGetVariable('sitemap', 'news_per_page')));
array_push($cfg, array('name' => 'localsource', 'title' => $lang['sitemap:localsource'], 'type' => 'select', 'values' => array('0' => $lang['sitemap:tpl_site'], '1' => $lang['sitemap:tpl_plugin']), 'value' => intval(pluginGetVariable($plugin, 'localsource'))));
// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes('sitemap', $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page('sitemap', $cfg);
}