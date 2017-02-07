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
array_push($cfg, array('name' => 'extdate', 'title' => 'ƒополнительные переменные дл€ управлени€ датой', 'descr' => 'ƒоступны переменные:<br>{day} - день (1 - 31)<br>{day0} - день (01 - 31)<br>{month} - мес€ц (1 - 12)<br>{month0} - мес€ц (01 - 12)<br>{year} - год (00 - 99)<br>{year2} - год (1980 - 2100)<br>{month_s} - текст мес€ца (янв, ‘ев,...)<br>{month_l} - текст мес€ца (январь, ‘евраль,...)', 'type' => 'select', 'values' => array('0' => 'выкл', '1' => 'вкл'), 'value' => extra_get_param($plugin, 'extdate')));
array_push($cfg, array('name' => 'newdate', 'title' => '»зменить формат даты', 'descr' => 'ѕри заполнении данного параметра измен€етс€ формат отображени€ даты в новост€х на указанный', 'type' => 'input', 'value' => extra_get_param($plugin, 'newdate')));
// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}
?>