<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

add_act('index', 'lastcomments_block');
register_plugin_page('lastcomments','','lastcomments_page',0);


function lastcomments_block() {
	// Action if sidepanel is enabled
	if (extra_get_param('lastcomments','sidepanel')) {
		lastcomments();
	}
}


function lastcomments_page() {
	// Action if sidepanel is enabled
	if (extra_get_param('lastcomments','ppage')) {
		lastcomments(1);
	}
}

function lastcomments($pp = 0) {
    global $config, $mysql, $template, $tvars, $tpl, $parse;

	if ($pp) { $pp = 'pp_'; } else { $pp = '';}

	// Generate cache file name [ we should take into account SWITCHER plugin & calling parameters ]
	$cacheFileName = md5('lastcomments'.$config['theme'].$config['default_lang'].$pp).'.txt';

	if (extra_get_param('lastcomments','cache')) {
		$cacheData = cacheRetrieveFile($cacheFileName, extra_get_param('lastcomments','cacheExpire'), 'lastcomments');
		if ($cacheData != false) {
			// We got data from cache. Return it and stop
			$template['vars'][($pp)?'mainblock':'plugin_lastcomments'] = $cacheData;
			return;
		}
	}

	$comm_num = 0;
	$number			= intval(extra_get_param('lastcomments',$pp.'number'));
	$comm_length	= intval(extra_get_param('lastcomments',$pp.'comm_length'));
	if (($number < 1)       || ($number > 50))          		  { $number      = $pp?30:10;  }
	if (($comm_length < 10) || ($comm_length > ($pp?500:100))) { $comm_length = $pp?500:50; }

	// Determine paths for all template files
	$tpath = locatePluginTemplates(array($pp.'lastcomments', $pp.'entries'), 'lastcomments', extra_get_param('lastcomments', 'localsource'));

	$query = "select c.*, u.avatar as users_avatar, u.id as uid, n.id as nid, n.title, n.alt_name, n.catid, n.postdate as npostdate from ".prefix."_comments c left join ".prefix."_news n on c.post=n.id left join ".uprefix."_users u on c.author_id = u.id where n.approve=1 order by c.id desc limit ".$number;
	$lastcomments = '';
	foreach ($mysql->select($query) as $row) {

		// Parse comments
		$text = $row['text'];
		if ($config['blocks_for_reg'])		{ $text = $parse -> userblocks($text); }
		if ($config['use_bbcodes'])			{ $text = $parse -> bbcodes($text); }
		if ($config['use_htmlformatter'])	{ $text = $parse -> htmlformatter($text); }
		if ($config['use_smilies'])			{ $text = $parse -> smilies($text); }
	    if (strlen($text) > $comm_length)	{ $text = $parse -> truncateHTML($text, $comm_length);}
	    ++$comm_num;

		$tvars['vars'] = array(
			'link'		=>	newsGenerateLink(array('id' => $row['nid'], 'alt_name' => $row['alt_name'], 'catid' => $row['catid'], 'postdate' => $row['npostdate'])),
			'date'		=>	langdate('d.m.Y', $row['postdate']),
			'author'	=>	str_replace('<', '&lt;', $row['author']),
			'author_id'	=>	$row['author_id'],
			'title'		=>	str_replace('<', '&lt;', $row['title']),
			'text'		=>	$text,
			'category_link'	=>	GetCategories($row['catid']),
			'comnum'	=>	$comm_num
		);

		// gen answer
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

		
		// gen avatar
		if ($config['use_avatars']) {
			if ($row['users_avatar']) {
				$tvars['vars']['avatar'] = "<img src=\"".avatars_url."/".$row['users_avatar']."\" alt=\"".$row['author']."\" />";
				$tvars['vars']['avatar_url'] = avatars_url."/".$row['users_avatar'];
			} else {
				// If gravatar integration is active, show avatar from GRAVATAR.COM
				if ($config['avatars_gravatar']) {
					$tvars['vars']['avatar'] = '<img src="http://www.gravatar.com/avatar/'.md5(strtolower($row['mail'])).'?s='.$config['avatar_wh'].'&amp;d='.urlencode(avatars_url."/noavatar.png").'" alt=""/>';
					$tvars['vars']['avatar_url'] = 'http://www.gravatar.com/avatar/'.md5(strtolower($row['mail'])).'?s='.$config['avatar_wh'].'&amp;d='.urlencode(avatars_url."/noavatar.gif");
				} else {
					$tvars['vars']['avatar'] = "<img src=\"".avatars_url."/noavatar.gif\" alt=\"\" />";
					$tvars['vars']['avatar_url'] = avatars_url."/noavatar.gif";
				}
			}
		} else {
			$tvars['vars']['avatar'] = '';
			$tvars['vars']['avatar_url'] = '';
		}

		if ($row['author_id'] && getPluginStatusActive('uprofile')) {
			$tvars['vars']['author_link'] = checkLinkAvailable('uprofile', 'show')?
				generateLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id'])):
				generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('id' => $row['author_id']));
			$tvars['regx']["'\[profile\](.*?)\[/profile\]'si"] = '$1';
		} else {
			$tvars['vars']['author_link'] = '';
			$tvars['regx']["'\[profile\](.*?)\[/profile\]'si"] = '';
		}

        $tpl -> template($pp.'entries', $tpath[$pp.'entries']);
        $tpl -> vars($pp.'entries', $tvars);
        $lastcomments .= $tpl -> show($pp.'entries');
    }

    unset($tvars);

    $tvars['vars'] = array(
    	'entries' => $lastcomments
    );

    if ($comm_num > 0) {
    	$tvars['regx']["'\[nocomments\](.*?)\[/nocomments\]'si"] = '';
    } else {
    	$tvars['regx']["'\[nocomments\](.*?)\[/nocomments\]'si"] = '$1';
    }

    $tpl -> template($pp.'lastcomments', $tpath[$pp.'lastcomments']);
    $tpl -> vars($pp.'lastcomments', $tvars);

	$output = $tpl -> show($pp.'lastcomments');
	$template['vars'][($pp)?'mainblock':'plugin_lastcomments'] = $output;

	if (extra_get_param('lastcomments','cache')) {
		cacheStoreFile($cacheFileName, $output, 'lastcomments');
	}
}