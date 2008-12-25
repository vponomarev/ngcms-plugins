<?php
plugins_load_config();
$cfg = array();
array_push($cfg, array('name' => 'c_title', 'title' => 'Title для категории', 'descr' => 'Разрешено только %1% и %3%','type' => 'input', 'value' => extra_get_param('simple_title','c_title')));
array_push($cfg, array('name' => 'n_title', 'title' => 'Title для новости', 'descr' => 'Все три разрешены','type' => 'input', 'value' => extra_get_param('simple_title','n_title')));
array_push($cfg, array('name' => 'm_title', 'title' => 'Title для главной страницы<br>Только %3%','type' => 'input', 'value' => extra_get_param('simple_title','m_title')));
array_push($cfg, array('name' => 'static_title', 'title' => 'Title для статической страницы<br>Только %3%','type' => 'input', 'value' => extra_get_param('simple_title','static_title')));

array_push($cfg, array('descr' => 'Ключи:<br><b>%1%</b> - имя категории<br><b>%2%</b> - имя новости<br><b>%3%</b> - заголовок сайта<br><b>%4%</b> - заголовок статической страницы<br>'));
if ($_REQUEST['action'] == 'commit') {
	commit_plugin_config_changes('simple_title', $cfg);
	print "Changes commited: <a href='admin.php?mod=extra-config&plugin=simple_title'>Назад</a>\n";
} else {
	generate_config_page('simple_title', $cfg);
}
?>