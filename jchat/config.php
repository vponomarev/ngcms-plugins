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
array_push($cfg, array('descr' => 'Плагин позволяет вести на сайте AJAX-style чат.<br/>Внимание! При большом кол-ве посетителей данный чат может создавать значительную нагрузку на сервер!'));
array_push($cfg, array('name' => 'access', 'title' => "Гостевой доступ", 'descr' => 'Укажите какие доступы будут у гостей:<br/><b>запрещено</b> - гости не будут видеть чат-бокса<br/><b>просмотр</b> - гости смогут просматривать чат-бокс<br/><b>просмотр+запись</b> - гости также смогут добавлять посты', 'type' => 'select', 'values' => array ('0' => 'запрещено', '1' => 'просмотр', '2' => 'просмотр + запись'), 'value' => extra_get_param($plugin,'access')));
array_push($cfg, array('name' => 'refresh', 'title' => "Период обновления страниц у пользователей", 'descr' => 'Укажите период (в секундах) между обновлениями чат-бокса у посетителей.<br/>Рекомендуемое значение: <b>30</b> секунд<br/>Разрешенные значения: от <b>5</b> до <b>1800</b><br/><font color="red">Внимание! Чем меньше это значение - тем выше нагрузка на сервер!</font>', 'type' => 'input', 'value' => extra_get_param($plugin,'refresh')));
array_push($cfg, array('name' => 'history', 'title' => "Кол-во сообщений в истории чата для отображения", 'descr' => 'Это количество (приблизительно) будет отображаться у посетителя. При превышении старые сообщения будут удаляться.<br/>Значение по умолчанию: <b>30</b><br/>Разрешенные значения: от <b>1</b> до <b>500</b>', 'type' => 'input', 'value' => extra_get_param($plugin,'history')));
array_push($cfg, array('name' => 'rate_limit', 'title' => "Минимальное время между сообщениями (в секундах)", 'descr' => 'Ограничение действует на один <b>IP адрес</b>, предназначено для защиты от флуда.<br/>Значение по умолчанию: <b>0</b> (флуд-защита отключена)', 'type' => 'input', 'value' => extra_get_param($plugin,'rate_limit')));
array_push($cfg, array('name' => 'maxidle', 'title' => "Максимальное время отображения чата (в секундах) при неактивности пользователя", 'descr' => 'Часто возникает ситуация, когда пользователь открывает страницу с чатом и забывает про неё - это лишняя нагрузка на сервер.<br/>Чтобы избежать такой ситуации вы можете указатьм максимальное время (в секундах), в течении которого пользовательский чат будет обновляться.<br/>Значение по умолчанию: <b>0</b> (чат обновляется бесконечно)', 'type' => 'input', 'value' => extra_get_param($plugin,'maxidle')));
array_push($cfg, array('name' => 'maxwlen', 'title' => "Максимальная длина слова в сообщении", 'descr' => 'Слова длиннее указанного значения будут разделяться пробелом. Необходимо для избегания появления горизонтального скроллинга.<br/>Значение по умолчанию: <b>40</b>', 'type' => 'input', 'value' => extra_get_param($plugin,'maxwlen')));
array_push($cfg, array('name' => 'maxlen', 'title' => "Максимальная длина сообщения", 'descr' => 'Пользователь не сможет отправить сообщение длиннее заданного значения.<br/>Значение по умолчанию: <b>500</b> символов', 'type' => 'input', 'value' => extra_get_param($plugin,'maxlen')));


// RUN
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}

