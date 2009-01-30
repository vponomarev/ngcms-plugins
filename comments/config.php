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
array_push($cfg, array('descr' => 'Плагин реализует базовый функционал работы с комментариями'));

$cfgX = array();
array_push($cfgX, array('name' => 'regonly', 'title' => "Комментарии только для зарегистрированных", 'descr' => '<b>Да</b> - комментарии могут оставлять только зарегистрированные пользователи<br/><b>Нет</b> - комментарии может оставить любой посетитель', 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'regonly'))));
array_push($cfgX, array('name' => 'backorder', 'title' => "Очередность отображения комментариев", 'descr' => "<b>Прямая</b> - отображение в порядке добавления<br/><b>Обратная</b> - самые новые показываются первыми", 'type' => 'select', 'values' => array ( '0' => 'Прямая', '1' => 'Обратная'), 'value' => intval(extra_get_param($plugin,'backorder'))));
array_push($cfgX, array('name' => 'maxlen', 'title' => "Максимальный размер", 'descr' => "Укажите максимальное кол-во символов для комментариев (например: <b>200</b>; <b>0</b> - не ограничивать)", 'type' => 'input', 'html_flags' => 'size="4"', 'value' => extra_get_param($plugin, 'maxlen')));
array_push($cfgX, array('name' => 'maxwlen', 'title' => "Автоурезание слов в комментариях", 'descr' => "В случае превышения заданного числа, в слово будет автоматически будет добавляться пробел (например: <b>50</b>)", 'type' => 'input', 'html_flags' => 'size="4"', 'value' => extra_get_param($plugin, 'maxwlen')));
array_push($cfgX, array('name' => 'multi', 'title' => "Разрешить множественные комментарии", 'descr' => "<b>Да</b> - пользователь может оставлять последовательно несколько комментариев<br/><b>Нет</b> - пользователю запрещено размещать последовательно несколько комментариев (необходимо дождаться комментария другого пользователя)", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'multi'))));
array_push($cfgX, array('name' => 'author_multi', 'title' => "Разрешить множественные комментарии <u>для автора</u>", 'descr' => "<b>Да</b> - автор может оставлять последовательно несколько комментариев<br/><b>Нет</b> - автору запрещено размещать последовательно несколько комментариев", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'author_multi'))));
array_push($cfgX, array('name' => 'timestamp', 'title' => "Формат отображения даты/времени", 'descr' => "Помощь по работе функции: <a href=\"http://php.net/date/\" target=\"_blank\">php.net/date</a><br/>Значение по умолчанию: <b>j.m.Y - H:i</b>", 'type' => 'input', 'value' => extra_get_param($plugin,'timestamp')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Общие настройки</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'multipage', 'title' => "Использовать многостраничное отображение", 'descr' => '<b>Да</b> - на странице новости будет отображаться только часть комментариев, остальные будут доступны на отдельной страничке<br/><b>Нет</b> - все комментарии будут отображаться на странице новости', 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'multipage'))));
array_push($cfgX, array('name' => 'multi_mcount', 'title' => "Кол-во комментариев на странице новости", 'descr' => "Укажите кол-во комментариев, отображаемых на странице новости<br/>(<b>0</b> - не отображать ни одного комментария)", 'type' => 'input', 'html_flags' => 'size="4"', 'value' => extra_get_param($plugin, 'multi_mcount')));
array_push($cfgX, array('name' => 'multi_scount', 'title' => "Кол-во комментариев на странице с комментариями", 'descr' => "Укажите кол-во комментариев, отображаемых на каждой странице с комментариями<br/>(<b>0</b> - отображать все на одной странице)", 'type' => 'input', 'html_flags' => 'size="4"', 'value' => extra_get_param($plugin, 'multi_scount')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Многостраничное отображение</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'inform_author', 'title' => "Оповещать автора по email о новом комментарии", 'descr' => "<b>Да</b> - при добавлении каждого комментария автор будет получать e-mail сообщение<br/><b>Нет</b> - автор не будет получать e-mail нотификаций", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'inform_author'))));
array_push($cfgX, array('name' => 'inform_admin', 'title' => "Оповещать администратора о новом комментарии", 'descr' => "<b>Да</b> - при добавлении каждого комментария администратор будет получать e-mail сообщение<br/><b>Нет</b> - администратор(ы) не будет получать e-mail нотификаций", 'type' => 'select', 'values' => array ( '0' => 'Нет', '1' => 'Да'), 'value' => intval(extra_get_param($plugin,'inform_admin'))));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>Настройки оповещений</b>', 'entries' => $cfgX));


// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}


?>