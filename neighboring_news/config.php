<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('Galaxy in danger');

// Preload config file
plugins_load_config();

// Fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => 'Плагин генерирует ссылки на следующую и предыдущую новости.'));
array_push($cfg, array('name' => 'full_mode', 'title' => "Выводить в полной новости", 'type' => 'checkbox', value => extra_get_param('neighboring_news', 'full_mode')));
array_push($cfg, array('name' => 'short_mode', 'title' => "Выводить в краткой новости<br /><small>Не рекомендуется, т.к. количество запросов к БД увеличится на (2*количество новостей на главной странице)</small>", 'type' => 'checkbox', value => extra_get_param('neighboring_news', 'short_mode')));
array_push($cfg, array('name' => 'compare', 'title' => 'Параметр выборки из категорий', 'type' => 'select', 'values' => array('1' => '1 - Учитываем только главную', '2' => '2 - Полное соответствие'), value => intval(extra_get_param('neighboring_news', 'compare'))));
array_push($cfg, array('name' => 'localsource', 'title' => "Выберите каталог из которого плагин будет брать шаблоны для отображения<br /><small><b>Шаблон сайта</b> - плагин будет пытаться взять шаблоны из общего шаблона сайта; в случае недоступности - шаблоны будут взяты из собственного каталога плагина<br /><b>Плагин</b> - шаблоны будут браться из собственного каталога плагина</small>", 'type' => 'select', 'values' => array('0' => 'Шаблон сайта', '1' => 'Плагин'), 'value' => intval(extra_get_param('neighboring_news', 'localsource'))));

// RUN
if ($_REQUEST['action'] == 'commit') {
    // If submit requested, do config save
    commit_plugin_config_changes('neighboring_news', $cfg);
    print_commit_complete('neighboring_news');
} else {
    generate_config_page('neighboring_news', $cfg);
}
?>
