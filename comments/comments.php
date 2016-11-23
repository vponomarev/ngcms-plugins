<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

$lang = LoadLang("comments", "site");

class CommentsNewsFilter extends NewsFilter {
	function addNewsForm(&$tvars){
		global $lang;
		loadPluginLang('comments', 'config', '', '', ':');

		for ($ix = 0; $ix <= 2; $ix++) {
			$tvars['plugin']['comments']['acom:'.$ix] = (pluginGetVariable('comments', 'default_news') == $ix)?'selected="selected"':'';
		}
	}

	function addNews(&$tvars, &$SQL) {
		$SQL['allow_com'] = intval($_REQUEST['allow_com']);
		return 1;
	}

	function editNewsForm($newsID, $SQLnews, &$tvars) {
		global $lang, $mysql, $config, $parse, $tpl, $PHP_SELF;

		loadPluginLang('comments', 'config', '', '', ':');

		// List comments
		$comments = '';
		$tpl -> template('comments', tpl_actions.'news');

		foreach ($mysql->select("select * from ".prefix."_comments where post='".$newsID."' order by id") as $crow) {
			$text	= $crow['text'];

			if ($config['blocks_for_reg'])		{ $text = $parse -> userblocks($text); }
			if ($config['use_bbcodes'])			{ $text = $parse -> bbcodes($text); }
			if ($config['use_htmlformatter'])	{ $text = $parse -> htmlformatter($text); }
			if ($config['use_smilies'])			{ $text = $parse -> smilies($text); }

			$txvars['vars'] = array(
				'php_self'		=>	$PHP_SELF,
				'com_author'	=>	$crow['author'],
				'com_post'		=>	$crow['post'],
				'com_url'		=>	($crow['url']) ? $crow['url'] : $PHP_SELF.'?mod=users&action=edituser&id='.$crow['author_id'],
				'com_id'		=>	$crow['id'],
				'com_ip'		=>	$crow['ip'],
				'com_time'		=>	LangDate(pluginGetVariable('comments','timestamp'), $crow['postdate']),
				'com_part'		=>	$text
			);

			if ($crow['reg']) {
				$txvars['vars']['[userlink]'] = '';
				$txvars['vars']['[/userlink]'] = '';
			} else {
				$txvars['regx']["'\\[userlink\\].*?\\[/userlink\\]'si"] = $crow['author'];
			}

			$tpl -> vars('comments', $txvars);
			$comments .= $tpl -> show('comments');
		}
		$tvars['plugin']['comments']['list'] = $comments;
		$tvars['plugin']['comments']['count'] = $SQLnews['com']?$SQLnews['com']:$lang['noa'];

		for ($ix = 0; $ix <= 2; $ix++) {
			$tvars['plugin']['comments']['acom:'.$ix] = ($SQLnews['allow_com'] == $ix)?'selected="selected"':'';
		}
	}

	function editNews($newsID, $SQLold, &$SQLnew, &$tvars) {
		$SQLnew['allow_com'] = intval($_REQUEST['allow_com']);
		return 1;
	}

