<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();

include_once root."extras/fin_sms/inc/httpget.inc.php";
include_once root."extras/fin_sms/inc/xml2dom.php";


// Generate balance list
$blist = array('' => 'основной баланс');
foreach ($mysql->select("select * from ".prefix."_balance_manager where monetary=1 order by id") as $brow) {
	$blist[$brow['id']] = $brow['id'].($brow['type']?' ('.$brow['type'].')':'');
}

// Fill configuration parameters
$cfg = array();

array_push($cfg, array('descr' => 'Плагин позволяет получать платежи от посетителей через систему электронных денег WebMoney.<br>В связи с отсутствием встроенных средств конвертации валют, плагин позволяет принимать деньги только в одной валюте.'));

$cfgX = array();
array_push($cfgX, array('name' => 'balance_no', 'title' => 'Номер баланса на который начисляются бонусы', 'descr' => 'В списке отображаются только монетарные балансы','type' => 'select', 'values' => $blist, value => extra_get_param('fin_wm','balance_no')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Финансовые настройки</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'currency', 'title' => 'Тип валюты кошелька на который производится приём денег','type' => 'select', 'values' => array('WMZ', 'WMR'), value => extra_get_param('fin_wm','currency')));
array_push($cfgX, array('name' => 'wm_number', 'title' => 'Номер кошелька на которой принимаются пополнения', 'descr' => 'Должен указываться вместе с буквой типа валюты. К примеру, <b>Z349152268411</b>','type' => 'input', value => extra_get_param('fin_wm','wm_number')));
array_push($cfgX, array('name' => 'secret_key', 'title' => 'Сектерный код сервиса <b>Web Merchant Interface</b>', 'descr' => 'Данный секретный код вводится в настройках сервиса https://merchant.webmoney.ru/ для вашего кошелька','type' => 'input', value => extra_get_param('fin_wm','secret_key')));
array_push($cfgX, array('name' => 'test_mode', 'title' => 'Режим работы', 'descr' => '<b>Реальный</b> - производится реальное списание средств с кошелька<br><b>Тестовый</b> - средства не списываются, проводится эмуляция списания. При этом тестовые средства <b><u>зачисляются</u></b> на внутренний кошелёк пользователя!','type' => 'select', values => array('0' => 'Реальный', '1' => 'Тестовый'), value => extra_get_param('fin_wm','test_mode')));
array_push($cfgX, array('name' => 'sign_mode', 'title' => 'Режим формирования подписи', 'descr' => '<b>MD5</b> - безопасность: <i>низкая</i>; но не требуется связи с сервисом WebMoney<br><b>SIGN</b> - безопасность: <i>средняя</i>; необходима связь с сервисом WebMoney<br><b>SIGN+AUTH</b> - безопасность: <i>высокая</i>; проводится проверка поступления средств на кошелёк','type' => 'select', values => array('0' => 'MD5'), value => extra_get_param('fin_wm','sign_mode')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Интеграционные настройки с сервисом WebMoney</b>', 'entries' => $cfgX));


// RUN 
if ($_REQUEST['action'] == 'commit') {
	
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}


?>