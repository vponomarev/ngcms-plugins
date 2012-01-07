<?php

/*
 * top_news for NextGeneration CMS (http://ngcms.ru/)
 * Copyright (C) 2010-2011 Alexey N. Zhukov (http://digitalplace.ru)
 * http://digitalplace.ru
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */
 
# protect against hack attempts
if (!defined('NGCMS')) die ('Galaxy in danger');

# preload config file
pluginsLoadConfig();

LoadPluginLang('top_news', 'config', '', 'tn', ':');

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
	
	$currentVar = "top_news{$i}";
	
	array_push($cfgX, array(
						'name'  => "{$currentVar}_number", 
						'title' => $lang['tn:number_title'], 
						'type'  => 'input', 
						'value' => intval(pluginGetVariable($plugin, "{$currentVar}_number")) ? pluginGetVariable($plugin, "{$currentVar}_number") : '10')
	);
	array_push($cfgX, array(
						'name'  => "{$currentVar}_maxlength", 
						'title' => $lang['tn:maxlength'], 
						'type'  => 'input', 
						'value' => intval(pluginGetVariable($plugin, "{$currentVar}_maxlength")) ? pluginGetVariable($plugin , "{$currentVar}_maxlength") : '100')
	);
	array_push($cfgX, array(
						'name'  => "{$currentVar}_newslength", 
						'title' => $lang['tn:newslength'], 
						'type'  => 'input', 
						'value' => intval(pluginGetVariable($plugin, "{$currentVar}_newslength")) ? pluginGetVariable($plugin , "{$currentVar}_newslength") : '100')
	);
	array_push($cfgX, array(
						'name'  => "{$currentVar}_offset", 
						'title' => $lang['tn:offset'], 
						'type'  => 'input', 
						'value' => intval(pluginGetVariable($plugin, "{$currentVar}_offset")) ? pluginGetVariable($plugin , "{$currentVar}_offset") : '1')
	);
	array_push($cfgX, array(
						'name'  => "{$currentVar}_date", 
						'title' => $lang['tn:date'], 
						'type'  => 'input', 
						'value' => intval(pluginGetVariable($plugin, "{$currentVar}_date")))
	);
	
	$orderby = array(
				'views'    => $lang['tn:orderby_views'], 
				'comments' => $lang['tn:orderby_comments'], 
				'random'   => $lang['tn:orderby_random'], 
				'last'     => $lang['tn:orderby_last']
	);
	
	array_push($cfgX, array(
						'name'   => "{$currentVar}_orderby", 
						'type'   => 'select', 
						'title'  => $lang['tn:orderby_title'], 
						'values' => $orderby, 
						'value'  => pluginGetVariable($plugin, "{$currentVar}_orderby"))
	);
	array_push($cfgX, array(
						'name' => "{$currentVar}_categories", 
						'title' => $lang['tn:categories'], 
						'type' => 'input',
						'value' => pluginGetVariable($plugin, "{$currentVar}_categories"))
	);
	array_push($cfgX, array(
						'name' => "{$currentVar}_ifcategory", 
						'title' => $lang['tn:ifcategory'], 
						'type' => 'checkbox', 
						'value' => pluginGetVariable($plugin, "{$currentVar}_ifcategory"))
	);
	array_push($cfgX, array(
						'name' => "{$currentVar}_content",
						'title' => $lang['tn:content'],
						'type' => 'checkbox',
						'value' => pluginGetVariable($plugin ,"{$currentVar}_content"))
	);
	array_push($cfgX, array(
						'name'  => "{$currentVar}_mainpage",
						'title' => $lang['tn:mainpage'], 
						'type' => 'checkbox', 
						'value' => pluginGetVariable($plugin, "{$currentVar}_mainpage"))
	);
	array_push($cfgX, array(
						'name'  => "{$currentVar}_img", 
						'title' => $lang['tn:img'], 
						'type'  => 'checkbox', 
						'value' => pluginGetVariable('top_news',"{$currentVar}_img"))
	);
	array_push($cfgX, array(
						'name'  => "{$currentVar}_name", 
						'title' => str_replace('currentVar', $currentVar, $lang['tn:name']), 
						'type'  => 'input', 
						'value' => pluginGetVariable($plugin, "{$currentVar}_name"))
	);
	array_push($cfgX, array(
						'name'  => "{$currentVar}_dateformat", 
						'title' => $lang['tn:dateformat'], 
						'descr' => $lang['tn:dateformat_descr'], 
						'type'  => 'input', 
						'value' => pluginGetVariable($plugin, "{$currentVar}_dateformat"))
	);
	
	$blockName = pluginGetVariable($plugin, "{$currentVar}_name") ? 'top_news_'.pluginGetVariable('top_news', "{$currentVar}_name") : $currentVar;
	array_push($cfg,  array(
					'mode'        => 'group', 
					'title'       => $lang['tn:group'].$blockName,
					'toggle'      => '1', 
					'toggle.mode' => 'hide', 
					'entries'     => $cfgX)
	);
}

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
