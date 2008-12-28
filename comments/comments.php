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
		global $catmap, $catz, $config, $userROW;
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

		// If news is found, check if we need to show comments
		//@include_once root.'includes/comments.show.php';
		comments_show($newsID, 0, 0, $callingCommentsParams);
		if ($SQLnews['allow_com'] && !$config['forbid_comments'] && (!$config['com_for_reg'] || is_array($userROW)))
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

loadPluginLang('comments', 'main', '', '', ':');
register_filter('news','comments', new CommentsNewsFilter);
register_plugin_page('comments','add','plugin_comments_add',0);
