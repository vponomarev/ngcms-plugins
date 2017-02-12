<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
// Preload config file
plugins_load_config();
include_once root . "includes/inc/httpget.inc.php";
include_once root . "includes/inc/xml2dom.php";
// Generate balance list
$blist = array('0' => 'основной баланс');
foreach ($mysql->select("select * from " . prefix . "_balance_manager where monetary=1 order by id") as $brow) {
	$blist[$brow['id']] = $brow['id'] . ($brow['type'] ? ' (' . $brow['type'] . ')' : '');
}
// Fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => 'Плагин позволяет получать платежи абонентов операторов сотовой связи, совершаемые посредством SMS сообщений (при помощи сервиса <a href="http://russianbilling.com/" target="_blank">RussianBilling.com</a>).<br />Данный плагин является дополнением к плагину <b>finance</b>.<br /><br />'));
$cfgX = array();
array_push($cfgX, array('name' => 'passkey', 'title' => 'Ключ-пароль сервиса', 'descr' => '<font color="red">Этот параметр необходимо заполнять для обеспечения безопасности!</font><br/>Параметр соответствует аналогичному параметру в настройках сервиса RussianBilling', 'type' => 'input', value => extra_get_param('fin_sms_rb', 'passkey')));
array_push($cfgX, array('name' => 'prefix', 'title' => 'SMS-префикс', 'descr' => 'Здесь необходимо указать префикс, на который абоненты должны отправлять свои SMS', 'type' => 'input', value => extra_get_param('fin_sms_rb', 'passkey')));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Настройки интеграции</b>', 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'bonus_mode', 'title' => 'Объём начисления бонуса пользователям', 'descr' => '<b>Ваша прибыль</b>', 'type' => 'select', 'values' => array('1' => 'Ваша прибыль'), value => extra_get_param('fin_sms_rb', 'bonus_mode')));
array_push($cfgX, array('name' => 'balance_no', 'title' => 'Номер баланса на который начисляются бонусы', 'descr' => '<b>Общая сумма</b> - начисляется общая сумма, списанная с его счёта<br /><b>Ваша прибыль</b> - начисляется сумма, которую получите вы<br /><font color=red>В списке указываются <u>только</u> монетарные балансы!</font>', 'type' => 'select', 'values' => $blist, value => extra_get_param('fin_sms_rb', 'balance_no')));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Финансовые настройки</b>', 'entries' => $cfgX));
// RUN
if (($_REQUEST['action'] == 'commit')) {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}

