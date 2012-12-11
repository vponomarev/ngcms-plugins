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
						'title' => '������������� �����<br/><small>�� ������� ID ����� ����� ����������� ������ ���� ����� ����� <b>TWIG</b> ������� <b>callPlugin()</b>',
						'type'  => 'input',
						'value' => pluginGetVariable($plugin, "{$currentVar}_name"))
	);

	array_push($cfgX, array(
						'name'		=> "{$currentVar}_template",
						'title'		=> '������������ ������',
						'type'		=> 'input',
//						'values'	=> $templateDirectories,
						'value'		=> pluginGetVariable($plugin, "{$currentVar}_template"))
	);

	array_push($cfgX, array(
						'name'		=> "{$currentVar}_visibilityMode",
						'title'		=> '������� ���������<br/><small>������� �� ����� ��������� ����� ������������ ������ ����</small>',
						'type'		=> 'select',
						'values'	=> array('0' => '�����', 1 => '�� �������� ���������', 2 => '�� �������� ��������', 3 => '�������� ��������� + ��������'),
						'value'		=> pluginGetVariable($plugin, "{$currentVar}_visibilityMode"))
	);

	array_push($cfgX, array(
						'name'  => "{$currentVar}_visibilityCList",
						'title' => '������ ��������� �� ������� ������������ ����<br/><small>����� ������� ���������� ��������� ��� ������ <b>���������/�������</b> � ���������� ������',
						'type'  => 'input',
						'value' => pluginGetVariable($plugin, "{$currentVar}_visibilityCList"))
	);

	array_push($cfgX, array(
						'name'		=> "{$currentVar}_categoryMode",
						'title'		=> '�� ����� ��������� ������������ ����� ��������',
						'type'		=> 'select',
						'values'	=> array('0' => '������ ���������', 1 => '������� ���������', 2 => '������ + �������'),
						'value'		=> pluginGetVariable($plugin, "{$currentVar}_categoryMode"))
	);

	array_push($cfgX, array(
						'name' => "{$currentVar}_categories",
						'title' => '������ ��������� ��� ��������� �����<br/><small>������� ������ ��������� (����� �������) ��� ������ <b>������</b> � ���������� ����. �������� ���� ������ ��� ��������� ����� �� ���� ����������</small>',
						'type' => 'input',
						'value' => pluginGetVariable($plugin, "{$currentVar}_categories"))
	);

	array_push($cfgX, array(
						'name'  => "{$currentVar}_mainMode",
						'title' => "����������� �������� � ������� ��������<br/><small>�������� ��� ��������, ������� ����� ������������ � �����</small>",
						'type' => 'select',
						'value' => pluginGetVariable($plugin, "{$currentVar}_mainMode"),
						'values' => array('0' => '���', 1 => '� �������', 2 => '�� � �������'),
	));

	array_push($cfgX, array(
						'name'  => "{$currentVar}_pinMode",
						'title' => "����������� ������������� ��������<br/><small>�������� ��� ��������, ������� ����� ������������ � �����</small>",
						'type' => 'select',
						'value' => pluginGetVariable($plugin, "{$currentVar}_pinMode"),
						'values' => array('0' => '���', 1 => '�������������', 2 => '�� �������������'),
	));

	array_push($cfgX, array(
						'name'  => "{$currentVar}_favMode",
						'title' => "����������� �������� �� ��������<br/><small>�������� ��� ��������, ������� ����� ������������ � �����</small>",
						'type' => 'select',
						'value' => pluginGetVariable($plugin, "{$currentVar}_favMode"),
						'values' => array('0' => '���', 1 => '������ �� ��������', 2 => '�� ����������� � ��������'),
	));

	array_push($cfgX, array(
						'name'  => "{$currentVar}_skipCurrent",
						'title' => "�� ���������� � ����� ������� �������<br/><small>������ ����� �� ��������� ������������ ����������� ������ � �������� �������� �� �������</small>",
						'type' => 'select',
						'value' => pluginGetVariable($plugin, "{$currentVar}_skipCurrent"),
						'values' => array('0' => '���', 1 => '��'),
	));

	array_push($cfgX, array(
					'name' => "{$currentVar}_showEmpty",
					'title' => '�������� ���� ���� � �� ��� ��������',
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
						'title' => '���������� ������ <b>X</b> �������� ��� ������ �����<br/><small>�������� �� ���������: 0',
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
