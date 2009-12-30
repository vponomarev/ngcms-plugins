<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

class TagsNewsfilter extends NewsFilter {
	function addNewsForm(&$tvars) {
	        global $tpl;
		$tpath = locatePluginTemplates(array('tags_addnews'), 'tags', extra_get_param('tags', 'localsource'), extra_get_param('tags', 'skin')?extra_get_param('tags', 'skin'):'default');

		$tpl -> template('tags_addnews', $tpath['tags_addnews']);
		$tpl -> vars('tags_addnews', array ( 'vars' => array ()));
		$tvars['vars']['plugin_tags'] = $tpl -> show('tags_addnews');

		return 1;
	}
	function addNews(&$tvars, &$SQL) {
		// Scan tags, delete dups
		$tags = array();
		foreach (explode(",", $_REQUEST['tags']) as $tag) {
			$tag = trim($tag);
			if (!strlen($tag)) continue;
			$tags[$tag] = 1;
		}

		// Make a resulting line
		$SQL['tags']   = sizeof($tags)?join(", ", array_keys($tags)):'';

		return 1;
	}

	function addNewsNotify(&$tvars, $SQL, $newsid) {
		global $mysql;

		// Make activities only in case when news is marked as 'published'
		if (!$SQL['approve'])
			return 1;

		// New Tags
		$tagsNew = array();
		$tagsNewQ = array();
		foreach (explode(",", $SQL['tags']) as $tag) {
			$tag = trim($tag);
			if (!strlen($tag)) continue;
			$tagsNew[] = $tag;
			$tagsNewQ[] = db_squote($tag);
		}

		// Update counters for TAGS - add
		if (sizeof($tagsNewQ))
			foreach ($tagsNewQ as $tag)
				$mysql->query("insert into ".prefix."_tags (tag) values (".$tag.") on duplicate key update posts = posts + 1");

		// Recreate indexes for this news
		if (sizeof($tagsNewQ))
			$mysql->query("insert into ".prefix."_tags_index (newsID, tagID) select ".db_squote($newsid).", id from ".prefix."_tags where tag in (".join(",",$tagsNewQ).")");

		return 1;
	}

	function editNewsForm($newsID, $SQLold, &$tvars) {
	        global $tpl;
		$tpath = locatePluginTemplates(array('tags_editnews'), 'tags', extra_get_param('tags', 'localsource'), extra_get_param('tags', 'skin')?extra_get_param('tags', 'skin'):'default');

		$tpl -> template('tags_editnews', $tpath['tags_editnews']);
		$tpl -> vars('tags_editnews', array ( 'vars' => array ( 'tags' => secure_html($SQLold['tags']))));
		$tvars['vars']['plugin_tags'] = $tpl -> show('tags_editnews');

		return 1;
	}

	function editNews($newsID, $SQLold, &$SQLnew, &$tvars) {
		// Scan tags, delete dups
		$tags = array();
		foreach (explode(",", $_REQUEST['tags']) as $tag) {
			$tag = trim($tag);
			if (!strlen($tag)) continue;
			$tags[$tag] = 1;
		}

		// Make a resulting line
		$SQLnew['tags']   = sizeof($tags)?join(", ", array_keys($tags)):'';
		return 1;
	}

	// Make changes in DB after EditNews was successfully executed
	function editNewsNotify($newsID, $SQLnews, &$SQLnew, &$tvars) {
		global $mysql;


		// If we edit unpublished news - no action
		if ((!$SQLnews['approve']) && (!$SQLnew['approve']))
			return 1;

		// OLD Tags
		$tagsOld = array();
		$tagsOldQ = array();

		// Mark OLD tags only if news was published before
		if ($SQLnews['approve'])
			foreach (explode(",", $SQLnews['tags']) as $tag) {
				$tag = trim($tag);
				if (!strlen($tag)) continue;
				$tagsOld[]  = $tag;
				$tagsOldQ[] = db_squote($tag);
			}

		// New Tags
		$tagsNew = array();
		$tagsNewQ = array();

		// Mark NEW tags only if news will stay/become published
		if ($SQLnew['approve'])
			foreach (explode(",", $SQLnew['tags']) as $tag) {
				$tag = trim($tag);
				if (!strlen($tag)) continue;
				$tagsNew[] = $tag;
				$tagsNewQ[] = db_squote($tag);
			}

		// List of deleted tags
		$tagsDelQ = array_diff($tagsOldQ, $tagsNewQ);
		$tagsAddQ = array_diff($tagsNewQ, $tagsOldQ);
		$tagsDiffQ = array_merge($tagsDelQ, $tagsAddQ);

		// Delete tag indexes for news
		$mysql->query("delete from ".prefix."_tags_index where newsID = ".$newsID);

		// Update conters for TAGS - delete old tags
		if (sizeof($tagsDelQ))
			$mysql->query("update ".prefix."_tags set posts = posts - 1 where tag in (".join(",",$tagsDelQ).")");

		// Delete unused tags
		$mysql->query("delete from ".prefix."_tags where posts = 0");

		// Update counters for TAGS - add
		if (sizeof($tagsAddQ))
			foreach ($tagsAddQ as $tag)
				$mysql->query("insert into ".prefix."_tags (tag) values (".$tag.") on duplicate key update posts = posts + 1");

		// Recreate indexes for this news
		if (sizeof($tagsNewQ))
			$mysql->query("insert into ".prefix."_tags_index (newsID, tagID) select ".db_squote($newsID).", id from ".prefix."_tags where tag in (".join(",",$tagsNewQ).")");

		return 1;
	}

