<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

$lang = LoadLang("comments", "site");

class CommentsNewsFilter extends NewsFilter {
	function editNewsForm($newsID, $SQLold, &$tvars) {
		global $lang, $mysql, $config, $parse, $tpl, $mod;

		// List comments
		$comments = '';
		$tpl -> template('comments', tpl_actions.$mod);

		foreach ($mysql->select("select * from ".prefix."_comments where post='".$newsID."' order by id") as $crow) {
			$text	= $crow['text'];

			if ($config['blocks_for_reg'])		{ $text = $parse -> userblocks($text); }
			if ($config['use_htmlformatter'])	{ $text = $parse -> htmlformatter($text); }
			if ($config['use_bbcodes'])			{ $text = $parse -> bbcodes($text); }
			if ($config['use_smilies'])			{ $text = $parse -> smilies($text); }

			$txvars['vars'] = array(
				'php_self'		=>	$PHP_SELF,
				'com_author'	=>	$crow['author'],
				'com_post'		=>	$crow['post'],
				'com_url'		=>	($crow['url']) ? $crow['url'] : $PHP_SELF.'?mod=users&action=edituser&id='.$crow['author_id'],
				'com_id'		=>	$crow['id'],
				'com_ip'		=>	$crow['ip'],
				'com_time'		=>	LangDate(extra_get_param('comments','timestamp'), $crow['postdate']),
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
		$tvars['vars']['comments'] = $comments;

		$tvars['vars']['comnum'] = $SQLold['com']?$SQLold['com']:$lang['noa'];
		$tvars['regx']['[\[comments\](.*)\[/comments\]]']     = ($SQLnews['com'])?'$1':'';
		$tvars['regx']['[\[nocomments\](.*)\[/nocomments\]]'] = ($SQLnews['com'])?'':'$1';
	}

	function showNews($newsID, $SQLnews, &$tvars, $mode = array()) {
		$tvars['vars']['comments-num']	=	$SQLnews['com'];
		$tvars['vars']['comnum']	=	$SQLnews['com'];
		$tvars['regx']['[\[comheader\](.*)\[/comheader\]]'] = ($SQLnews['com'])?'$1':'';

		// Blocks [comments] .. [/comments] and [nocomments] .. [/nocomments]
		$tvars['regx']['[\[comments\](.*)\[/comments\]]']     = ($SQLnews['com'])?'$1':'';
		$tvars['regx']['[\[nocomments\](.*)\[/nocomments\]]'] = ($SQLnews['com'])?'':'$1';

	}

	function onAfterNewsShow ($newsID, $SQLnews, $mode = array()) {
		global $catmap, $catz, $config, $userROW, $template, $lang;
		// Skin short mode
		if ($mode['style'] != 'full')
			return 1;

		// Prepare params for call
		$callingCommentsParams = array();

		// Set default template path
		$templatePath = tpl_dir.$config['theme'];

		$fcat = array_shift(explode(",", $SQLnews['catid']));

		// Check if there is a custom mapping
		if ($fcat && $catmap[$fcat] && ($ctname = $catz[$catmap[$fcat]]['tpl'])) {
			// Check if directory exists
			if (is_dir($templatePath.'/ncustom/'.$ctname))
				$callingCommentsParams['overrideTemplatePath'] = $templatePath.'/ncustom/'.$ctname;
		}

		include_once(root."/plugins/comments/inc/comments.show.php");

		// Check if we need pagination
		$flagMoreComments	= false;
		$skipCommShow		= false;

		if (extra_get_param('comments', 'multipage')) {
			$multi_mcount = intval(extra_get_param('comments', 'multi_mcount'));
			// If we have comments more than for one page - activate pagination
			if (($multi_mcount >= 0) && ($SQLnews['com'] > $multi_mcount)) {
				$callingCommentsParams['limitCount'] = $multi_mcount;
				$flagMoreComments = true;
				if (!$multi_mcount)
					$skipCommShow = true;
			}

		}

		// Show comments [ if not skipped ]
		if (!$skipCommShow)
			comments_show($newsID, 0, 0, $callingCommentsParams);

		// If multipage is used and we have more comments - show
		if ($flagMoreComments) {
			$link = GetLink('plugins', array('plugin_name' => 'comments'), 1);
			$link .= ((strpos($link,'?') === false)?'?':'&').'plugin_cmd=show&news_id='.$newsID;
			$template['vars']['mainblock'] .= str_replace(array('{link}', '{count}'), array($link, $SQLnews['com']), $lang['comments:link.more']);
		}

		// Show form for adding comments
		if ($SQLnews['allow_com'] && (!extra_get_param('comments', 'regonly') || is_array($userROW)))
			comments_showform($newsID, $callingCommentsParams);

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
			$link = GetLink('full', $addResult[0], 1);
			// Make redirect to full news
			@header("Location: ".$link);
			return 1;
		}

		// AJAX MODE.
		// Let's print (ONLY) new comment
		$SQLnews	= $addResult[0];
		$commentId	= $addResult[1];

		// Check if we need to override news template
		$callingCommentsParams = array();

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

		comments_show($SQLnews['id'], $commentId, $SQLnews['com']+1, $callingCommentsParams);
		return 1;
	} else {
		// Some errors.
		if ($_REQUEST['ajax']) {
			// AJAX MODE
			$tpl -> template('error', tpl_site);
			$tpl -> vars('error', array( 'vars' => array('content' => $template['vars']['mainblock'])));
			$template['vars']['mainblock'] = $tpl -> show('error');
		} else {
			// NON-AJAX MODE
			$tavars = array( 'vars' => array(
				'title'		=> $lang['comments:err.redir.title'],
				'message'	=> $template['vars']['mainblock'],
				'link'		=> ($_REQUEST['referer'])?$_REQUEST['referer']:'/',
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
	global $config, $catz, $mysql, $catmap, $tpl, $template, $lang, $SUPRESS_TEMPLATE_SHOW, $userROW;

	include_once(root."/plugins/comments/inc/comments.show.php");

	// Try to fetch news
	$newsID = intval($_REQUEST['news_id']);

	if (!$newsID || !is_array($newsRow = $mysql->record("select * from ".prefix."_news where id = ".$newsID))) {
		$template['vars']['mainblock'] = $lang['comments:err.nonews'];
		return;
	}

	// Prepare params for call
	// AJAX is turned off by default
	$callingCommentsParams = array( 'noajax' => 1);

	// Set default template path
	$templatePath = tpl_dir.$config['theme'];

	$fcat = array_shift(explode(",", $newsRow['catid']));

	// Check if there is a custom mapping
	if ($fcat && $catmap[$fcat] && ($ctname = $catz[$catmap[$fcat]]['tpl'])) {
		// Check if directory exists
		if (is_dir($templatePath.'/ncustom/'.$ctname))
			$callingCommentsParams['overrideTemplatePath'] = $templatePath.'/ncustom/'.$ctname;
			$templatePath = $templatePath.'/ncustom/'.$ctname;
	}

	// Show header file
	$tvars = array();
	$tvars['vars']['link']	= GetLink('full', $newsRow);
	$tvars['vars']['title']	= secure_html($newsRow['title']);

	$tpl->template('comments.header', $templatePath);
	$tpl->vars('comments.header', $tvars);

	$template['vars']['mainblock'] .= $tpl->show('comments.header');

	// Check if we need pagination
	$page				= 0;
	$pageCount			= 0;

	// If we have comments more than for one page - activate pagination
	$multi_scount = intval(extra_get_param('comments', 'multi_scount'));
	if (($multi_scount > 0) && ($newsRow['com'] > $multi_scount)) {

		// Page count
		$pageCount = ceil($newsRow['com'] / $multi_scount);

		// Check if user wants to access not first page
		$page = intval($_REQUEST['page']);
		if ($page < 1) $page = 1;

		$callingCommentsParams['limitCount'] = intval(extra_get_param('comments', 'multi_scount'));
		$callingCommentsParams['limitStart'] = ($page-1) * intval(extra_get_param('comments', 'multi_scount'));
	}

	// Show comments
	comments_show($newsID, 0, 0, $callingCommentsParams);

	if ($pageCount > 1) {
		$link = GetLink('plugins', array('plugin_name' => 'comments'), 1);
		$link .= ((strpos($link,'?') === false)?'?':'&').'plugin_cmd=show&news_id='.$newsID.'&page={page}';

		$navigations = getNavigations(tpl_dir.$config['theme']);
		$template['vars']['mainblock'] .= generatePagination($page, 1, $pageCount, 10, $link, $navigations);
	}

	// Enable AJAX in case if we are on last page
	if ($page == $pageCount)
		$callingCommentsParams['noajax'] = 0;

	// Show form for adding comments
	if ($newsRow['allow_com'] && (!extra_get_param('comments', 'regonly') || is_array($userROW))) {
		comments_showform($newsID, $callingCommentsParams);
	}

}


loadPluginLang('comments', 'main', '', '', ':');
register_filter('news','comments', new CommentsNewsFilter);
register_plugin_page('comments','add','plugin_comments_add',0);
register_plugin_page('comments','show','plugin_comments_show',0);
