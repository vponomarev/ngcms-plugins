<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
// Preload config file
plugins_load_config();
loadPluginLang('jchat', 'config', '', '', ':');
// Calculate row count
$jcRowCount = $mysql->result("select count(*) from " . prefix . "_jchat");
// Fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => $lang['jchat:desc']));
$cfgX = array();
array_push($cfgX, array('type' => 'flat', 'input' => '<tr><td class="contentEntry1" valign="top" colspan="2">Всего записей: ' . $jcRowCount . '</td></tr>'));
array_push($cfgX, array('type' => 'flat', 'input' => '<tr><td class="contentEntry1" valign="top" colspan="2"><input type="checkbox" name="purge" value="1"/> Удалить старые записи, оставив последние <input type="text" name="purge_save" size="3" value="50"/></td></tr>'));
array_push($cfgX, array('type' => 'flat', 'input' => '<tr><td class="contentEntry1" valign="top" colspan="2"><input type="checkbox" name="reload" value="1"/> Перезагрузить страницу у всех посетителей</td></tr>'));
array_push($cfg, array('mode' => 'group', 'title' => '<b>' . $lang['jchat:conf.stat'] . '</b>', 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'localsource', 'title' => $lang['jchat:localsource'], 'descr' => $lang['jchat:localsource#desc'], 'type' => 'select', 'values' => array('0' => $lang['jchat:lsrc.site'], '1' => $lang['jchat:lsrc.plugin']), 'value' => intval(pluginGetVariable($plugin, 'localsource'))));
array_push($cfgX, array('name' => 'access', 'title' => $lang['jchat:access'], 'descr' => $lang['jchat:access#desc'], 'type' => 'select', 'values' => array('0' => $lang['jchat:access.off'], '1' => $lang['jchat:access.ro'], '2' => $lang['jchat:access.rw']), 'value' => pluginGetVariable($plugin, 'access')));
array_push($cfgX, array('name' => 'rate_limit', 'title' => $lang['jchat:rate_limit'], 'descr' => $lang['jchat:rate_limit#desc'], 'type' => 'input', 'value' => pluginGetVariable($plugin, 'rate_limit')));
array_push($cfgX, array('name' => 'maxwlen', 'title' => $lang['jchat:maxwlen'], 'descr' => $lang['jchat:maxwlen#desc'], 'type' => 'input', 'value' => pluginGetVariable($plugin, 'maxwlen')));
array_push($cfgX, array('name' => 'maxlen', 'title' => $lang['jchat:maxlen'], 'descr' => $lang['jchat:maxlen#desc'], 'type' => 'input', 'value' => pluginGetVariable($plugin, 'maxlen')));
array_push($cfgX, array('name' => 'format_time', 'title' => $lang['jchat:format_time'], 'descr' => $lang['jchat:format_time#desc'], 'type' => 'input', 'value' => pluginGetVariable($plugin, 'format_time')));
array_push($cfgX, array('name' => 'format_date', 'title' => $lang['jchat:format_date'], 'descr' => $lang['jchat:format_date#desc'], 'type' => 'input', 'value' => pluginGetVariable($plugin, 'format_date')));
array_push($cfg, array('mode' => 'group', 'title' => '<b>' . $lang['jchat:conf.main'] . '</b>', 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'enable_panel', 'title' => $lang['jchat:enable.panel'], 'descr' => $lang['jchat:enable.panel#desc'], 'type' => 'select', 'values' => array('0' => $lang['noa'], '1' => $lang['yesa']), 'value' => pluginGetVariable($plugin, 'enable_panel')));
array_push($cfgX, array('name' => 'refresh', 'title' => $lang['jchat:refresh'], 'descr' => $lang['jchat:refresh#desc'], 'type' => 'input', 'value' => pluginGetVariable($plugin, 'refresh')));
array_push($cfgX, array('name' => 'history', 'title' => $lang['jchat:history'], 'descr' => $lang['jchat:history#desc'], 'type' => 'input', 'value' => pluginGetVariable($plugin, 'history')));
array_push($cfgX, array('name' => 'maxidle', 'title' => $lang['jchat:maxidle'], 'descr' => $lang['jchat:maxidle#desc'], 'type' => 'input', 'value' => pluginGetVariable($plugin, 'maxidle')));
array_push($cfgX, array('name' => 'order', 'title' => $lang['jchat:order'], 'descr' => $lang['jchat:order#desc'], 'type' => 'select', 'values' => array('0' => $lang['jchat:order.asc'], '1' => $lang['jchat:order.desc']), 'value' => pluginGetVariable($plugin, 'order')));
array_push($cfg, array('mode' => 'group', 'title' => '<b>' . $lang['jchat:conf.panel'] . '</b>', 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'enable_win', 'title' => $lang['jchat:enable.win'], 'descr' => $lang['jchat:enable.win#desc'], 'type' => 'select', 'values' => array('0' => $lang['noa'], '1' => $lang['yesa']), 'value' => pluginGetVariable($plugin, 'enable_win')));
array_push($cfgX, array('name' => 'win_mode', 'title' => $lang['jchat:win.mode'], 'descr' => $lang['jchat:win.mode#desc'], 'type' => 'select', 'values' => array('0' => $lang['jchat:win.mode.internal'], '1' => $lang['jchat:win.mode.external']), 'value' => pluginGetVariable($plugin, 'win_mode')));
array_push($cfgX, array('name' => 'win_refresh', 'title' => $lang['jchat:refresh'], 'type' => 'input', 'value' => pluginGetVariable($plugin, 'win_refresh')));
array_push($cfgX, array('name' => 'win_history', 'title' => $lang['jchat:history'], 'type' => 'input', 'value' => pluginGetVariable($plugin, 'win_history')));
array_push($cfgX, array('name' => 'win_maxidle', 'title' => $lang['jchat:maxidle'], 'type' => 'input', 'value' => pluginGetVariable($plugin, 'win_maxidle')));
array_push($cfgX, array('name' => 'win_order', 'title' => $lang['jchat:order'], 'type' => 'select', 'values' => array('0' => $lang['jchat:order.asc'], '1' => $lang['jchat:order.desc']), 'value' => pluginGetVariable($plugin, 'win_order')));
array_push($cfg, array('mode' => 'group', 'title' => '<b>' . $lang['jchat:conf.window'] . '</b>', 'entries' => $cfgX));
// RUN
if ($_REQUEST['action'] == 'commit') {
	// Check if we need to purge old messages
	if ($_REQUEST['purge']) {
		// Delete all extra records
		$dc = $jcRowCount - intval($_REQUEST['purge_save']);
		if (($_REQUEST['purge_save'] != '') && ($dc > 0)) {
			$mysql->query("delete from " . prefix . "_jchat order by id limit " . $dc);
		}
	}
	// Check if we need to reload page
	if ($_REQUEST['reload']) {
		$mysql->query("insert into " . prefix . "_jchat_events (chatid, postdate, type) values (1, unix_timestamp(now()), 3)");
		$lid = $mysql->result("select LAST_INSERT_ID()");
		$mysql->query("delete from " . prefix . "_jchat_events where type=3 and id <> " . db_squote($lid));
	}
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}