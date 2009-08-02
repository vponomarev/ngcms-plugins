<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Params for filtering and processing
//
function comments_add(){
	global $mysql, $config, $AUTH_METHOD, $userROW, $ip, $lang, $parse, $HTTP_REFERER, $catmap, $catz, $PFILTERS;

	// Check membership
	// If login/pass is entered (either logged or not)
	if ($_POST['name'] && $_POST['password']) {
		$auth	= $AUTH_METHOD[$config['auth_module']];
		$user	= $auth->login(0, $_POST['name'], $_POST['password']);
		if (!is_array($user)) {
			msg(array("type" => "error", "text" => $lang['comments:err.password']));
			return;
		}
	}

	// Entered data have higher priority then login data
	$memberRec = null;
	if (is_array($user)) {
		$SQL['author']			= $user['name'];
		$SQL['author_id']		= $user['id'];
		$SQL['mail']			= $user['mail'];
		$is_member				= 1;
		$memberRec				= $user;
	} else if (is_array($userROW)) {
		$SQL['author']			= $userROW['name'];
		$SQL['author_id']		= $userROW['id'];
		$SQL['mail']			= $userROW['mail'];
		$is_member				= 1;
		$memberRec				= $userROW;
	} else {
		$SQL['author']			= secure_html(convert(trim($_POST['name'])));
		$SQL['author_id']		= 0;
		$SQL['mail']			= secure_html(trim($_POST['mail']));
		$is_member				= 0;
	}

	$SQL['post']	=	intval($_POST['newsid']);
	$SQL['text']	=	secure_html(convert(trim($_POST['content'])));

	// If user is not logged, make some additional tests
	if (!$is_member) {
		// Check if unreg are allowed to make comments
		if (extra_get_param('comments', 'regonly')) {
			msg(array("type" => "error", "text" => $lang['comments:err.regonly']));
			return;
		}
		// Check captcha for unregistered visitors
		if ($config['use_captcha']) {
			$vcode = $_POST['vcode'];

			if ($vcode != $_SESSION['captcha']) {
				msg(array("type" => "error", "text" => $lang['comments:err.vcode']));
				return;
			}

			// Update captcha
			$_SESSION['captcha'] = rand(00000, 99999);
		}

		if (!$SQL['author']) {
			msg(array("type" => "error", "text" => $lang['comments:err.name']));
			return;
		}
		if (!$SQL['mail']) {
			msg(array("type" => "error", "text" => $lang['comments:err.mail']));
			return;
		}

		// Check if author name use incorrect symbols. Check should be done only for unregs
		if ((!$SQL['author_id']) && (preg_match("/[^(\w)|(\x7F-\xFF)|(\s)]/", $SQL['author']) || strlen($SQL['author']) > 60)) {
			msg(array("type" => "error", "text" => $lang['comments:err.badname']));
			return;
		}
		if (strlen($SQL['mail']) > 70 || !preg_match("/^[\.A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $SQL['mail'])) {
			msg(array("type" => "error", "text" => $lang['comments:err.badmail']));
			return;
		}
	}

	$maxlen = intval(extra_get_param('comments', 'maxlen'));
	if (($maxlen) && (strlen($SQL['text']) > $maxlen || strlen($SQL['text']) < 2)) {
		msg(array("type" => "error", "text" => str_replace('{maxlen}',extra_get_param('comments', 'maxlen') ,$lang['comments:err.badtext'])));
		return;
	}

	// Check for flood
	if (checkFlood(0, $ip, 'comments', 'add', $is_member?$memberRec:null, $is_member?null:$SQL['author'])) {
		msg(array("type" => "error", "text" => str_replace('{timeout}',$config['flood_time'] ,$lang['comments:err.flood'])));
		return;
	}

	// Check for bans
	if ($ban_mode = checkBanned($ip, 'comments', 'add', $is_member?$memberRec:null, $is_member?null:$SQL['author'])) {
		// If hidden mode is active - say that news is not found
		if ($ban_mode == 2) {
			msg(array("type" => "error", "text" => $lang['comments:err.notfound']));
	        } else {
	        	msg(array("type" => "error", "text" => $lang['comments:err.ipban']));
	        }
		return;
	}

	// Locate news
	if ($news_row = $mysql->record("select * from ".prefix."_news where id = ".db_squote($SQL['post']))) {
		if (!$news_row['allow_com']) {
			msg(array("type" => "error", "text" => $lang['comments:err.forbidden']));
			return;
		}
	} else {
		msg(array("type" => "error", "text" => $lang['comments:err.notfound']));
		return;
	}

	// Check for multiple comments block [!!! ADMINS CAN DO IT IN ANY CASE !!!]
	$multiCheck = 0;

	// Make tests only for non-admins
	if (!is_array($userROW)) {
		// Not logged
		$multiCheck = !intval(extra_get_param('comments', 'multi'));
	} else {
		// Logged. Skip admins
		if ($userROW['status'] != 1) {
			// Check for author
			$multiCheck = !intval(extra_get_param('comments', (($userROW['id'] == $news_row['author_id'])?'author_':'').'multi'));
		}
	}

	if ($multiCheck) {

		// Locate last comment for this news
		if (is_array($lpost = $mysql->record("select author_id, author, ip, mail from ".prefix."_comments where post=".db_squote($SQL['post'])." order by id desc limit 1"))) {
			// Check for post from the same user
			if (is_array($userROW)) {
				 if ($userROW['id'] == $lpost['author_id']) {
					msg(array("type" => "error", "text" => $lang['comments:err.multilock']));
					return;
				}
			} else {
				//print "Last post: ".$lpost['id']."<br>\n";
				if (($lpost['author'] == $SQL['author'])||($lpost['mail'] == $SQL['mail'])) {
					msg(array("type" => "error", "text" => $lang['comments:err.multilock']));
					return;
				}
			}
		}
	}


	$SQL['postdate'] = time() + ($config['date_adjust'] * 60);

	if (extra_get_param('comments', 'maxwlen') > 1){
		$SQL['text'] = preg_replace('/(\S{'.intval(extra_get_param('comments', 'maxwlen')).'})(?!\s)/', '$1 ', $SQL['text']);

		if ((!$SQL['author_id']) && (strlen($SQL['author']) > extra_get_param('comments', 'maxwlen'))) {
			$SQL['author'] = substr($SQL['author'], 0, extra_get_param('comments', 'maxwlen'))." ...";
		}
	}
	$SQL['text']	= str_replace("\r\n", "<br />", $SQL['text']);
	$SQL['ip']		= $ip;
	$SQL['reg']		= ($is_member) ? '1' : '0';

	// RUN interceptors
	load_extras('comments:add');

	$pluginNoError = 1;
	if (is_array($PFILTERS['comments']))
		foreach ($PFILTERS['comments'] as $k => $v) {
			if (!($pluginNoError = $v->addComments($memberRec, $news_row, $tvars, $SQL))) {
				msg(array("type" => "error", "text" => str_replace('{plugin}', $k, $lang['comments:err.pluginlock'])));
				break;
			}
		}

	if (!$pluginNoError) {
		return 0;
	}


	// Create comment
	$vnames = array(); $vparams = array();
	foreach ($SQL as $k => $v) { $vnames[]  = $k; $vparams[] = db_squote($v); }

	$mysql->query("insert into ".prefix."_comments (".implode(",",$vnames).") values (".implode(",",$vparams).")");

	// Retrieve comment ID
	$comment_id = $mysql->result("select LAST_INSERT_ID() as id");

	// Update comment counter in news
	$mysql->query("update ".prefix."_news set com=com+1 where id=".db_squote($SQL['post']));
	$comment_no = $new_row['com']+1;

	// Update counter for user
	if ($SQL['author_id']) {
		$mysql->query("update ".prefix."_users set com=com+1 where id = ".db_squote($SQL['author_id']));
	}

	// Update flood protect database
	checkFlood(1, $ip, 'comments', 'add', $is_member?$memberRec:null, $is_member?null:$SQL['author']);

	// RUN interceptors
	if (is_array($PFILTERS['comments']))
		foreach ($PFILTERS['comments'] as $k => $v)
			$v->addCommentsNotify($memberRec, $news_row, $tvars, $SQL, $comment_id);


	// Email informer
	if (extra_get_param('comments', 'inform_author') || extra_get_param('comments', 'inform_admin')) {
		if ($SQL['author_id']) {
			$alink = $config['home_url'].(checkLinkAvailable('uprofile', 'show')?
						generateLink('uprofile', 'show', array('name' => $SQL['author'], 'id' => $SQL['author_id'])):
						generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('name' => $SQL['author'], 'id' => $SQL['author_id'])));
		} else {
			$alink = '';
		}
		$body = str_replace(
			array(	'{username}',
					'[userlink]',
					'[/userlink]',
					'{comment}',
					'{newslink}',
					'{newstitle}'),
			array(	$SQL['author'],
					($SQL['author_id'])?'<a href="'.$alink.'">':'',
					($SQL['author_id'])?'</a>':'',
					$parse->bbcodes($parse->smilies(secure_html($SQL['text']))),
					$config['home_url'].newsGenerateLink($news_row),
					$news_row['title'],
					),
			$lang['notice']
		);

		if (extra_get_param('comments', 'inform_author')) {
			// Determine author's email
			if (is_array($umail=$mysql->record("select * from ".uprefix."_users where id = ".db_squote($news_row['author_id'])))) {
				zzMail($umail['mail'], $lang['newcomment'], $body, 'html');
			}
		}

		if (extra_get_param('comments', 'inform_admin'))
			zzMail($config['admin_mail'], $lang['newcomment'], $body, 'html');

	}

	@setcookie("com_username", urlencode($SQL['author']), 0, '/');
	@setcookie("com_usermail", urlencode($SQL['mail']), 0, '/');

	return array($news_row, $comment_id);
}