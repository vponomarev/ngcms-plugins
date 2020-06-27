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
						'title' => 'Р�РґРµРЅС‚РёС„РёРєР°С‚РѕСЂ Р±Р»РѕРєР°<br/><small>РџРѕ РґР°РЅРЅРѕРјСѓ ID РјРѕР¶РЅРѕ Р±СѓРґРµС‚ С„РѕСЂРјРёСЂРѕРІР°С‚СЊ РґР°РЅРЅС‹Р№ Р±Р»РѕРє С‡РµСЂРµР· РІС‹Р·РѕРІ <b>TWIG</b> С„СѓРЅРєС†РёРё <b>callPlugin()</b>',
						'type'  => 'input',
						'value' => pluginGetVariable($plugin, "{$currentVar}_name"))
	);

	array_push($cfgX, array(
						'name'		=> "{$currentVar}_template",
						'title'		=> 'Р�СЃРїРѕР»СЊР·СѓРµРјС‹Р№ С€Р°Р±Р»РѕРЅ',
						'type'		=> 'input',
//						'values'	=> $templateDirectories,
						'value'		=> pluginGetVariable($plugin, "{$currentVar}_template"))
	);

	array_push($cfgX, array(
						'name'		=> "{$currentVar}_visibilityMode",
						'title'		=> 'РћР±Р»Р°СЃС‚СЊ РІРёРґРёРјРѕСЃС‚Рё<br/><small>РЈРєР°Р¶РёС‚Рµ РЅР° РєР°РєРёС… СЃС‚СЂР°РЅРёС†Р°С… Р±СѓРґРµС‚ РѕС‚РѕР±СЂР°Р¶Р°С‚СЊСЃСЏ РґР°РЅРЅС‹Р№ Р±Р»РѕРє</small>',
						'type'		=> 'select',
						'values'	=> array('0' => 'Р’РµР·РґРµ', 1 => 'РќР° СЃС‚СЂР°РЅРёС†Рµ РєР°С‚РµРіРѕСЂРёР№', 2 => 'РќР° СЃС‚СЂР°РЅРёС†Рµ РЅРѕРІРѕСЃС‚РµР№', 3 => 'РЎС‚СЂР°РЅРёС†Р° РєР°С‚РµРіРѕСЂРёР№ + РЅРѕРІРѕСЃС‚РµР№'),
						'value'		=> pluginGetVariable($plugin, "{$currentVar}_visibilityMode"))
	);

	array_push($cfgX, array(
						'name'  => "{$currentVar}_visibilityCList",
						'title' => 'РЎРїРёСЃРѕРє РєР°С‚РµРіРѕСЂРёР№ РЅР° РєРѕС‚РѕСЂС‹С… РѕС‚РѕР±СЂР°Р¶Р°РµС‚СЃСЏ Р±Р»РѕРє<br/><small>РњРѕР¶РЅРѕ СѓРєР°Р·Р°С‚СЊ РєРѕРЅРєСЂРµС‚РЅС‹Рµ РєР°С‚РµРіРѕСЂРёРё РїСЂРё РІС‹Р±РѕСЂРµ <b>РєР°С‚РµРіРѕСЂРёРё/РЅРѕРІРѕСЃС‚Рё</b> РІ РїСЂРµРґС‹РґСѓС‰РµРј РїСѓРЅРєС‚Рµ',
						'type'  => 'input',
						'value' => pluginGetVariable($plugin, "{$currentVar}_visibilityCList"))
	);

	array_push($cfgX, array(
						'name'		=> "{$currentVar}_categoryMode",
						'title'		=> 'Р�Р· РєР°РєРёС… РєР°С‚РµРіРѕСЂРёР№ РіРµРЅРµСЂРёСЂСѓРµС‚СЃСЏ Р»РµРЅС‚Р° РЅРѕРІРѕСЃС‚РµР№',
						'type'		=> 'select',
						'values'	=> array('0' => 'РЎРїРёСЃРѕРє РєР°С‚РµРіРѕСЂРёР№', 1 => 'РўРµРєСѓС‰Р°СЏ РєР°С‚РµРіРѕСЂРёСЏ', 2 => 'РЎРїРёСЃРѕРє + С‚РµРєСѓС‰Р°СЏ'),
						'value'		=> pluginGetVariable($plugin, "{$currentVar}_categoryMode"))
	);

	array_push($cfgX, array(
						'name' => "{$currentVar}_categories",
						'title' => 'РЎРїРёСЃРѕРє РєР°С‚РµРіРѕСЂРёР№ РґР»СЏ РіРµРЅРµСЂР°С†РёРё Р»РµРЅС‚С‹<br/><small>Р—Р°РґР°С‘С‚СЃСЏ СЃРїРёСЃРѕРє РєР°С‚РµРіРѕСЂРёР№ (С‡РµСЂРµР· Р·Р°РїСЏС‚СѓСЋ) РїСЂРё РІС‹Р±РѕСЂРµ <b>СЃРїРёСЃРѕРє</b> РІ РїСЂРµРґС‹РґСѓС‰РµРј РїРѕР»Рµ. РћСЃС‚Р°РІСЊС‚Рµ РїРѕР»Рµ РїСѓСЃС‚С‹Рј РґР»СЏ РіРµРЅРµСЂР°С†РёРё Р»РµРЅС‚С‹ РїРѕ РІСЃРµРј РєР°С‚РµРіРѕСЂРёСЏРј</small>',
						'type' => 'input',
						'value' => pluginGetVariable($plugin, "{$currentVar}_categories"))
	);

	array_push($cfgX, array(
						'name'  => "{$currentVar}_mainMode",
						'title' => "РћС‚РѕР±СЂР°Р¶РµРЅРёРµ РЅРѕРІРѕСЃС‚РµР№ СЃ РіР»Р°РІРЅРѕР№ СЃС‚СЂР°РЅРёС†С‹<br/><small>Р’С‹Р±РµСЂРёС‚Рµ С‚РёРї РЅРѕРІРѕСЃС‚РµР№, РєРѕС‚РѕСЂС‹Рµ Р±СѓРґСѓС‚ РѕС‚РѕР±СЂР°Р¶Р°С‚СЊСЃСЏ РІ Р±Р»РѕРєРµ</small>",
						'type' => 'select',
						'value' => pluginGetVariable($plugin, "{$currentVar}_mainMode"),
						'values' => array('0' => 'Р’СЃРµ', 1 => 'РЎ РіР»Р°РІРЅРѕР№', 2 => 'РќРµ СЃ РіР»Р°РІРЅРѕР№'),
	));

	array_push($cfgX, array(
						'name'  => "{$currentVar}_pinMode",
						'title' => "РћС‚РѕР±СЂР°Р¶РµРЅРёРµ РїСЂРёРєСЂРµРїР»РµРЅРЅС‹С… РЅРѕРІРѕСЃС‚РµР№<br/><small>Р’С‹Р±РµСЂРёС‚Рµ С‚РёРї РЅРѕРІРѕСЃС‚РµР№, РєРѕС‚РѕСЂС‹Рµ Р±СѓРґСѓС‚ РѕС‚РѕР±СЂР°Р¶Р°С‚СЊСЃСЏ РІ Р±Р»РѕРєРµ</small>",
						'type' => 'select',
						'value' => pluginGetVariable($plugin, "{$currentVar}_pinMode"),
						'values' => array('0' => 'Р’СЃРµ', 1 => 'РџСЂРёРєСЂРµРїР»РµРЅРЅС‹Рµ', 2 => 'РќРµ РїСЂРёРєСЂРµРїР»РµРЅРЅС‹Рµ'),
	));

	array_push($cfgX, array(
						'name'  => "{$currentVar}_favMode",
						'title' => "РћС‚РѕР±СЂР°Р¶РµРЅРёРµ РЅРѕРІРѕСЃС‚РµР№ РёР· Р·Р°РєР»Р°РґРѕРє<br/><small>Р’С‹Р±РµСЂРёС‚Рµ С‚РёРї РЅРѕРІРѕСЃС‚РµР№, РєРѕС‚РѕСЂС‹Рµ Р±СѓРґСѓС‚ РѕС‚РѕР±СЂР°Р¶Р°С‚СЊСЃСЏ РІ Р±Р»РѕРєРµ</small>",
						'type' => 'select',
						'value' => pluginGetVariable($plugin, "{$currentVar}_favMode"),
						'values' => array('0' => 'Р’СЃРµ', 1 => 'РўРѕР»СЊРєРѕ РёР· Р·Р°РєР»Р°РґРѕРє', 2 => 'РќРµ РґРѕР±Р°РІР»РµРЅРЅС‹Рµ РІ Р·Р°РєР»Р°РґРєРё'),
	));

	array_push($cfgX, array(
						'name'  => "{$currentVar}_skipCurrent",
						'title' => "РќРµ РѕС‚РѕР±СЂР°Р¶Р°С‚СЊ РІ Р±Р»РѕРєРµ С‚РµРєСѓС‰СѓСЋ РЅРѕРІРѕСЃС‚СЊ<br/><small>Р”Р°РЅРЅС‹Р№ СЂРµР¶РёРј РЅРµ РїРѕР·РІРѕР»СЏРµС‚ РёСЃРїРѕР»СЊР·РѕРІР°С‚СЊ РєРµС€РёСЂРѕРІР°РЅРёРµ Р±Р»РѕРєРѕРІ Рё РїРѕРІС‹С€Р°РµС‚ РЅР°РіСЂСѓР·РєСѓ РЅР° СЃРёСЃС‚РµРјСѓ</small>",
						'type' => 'select',
						'value' => pluginGetVariable($plugin, "{$currentVar}_skipCurrent"),
						'values' => array('0' => 'РќРµС‚', 1 => 'Р”Р°'),
	));

	array_push($cfgX, array(
						'name'  => "{$currentVar}_extractEmbeddedItems",
						'title' => "Р�Р·РІР»РµРєР°С‚СЊ URL'С‹ РёР·РѕР±СЂР°Р¶РµРЅРёР№ РёР· С‚РµРєСЃС‚Р° РЅРѕРІРѕСЃС‚Рё<br/><small>РЎРїРёСЃРѕРє URL'РѕРІ Р±СѓРґРµС‚ РґРѕСЃС‚СѓРїРµРЅ РІ РјР°СЃСЃРёРІРµ news.embed.images, РєРѕР»-РІРѕ - РІ news.embed.imgCount</small>",
						'type' => 'select',
						'value' => pluginGetVariable($plugin, "{$currentVar}_extractEmbeddedItems"),
						'values' => array('0' => 'РќРµС‚', 1 => 'Р”Р°'),
	));

	array_push($cfgX, array(
					'name' => "{$currentVar}_showNoNews",
					'title' => 'Р’С‹РІРѕРґРёС‚СЊ Р±Р»РѕРє РµСЃР»Рё РІ РЅС‘Рј РЅРµС‚ РЅРѕРІРѕСЃС‚РµР№',
					'type' => 'checkbox',
					'value' => pluginGetVariable($plugin ,"{$currentVar}_showNoNews"))
	);

	array_push($cfgX, array(
						'name'  => "{$currentVar}_count",
						'title' => $lang['tn:number_title'],
						'type'  => 'input',
						'value' => intval(pluginGetVariable($plugin, "{$currentVar}_count")) ? pluginGetVariable($plugin, "{$currentVar}_count") : '10')
	);

	array_push($cfgX, array(
						'name'  => "{$currentVar}_skip",
						'title' => 'РџСЂРѕРїСѓСЃС‚РёС‚СЊ РїРµСЂРІС‹Рµ <b>X</b> РЅРѕРІРѕСЃС‚РµР№ РїСЂРё РїРѕРєР°Р·Рµ Р±Р»РѕРєР°<br/><small>Р—РЅР°С‡РµРЅРёРµ РїРѕ СѓРјРѕР»С‡Р°РЅРёСЋ: 0',
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
