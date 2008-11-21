<?php

plugins_load_config();
LoadPluginLang('user_ranks', 'main', '', 'ur');

$cfg = array();
array_push($cfg, array('descr' => $lang['ur_descr']));


for ($i = 1; $i <= 7; $i++) {
	$cfgX = array();
	array_push($cfgX, array('name' => 'rank_'.$i.'_name', 'title' => $lang['ur_rank_name'],'type' => 'input', 'html_flags' => 'size=40', 'value' => extra_get_param('user_ranks','rank_'.$i.'_name')));
	array_push($cfgX, array('name' => 'rank_'.$i.'_com', 'title' => $lang['ur_rank_com'],'type' => 'input', 'html_flags' => 'size=20', 'value' => extra_get_param('user_ranks','rank_'.$i.'_com')));
	array_push($cfgX, array('name' => 'rank_'.$i.'_news', 'title' => $lang['ur_rank_news'],'type' => 'input', 'html_flags' => 'size=20', 'value' => extra_get_param('user_ranks','rank_'.$i.'_news')));
	array_push($cfg,  array('mode' => 'group', 'title' => 'Параметры ранга № <b>'.$i.'</b>', 'entries' => $cfgX));
}

$cfgX = array();
array_push($cfgX, array('name' => 'rank_0_name', 'title' => $lang['ur_rank_name'],'type' => 'input', 'html_flags' => 'size=40', 'value' => extra_get_param('user_ranks','rank_'.$i.'_name')));
array_push($cfg,  array('mode' => 'group', 'title' => 'Параметры ранга для незарегистрированных пользователей № <b>0</b>', 'entries' => $cfgX));

if ($_REQUEST['action'] == 'commit') {
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page('user_ranks', $cfg);
}


?>