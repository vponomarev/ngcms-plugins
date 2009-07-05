<?php

//
// Copyright (C) 2006-2008 Next Generation CMS (http://ngcms.ru/)
// Name: comments.show.php
// Description: Routines for showing comments
// Author: Vitaly Ponomarev, Alexey Zinchenko
//

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');


//
// Show comments for a news
// $newsID - [required] ID of the news for that comments should be showed
// $commID - [optional] ID of comment for showing in case if we just added it
// $commDisplayNum - [optional] num that is showed in 'show comment' template
// $callingParams
//		'plugin'  => if is called from plugin - ID of plugin
//		'overrideTemplateName' => alternative template for display
//		'overrideTemplatePath' => alternative path for searching of template
//		'limitStart' => order comment no to start (for pagination)
//		'limitCount' => number of comments to show (for pagination)
function comments_show($newsID, $commID = 0, $commDisplayNum = 0, $callingParams = array()){
	global $mysql, $tpl, $template, $config, $userROW, $parse, $lang, $PFILTERS;

	// -> desired template path
	$templatePath = ($callingParams['overrideTemplatePath'])?$callingParams['overrideTemplatePath']:tpl_dir.$config['theme'];

	// -> desired template
	if ($callingParams['overrideTemplateName']) {
		$templateName = $callingParams['overrideTemplateName'];
	} else {
		$templateName = 'comments.show';
	}

	$tpl -> template($templateName, $templatePath);

	if ($config['use_avatars']) {
		$sql = "select c.*, u.avatar from ".prefix."_comments c left join ".uprefix."_users u on c.author_id = u.id where c.post=".db_squote($newsID).($commID?(" and c.id=".db_squote($commID)):'');
	} else {
		$sql = "select c.* from ".prefix."_comments c WHERE c.post=".db_squote($newsID).($commID?(" and c.id=".db_squote($commID)):'');
	}
	$sql .= " order by c.id".(extra_get_param('comments', 'backorder')?' desc':'');

	// Comments counter
	$comnum = 0;

	// Check if we need to use limits
	if ($callingParams['limitStart'] || $callingParams['limitCount']) {
		$sql .= ' limit '.intval($callingParams['limitStart']).", ".intval($callingParams['limitCount']);
		$comnum = intval($callingParams['limitStart']);
	}

	$timestamp = extra_get_param('comments', 'timestamp');
	if (!$timestamp)
		$timestamp = 'j.m.Y - H:i';

	foreach ($mysql->select($sql) as $row) {
		$comnum++;
		$tvars['vars']['id']		=	$row['id'];
		$tvars['vars']['author']	=	$row['author'];
		$tvars['vars']['mail']		=	$row['mail'];
		$tvars['vars']['date']		=	LangDate($timestamp, $row['postdate']);

		if ($row['reg'] && getPluginStatusActive('uprofile')) {
			$tvars['vars']['profile_link'] = checkLinkAvailable('uprofile', 'show')?
				generateLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id'])):
				generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('id' => $row['author_id']));
			$tvars['regx']["'\[profile\](.*?)\[/profile\]'si"] = '$1';
		} else {
			$tvars['vars']['profile_link'] = '';
			$tvars['regx']["'\[profile\](.*?)\[/profile\]'si"] = '';
		}

		// Add [hide] tag processing
		$text	= $row['text'];

		if ($config['blocks_for_reg'])		{ $text = $parse -> userblocks($text); }
		if ($config['use_htmlformatter'])	{ $text = $parse -> htmlformatter($text); }
		if ($config['use_bbcodes'])			{ $text = $parse -> bbcodes($text); }
		if ($config['use_smilies'])			{ $text = $parse -> smilies($text); }


		if (intval($config['com_wrap']) && (strlen($text) > $config['com_wrap'])) {
			$tvars['vars']['comment-short']	=	substr($text, 0, $config['com_wrap']);
			$tvars['vars']['comment-full']	=	substr($text, $config['com_wrap']);
			$tvars['regx']["'\[comment_full\](.*?)\[/comment_full\]'si"] = '$1';
		} else {
			$tvars['vars']['comment-short'] = $text;
			$tvars['regx']["'\[comment_full\](.*?)\[/comment_full\]'si"] = '';
		}
		if ($commID && $commDisplayNum)
			$comnum = $commDisplayNum;

		$tvars['vars']['comnum'] = $comnum;
		$tvars['vars']['alternating'] = ($comnum%2) ? "comment_even" : "comment_odd";

		if ($config['use_avatars']) {
			if ($row['avatar']) {
				$tvars['vars']['avatar'] = "<img src=\"".avatars_url."/".$row['avatar']."\" alt=\"".$row['author']."\" />";
			} else {
				// If gravatar integration is active, show avatar from GRAVATAR.COM
				if ($config['avatars_gravatar']) {
					$tvars['vars']['avatar'] = '<img src="http://www.gravatar.com/avatar/'.md5(strtolower($row['mail'])).'.jpg?s='.$config['avatar_wh'].'&d='.urlencode(avatars_url."/noavatar.gif").'" alt=""/>';
				} else {
					$tvars['vars']['avatar'] = "<img src=\"".avatars_url."/noavatar.gif\" alt=\"\" />";
				}
			}
		} else {
			$tvars['vars']['avatar'] = '';
		}

		if ($config['use_bbcodes']) {
			$tvars['regx']["'\[quote\](.*?)\[/quote\]'si"] = '$1';
		} else {
			$tvars['regx']["'\[quote\](.*?)\[/quote\]'si"] = '';
		}

		if ($row['answer'] != '') {
			$answer = $row['answer'];

			if ($config['blocks_for_reg'])		{ $answer = $parse -> userblocks($answer); }
			if ($config['use_htmlformatter'])	{ $answer = $parse -> htmlformatter($answer); }
			if ($config['use_bbcodes'])			{ $answer = $parse -> bbcodes($answer); }
			if ($config['use_smilies'])			{ $answer = $parse -> smilies($answer); }

			$tvars['vars']['answer']	=	$answer;
			$tvars['vars']['name']		=	$row['name'];
			$tvars['regx']["'\[answer\](.*?)\[/answer\]'si"] = '$1';
		} else {
			$tvars['regx']["'\[answer\](.*?)\[/answer\]'si"] = '';
		}

		if (is_array($userROW) && (($userROW['status'] == 1) || ($userROW['status'] == 2))) {
			$tvars['vars']['[edit-com]'] = "<a href=\"".admin_url."/admin.php?mod=editcomments&amp;newsid=$newsID&amp;comid=$row[id]\" target=\"_blank\" title=\"".$lang['addanswer']."\">";
			$tvars['vars']['[/edit-com]'] = "</a>";
			$tvars['vars']['[del-com]'] = "<a href=\"".admin_url."/admin.php?mod=editcomments&amp;subaction=deletecomment&amp;newsid=$newsID&amp;comid=$row[id]&amp;oster=$row[author]\" title=\"".$lang['comdelete']."\">";
			$tvars['vars']['[/del-com]'] = "</a>";
			$tvars['vars']['ip'] = "<a href=\"http://www.nic.ru/whois/?ip=$row[ip]\" title=\"".$lang['whois']."\">".$lang['whois']."</a>";
		} else {
			$tvars['regx']["'\\[edit-com\\].*?\\[/edit-com\\]'si"]	=	'';
			$tvars['regx']["'\\[del-com\\].*?\\[/del-com\\]'si"]	=	'';
			$tvars['vars']['ip'] = '';
		}

		// RUN interceptors
		if (is_array($PFILTERS['comments']))
			foreach ($PFILTERS['comments'] as $k => $v)
				$v->showComments($newsID, $row, $comnum, $tvars);

		// run OLD-STYLE interceptors
		exec_acts('comments', $row);

		// Show template
		$tpl -> vars($templateName, $tvars);
		$template['vars']['mainblock'] .= $tpl -> show($templateName);

	}
}

