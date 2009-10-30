<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');


//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();

// Load lang files
LoadPluginLang('finance', 'config', '', '', ':');

// Fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => $lang['finance:description']));

$cfgX = array();
array_push($cfgX, array('name' => 'syscurrency', 'title' => $lang['finance:syscurrency'], 'descr' => $lang['finance:syscurrency.descr'], 'type' => 'select', 'values' => array('RUR' => 'RUR', 'EUR' => 'EUR', 'USD' => 'USD'), value => extra_get_param('finance','syscurrency')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Общие настройки</b>', 'entries' => $cfgX));

$b = array();

foreach ($mysql->select("select * from ".prefix."_balance_manager order by id") as $row) {
 $b[$row['id']] = array('monetary' => $row['monetary'], 'type' => $row['type'], 'description' => $row['description']);
}

for ($i = 1; $i < 5; $i++) {
	$cfgX = array();
	//array_push($cfgX, array('title' => '== <b>Настройки баланса №'.$i.'</b> =='));
	array_push($cfgX, array('nosave' => 1, 'name' => 'balance'.$i.'_monetary', 'title' => $lang['finance:balance.monetary'], 'descr' => $lang['finance:balance.monetary.descr'],'type' => 'select', 'values' => array ( '1' => 'Да', '0' => 'Нет'), value => $b[$i]['monetary']));
	array_push($cfgX, array('nosave' => 1, 'name' => 'balance'.$i.'_type', 'title' => $lang['finance:balance.type'], 'descr' => $lang['finance:balance.type.descr'], 'type' => 'input', value => $b[$i]['type']));
	array_push($cfgX, array('nosave' => 1, 'name' => 'balance'.$i.'_description', 'title' => $lang['finance:balance.descr'], 'descr' => $lang['finance:balance.descr.descr'],'type' => 'input', 'value' => $b[$i]['description']));
	array_push($cfg,  array('mode' => 'group', 'title' => '<b>'.$lang['finance:balance.header'].$i.'</b>', 'entries' => $cfgX));
}

if ($_REQUEST['action'] == 'commit') {
        $params = load_commit_params($cfg, $params);

	commit_plugin_config_changes('finance', $cfg);

	//
	// Save changes into DB
	for ($i = 1; $i < 5; $i++) {
		if ($row = $mysql->record("select * from ".prefix."_balance_manager where id = $i")) {
			$query = "update ".prefix."_balance_manager set monetary = ".db_squote($_POST['balance'.$i.'_monetary']).", type = ".db_squote($_POST['balance'.$i.'_type']).", description = ".db_squote($_POST['balance'.$i.'_description'])." where id = $i";
		} else {
			$query = "insert into ".prefix."_balance_manager (id, monetary, type, description) values ($i,".db_squote($_POST['balance'.$i.'_monetary']).",".db_squote($_POST['balance'.$i.'_type']).",".db_squote($_POST['balance'.$i.'_description']).")";
		}
		$mysql->query($query);
	}

	print_commit_complete('finance');
} else {
	generate_config_page('finance', $cfg);
}


?>