	// Add {plugin_tags_news} variable into news
	function showNews($newsID, $SQLnews, &$tvars, $mode = array()) {
		global $mysql, $tpl;

		// Check if we have tags in news
		if (!$SQLnews['tags'] && !pluginGetVariable('tags', 'show_always')) {
			$tvars['regx']["'\[tags\](.*?)\[/tags\]'si"] = '';
			$tvars['vars']['tags'] = '';
			return 1;
		}

		// Load params for display (if needed)
		if (!is_array($this->displayParams)) {
			$tpath = locatePluginTemplates(array(':params.ini'), 'tags', extra_get_param('tags', 'localsource'), extra_get_param('tags', 'skin')?extra_get_param('tags', 'skin'):'default');
			$this->displayParams = parse_ini_file($tpath[':params.ini'].'params.ini');
		}

		// Make a line for display
		$tags = array();
		foreach (explode(",", $SQLnews['tags']) as $tag) {
			$tag = trim($tag);
			if (!$tag) continue;

		    $link = checkLinkAvailable('tags', 'tag')?
						generateLink('tags', 'tag', array('tag' => $tag)):
						generateLink('core', 'plugin', array('plugin' => 'tags', 'handler' => 'tag'), array('tag' => $tag));
			$tags[] = str_replace(array('{url}', '{tag}'), array($link, $tag), $this->displayParams['news.tag']);
		}

		$tvars['vars']['tags'] = count($tags)?(join($this->displayParams['news.tag.delimiter'], $tags)):$this->displayParams['news.notags'];
		$tvars['vars']['[tags]'] = '';
		$tvars['vars']['[/tags]'] = '';

		return 1;
	}

	// Delete news call
	function deleteNews($newsID, $SQLnews) {
		global $mysql;

		$mysql->query("update ".prefix."_tags set posts = posts-1 where id in (select tagID from ".prefix."_tags_index where newsID=".intval($newsID).")");
		$mysql->query("delete from ".prefix."_tags_index where newsID = ".intval($newsID));
		$mysql->query("delete from ".prefix."_tags where posts = 0");

		return 1;
	}

	// Mass news modify
	function massModifyNewsNotify($idList, $setValue, $currentData) {
		global $mysql;

		// We are interested only in 'approve' field modification
		if (!isset($setValue['approve']))
			return 1;

		// Catch a list of changed news
		$modList = array();
		foreach ($currentData as $newsID => $newsData)
			if ($newsData['approve'] != $setValue['approve'])
				$modList [] = $newsID;

		// If no news was changed - exit
		if (!count($modList))
			return 1;

		// Now we have a list of modified news. Let's process this news
		if ($setValue['approve']) {
			// * APPROVE NEWS ACTION
			foreach ($mysql->select("select id, tags from ".prefix."_news where id in (".join(", ", $modList).")") as $SQL) {
				$newsid = $SQL['id'];

				// New Tags
				$tagsNew = array();
				$tagsNewQ = array();
				foreach (explode(",", $SQL['tags']) as $tag) {
					$tag = trim($tag);
					if (!$tag) continue;
					$tagsNew[] = $tag;
					$tagsNewQ[] = db_squote($tag);
				}

				// Update counters for TAGS - add
				if (sizeof($tagsNewQ))
					foreach ($tagsNewQ as $tag)
						$mysql->query("insert into ".prefix."_tags (tag) values (".$tag.") on duplicate key update posts = posts + 1");

				// Recreate indexes for this news
				if (sizeof($tagsNewQ))
					$mysql->query("insert into ".prefix."_tags_index (newsID, tagID) select ".db_squote($newsid).", id from ".prefix."_tags where tag in (".join(",",$tagsNewQ).")");
			}
		} else {
			// * UNAPPROVE NEWS ACTION
			foreach ($modList as $newsID) {
				$mysql->query("update ".prefix."_tags set posts = posts-1 where id in (select tagID from ".prefix."_tags_index where newsID=".intval($newsID).")");
			}
			$mysql->query("delete from ".prefix."_tags_index where newsID in (".join(", ", $modList).")");
			$mysql->query("delete from ".prefix."_tags where posts = 0");
		}
		return 1;
	}
}