	function showNews($newsID, $SQLnews, &$tvars, $callingParams = array()) {
		global $catmap, $catz, $config, $userROW, $template, $lang, $tpl;

		// Determine if comments are allowed in  this specific news
		$allowCom = $SQLnews['allow_com'];
		if ($allowCom == 2) {
			// `Use default` - check master category
			$masterCat = intval(array_shift(explode(',', $SQLnews['catid'])));
			if ($masterCat && isset($catmap[$masterCat])) {
				$allowCom = intval($catz[$catmap[$masterCat]]['allow_com']);
			}

			// If we still have 2 (no master category or master category also have 'default' - fetch plugin's config
			if ($allowCom == 2) {
				$allowCom = pluginGetVariable('comments', 'global_default');
			}
		}

		// Fill variables within news template
		$tvars['vars']['comments-num']	=	$SQLnews['com'];
		$tvars['vars']['comnum']	=	$SQLnews['com'];
		$tvars['regx']['[\[comheader\](.*)\[/comheader\]]'] = ($SQLnews['com'])?'$1':'';

		// Blocks [comments] .. [/comments] and [nocomments] .. [/nocomments]
		$tvars['regx']['[\[comments\](.*)\[/comments\]]']     = ($SQLnews['com'])?'$1':'';
		$tvars['regx']['[\[nocomments\](.*)\[/nocomments\]]'] = ($SQLnews['com'])?'':'$1';

		// Check if we need to add comments block:
		//	* style == full
		//  * emulate == false
		//  * plugin == not set
		if (!(($callingParams['style'] == 'full')&&(!$callingParams['emulate'])&&(!isset($callingParams['plugin'])))) {
			// No, we don't need to show comments
			$tvars['vars']['plugin_comments'] = '';
			return 1;
		}

		// ******************************************** //
		// Yeah, let's show comments here
		// ******************************************** //

		// Prepare params for call
		$callingCommentsParams = array('outprint' => true, 'total' => $SQLnews['com']);

		// Set default template path
		$templatePath = tpl_site.'plugins/comments';

		$fcat = array_shift(explode(",", $SQLnews['catid']));

		// Check if there is a custom mapping
		if ($fcat && $catmap[$fcat] && ($ctname = $catz[$catmap[$fcat]]['tpl'])) {
			// Check if directory exists
			if (is_dir(tpl_site.'ncustom/'.$ctname)) {
				$callingCommentsParams['overrideTemplatePath'] = tpl_site.'ncustom/'.$ctname;
				$templatePath = tpl_site.'ncustom/'.$ctname;
			}
		}

		include_once(root."/plugins/comments/inc/comments.show.php");

		// Check if we need pagination
		$flagMoreComments	= false;
		$skipCommShow		= false;

		if (pluginGetVariable('comments', 'multipage')) {
			$multi_mcount = intval(pluginGetVariable('comments', 'multi_mcount'));
			// If we have comments more than for one page - activate pagination
			if (($multi_mcount >= 0) && ($SQLnews['com'] > $multi_mcount)) {
				$callingCommentsParams['limitCount'] = $multi_mcount;
				$flagMoreComments = true;
				if (!$multi_mcount)
					$skipCommShow = true;
			}

		}

		$tcvars = array();
		// Show comments [ if not skipped ]
		$tcvars['vars']['entries'] = $skipCommShow?'':comments_show($newsID, 0, 0, $callingCommentsParams);

		// If multipage is used and we have more comments - show
		if ($flagMoreComments) {
			$link = checkLinkAvailable('comments', 'show')?
						generateLink('comments', 'show', array('news_id' => $newsID)):
						generateLink('core', 'plugin', array('plugin' => 'comments', 'handler' => 'show'), array('news_id' => $newsID));

			$tcvars['vars']['more_comments'] = str_replace(array('{link}', '{count}'), array($link, $SQLnews['com']), $lang['comments:link.more']);
			$tcvars['regx']['#\[more_comments\](.*?)\[\/more_comments\]#is'] = '$1';
		} else {
			$tcvars['vars']['more_comments'] = '';
			$tcvars['regx']['#\[more_comments\](.*?)\[\/more_comments\]#is'] = '';
		}

		// Show form for adding comments
		if ($allowCom && (!pluginGetVariable('comments', 'regonly') || is_array($userROW))) {
			$tcvars['vars']['form'] = comments_showform($newsID, $callingCommentsParams);
			$tcvars['regx']['#\[regonly\](.*?)\[\/regonly\]#is'] = '';
			$tcvars['regx']['#\[commforbidden\](.*?)\[\/commforbidden\]#is'] = '';
		} else {
			$tcvars['vars']['form'] = '';
			$tcvars['regx']['#\[regonly\](.*?)\[\/regonly\]#is'] = $allowCom?'$1':'';
			$tcvars['regx']['#\[commforbidden\](.*?)\[\/commforbidden\]#is'] = $allowCom?'':'$1';
		}
		$tcvars['regx']['#\[comheader\](.*)\[/comheader\]#is'] = ($SQLnews['com'])?'$1':'';

		$tpl->template('comments.internal', $templatePath);
		$tpl->vars('comments.internal', $tcvars);
		$tvars['vars']['plugin_comments'] = $tpl->show('comments.internal');
	}
}


class CommentsFilterAdminCategories extends FilterAdminCategories{
	function addCategory(&$tvars, &$SQL) {
		$SQL['allow_com'] = intval($_REQUEST['allow_com']);
		return 1;
	}

