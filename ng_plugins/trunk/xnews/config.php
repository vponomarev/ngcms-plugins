<?php


# protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

# preload config file
pluginsLoadConfig();

LoadPluginLang('xnews', 'config', '', 'tn', ':');


$count = intval(pluginGetVariable($plugin, 'count'));
if ($count < 1 || $count > 50)
	$count = 1;

# fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => $lang['tn:description']));
array_push($cfg, array('name'  => 'count',
                       'title' => $lang['tn:count_title'],
                       'type'  => 'input',
                       'value' => $count));

for ($i = 1; $i <= $count; $i++) {
	$cfgX = array();

	$currentVar = "{$i}";

	array_push($cfgX, array(
						'name'  => "{$currentVar}_name",
						'title' => 'Идентификатор блока<br/><small>По данному ID можно будет формировать данный блок через вызов <b>TWIG</b> функции <b>callPlugin()</b>',
						'type'  => 'input',
						'value' => pluginGetVariable($plugin, "{$currentVar}_name"))
	);

	array_push($cfgX, array(
						'name'		=> "{$currentVar}_template",
						'title'		=> 'Используемый шаблон',
						'type'		=> 'input',
//						'values'	=> $templateDirectories,
						'value'		=> pluginGetVariable($plugin, "{$currentVar}_template"))
	);

	array_push($cfgX, array(
						'name'		=> "{$currentVar}_visibilityMode",
						'title'		=> 'Область видимости<br/><small>Укажите на каких страницах будет отображаться данный блок</small>',
						'type'		=> 'select',
						'values'	=> array('0' => 'Везде', 1 => 'На странице категорий', 2 => 'На странице новостей', 3 => 'Страница категорий + новостей'),
						'value'		=> pluginGetVariable($plugin, "{$currentVar}_visibilityMode"))
	);

	array_push($cfgX, array(
						'name'  => "{$currentVar}_visibilityCList",
						'title' => 'Список категорий на которых отображается блок<br/><small>Можно указать конкретные категории при выборе <b>категории/новости</b> в предыдущем пункте',
						'type'  => 'input',
						'value' => pluginGetVariable($plugin, "{$currentVar}_visibilityCList"))
	);

	array_push($cfgX, array(
						'name'		=> "{$currentVar}_categoryMode",
						'title'		=> 'Из каких категорий генерируется лента новостей',
						'type'		=> 'select',
						'values'	=> array('0' => 'Список категорий', 1 => 'Текущая категория', 2 => 'Список + текущая'),
						'value'		=> pluginGetVariable($plugin, "{$currentVar}_categoryMode"))
	);

	array_push($cfgX, array(
						'name' => "{$currentVar}_categories",
						'title' => 'Список категорий для генерации ленты<br/><small>Задаётся список категорий (через запятую) при выборе <b>список</b> в предыдущем поле. Оставьте поле пустым для генерации ленты по всем категориям</small>',
						'type' => 'input',
						'value' => pluginGetVariable($plugin, "{$currentVar}_categories"))
	);

	array_push($cfgX, array(
						'name'  => "{$currentVar}_mainMode",
						'title' => "Отображение новостей с главной страницы<br/><small>Выберите тип новостей, которые будут отображаться в блоке</small>",
						'type' => 'select',
						'value' => pluginGetVariable($plugin, "{$currentVar}_mainMode"),
						'values' => array('0' => 'Все', 1 => 'С главной', 2 => 'Не с главной'),
	));

	array_push($cfgX, array(
						'name'  => "{$currentVar}_pinMode",
						'title' => "Отображение прикрепленных новостей<br/><small>Выберите тип новостей, которые будут отображаться в блоке</small>",
						'type' => 'select',
						'value' => pluginGetVariable($plugin, "{$currentVar}_pinMode"),
						'values' => array('0' => 'Все', 1 => 'Прикрепленные', 2 => 'Не прикрепленные'),
	));

	array_push($cfgX, array(
						'name'  => "{$currentVar}_favMode",
						'title' => "Отображение новостей из закладок<br/><small>Выберите тип новостей, которые будут отображаться в блоке</small>",
						'type' => 'select',
						'value' => pluginGetVariable($plugin, "{$currentVar}_favMode"),
						'values' => array('0' => 'Все', 1 => 'Только из закладок', 2 => 'Не добавленные в закладки'),
	));

	array_push($cfgX, array(
						'name'  => "{$currentVar}_skipCurrent",
						'title' => "Не отображать в блоке текущую новость<br/><small>Данный режим не позволяет использовать кеширование блоков и повышает нагрузку на систему</small>",
						'type' => 'select',
						'value' => pluginGetVariable($plugin, "{$currentVar}_skipCurrent"),
						'values' => array('0' => 'Нет', 1 => 'Да'),
	));

	array_push($cfgX, array(
					'name' => "{$currentVar}_showEmpty",
					'title' => 'Выводить блок если в нём нет новостей',
					'type' => 'checkbox',
					'value' => pluginGetVariable($plugin ,"{$currentVar}_showEmpty"))
	);

	array_push($cfgX, array(
						'name'  => "{$currentVar}_count",
						'title' => $lang['tn:number_title'],
						'type'  => 'input',
						'value' => intval(pluginGetVariable($plugin, "{$currentVar}_count")) ? pluginGetVariable($plugin, "{$currentVar}_count") : '10')
	);

	array_push($cfgX, array(
						'name'  => "{$currentVar}_skip",
						'title' => 'Пропустить первые <b>X</b> новостей при показе блока<br/><small>Значение по умолчанию: 0',
						'type'  => 'input',
						'value' => intval(pluginGetVariable($plugin, "{$currentVar}_skip")) ? pluginGetVariable($plugin , "{$currentVar}_skip") : '0')
	);

	array_push($cfgX, array(
						'name'  => "{$currentVar}_maxAge",
						'title' => $lang['tn:date'],
						'type'  => 'input',
						'value' => intval(pluginGetVariable($plugin, "{$currentVar}_maxAge")))
	);

	$orderby = array(
				'viewed'    => $lang['tn:orderby_views'],
				'commented' => $lang['tn:orderby_comments'],
				'random'   => $lang['tn:orderby_random'],
				'last'     => $lang['tn:orderby_last']
	);

	array_push($cfgX, array(
						'name'   => "{$currentVar}_order",
						'type'   => 'select',
						'title'  => $lang['tn:orderby_title'],
						'values' => $orderby,
						'value'  => pluginGetVariable($plugin, "{$currentVar}_order"))
	);

/*
	array_push($cfgX, array(
						'name' => "{$currentVar}_content",
						'title' => $lang['tn:content'],
						'type' => 'checkbox',
						'value' => pluginGetVariable($plugin ,"{$currentVar}_content"))
	);
	array_push($cfgX, array(
						'name'  => "{$currentVar}_img",
						'title' => $lang['tn:img'],
						'type'  => 'checkbox',
						'value' => pluginGetVariable('xnews',"{$currentVar}_img"))
	);
*/
	$blockName = pluginGetVariable($plugin, "{$currentVar}_name") ? pluginGetVariable('xnews', "{$currentVar}_name") : '# '.$currentVar;
	array_push($cfg,  array(
					'mode'        => 'group',
					'title'       => $lang['tn:group'].$blockName,
					'toggle'      => '1',
					'toggle.mode' => 'hide',
					'entries'     => $cfgX)
	);
}

