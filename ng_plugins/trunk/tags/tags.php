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
	function showNews($newsID, $SQLnews, &$tvars, $mode) {
		global $mysql, $tpl;

		// Check if we have tags in news
		if (!$SQLnews['tags']) {
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
			$tags[] = str_replace(array('{url}', '{tag}'), array($link, $tag), $this->displayParams['tag_news']);
		}

		$tvars['vars']['tags'] = join($this->displayParams['tag_news_delimiter'], $tags);
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

	LoadPluginLang('tags', 'main');
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
	global $tpl, $template, $mysql, $lang, $SYSTEM_FLAGS, $CurrentHandler;

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


	LoadPluginLang('tags', 'main');

	$SYSTEM_FLAGS['info']['title']['group']		= 'Облако тегов';
	$tpath = locatePluginTemplates(array('plugin', 'entry'), 'tags', extra_get_param('tags', 'localsource'), extra_get_param('tags', 'skin')?extra_get_param('tags', 'skin'):'default');


	include_once root.'includes/news.php';
	// Search for tag in tags table
	if (!($rec = $mysql->record("select * from ".prefix."_tags where tag=".db_squote($tag)))) {
		// Unknown tag
		$entries = $lang['tags_nonews'];
	} else {
		$SYSTEM_FLAGS['info']['title']['secure_html']	= secure_html($tag);
		foreach ($mysql->select("select n.* from ".prefix."_tags_index i left join ".prefix."_news n on n.id = i.newsID where i.tagID =".db_squote($rec['id'])." order by n.postdate desc") as $row) {
			$entries .= news_showone(0, '', array('overrideTemplateName' => 'entry', 'overrideTemplatePath' => $tpath['entry'], 'emulate' => $row, 'style' => 'export', 'plugin' => 'tags'));
		}

	}

	$tpl -> template('plugin', $tpath['plugin']);
	$tpl -> vars('plugin', array ( 'vars' => array ( 'entries' => $entries, 'tag' => $tag)));
	$template['vars']['mainblock'] = $tpl -> show('plugin');


}

function plugin_tags_generatecloud($ppage = 0){
	global $tpl, $template, $mysql, $lang, $config;

	LoadPluginLang('tags', 'main');

	$masterTPL = $ppage?'plugin':'cloud';

	// Generate cache file name [ we should take into account SWITCHER plugin ]
	$cacheFileName = md5('tags'.$config['home_url'].$config['theme'].$config['default_lang']).$masterTPL.'.txt';

	if (extra_get_param('tags','cache')) {
		$cacheData = cacheRetrieveFile($cacheFileName, extra_get_param('tags','cacheExpire'), 'tags');
		if ($cacheData != false) {
			// We got data from cache. Return it and stop
			$template['vars'][$ppage?'mainblock':'plugin_tags'] = $cacheData;
			return;
		}
	}

	// Load params for display (if needed)
	$tpath = locatePluginTemplates(array(':params.ini', $masterTPL), 'tags', extra_get_param('tags', 'localsource'), extra_get_param('tags', 'skin')?extra_get_param('tags', 'skin'):'default');
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
	$limit = intval(extra_get_param('tags', ($ppage?'ppage_':'').'limit'));
	if (($limit < 1)||($limit > 1000)) $limit = 1000;

	$rows = $mysql->select("select * from ".prefix."_tags order by ".$orderby." limit ".$limit);

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
	$min = 0; $max = 0;
	foreach ($rows as $row) { if ($row['posts'] > $max) $max = $row['posts']; }

	// Prepare output rows
	foreach ($rows as $row) {
	    $link = checkLinkAvailable('tags', 'tag')?
					generateLink('tags', 'tag', array('tag' => $row['tag'])):
					generateLink('core', 'plugin', array('plugin' => 'tags', 'handler' => 'tag'), array('tag' => $row['tag']));

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
			$params = 'style ="font-size: '.(($row['posts']/$max)*100+100).'%;"';
		}

		$tags[] = str_replace(array('{url}', '{tag}', '{posts}', '{params}'), array($link, $row['tag'], $row['posts'], $params), $displayParams['tag_cloud']);
	}

	$tagList = join($displayParams['tag_cloud_delimiter']."\n", $tags);

	$tpl -> template($masterTPL, $tpath[$masterTPL]);
	$tpl -> vars($masterTPL, array ( 'vars' => array ( 'entries' => $tagList, 'tag' => $lang['tags_taglist'])));
	$output = $tpl -> show($masterTPL);
	$template['vars'][$ppage?'mainblock':'plugin_tags'] = $output;

	if (extra_get_param('tags','cache'))
		cacheStoreFile($cacheFileName, $output, 'tags');
}
