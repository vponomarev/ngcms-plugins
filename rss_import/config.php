<?php
//
// Configuration file for plugin
//
// Preload config file
plugins_load_config();
$count = extra_get_param($plugin, 'count');
if ((intval($count) < 1) || (intval($count) > 20))
	$count = 1;
// Fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => 'Плагин RSS новостей.'));
array_push($cfg, array('name' => 'count', 'title' => 'Количество блоков с RSS новостями', 'type' => 'input', 'value' => $count));
for ($i = 1; $i <= $count; $i++) {
	$cfgX = array();
	array_push($cfgX, array('name' => 'rss' . $i . '_name', 'title' => 'Заголовок новостей для отображения<br /><small>Например: <b>Next Generation CMS</b></small>', 'type' => 'input', 'value' => extra_get_param($plugin, 'rss' . $i . '_name')));
	array_push($cfgX, array('name' => 'rss' . $i . '_url', 'title' => 'Адрес новостей для отображения<br /><small>Например: <b>http://ngcms.ru</b></small>', 'type' => 'input', 'value' => extra_get_param($plugin, 'rss' . $i . '_url')));
	array_push($cfgX, array('name' => 'rss' . $i . '_number', 'title' => 'Количество новостей для отображения<br /><small>Значение по умолчанию: <b>10</b></small>', 'type' => 'input', 'value' => intval(extra_get_param($plugin, 'rss' . $i . '_number')) ? extra_get_param($plugin, 'rss' . $i . '_number') : '10'));
	array_push($cfgX, array('name' => 'rss' . $i . '_maxlength', 'title' => 'Ограничение длины названия новости<br /><small>Если название превышает указанные пределы, то оно будет урезано<br />Значение по умолчанию: <b>100</b></small>', 'type' => 'input', 'value' => intval(extra_get_param($plugin, 'rss' . $i . '_maxlength')) ? extra_get_param($plugin, 'rss' . $i . '_maxlength') : '100'));
	array_push($cfgX, array('name' => 'rss' . $i . '_newslength', 'title' => 'Ограничение длины короткой новости<br /><small>Если название превышает указанные пределы, то оно будет урезано<br />Значение по умолчанию: <b>100</b></small>', 'type' => 'input', 'value' => intval(extra_get_param($plugin, 'rss' . $i . '_newslength')) ? extra_get_param($plugin, 'rss' . $i . '_newslength') : '100'));
	array_push($cfgX, array('name' => 'rss' . $i . '_content', 'title' => "Генерировать переменную {short_news}", 'type' => 'checkbox', value => extra_get_param($plugin, 'rss' . $i . '_content')));
	array_push($cfgX, array('name' => 'rss' . $i . '_img', 'title' => "Удалить все картинки из {short_news}", 'type' => 'checkbox', value => extra_get_param($plugin, 'rss' . $i . '_img')));
	array_push($cfg, array('mode' => 'group', 'title' => '<b>Настройки блока № <b>' . $i . '</b> {rss' . $i . '}', 'entries' => $cfgX));
}
$cfgX = array();
array_push($cfgX, array('name' => 'localsource', 'title' => 'Выберите каталог из которого плагин будет брать шаблоны для отображения<br /><small><b>Шаблон сайта</b> - плагин будет пытаться взять шаблоны из общего шаблона сайта; в случае недоступности - шаблоны будут взяты из собственного каталога плагина<br /><b>Плагин</b> - шаблоны будут браться из собственного каталога плагина</small>', 'type' => 'select', 'values' => array('0' => 'Шаблон сайта', '1' => 'Плагин'), 'value' => intval(extra_get_param($plugin, 'localsource'))));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Настройки отображения</b>', 'entries' => $cfgX));
$cfgX = array();
array_push($cfgX, array('name' => 'cache', 'title' => 'Использовать кеширование данных<br /><small><b>Да</b> - кеширование используется<br /><b>Нет</b> - кеширование не используется</small>', 'type' => 'select', 'values' => array('1' => 'Да', '0' => 'Нет'), 'value' => intval(extra_get_param($plugin, 'cache'))));
array_push($cfgX, array('name' => 'cacheExpire', 'title' => 'Период обновления кеша<br /><small>(через сколько секунд происходит обновление кеша. Значение по умолчанию: <b>60</b>)</small>', 'type' => 'input', 'value' => intval(extra_get_param($plugin, 'cacheExpire')) ? extra_get_param($plugin, 'cacheExpire') : '60'));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Настройки кеширования</b>', 'entries' => $cfgX));
// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}