/*
$cfgX = array();
array_push($cfgX, array(
					'name'   => 'localsource',
					'title'  => $lang['tn:localsource'],
					'type'   => 'select',
					'values' => array ( '0' => $lang['tn:localsource_0'], '1' => $lang['tn:localsource_1']),
					'value'  => intval(pluginGetVariable($plugin, 'localsource')))
);
array_push($cfg, array(
					'mode'    => 'group',
					'title'   => $lang['tn:group_2'],
					'entries' => $cfgX)
);
*/

$cfgX = array();
array_push($cfgX, array(
					'name'   => 'cache',
					'title'  => $lang['tn:cache'],
					'type'   => 'select',
					'values' => array ( '1' => $lang['yesa'], '0' => $lang['noa']),
					'value'  => intval(pluginGetVariable($plugin, 'cache')))
);
array_push($cfgX, array(
					'name'  => 'cacheExpire',
					'title' => $lang['tn:cacheExpire'],
					'type' => 'input',
					'value' => intval(pluginGetVariable($plugin, 'cacheExpire')) ? pluginGetVariable($plugin, 'cacheExpire') :'60')
);
array_push($cfg,  array(
					'mode'    => 'group',
					'title'   => $lang['tn:group_3'],
					'entries' => $cfgX)
);


# RUN
if ($_REQUEST['action'] == 'commit') {
	# if submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}
