<?php
if (!defined('NGCMS')) die ('HAL');
plugins_load_config();
$cfg = array();
$cfgX = array();
array_push($cfg, array('descr' => 'Замена слов на адрес страниц'));
$cfgX = array();
array_push($cfgX, array('name' => 'p_count', 'title' => "Количество замен одной ссылке в одной новости", 'type' => 'input', 'value' => intval(extra_get_param($plugin, 'p_count'))));
array_push($cfgX, array('name' => 'c_replace', 'title' => "Режим поиска", 'type' => 'select', 'values' => array('0' => 'Не точное совпадение', '1' => 'Точное совпадени без учета регистра', '2' => 'Точное совпадение с учетом регистра'), 'value' => intval(extra_get_param($plugin, 'c_replace'))));
array_push($cfgX, array('name' => 'replace', 'title' => "Списки<br><br><i>Укажите слова через разделить | и переводом строк</i><br />Пример:<br />test|http://test|2<br />test2|http://test2<br>Шаблон: Что_искать|На_что_заменить|Количество_в_одной_новости", 'type' => 'text', 'html_flags' => 'rows=20 cols=130', 'value' => extra_get_param($plugin, 'replace')));
array_push($cfgX, array('name' => 'str_url', 'title' => "Шаблон подмены<br /><small>Ключи:<br /><b>%search%</b> - Искомое слово<br /><b>%replace%</b> - Заменяемое слово<br /><b>%scriptLibrary%</b> - Путь до библиотек http://site/lib<br /><b>%home%</b> - Адрес сайта http://ngcms<br /></small><br />Пример: <pre><a href='%replace%'>%search%</a></pre>", 'type' => 'input', 'html_flags' => 'size="80"', 'value' => extra_get_param($plugin, 'str_url')));
array_push($cfg, array('mode' => 'group', 'title' => '<b>Настройки плагина</b>', 'entries' => $cfgX));
if ($_REQUEST['action'] == 'commit') {
	commit_plugin_config_changes('text_replace', $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page('text_replace', $cfg);
}