	function addCategoryForm(&$tvars) {
		global $lang;
		loadPluginLang('comments', 'config', '', '', ':');

		$allowCom = pluginGetVariable('comments', 'default_categories');

		$ms = '<select name="allow_com">';
		$cv = array( '0' => 'запретить', '1' => 'разрешить', '2' => 'по умолчанию');
		for ($i = 0; $i < 3; $i++) {
			$ms .= '<option value="'.$i.'"'.(($allowCom == $i)?' selected="selected"':'').'>'.$cv[$i].'</option>';
		}

		$tvars['extend'] .= '<tr><td width="70%" class="contentEntry1">'.$lang['comments:categories.comments'].'<br/><small>'.$lang['comments:categories.comments#desc'].'</small></td><td width="30%" class="contentEntry2">'.$ms.'</td></tr>';
		return 1;
	}


	function editCategoryForm($categoryID, $SQL, &$tvars) {
		global $lang;
		loadPluginLang('comments', 'config', '', '', ':');

		if (!isset($SQL['allow_com'])) {
			$SQL['allow_com'] = pluginGetVariable('comments', 'default_categories');
		}

		$ms = '<select name="allow_com">';
		$cv = array( '0' => 'запретить', '1' => 'разрешить', '2' => 'по умолчанию');
		for ($i = 0; $i < 3; $i++) {
			$ms .= '<option value="'.$i.'"'.(($SQL['allow_com'] == $i)?' selected="selected"':'').'>'.$cv[$i].'</option>';
		}

		$tvars['extend'] .= '<tr><td width="70%" class="contentEntry1">'.$lang['comments:categories.comments'].'<br/><small>'.$lang['comments:categories.comments#desc'].'</small></td><td width="30%" class="contentEntry2">'.$ms.'</td></tr>';
		return 1;
	}

	function editCategory($categoryID, $SQL, &$SQLnew, &$tvars) {
		$SQLnew['allow_com'] = intval($_REQUEST['allow_com']);
		return 1;
	}



}



function plugin_comments_add() {
	global $config, $catz, $catmap, $tpl, $template, $lang, $SUPRESS_TEMPLATE_SHOW;

	$SUPRESS_TEMPLATE_SHOW = 1;

	// Connect library
	include_once(root."/plugins/comments/inc/comments.show.php");
	include_once(root."/plugins/comments/inc/comments.add.php");

	// Call comments_add() to ADD COMMENT
	if (is_array($addResult = comments_add())) {

		// Ok.
		// Check if AJAX mode is turned OFF
		if (!$_REQUEST['ajax']) {
			// We should JUMP to this new comment

			// Make FULL news link
			$nlink = newsGenerateLink($addResult[0]);

			// Make redirect to full news
			@header("Location: ".$nlink);
			return 1;
		}

		// AJAX MODE.
		// Let's print (ONLY) new comment
		$SQLnews	= $addResult[0];
		$commentId	= $addResult[1];

		// Check if we need to override news template
		$callingCommentsParams = array('outprint' => true);

		// Set default template path
		$templatePath = tpl_dir.$config['theme'];

		// Find first category
		$fcat = array_shift(explode(",", $SQLnews['catid']));
		// Check if there is a custom mapping
		if ($fcat && $catmap[$fcat] && ($ctname = $catz[$catmap[$fcat]]['tpl'])) {
			// Check if directory exists
			if (is_dir($templatePath.'/ncustom/'.$ctname))
				$callingCommentsParams['overrideTemplatePath'] = $templatePath.'/ncustom/'.$ctname;
		}
		$output = array(
			'status'	=> 1,
			'rev'		=> intval(pluginGetVariable('comments', 'backorder')),
			'data'		=> iconv('Windows-1251', 'UTF-8', comments_show($SQLnews['id'], $commentId, $SQLnews['com']+1, $callingCommentsParams))
		);

		print json_encode($output);
		$template['vars']['mainblock'] = '';

		return 1;
	} else {
		// Some errors.
		if ($_REQUEST['ajax']) {
			// AJAX MODE
			// Set default template path [from site template / comments plugin subdirectory]
			$templatePath = tpl_site.'plugins/comments';

			$tpl -> template('comments.error', $templatePath);
			$tpl -> vars('comments.error', array( 'vars' => array('content' => $template['vars']['mainblock'])));

			$output = array(
				'status' => 0,
				'data' => iconv('Windows-1251', 'UTF-8', $tpl -> show('comments.error'))
			);
			print json_encode($output);
			$template['vars']['mainblock'] = '';

		} else {
			// NON-AJAX MODE
			$tavars = array( 'vars' => array(
				'title'		=> $lang['comments:err.redir.title'],
				'message'	=> $template['vars']['mainblock'],
				'link'		=> secure_html(($_REQUEST['referer'])?$_REQUEST['referer']:'/'),
				'linktext'	=> $lang['comments:err.redir.url'],
			));
			$tpl -> template('redirect', tpl_site);
			$tpl -> vars('redirect', $tavars);
			$template['vars']['mainblock'] = $tpl -> show('redirect');
		}
	}
}

