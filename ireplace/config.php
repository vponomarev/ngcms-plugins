<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
pluginsLoadConfig();
LoadPluginLang('ireplace', 'main', '', '', ':');
$cfg = array();
array_push($cfg, array('descr' => $lang['ireplace:descr']));
array_push($cfg, array('name' => 'area', 'title' => $lang['ireplace:area'], 'descr' => $lang['ireplace:area.descr'], 'type' => 'select', 'values' => array('' => $lang['ireplace:area.choose'], 'news' => $lang['ireplace:area.news'], 'static' => $lang['ireplace:area.static'], 'comments' => $lang['ireplace:area.comments'])));
array_push($cfg, array('name' => 'src', 'title' => $lang['ireplace:source'], 'type' => 'input', 'html_flags' => 'size=40', 'value' => ''));
array_push($cfg, array('name' => 'dest', 'title' => $lang['ireplace:destination'], 'type' => 'input', 'html_flags' => 'size=40', 'value' => ''));
if ($_REQUEST['action'] == 'commit') {
	// Perform a replace
	$query = '';
	do {
		// Check src/dest values
		$src = $_REQUEST['src'];
		$dest = $_REQUEST['dest'];
		if (!strlen($src) || !strlen($dest)) {
			// No src/dest text
			msg(array("type" => "error", "text" => $lang['ireplace:error.notext']));
			break;
		}
		// Check area
		switch ($_REQUEST['area']) {
			case 'news':
				$query = "update " . prefix . "_news set content = replace(content, " . db_squote($src) . ", " . db_squote($dest) . ")";
				break;
			case 'static':
				$query = "update " . prefix . "_static set content = replace(content, " . db_squote($src) . ", " . db_squote($dest) . ")";
				break;
			case 'comments':
				$query = "update " . prefix . "_comments set text = replace(text, " . db_squote($src) . ", " . db_squote($dest) . ")";
				break;
		}
		if (!$query) {
			// No area selected
			msg(array("type" => "error", "text" => $lang['ireplace:error.noarea']));
			break;
		}
	} while (0);
	// Check if we should make replacement
	if ($query) {
		// Yeah !!
		$result = $mysql->select($query);
		$count = $mysql->affected_rows($mysql->connect);
		if ($count) {
			msg(array("type" => "info", "info" => str_replace('{count}', $count, $lang['ireplace:info.done'])));
		} else {
			msg(array("type" => "info", "info" => $lang['ireplace:info.nochange']));
		}
	}
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}
