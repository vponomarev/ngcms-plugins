<?php

plugins_load_config();
LoadPluginLang('scn', 'main', '', 'scn');

$cfg = array();
array_push($cfg, array('descr' => $lang['scn_descr']));
array_push($cfg, array('name' => 'number', 'title' => $lang['scn_number'],'type' => 'input', 'html_flags' => 'size=40', 'value' => extra_get_param($plugin,'number')));
array_push($cfg, array('name' => 'orderby', 'title' => $lang['scn_orderby'], 'descr' => '', 'type' => 'select', 'values' => array ( '4' => $lang['scn_by_id_asc'], '3' => $lang['scn_by_id_desc'], '2' => $lang['scn_by_date_asc'], '1' => $lang['scn_by_date_desc'], '0' => $lang['scn_by_rand']), 'value' => extra_get_param($plugin, 'orderby')));
array_push($cfg, array('name' => 'mantemplate', 'title' => $lang['scn_mantemplate'], 'descr' => '', 'type' => 'select', 'values' => array ( '1' => $lang['scn_yesa'], '0' => $lang['scn_noa']), 'value' => extra_get_param($plugin,'mantemplate')));
array_push($cfg, array('name' => 'template', 'title' => $lang['scn_template'], 'descr' => $lang['scn_tpl_desc'], 'type' => 'text', 'html_flags' => 'rows=8 cols=60', 'value' => extra_get_param($plugin,'template')));

if ($_REQUEST['action'] == 'commit') {
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}