// Show dedicated page for comments
function plugin_comments_show(){
	global $config, $catz, $mysql, $catmap, $tpl, $template, $lang, $SUPRESS_TEMPLATE_SHOW, $userROW, $TemplateCache, $SYSTEM_FLAGS;

	// Load lang file, that is required for [hide]..[/hide] block
	$lang = LoadLang('news', 'site');


	$SYSTEM_FLAGS['info']['title']['group']		= $lang['comments:header.title'];

	include_once(root."/plugins/comments/inc/comments.show.php");

	// Try to fetch news
	$newsID = intval($_REQUEST['news_id']);

	if (!$newsID || !is_array($newsRow = $mysql->record("select * from ".prefix."_news where id = ".$newsID))) {
		error404();
		return;
	}
	$SYSTEM_FLAGS['info']['title']['item']		= $newsRow['title'];

	// Prepare params for call
	// AJAX is turned off by default
	$callingCommentsParams = array( 'noajax' => 1, 'outprint' => true);

	// Set default template path [from site template / comments plugin subdirectory]
	$templatePath = tpl_site.'plugins/comments';

	$fcat = array_shift(explode(",", $newsRow['catid']));

	// Check if there is a custom mapping
	if ($fcat && $catmap[$fcat] && ($ctname = $catz[$catmap[$fcat]]['tpl'])) {
		// Check if directory exists
		if (is_dir(tpl_site.'ncustom/'.$ctname))
			$callingCommentsParams['overrideTemplatePath'] = tpl_site.'ncustom/'.$ctname;
			$templatePath = tpl_site.'ncustom/'.$ctname;
	}

	// Check if we need pagination
	$page				= 0;
	$pageCount			= 0;

	// If we have comments more than for one page - activate pagination
	$multi_scount = intval(pluginGetVariable('comments', 'multi_scount'));
	if (($multi_scount > 0) && ($newsRow['com'] > $multi_scount)) {

		// Page count
		$pageCount = ceil($newsRow['com'] / $multi_scount);

		// Check if user wants to access not first page
		$page = intval($_REQUEST['page']);
		if ($page < 1) $page = 1;

		$callingCommentsParams['limitCount'] = intval(pluginGetVariable('comments', 'multi_scount'));
		$callingCommentsParams['limitStart'] = ($page-1) * intval(pluginGetVariable('comments', 'multi_scount'));
	}

	// Pass total number of comments
	$callingCommentsParams['total'] = $newsRow['com'];

	// Show comments
	$tcvars = array();
	$tcvars['vars']['entries'] = comments_show($newsID, 0, 0, $callingCommentsParams);

	if ($pageCount > 1) {
	    $paginationParams = checkLinkAvailable('comments', 'show')?
	    			array('pluginName' => 'comments', 'pluginHandler' => 'show', 'params' => array('news_id' => $newsID), 'xparams' => array(), 'paginator' => array('page', 0, false)):
	    			array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'comments', 'handler' => 'show'), 'xparams' => array('news_id' => $newsID), 'paginator' => array('page', 1, false));

		templateLoadVariables(true);
		$navigations = $TemplateCache['site']['#variables']['navigation'];
		$tcvars['vars']['more_comments'] = generatePagination($page, 1, $pageCount, 10, $paginationParams, $navigations, true);
		$tcvars['regx']['#\[more_comments\](.*?)\[\/more_comments\]#is'] = '$1';
	} else {
		$tcvars['vars']['more_comments'] = '';
		$tcvars['regx']['#\[more_comments\](.*?)\[\/more_comments\]#is'] = '';
	}

	// Enable AJAX in case if we are on last page
	if ($page == $pageCount)
		$callingCommentsParams['noajax'] = 0;

    $allowCom = $newsRow['allow_com'];

	// Show form for adding comments
	if ($newsRow['allow_com'] && (!pluginGetVariable('comments', 'regonly') || is_array($userROW))) {
		$tcvars['vars']['form'] = comments_showform($newsID, $callingCommentsParams);
		$tcvars['regx']['#\[regonly\](.*?)\[\/regonly\]#is'] = '';
		$tcvars['regx']['#\[commforbidden\](.*?)\[\/commforbidden\]#is'] = '';
	} else {
		$tcvars['vars']['form'] = '';
		$tcvars['regx']['#\[regonly\](.*?)\[\/regonly\]#is'] = $allowCom?'$1':'';
		$tcvars['regx']['#\[commforbidden\](.*?)\[\/commforbidden\]#is'] = $allowCom?'':'$1';
	}

	// Show header file
	$tcvars['vars']['link']	= newsGenerateLink($newsRow);
	$tcvars['vars']['title']	= secure_html($newsRow['title']);
	$tcvars['regx']['[\[comheader\](.*)\[/comheader\]]'] = ($newsRow['com'])?'$1':'';


	$tpl->template('comments.external', $templatePath);
	$tpl->vars('comments.external', $tcvars);

	$template['vars']['mainblock'] .= $tpl->show('comments.external');
}


