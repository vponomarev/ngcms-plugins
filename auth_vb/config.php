<?php
//
// Configuration file for plugin
//
// Preload config file
pluginsLoadConfig();
// Load lang files
LoadPluginLang('auth_vb', 'config', '', 'auth');
// Fill configuration parameters
$cfg = array();
$cfgX = array();
array_push($cfg, array('descr' => $lang['auth_description']));
array_push($cfgX, array('descr' => $lang['auth_extdb_fulldesc']));
array_push($cfgX, array('name' => 'extdb', 'title' => $lang['auth_extdb_extdb'], 'descr' => $lang['auth_extdb_extdb_desc'], 'type' => 'select', 'values' => array('1' => $lang['yesa'], '0' => $lang['noa']), 'value' => extra_get_param('auth_vb', 'extdb')));
array_push($cfgX, array('name' => 'dbhost', 'title' => $lang['auth_extdb_dbhost'], 'type' => 'input', value => extra_get_param('auth_vb', 'dbhost')));
array_push($cfgX, array('name' => 'dbname', 'title' => $lang['auth_extdb_dbname'], 'type' => 'input', value => extra_get_param('auth_vb', 'dbname')));
array_push($cfgX, array('name' => 'dblogin', 'title' => $lang['auth_extdb_dblogin'], 'type' => 'input', value => extra_get_param('auth_vb', 'dblogin')));
array_push($cfgX, array('name' => 'dbpass', 'title' => $lang['auth_extdb_dbpass'], 'type' => 'input', value => extra_get_param('auth_vb', 'dbpass')));
array_push($cfg, array('mode' => 'group', 'title' => $lang['auth_extdb'], 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'dbprefix', 'title' => $lang['auth_params_prefix'], 'descr' => $lang['auth_params_prefix_desc'], 'type' => 'input', value => extra_get_param('auth_vb', 'dbprefix')));
array_push($cfgX, array('name' => 'ipcheck', 'title' => 'Параметр &quot;<b>ipcheck</b>&quot; - Длина IP адреса для проверки сессии', 'descr' => 'Этот параметр необходимо взять из настроек форума, он указывает какая глубина проверки IP адреса будет делаться при проверке правильности сессии пользователя.<br/><b>*</b> Раздел: "Опции vBulletin" => "Настройки сервера и параметры оптимизации"', 'type' => 'select', 'values' => array('0' => '0 | 255.255.255.255', '1' => '1 | 255.255.255.0', '2' => '2 | 255.255.0.0'), value => extra_get_param('auth_vb', 'ipcheck')));
array_push($cfgX, array('name' => 'cookietimeout', 'title' => 'Параметр &quot;<b>cookietimeout</b>&quot; - время жизни для сессии в cookie', 'descr' => 'Этот параметр необходимо взять из настроек форума.<br/><b>*</b> Раздел: "Опции vBulletin" => "Настройки сервера и параметры оптимизации"', 'type' => 'input', value => extra_get_param('auth_vb', 'cookietimeout')));
array_push($cfgX, array('name' => 'cookie_security_hash', 'title' => 'Значение переменной &quot;<b>cookie_security_hash</b>&quot; - параметр шифрации Cookie', 'descr' => 'Этот параметр необходимо взять из файла-конфигурации форума includes/config.php<br/><b>*</b>Значение по умолчанию <u>отсутствует</u>', 'type' => 'input', value => extra_get_param('auth_vb', 'cookie_security_hash')));
array_push($cfgX, array('name' => 'cookie_domain', 'title' => $lang['auth_params_domain'], 'descr' => $lang['auth_params_domain_desc'], 'type' => 'input', value => extra_get_param('auth_vb', 'cookie_domain')));
array_push($cfgX, array('name' => 'setremember', 'title' => $lang['auth_setremember'], 'descr' => $lang['auth_setremember_desc'], 'type' => 'select', 'values' => array('0' => $lang['auth_mauto'], '1' => $lang['yesa'], '2' => $lang['noa']), 'value' => extra_get_param('auth_vb', 'setremember')));
array_push($cfg, array('mode' => 'group', 'title' => $lang['auth_params'], 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'userjoin', 'title' => $lang['auth_auto_join'], 'descr' => $lang['auth_auto_join_desc'], 'type' => 'select', 'values' => array('1' => $lang['yesa'], '0' => $lang['noa']), 'value' => extra_get_param('auth_vb', 'userjoin')));
array_push($cfgX, array('name' => 'autocreate_ng', 'title' => $lang['auth_auto_ng'], 'descr' => $lang['auth_auto_ng_desc'], 'type' => 'select', 'values' => array('1' => $lang['yesa'], '0' => $lang['noa']), 'value' => extra_get_param('auth_vb', 'autocreate_ng')));
array_push($cfg, array('mode' => 'group', 'title' => $lang['auth_auto'], 'entries' => $cfgX));
// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes('auth_vb', $cfg);
	print_commit_complete('auth_vb');
} else {
	generate_config_page('auth_vb', $cfg);
}
?>