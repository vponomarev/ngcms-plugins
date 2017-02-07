<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
// Preload config file
plugins_load_config();
// Generate balance list
$blist = array('' => 'основной баланс');
foreach ($mysql->select("select * from " . prefix . "_balance_manager where monetary=1 order by id") as $brow) {
	$blist[$brow['id']] = $brow['id'] . ($brow['type'] ? ' (' . $brow['type'] . ')' : '');
}
// Fill configuration parameters
$cfg = array();
array_push($cfg, array(
	'descr' => 'Плагин позволяет получать платежи от посетителей через SMS при помощи сервиса smscoin.com.<br>Для приёма средств исполизуется сервис <b>смс:банк</b>.<br/><b><u>Настроечные параметры для сервиса SMSCOIN:</u></b><br/>' .
		'<b>Result URL:</b> ' . home . generateLink('core', 'plugin', array('plugin' => 'finance'), array('mode' => 'pay_accept', 'acceptor' => 'smscoin')) . '<br/>' .
		'<b>Success URL:</b> ' . home . generateLink('core', 'plugin', array('plugin' => 'finance'), array('mode' => 'pay_accept_form', 'acceptor' => 'smscoin', 'result_ok' => '1')) . '<br/>' .
		'<b>Fail URL:</b> ' . home . generateLink('core', 'plugin', array('plugin' => 'finance'), array('mode' => 'pay_accept_form', 'acceptor' => 'smscoin', 'result_fail' => '1')) . '<br/>'
));
$cfgX = array();
array_push($cfgX, array('name' => 'balance_no', 'title' => 'Номер баланса на который начисляются бонусы', 'descr' => 'В списке отображаются только монетарные балансы<br/><font color="red"><b>Для разделения <u>реальных</u> платежей (например, из WEBMONEY) и <u>виртуальных</u> платежей (из SMSCOIN) настоятельно рекомендуется для платежей из SMSCOIN создавать выделенный дополнительный монетарный баланс - это позволит более точно учитывать поступления средств от посетителей.</b><br/>Балансы настраиваются в настройках плагина finance</font>', 'type' => 'select', 'values' => $blist, value => extra_get_param('fin_smscoin', 'balance_no')));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Финансовые настройки</b>', 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'post_url', 'title' => 'URL платёжного шлюза', 'descr' => 'Значение опции `<b>Адрес шлюза</b> в настройках вашего <b>sms:банк</b>а', 'type' => 'input', value => extra_get_param('fin_smscoin', 'post_url')));
array_push($cfgX, array('name' => 'bank_id', 'title' => 'Идентификатор вашего сервиса <b>sms:банк</b>', 'descr' => 'Необходимо указать идентификационный номер вашего сервиса <b>sms:банк</b>', 'type' => 'input', value => intval(extra_get_param('fin_smscoin', 'bank_id'))));
array_push($cfgX, array('name' => 'secret_key', 'title' => 'Секрерный код', 'descr' => 'Данный секретный код вводится в настройках сервиса <b>смс:банк</b>', 'type' => 'input', value => extra_get_param('fin_smscoin', 'secret_key')));
array_push($cfgX, array('name' => 'clear_mode', 'title' => 'Вид суммы пополнения счёта', 'descr' => '<b>Платёж</b> - счёт пополняется на сумму, равную сумме платежа пользователя<br/><b>Прибыль</b> - счёт пополняется на сумму, равную полученной от отправки SMS прибыли', 'type' => 'select', 'values' => array(0 => 'Платёж', 1 => 'Прибыль'), 'value' => extra_get_param('fin_smscoin', 'clear_mode')));
array_push($cfgX, array('name' => 'pay_rate', 'title' => 'Коэффициент пересчета из `USD` (в которых производятся расчёты с сервисом SMSCOIN) в валюту сайта (текущая валюта: <b>' . pluginGetVariable('finance', 'syscurrency') . '</b>)', 'descr' => '1 <b>USD</b> SMSCOIN = XX.XXX <b>' . pluginGetVariable('finance', 'syscurrency') . '</b>', 'type' => 'input', value => pluginGetVariable('fin_smscoin', 'pay_rate')));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Интеграционные настройки с сервисом SMSCOIN</b>', 'entries' => $cfgX));
// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}
?>