<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();

// Preload engine
include_once 'cron.php';

global $CRONDATA;
$cronLines = array();
foreach ($cron=cron_load() as $k => $v) {
 $cronLines[] = $v[0];
}


// Fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => 'ѕлагин реализует функционал аналогичный unix программе cron, а именно - позвол€ет запукать периодические задачи'));
array_push($cfg, array('name' => 'crondata', 'title' => "—писок активных задач<br /><small> ажда€ строка представл€ет из себ€ набор параметров разделенных пробелом. ¬о всех параметрах св€занных со временем должно указыватьс€ либо конкретное значение, либо - <b>*</b>, что означает 'в любой момент'.<br />—писок параметров:<br /><b>min</b> - номер минуты<br /><b>hour</b> - номер часа<br /><b>day</b> - номер дн€<br /><b>month</b> - номер мес€ца<br /><b>DOW</b> - день недели [не поддерживаетс€]<br /><b>plugin</b> - ID плагина который надо запускать<br /><b>plugin CMD</b> - команда передаваема€ плагину<br /><br /><u>ѕример:</u><br /> <b><font color=blue>15 0 * * * test help</font></b><br /> - вызывать каждый день в 00:15 плагин <i>test</i> с параметром <i>help</i></small>", 'type' => 'text', 'html_flags' => 'rows=10 cols=100', 'value' => implode("\n",$cronLines)));

// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	//commit_plugin_config_changes($plugin, $cfg);
	$CRONDATA = array();
	foreach (explode("\n",$_REQUEST['crondata']) as $v) {
		array_push($CRONDATA, array($v));
	}	
	cron_save();

	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}


?>