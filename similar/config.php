<?php

//
// Configuration file for plugin
//

plugins_load_config();
LoadPluginLang($plugin, 'main', '', 'similar');
include_once('inc/similar.php');


$cfg = array();
array_push($cfg, array('name' => 'rebuild', 'title' => $lang['similar_rebuild'], 'descr' => $lang['similar_rebuild_desc'], 'type' => 'select', 'value' => 0, 'values' => array ( 0 => $lang['noa'], 1 => $lang['yesa']), 'nosave' => 1));

$cfgX = array();
array_push($cfgX, array('name' => 'localsource', 'title' => $lang['similar_localsource'], 'descr' => $lang['simiar_localsource'], 'type' => 'select', 'values' => array ( '0' => $lang['similar_lsrc_site'], '1' => $lang['similar_lsrc_plugin']), 'value' => intval(extra_get_param($plugin,'localsource'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>'.$lang['similar_cfg_display'].'</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'count', 'title' => $lang['similar_count'], 'descr' => $lang['similar_count_desc'], 'type' => 'input', 'html_flags' => 'size="4"', 'value' => extra_get_param($plugin, 'count')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>'.$lang['similar_cfg_common'].'</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'count', 'title' => $lang['similar_similarity']));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>'.$lang['similar_cfg_similarity'].'</b>', 'entries' => $cfgX));


if (!$_REQUEST['action']) {
	generate_config_page($plugin, $cfg);
}
elseif ($_REQUEST['action'] == 'commit') {
	commit_plugin_config_changes($plugin, $cfg);
	if ($_REQUEST['rebuild']) {
		// Rebuild index table

		// * Truncate index
		$mysql->query("truncate table ".prefix."_similar_index");

		// * Mark all news to have broken index
		$mysql->query("update ".prefix."_news set similar_status = 0");

		print $lang['tags_rebuild_done']."<br/>";
	}
	print_commit_complete($plugin);
}