register_filter('news','tags', new TagsNewsFilter);
register_plugin_page('tags','','plugin_tags_cloud');
register_plugin_page('tags','tag','plugin_tags_tag');
add_act('index', 'plugin_tags_cloudblock');

//
// Show tags cloud
function plugin_tags_cloud(){
	global $tpl, $template, $mysql, $lang, $SYSTEM_FLAGS;

	plugin_tags_generatecloud(1);
}

//
// Show side cloud block
function plugin_tags_cloudblock() {
	plugin_tags_generatecloud(0);
}

//
// Show current tag
function plugin_tags_tag() {
	global $tpl, $template, $mysql, $lang, $SYSTEM_FLAGS, $CurrentHandler, $TemplateCache;

	// Determine MONTH and YEAR for current show process
	if (($CurrentHandler['pluginName'] == 'tags')&&
		($CurrentHandler['handlerName'] == 'tag') &&
		isset($CurrentHandler['params']['tag'])) {
			$tag = $CurrentHandler['params']['tag'];
	} else {
		$tag = $_REQUEST['tag'];
	}

	$tag = str_replace(array('&', '<'), array('&amp;','&lt;'), $tag);

	// IF no tag is specified - show cloud
	if (!$tag) {
		plugin_tags_cloud();
		return;
	}


	LoadPluginLang('tags', 'main', '', '', ':');

	$SYSTEM_FLAGS['info']['title']['group']		= $lang['tags:header.tag.title'];
	$tpath = locatePluginTemplates(array('cloud', 'cloud.tag', 'pages', 'cloud.tag.entry'), 'tags', extra_get_param('tags', 'localsource'), extra_get_param('tags', 'skin')?extra_get_param('tags', 'skin'):'default');


	include_once root.'includes/news.php';
	// Search for tag in tags table
	if (!($rec = $mysql->record("select * from ".prefix."_tags where tag=".db_squote($tag)))) {
		// Unknown tag
		$entries = $lang['tags:nonews'];
	} else {
		$SYSTEM_FLAGS['info']['title']['secure_html']	= secure_html($tag);

		// Set page display limit
		$perPage = intval(pluginGetVariable('tags', 'tpage_limit'));
		if (($perPage < 1) || ($perPage > 1000))
			$perPage = 1000;

		// Manage pagination
		if (pluginGetVariable('tags', 'tpage_paginator')) {
			$tagCount = $mysql->result("select count(*) as cnt from ".prefix."_tags_index where tagID = ".db_squote($rec['id']));
			$pagesCount = ceil($tagCount / $perPage);
			$pageNo = intval($_REQUEST['page']);
			if ($pageNo < 1)
				$pageNo = 1;

			$limit = 'limit '.(intval($pageNo - 1)*$perPage).", ".$perPage;

			// If we have more than 1 page or current page != 1, we should generate paginator
			// Load navigation bar
			templateLoadVariables(true);
			$navigations = $TemplateCache['site']['#variables']['navigation'];

			$paginationParams = array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'tags', 'handler' => 'tag'), 'xparams' => array('tag' => $tag), 'paginator' => array('page', 1, false));
			//
			$tvars = array();
			$tvars['regx']["'\[prev-link\](.*?)\[/prev-link\]'si"] = ($pageNo > 1)?str_replace('%page%',"$1",str_replace('%link%',generatePageLink($paginationParams, $pageNo - 1), $navigations['prevlink'])):'';
			$tvars['regx']["'\[next-link\](.*?)\[/next-link\]'si"] = ($pageNo < $pagesCount)?str_replace('%page%',"$1",str_replace('%link%',generatePageLink($paginationParams, $pageNo + 1), $navigations['nextlink'])):'';
			$tvars['vars']['pages'] = generatePagination($pageNo, 1, $pagesCount, 10, $paginationParams, $navigations);

			$tpl -> template('pages', $tpath['pages']);
			$tpl -> vars('pages', $tvars);
			$pages = $tpl -> show('pages');

		} else {
			$limit = 'limit '.$perPage;
			$pagesCount = 1;
		}

		foreach ($mysql->select("select n.* from ".prefix."_tags_index i left join ".prefix."_news n on n.id = i.newsID where i.tagID =".db_squote($rec['id'])." order by n.postdate desc ".$limit) as $row) {
			$entries .= news_showone(0, '', array('overrideTemplateName' => 'cloud.tag.entry', 'overrideTemplatePath' => $tpath['cloud.tag.entry'], 'emulate' => $row, 'style' => 'export', 'plugin' => 'tags'));
		}
	}
	$tvars = array ( 'vars' => array ( 'entries' => $entries, 'tag' => $tag, 'pages' => $pages));
	$tvars['regx']['#\[paginator\](.*?)\[\/paginator\]#is'] = ($pagesCount > 1)?'$1':'';

	// Check if we have template `tag`
	$tplName = isset($tpath['cloud.tag'])?'cloud.tag':'cloud';
	$tpl -> template($tplName, $tpath[$tplName]);
	$tpl -> vars($tplName, $tvars);
	$template['vars']['mainblock'] = $tpl -> show($tplName);


}

