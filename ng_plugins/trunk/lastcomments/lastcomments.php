<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

add_act('index', 'lastcomments_block');
register_plugin_page('lastcomments','','lastcomments_page',0);


function lastcomments_block() {
	// Action if sidepanel is enabled
	if (extra_get_param('lastcomments','sidepanel')) {
		lastcomments();
	}
}


function lastcomments_page() {
	// Action if sidepanel is enabled
	if (extra_get_param('lastcomments','ppage')) {
		lastcomments(1);
	}
}

function lastcomments($pp = 0) {
    global $config, $mysql, $template, $tvars, $tpl, $parse, $TemplateCache, $SYSTEM_FLAGS;

	$page = 1;
	if ($pp) { 
		$pp = 'pp_'; 
		if(isset($params['page']) && $params['page']) { $page = intval($params['page']);}
		else if(isset($_REQUEST['page']) && $_REQUEST['page']) { $page = intval($_REQUEST['page']);}
		$SYSTEM_FLAGS['info']['title']['group'] = pluginGetVariable('lastcomments', 'title').($page > 1 ? ' - страница '.$page : '');
	} else { $pp = '';}

	

	// Generate cache file name [ we should take into account SWITCHER plugin & calling parameters ]
	$cacheFileName = md5('lastcomments'.$config['theme'].$config['default_lang'].$pp.$page).'.txt';

	if (extra_get_param('lastcomments','cache')) {
		$cacheData = cacheRetrieveFile($cacheFileName, extra_get_param('lastcomments','cacheExpire'), 'lastcomments');
		if ($cacheData != false) {
			// We got data from cache. Return it and stop
			$template['vars'][($pp)?'mainblock':'plugin_lastcomments'] = $cacheData;
			return;
		}
	}

	$number =  intval(pluginGetVariable('lastcomments', $pp.'number'));
	if ($number < 1) { $number = 10; }
	$comm_length	= intval(extra_get_param('lastcomments',$pp.'comm_length'));
	if (($comm_length < 10) || ($comm_length > ($pp?500:100))) { $comm_length = $pp?500:50; }
	if ($pp){
		$limit_count = intval(pluginGetVariable('lastcomments', $pp.'limit_count'));
		if ($limit_count < 1) { $limit_count = 10; }
	}
	else {
		$limit_count = $number;
	}


	// Determine paths for all template files
	$tpath = locatePluginTemplates(array($pp.'lastcomments', $pp.'entries'), 'lastcomments', extra_get_param('lastcomments', 'localsource'));

	$query = "select c.id, c.postdate, c.author, c.author_id, c.text, n.id as nid, n.title, n.alt_name, n.catid, n.postdate as npostdate from ".prefix."_comments c left join ".prefix."_news n on c.post=n.id order by c.id desc limit ".($page - 1) * $limit_count.", ".$limit_count;

	$lastcomments = '';
	foreach ($mysql->select($query) as $row) {

		// Parse comments
		$text = $row['text'];
		if ($config['blocks_for_reg'])		{ $text = $parse -> userblocks($text); }
		if ($config['use_htmlformatter'])	{ $text = $parse -> htmlformatter($text); }
		if ($config['use_bbcodes'])			{ $text = $parse -> bbcodes($text); }
		if ($config['use_smilies'])			{ $text = $parse -> smilies($text); }
		if (!$pp && intval(pluginGetVariable('lastcomments', 'cutbr'))){
			$text = str_ireplace('<br />', ' ', $text);
		}
	    if (strlen($text) > $comm_length)	{ $text = $parse -> truncateHTML($text, $comm_length);}

		$tvars['vars'] = array(
			'link'		=>	newsGenerateLink(array('id' => $row['nid'], 'alt_name' => $row['alt_name'], 'catid' => $row['catid'], 'postdate' => $row['npostdate'])),
			'date'		=>	langdate('d.m.Y', $row['postdate']),
			'author'	=>	str_replace('<', '&lt;', $row['author']),
			'author_id'	=>	$row['author_id'],
			'title'		=>	str_replace('<', '&lt;', $row['title']),
			'text'		=>	$text,
			'category_link'	=>	GetCategories($row['catid'])
		);

		if ($row['author_id'] && getPluginStatusActive('uprofile')) {
			$tvars['vars']['author_link'] = checkLinkAvailable('uprofile', 'show')?
				generateLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id'])):
				generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('id' => $row['author_id']));
			$tvars['regx']["'\[profile\](.*?)\[/profile\]'si"] = '$1';
		} else {
			$tvars['vars']['author_link'] = '';
			$tvars['regx']["'\[profile\](.*?)\[/profile\]'si"] = '';
		}

        $tpl -> template($pp.'entries', $tpath[$pp.'entries']);
        $tpl -> vars($pp.'entries', $tvars);
        $lastcomments .= $tpl -> show($pp.'entries');
    }
	
	$page_count = 1;
	if ($pp) $page_count = ceil($number / $limit_count);
	if ($page_count > 1) {
		$paginationParams = checkLinkAvailable('lastcomments', '')?
			array('pluginName' => 'lastcomments', 'pluginHandler' => '', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)):
			array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'lastcomments'), 'xparams' => array(), 'paginator' => array('page', 1, false));
		templateLoadVariables(true); 
		$navigations = $TemplateCache['site']['#variables']['navigation'];
		$lastcomments .= generatePagination($page, 1, $page_count, 10, $paginationParams, $navigations);
	}

    $tpl -> template($pp.'lastcomments', $tpath[$pp.'lastcomments']);
    $tpl -> vars($pp.'lastcomments', array ('vars' => array ('entries' => $lastcomments)));

	$output = $tpl -> show($pp.'lastcomments');
	

	$template['vars'][($pp)?'mainblock':'plugin_lastcomments'] = $output;

	if (extra_get_param('lastcomments','cache')) {
		cacheStoreFile($cacheFileName, $output, 'lastcomments');
	}
}