// Delete comment
function plugin_comments_delete(){
	global $mysql, $config, $userROW, $lang, $tpl, $template, $SUPRESS_MAINBLOCK_SHOW, $SUPRESS_TEMPLATE_SHOW;

	$output = array();
	$params = array();

	// First: check if user have enough permissions
	if (!is_array($userROW) || ($userROW['status'] > 2) || ($_GET['uT'] != genUToken(intval($_REQUEST['id'])))) {
		// Not allowed
		$output['status']	= 0;
		$output['data']		= $lang['perm.denied'];
	} else {
		// Second: check if this comment exists
		$comid = intval($_REQUEST['id']);

		if (($comid) && ($row = $mysql->record("select * from ".prefix."_comments where id=".db_squote($comid)))) {
			$mysql->query("delete from ".prefix."_comments where id=".db_squote($comid));
			$mysql->query("update ".uprefix."_users set com=com-1 where id=".db_squote($row['author_id']));
			$mysql->query("update ".prefix."_news set com=com-1 where id=".db_squote($row['post']));

			$output['status']	= 1;
			$output['data']		= $lang['comments:deleted.text'];
			$params['newsid']	= $row['post'];
		} else {
			$output['status']	= 0;
			$output['data']		= $lang['comments:err.nocomment'];
		}
	}

	$SUPRESS_TEMPLATE_SHOW = 1;

	// Check if we run AJAX request
	if ($_REQUEST['ajax']) {
		$output['data'] = iconv('Windows-1251', 'UTF-8', $output['data']);
		$template['vars']['mainblock'] = json_encode($output);
	} else {
		// NON-AJAX mode

		// Fetch news record
		if ($nrow = $mysql->record("select * from ".prefix."_news where id = ".db_squote($params['newsid']))) {
			$url = newsGenerateLink($nrow);
		} else {
			$url = $config['home_url'];
		}

		$tavars = array( 'vars' => array(
			'message'	=> $output['data'],
			'link'		=> $url,
		));

		// If ok - redirect to news
		if ($output['status']) {
			$tavars['vars']['title']	= $lang['comments:deleted.title'];
			$tavars['vars']['linktext']	= $lang['comments:deleted.url'];
		} else {
			// Print error messag
			// NON-AJAX MODE
			$tavars['vars']['title']	= $lang['comments:err.redir.title'];
			$tavars['vars']['linktext']	= $lang['comments:err.redir.url'];
		}
		$tpl -> template('redirect', tpl_site);
		$tpl -> vars('redirect', $tavars);
		$template['vars']['mainblock'] = $tpl -> show('redirect');
	}
}

loadPluginLang('comments', 'main', '', '', ':');
register_filter('news','comments', new CommentsNewsFilter);
register_admin_filter('categories', 'comments', new CommentsFilterAdminCategories);

register_plugin_page('comments','add','plugin_comments_add',0);
register_plugin_page('comments','show','plugin_comments_show',0);
register_plugin_page('comments','delete','plugin_comments_delete',0);