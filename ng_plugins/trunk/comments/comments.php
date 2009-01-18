<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

$lang = LoadLang("comments", "site");

class CommentsNewsFilter extends NewsFilter {
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
		$flagMoreComments = 0;

		if (extra_get_param('comments', 'multipage')) {
			// If we have comments more than for one page - activate pagination
			if ((intval(extra_get_param('comments', 'multi_mcount')) > 0) && ($SQLnews['com'] > intval(extra_get_param('comments', 'multi_mcount')))) {
				$callingCommentsParams['limitCount'] = intval(extra_get_param('comments', 'multi_mcount'));
				$flagMoreComments = 1;
			}

		}

		// Show comments
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
	global $config, $catz, $catmap, $tpl, $template, $SUPRESS_TEMPLATE_SHOW;

	$SUPRESS_TEMPLATE_SHOW = 1;

	// Connect library
	include_once(root."/plugins/comments/inc/comments.show.php");
	include_once(root."/plugins/comments/inc/comments.add.php");

	// Call comments_add() to ADD COMMENT
	if (is_array($addResult = comments_add())) {
		// OK. Let's print new comment
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
		// Some errors
		$tpl -> template('error', tpl_site);
		$tpl -> vars('error', array( 'vars' => array('content' => $template['vars']['mainblock'])));
		echo $tpl -> show('error');

		$template['vars']['mainblock'] = '';
	}
}

// Show dedicated page for comments
function plugin_comments_show(){
	global $config, $catz, $mysql, $catmap, $tpl, $template, $SUPRESS_TEMPLATE_SHOW;

	include_once(root."/plugins/comments/inc/comments.show.php");

	// Try to fetch news
	$newsID = intval($_REQUEST['news_id']);

	if (!$newsID || !is_array($newsRow = $mysql->record("select * from ".prefix."_news where id = ".$newsID))) {
		$template['vars']['mainblock'] = '��������� ������� �� ����������';
		return;
	}

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
	$flagMoreComments = 0;

	if (extra_get_param('comments', 'multipage')) {
		// If we have comments more than for one page - activate pagination
		if ((intval(extra_get_param('comments', 'multi_mcount')) > 0) && ($SQLnews['com'] > intval(extra_get_param('comments', 'multi_mcount')))) {
			$callingCommentsParams['limitCount'] = intval(extra_get_param('comments', 'multi_mcount'));
			$flagMoreComments = 1;
		}

	}

	// Show comments
	comments_show($newsID, 0, 0, $callingCommentsParams);

	// If multipage is used and we have more comments - show
	if ($flagMoreComments) {
		$link = GetLink('plugins', array('plugin_name' => 'comments'), 1);
		$link .= ((strpos($link,'?') === false)?'?':'&').'plugin_cmd=show&news_id='.$newsID;
		$template['vars']['mainblock'] .= str_replace('{link}', $link, $lang['comments:link.more']);
	}

	// Show form for adding comments
	if ($SQLnews['allow_com'] && (!extra_get_param('comments', 'regonly') || is_array($userROW)))
		comments_showform($newsID, $callingCommentsParams);

}


loadPluginLang('comments', 'main', '', '', ':');
register_filter('news','comments', new CommentsNewsFilter);
register_plugin_page('comments','add','plugin_comments_add',0);
register_plugin_page('comments','show','plugin_comments_show',0);
