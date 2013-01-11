<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

// Preload required libraries
loadPluginLibrary('uprofile', 'lib');
loadPluginLang('ublog', 'main', '', '', ':');

include_once root.'includes/news.php';

class UblogFilter extends p_uprofileFilter {
	function showProfile($userID, $SQLrow, &$tvars) {
		$link = generatePluginLink('ublog', null, array('uid' => $userID, 'uname' => $SQLrow['name']));
		if (pluginGetVariable('ublog','replaceCount') && ($SQLrow['news'] > 0)) {
			$tvars['vars']['news'] = '<a href="'.$link.'">'.$SQLrow['news'].'</a>';
			$tvars['vars']['p']['ublog']['flags']['haveBlog'] = true;
			$tvars['vars']['p']['ublog']['blogLink'] = $link;
		} else {
			$tvars['vars']['p']['ublog']['flags']['haveBlog'] = false;
		}
		return 1;
	}

}

// Register plugin handler
register_filter('plugin.uprofile','ublog', new UblogFilter);
register_plugin_page('ublog','','plugin_ublog');


function plugin_ublog() {
	global $catz, $catmap, $mysql, $config, $userROW, $tpl, $parse, $template, $lang, $PFILTERS, $SYSTEM_FLAGS, $CurrentHandler;


	// PREPARE FILTER RULES FOR NEWS SHOWER
	$filter = array();
	$pparams = array();

	// preload plugins
	load_extras('news');
	load_extras('news:show');
	load_extras('news:show:list');

	if (isset($CurrentHandler['params']['uid'])) {
		$filter = array('DATA', 'author_id', '=', intval($CurrentHandler['params']['uid']));
		$pparams['uid'] = intval($CurrentHandler['params']['uid']);
	} else if (isset($CurrentHandler['params']['uname'])) {
		$pparams['uname'] = $CurrentHandler['params']['uname'];
		$filter = array('DATA', 'author', '=', $CurrentHandler['params']['uname']);
	} else if (isset($_GET['uid'])) {
		$filter = array('DATA', 'author_id', '=', intval($_GET['uid']));
		$pparams['uid'] = intval($_GET['uid']);
	} else if (isset($_GET['uname'])) {
		$pparams['uname'] = $_GET['uname'];
		$filter = array('DATA', 'author', '=', $_GET['uname']);
	} else {
		$template['vars']['mainblock'] = 'No user specified';
		return;
	}

	// Check if user exists
	if (isset($pparams['uid']) && ($urow = $mysql->record("select * from ".uprefix."_users where id = ".intval($pparams['uid'])))) {
		// found :)
	} else if (isset($pparams['uname']) && ($urow = $mysql->record("select * from ".uprefix."_users where name = ".db_squote($pparams['uname'])))) {
		// found :)
	} else {
		$template['vars']['mainblock'] = 'User not found';
		return;
	}

	$SYSTEM_FLAGS['info']['title']['group'] = str_replace('{uname}', $urow['name'], $lang['ublog:header']);

	$showNumber = intval(pluginGetVariable('ublog','personalCount'));
	if (($showNumber < 2)||($showNumber > 100))
		$showNumber = 10;


	$callingParams = array(
		'style' => 'short',
		'searchFlag' => true,
		'extendedReturn' => false,
		'customCategoryTemplate' => false,
		'showNumber'	=> $showNumber,
		'page'	=> ((isset($_GET['page']) && (intval($_GET['page'])>0))?intval($_GET['page']):0),
	);

	$paginationParams['params'] = $pparams;
	$paginationParams['pluginName'] = 'ublog';
	$paginationParams['handlerName'] = '';
	$paginationParams['xparams'] = array();
	$paginationParams['paginator'] = array('page', 1, 1);

	$template['vars']['mainblock'] .= news_showlist($filter, $paginationParams, $callingParams);
}
