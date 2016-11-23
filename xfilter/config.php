<?php


# protect against hack attempts
if (!defined('NGCMS')) die ('Galaxy in danger');

# preload config file
pluginsLoadConfig();

LoadPluginLang('xfilter', 'config', '', 'xfl', ':');

# fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => $lang['xfl:description']));

$cfgX = array();
array_push($cfgX, array(
				'name'	=> "{$currentVar}_skipcat", 
				'title' => $lang['xfl:skipcat'], 
				'type'	=> 'input',
				'value' => pluginGetVariable($plugin, "{$currentVar}_skipcat"))
);

array_push($cfgX, array(
				'name'   => "{$currentVar}_showAllCat",
				'type'   => 'select',
				'title'  => $lang['xfl:showAllCat'],
				'values'  => array(1 => $lang['yesa'], 0 => $lang['noa']), 
				'value'  => pluginGetVariable($plugin, "{$currentVar}_showAllCat"))
);

$orderby = array(
			'id_desc'		=> $lang['xfl:orderby_iddesc'], 
			'id_asc'		=> $lang['xfl:orderby_idasc'], 
			'postdate_desc' => $lang['xfl:orderby_postdatedesc'], 
			'postdate_asc'	=> $lang['xfl:orderby_postdateasc'], 
			'title_desc'	=> $lang['xfl:orderby_titledesc'], 
			'title_asc'		=> $lang['xfl:orderby_titleasc']
);

array_push($cfgX, array(
				'name'   => "{$currentVar}_order",
				'type'   => 'select',
				'title'  => $lang['xfl:orderby_title'],
				'values' => $orderby,
				'value'  => pluginGetVariable($plugin, "{$currentVar}_order"))
);

array_push($cfgX, array(
				'name'  => "{$currentVar}_showNumber",
				'title' => $lang['xfl:number_title'],
				'type'  => 'input',
				'value' => intval(pluginGetVariable($plugin, "{$currentVar}_showNumber")) ? pluginGetVariable($plugin, "{$currentVar}_showNumber") : '10')
);

array_push($cfg, array(
					'mode'    => 'group', 
					'title'   => $lang['xfl:group'], 
					'entries' => $cfgX)
);

$cfgX = array();
array_push($cfgX, array(
					'name'   => 'localsource', 
					'title'  => $lang['xfl:localsource'], 
					'type'   => 'select', 
					'values' => array ( '0' => $lang['xfl:localsource_0'], '1' => $lang['xfl:localsource_1']), 
					'value'  => intval(pluginGetVariable($plugin, 'localsource')))
);
array_push($cfg, array(
					'mode'    => 'group', 
					'title'   => $lang['xfl:group_2'], 
					'entries' => $cfgX)
);


# RUN 
if ($_REQUEST['action'] == 'commit') {
	# if submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}
