<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

add_act('index', 'bookmarks_view', 3);
register_plugin_page('bookmarks','','bookmarks_t', 0);

// Declare variables to be global
global $bookmarksLoaded, $bookmarksList;
$bookmarksLoaded = 0;
$bookmarksList = array();

class BookmarksNewsFilter extends NewsFilter {

	// Add {plugin_bookmarks_news} variable into news for "Add/Remove" link
	function showNews($newsID, $SQLnews, &$tvars) {
		global $lang, $bookmarksLoaded, $bookmarksList, $userROW;

		// Exit if user is not logged in
		if (!is_array($userROW)) {
			$tvars['vars']['bookmarks_news'] = '';
			return;
		}	

		// Preload user's bookmarks
		if (!$bookmarksLoaded)
			bookmarks_sql();
		// Check if this news is already in bookmark
		$found = 0;
		foreach ($bookmarksList as $brow) {
			if ($brow['id'] == $newsID) {
				$found = 1;
				break;
			}
		}
		// Generate link, take into account a little bug in engine's versions up to 0.9.7 FixPack #03 (included)
		$link  = str_replace('{plugin_name}', 'bookmarks', getLink('plugins', array('plugin_name' => 'bookmarks')));
		$link .= ((strpos($link, '?') === false)?'?':'&') . 'act='.($found?'delete':'add').'&news='.$newsID;

		LoadPluginLang('bookmarks', 'main');
		$tvars['vars']['bookmarks_news'] = str_replace('{link}', $link, $lang['bookmarks_'.($found?'del':'add').'link']);
	}
}

register_filter('news','bookmarks', new BookmarksNewsFilter);


// Function for fetching SQL bookmarks data
function bookmarks_sql(){
	global $mysql, $config, $userROW, $bookmarksLoaded, $bookmarksList;

	$bookmarksLoaded = 1;
	if ($userROW['id']) {
		$bookmarksList = $mysql->select("select n.id, n.title, n.alt_name, n.catid from ".prefix."_bookmarks as b left join ".prefix."_news n on n.id = b.news_id where b.user_id = ".db_squote($userROW['id']));
	}
}

function bookmarks_view(){
	global $template, $tpl, $lang, $mysql, $config, $parse, $userROW, $bookmarksLoaded, $bookmarksList;

	// Generate cache file name [ we should take into account SWITCHER plugin ]
	$cacheFileName = md5('bookmarks'.$config['theme'].$config['default_lang']).$userROW['id'].'.txt';

	if (extra_get_param('bookmarks','cache')) {
		$cacheData = cacheRetrieveFile($cacheFileName, extra_get_param('bookmarks','cacheExpire'), 'bookmarks');
		if ($cacheData != false) {
			// We got data from cache. Return it and stop
			$template['vars']['plugin_bookmarks'] = $cacheData;
			return;
		}
	}

	// Determine paths for all template files
	$tpath = locatePluginTemplates(array('entries', 'bookmarks'), 'bookmarks', extra_get_param('bookmarks', 'localsource'));

	$maxlength = intval(extra_get_param('bookmarks','maxlength'));
	if (!$maxlength)	{ $maxlength = 100; }

	// Preload user's bookmarks
	if (!$bookmarksLoaded)
		bookmarks_sql();

	$output = '';
	$count = 0;
	foreach ($bookmarksList as $row)
	{
		$count++;
		$tvars = array('vars' =>  array( 'link'		=>	GetLink('full', $row)	));

		if (strlen($row['title']) > $maxlength) {
			$tvars['vars']['title'] = substr(secure_html($row['title']), 0, $maxlength)."...";
		} else {
			$tvars['vars']['title'] = secure_html($row['title']);
		}

		$tpl -> template('entries', $tpath['entries']);
		$tpl -> vars('entries', $tvars);
		$result .= $tpl -> show('entries');
	}

	// Action on "hide empty"
	if ((!$count) && extra_get_param('bookmarks','hideempty')) {
		if (extra_get_param('bookmarks','cache')) {
			cacheStoreFile($cacheFileName, ' ', 'bookmarks');
		}
		$template['vars']['plugin_bookmarks'] = '';
		return;
	}

	if (!$count) {
		LoadPluginLang('bookmarks', 'main');
		$result = $lang['bookmarks_noentries'];
	}

	unset($tvars);
	$tvars['vars'] = array ( 'tpl_url' => tpl_url, 'entries' => $result);

	$tpl -> template('bookmarks', $tpath['bookmarks']);
	$tpl -> vars('bookmarks', $tvars);

	$output = $tpl -> show('bookmarks');
	$template['vars']['plugin_bookmarks'] = $output;

	if (extra_get_param('bookmarks','cache')) {
		cacheStoreFile($cacheFileName, $output, 'bookmarks');
	}
}

function bookmarks_t(){
	global $mysql, $config, $userROW, $HTTP_REFERER;

	// Process bookmarks only for logged in users
	if (!is_array($userROW)) {
		// Redirect UNREG users far away :)
		header('Location: '.$config['home_url'].'');
		return;
	}

	// News ID
	$newsID = intval($_REQUEST['news']);

	// Add action
	if ($_REQUEST['act'] == 'add') {
		// Check that this news exists & we didn't bookmarked this news earlier
		if (count($mysql->select("select id from ".prefix."_news where id = ".$newsID)) &&
		    (!count($mysql->select("select * from ".prefix.'_bookmarks where user_id = '.db_squote($userROW['id'])." and news_id=".$newsID))))
				// Ok, bookmark it
				$mysql->query("insert into `".prefix."_bookmarks` (`user_id`,`news_id`) values (".db_squote($userROW['id']).",".db_squote($newsID).")");
	} else
	// Del action
	if ($_REQUEST['act'] == 'delete') {
		$mysql->query("delete from `".prefix."_bookmarks` where `user_id`=".db_squote($userROW['id'])." and `news_id`=".db_squote($newsID));
	}

	// If cache is activated - truncate cache file [ to clear cache ]
	if (extra_get_param('bookmarks','cache')) {
		$cacheFileName = md5('bookmarks'.$config['theme'].$config['default_lang']).$userROW['id'].'.txt';
		cacheStoreFile($cacheFileName, '', 'bookmarks');
	}

	// Make redirection back
	@header("Location: ".$HTTP_REFERER);
}