function plugin_tags_generatecloud($ppage = 0){
	global $tpl, $template, $mysql, $lang, $config, $SYSTEM_FLAGS, $TemplateCache;

	LoadPluginLang('tags', 'main', '', '', ':');

	if ($ppage)
		$SYSTEM_FLAGS['info']['title']['group']		= $lang['tags:header.tags.title'];

	$masterTPL = $ppage?'cloud':'sidebar';

	// Generate cache file name [ we should take into account SWITCHER plugin ]
	$cacheFileName = md5('tags'.$config['home_url'].$config['theme'].$config['default_lang']).$masterTPL.$_REQUEST['page'].'.txt';

	if (extra_get_param('tags','cache')) {
		$cacheData = cacheRetrieveFile($cacheFileName, extra_get_param('tags','cacheExpire'), 'tags');
		if ($cacheData != false) {
			// We got data from cache. Return it and stop
			$template['vars'][$ppage?'mainblock':'plugin_tags'] = $cacheData;
			return;
		}
	}

	// Load params for display (if needed)
	$tpath = locatePluginTemplates(array(':params.ini', 'pages', $masterTPL), 'tags', extra_get_param('tags', 'localsource'), extra_get_param('tags', 'skin')?extra_get_param('tags', 'skin'):'default');
	$displayParams = parse_ini_file($tpath[':params.ini'].'params.ini');

	$tags = array();

	// Get tags list from SQL
	switch (extra_get_param('tags', ($ppage?'ppage_':'').'orderby')) {
		case 1: $orderby = 'tag'; break;
		case 2: $orderby = 'tag desc'; break;
		case 3: $orderby = 'posts'; break;
		case 4: $orderby = 'posts desc'; break;
		default: $orderby = 'rand()';
	}

	// Set page display limit
	$perPage = intval(pluginGetVariable('tags', ($ppage?'ppage_':'').'limit'));
	if (($perPage < 1) || ($perPage > 1000))
		$perPage = 1000;

	if ($ppage) {
		if (pluginGetVariable('tags', 'ppage_paginator')) {
			$tagCount = $mysql->result("select count(*) as cnt from ".prefix."_tags");
			$pagesCount = ceil($tagCount / $perPage);
			$pageNo = intval($_REQUEST['page']);
			if ($pageNo < 1)
				$pageNo = 1;

			$limit = 'limit '.(intval($pageNo - 1)*$perPage).", ".$perPage;

			if ($orderby == 'rand()')
				$orderby = 'tag';
		} else {
			$limit = 'limit '.$perPage;
		}
	} else {
		$limit = 'limit '.$perPage;
	}

	$rows = $mysql->select("select * from ".prefix."_tags order by ".$orderby.' '.$limit);

	// Prepare style definition
	$wlist = array();
	if ($manualstyle = intval(extra_get_param('tags', 'manualstyle'))) {
	        foreach (explode("\n",extra_get_param('tags', 'styles_weight')) as $wrow) {
	         if (preg_match('#^ *(\d+) *\| *(\d+) *\|(.+?) *$#', trim($wrow), $m))
	         	array_push($wlist, array($m[1], $m[2], $m[3]));
	        }

		$stylelist = preg_split("/\, */", trim(extra_get_param('tags', 'styles')));

		if ((($styleListCount = count($stylelist)) < 2)&&(($styleWeightListCount = count($wlist)) < 1))
			$manualstyle = 0;
	}
	// Calculate min/max if we have any rows
	$min = -1; $max = 0;
	foreach ($rows as $row) { 
		if ($row['posts'] > $max) $max = $row['posts'];
		if (($min == -1)||($row['posts'] < $min)) $min = $row['posts'];
	}
	
	// Init variables for 3D cloud
	$cloud3d = array();
	$cloudMin = (intval($displayParams['size3d.min'])>0)?intval($displayParams['size3d.min']):10;
	$cloudMax = (intval($displayParams['size3d.max'])>0)?intval($displayParams['size3d.max']):18;
	if ($cloudMax == $cloudMin) { $cloudMin = 10; $cloudMax = 18; }

	$cloudStep = abs(round(($max - $min)/($cloudMax-$cloudMin), 2));
	if ($cloudStep < 0.01) $cloudStep = 1;

	// Prepare output rows
	foreach ($rows as $row) {
	    $link = checkLinkAvailable('tags', 'tag')?
					generateLink('tags', 'tag', array('tag' => $row['tag'])):
					generateLink('core', 'plugin', array('plugin' => 'tags', 'handler' => 'tag'), array('tag' => $row['tag']));

		$cloud3d[] = '<a href="'.$link.'" style="font-size: '.(round(($row['posts']-$min)/$cloudStep)+$cloudMin).'pt">'.iconv('Windows-1251', 'UTF-8', $row['tag']).'</a>';
		if ($manualstyle) {
			$mmatch = 0;
			foreach ($wlist as $wrow) {
				if (($row['posts'] >= $wrow[0]) && ($row['posts'] <= $wrow[1])) {
					$params = 'class ="'.$wrow[2].'"';
					$mmatch = 1;
					break;
				}
			}
			if (!$mmatch)
				$params = 'class ="'.($stylelist[$styleListCount - round($row['posts']/$max * $styleListCount)]).'"';
		} else {
			$params = 'style ="font-size: '.(round(($row['posts']/$max)*100+100)).'%;"';
		}

		$tags[] = str_replace(array('{url}', '{tag}', '{posts}', '{params}'), array($link, $row['tag'], $row['posts'], $params), $displayParams[($ppage?'cloud':'sidebar').'.tag']);
	}

	$tagList = join($displayParams[($ppage?'cloud':'sidebar').'.tag.delimiter']."\n", $tags);

	// If we have more than 1 page or current page != 1, we should generate paginator
	if ( $ppage && (($pagesCount > 1) || ($pageNo != 1))) {
		// Load navigation bar
		templateLoadVariables(true);
		$navigations = $TemplateCache['site']['#variables']['navigation'];

		$paginationParams = array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'tags'), 'xparams' => array(), 'paginator' => array('page', 1, false));

		//
		$tvars = array();
		$tvars['regx']["'\[prev-link\](.*?)\[/prev-link\]'si"] = ($pageNo > 1)?str_replace('%page%',"$1",str_replace('%link%',generatePageLink($paginationParams, $pageNo - 1), $navigations['prevlink'])):'';
		$tvars['regx']["'\[next-link\](.*?)\[/next-link\]'si"] = ($pageNo < $pagesCount)?str_replace('%page%',"$1",str_replace('%link%',generatePageLink($paginationParams, $pageNo + 1), $navigations['nextlink'])):'';
		$tvars['vars']['pages'] = generatePagination($pageNo, 1, $pagesCount, 10, $paginationParams, $navigations);

		$tpl -> template('pages', $tpath['pages']);
		$tpl -> vars('pages', $tvars);
		$pages = $tpl -> show('pages');
	} else {
		$pages = '';
	}


	$tvars = array ( 'vars' => array ( 'entries' => $tagList, 'tag' => $lang['tags:taglist'], 'pages' => $pages));
	if (pluginGetVariable('tags', 'cloud3d'))
		$tvars['vars']['cloud3d'] = urlencode('<tags>'.join(' ', $cloud3d).'</tags>');
	$tvars['regx']['#\[paginator\](.*?)\[\/paginator\]#is'] = ($pages != '')?'$1':'';

	$tpl -> template($masterTPL, $tpath[$masterTPL]);
	$tpl -> vars($masterTPL, $tvars);
	$output = $tpl -> show($masterTPL);
	$template['vars'][$ppage?'mainblock':'plugin_tags'] = $output;

	if (extra_get_param('tags','cache'))
		cacheStoreFile($cacheFileName, $output, 'tags');
}