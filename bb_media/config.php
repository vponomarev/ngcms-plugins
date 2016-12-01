<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();


// Fill configuration parameters
$cfg = array();
$cfgX = array();

function getPlayersNames($path) {
    $dirs = array_filter(glob($path.'*'), 'is_dir');
    $dirNames = array();
    foreach($dirs as $key => $dir) {
        $basename = basename($dir);
        $dirNames[$basename] = $basename;
    }

    return $dirNames;
}

$dirNames = getPlayersNames(__DIR__.'/players/');

array_push($cfgX, array('name' => 'player_name', 'title' => "Выберите плеер", 'type' => 'select', 'values' => $dirNames, 'value' => pluginGetVariable($plugin,'player_name')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки отображения</b>', 'entries' => $cfgX));

// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}


?>