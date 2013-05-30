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
			$tvars['user']['news'] = '<a href="'.$link.'">'.$SQLrow['news'].'</a>';
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
	global $catz, $catmap, $mysql, $config, $userROW, $tpl, $twig, $twigLoader, $parse, $template, $lang, $PFILTERS, $SYSTEM_FLAGS, $CurrentHandler;


	// PREPARE FILTER RULES FOR NEWS SHOWER
	$filter = array();
	$pparams = array();

	// preload plugins
	loadActionHandlers('news');
	loadActionHandlers('news:show');
	loadActionHandlers('news:show:list');

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
		// No user is specified - return an error
		error404();
		return;
	}

	// Check if user exists
	if (isset($pparams['uid']) && ($urow = $mysql->record("select * from ".uprefix."_users where id = ".intval($pparams['uid'])))) {
		// found :)
	} else if (isset($pparams['uname']) && ($urow = $mysql->record("select * from ".uprefix."_users where name = ".db_squote($pparams['uname'])))) {
		// found :)
	} else {
		// User not found - return an error
		error404();
		return;
	}

	// Get user's photo and avatar
	$userPhoto	= userGetPhoto($urow);
	$userAvatar	= userGetAvatar($urow);

	// Prepare variables
	$tVars = array(
		'userRec'		=> $urow,
		'user'			=> array(
			'id'			=>	$urow['id'],
			'name'			=>	$urow['name'],
			'news'			=>	$urow['news'],
			'com'			=>	$urow['com'],
			'status'		=>	$status,
			'last'			=>	($urow['last'] > 0) ? LangDate("l, j Q Y - H:i", $urow['last']) : $lang['no_last'],
			'reg'			=>	langdate("j Q Y", $urow['reg']),
			'site'			=>	secure_html($urow['site']),
			'icq'			=>	secure_html($urow['icq']),
			'from'			=>	secure_html($urow['where_from']),
			'info'			=>	secure_html($urow['info']),
			'photo_thumb'	=>	$userPhoto[1],
			'photo'			=>	$userPhoto[2],
			'avatar'		=>	$userAvatar[1],
			'flags'			=> array(
				'hasPhoto'		=> $config['use_photos'] && $userPhoto[0],
				'hasAvatar'		=> $config['use_avatars'] && $userAvatar[0],
				'hasIcq'		=> is_numeric($urow['icq'])?1:0,
			),
		),

	);

	// [MANUALLY] call xfields plugin for generating user's profile
	if (is_array($PFILTERS['plugin.uprofile']) && isset($PFILTERS['plugin.uprofile']['xfields']))
		 { $PFILTERS['plugin.uprofile']['xfields']->showProfile($urow['id'], $urow, $tVars); }



	$SYSTEM_FLAGS['info']['title']['group'] = str_replace('{uname}', $urow['name'], $lang['ublog:header']);

	$showNumber = intval(pluginGetVariable('ublog','personalCount'));
	if (($showNumber < 2)||($showNumber > 100))
		$showNumber = 10;


	$callingParams = array(
		'style' => 'short',
		'searchFlag' => true,
		'extendedReturn' => true,
		'extendedReturnData' => true,
		'extendedReturnPagination' => true,
		'customCategoryTemplate' => false,
		'showNumber'	=> $showNumber,
		'page'	=> ((isset($_GET['page']) && (intval($_GET['page'])>0))?intval($_GET['page']):0),
	);

	$paginationParams['params'] = $pparams;
	$paginationParams['pluginName'] = 'ublog';
	$paginationParams['handlerName'] = '';
	$paginationParams['xparams'] = array();
	$paginationParams['paginator'] = array('page', 1, 1);

	$newsResults = news_showlist($filter, $paginationParams, $callingParams);

	$tVars['news'] = array(
		'count'		=> $newsResults['count'],
		'entries'	=> $newsResults['data'],
	);
	$tVars['pages']	= $newsResults['pages'];

	$tpath = locatePluginTemplates(array('ublog'), 'ublog', pluginGetVariable('calendar', 'localsource'));
	$xt = $twig->loadTemplate($tpath['ublog'].'ublog.tpl');
	$output = $xt->render($tVars);

	$template['vars']['mainblock'] .= $output;
}