// $callingParams
//		'plugin'  => if is called from plugin - ID of plugin
//		'overrideTemplateName' => alternative template for display
//		'overrideTemplatePath' => alternative path for searching of template
//		'noajax'		=> DISABLE AJAX mode
function comments_showform($newsID, $callingParams = array()){
	global $mysql, $config, $template, $tpl, $userROW, $PFILTERS;

	// -> desired template path
	$templatePath = ($callingParams['overrideTemplatePath'])?$callingParams['overrideTemplatePath']:tpl_dir.$config['theme'];

	// -> desired template
	if ($callingParams['overrideTemplateName']) {
		$templateName = $callingParams['overrideTemplateName'];
	} else {
		$templateName = 'comments.form';
	}

	$tpl -> template($templateName, $templatePath);

	if($config['use_smilies']) {
		$tvars['vars']['smilies'] = InsertSmilies('comments', 10);
	} else {
		$tvars['vars']['smilies'] = "";
	}

	// Lock AJAX calls if required
	$tvars['regx']['#\[ajax\](.*?)\[\/ajax\]#is'] = $callingParams['noajax']?'':'$1';

	if ($_COOKIE['com_username'] && trim($_COOKIE['com_username']) != "") {
		$tvars['vars']['savedname'] = secure_html(urldecode($_COOKIE['com_username']));
		$tvars['vars']['savedmail'] = secure_html(urldecode($_COOKIE['com_usermail']));
	} else {
		$tvars['vars']['savedname'] = '';
		$tvars['vars']['savedmail'] = '';
	}

	if (!is_array($userROW)) {
		$tvars['vars']['[not-logged]'] = "";
		$tvars['vars']['[/not-logged]'] = "";
	} else {
		$tvars['regx']["'\[not-logged\].*?\[/not-logged\]'si"] = "";
	}

	$tvars['vars']['admin_url'] = admin_url;
	$tvars['vars']['rand'] = rand(00000, 99999);

	if ($config['use_captcha'] && (!is_array($userROW))) {
		@session_register('captcha');
		$_SESSION['captcha'] = rand(00000, 99999);
		$tvars['regx']["'\[captcha\](.*?)\[/captcha\]'si"] = '$1';
	} else {
		$tvars['regx']["'\[captcha\](.*?)\[/captcha\]'si"] = '';
	}

	$tvars['vars']['captcha_url']	=	admin_url."/captcha.php";
	$tvars['vars']['bbcodes']		=	BBCodes();
	$tvars['vars']['skins_url']		=	skins_url;
	$tvars['vars']['newsid']		=	$newsID;
	$tvars['vars']['request_uri']	=	$_SERVER['REQUEST_URI'];

	// Generate request URL
	$link = generateLink('core', 'plugin', array('plugin' => 'comments', 'handler' => 'add'));
	$tvars['vars']['post_url'] = $link;

	// RUN interceptors
	if (is_array($PFILTERS['comments']))
		foreach ($PFILTERS['comments'] as $k => $v)
			$v->showComments($newsID, $row, $comnum, $tvars);

	// RUN interceptors ( OLD-style )
	exec_acts('comments_form', $row);

	$tpl -> vars($templateName, $tvars);
	$template['vars']['mainblock'] .= $tpl -> show($templateName);
}

// preload plugins
load_extras('comments');
load_extras('comments:show');
