<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Params for filtering and processing
//
function comments_add(){
	global $mysql, $config, $AUTH_METHOD, $userROW, $ip, $lang, $parse, $HTTP_REFERER, $catmap, $catz;

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
	if (is_array($user)) {
		$params['user_name']	= $user['name'];
		$params['author_id']	= $user['id'];
		$params['mail']			= $user['mail'];
		$is_member				= 1;
	} else if (is_array($userROW)) {
		$params['user_name']	= $userROW['name'];
		$params['author_id']	= $userROW['id'];
		$params['mail']			= $userROW['mail'];
		$is_member				= 1;
	} else {
		$params['user_name']	= secure_html(convert(trim($_POST['name'])));
		$params['author_id']	= 0;
		$params['mail']			= secure_html(trim($_POST['mail']));
		$is_member				= 0;
	}

	$params['newsid']	=	intval($_POST['newsid']);
	$params['content']	=	secure_html(convert(trim($_POST['content'])));

	// If user is not logged, make some additional tests
	if (!$is_member) {
		// Check if unreg are allowed to make comments
		if (!extra_get_param('comments', 'regonly')) {
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

		if (!$params['user_name']) {
			msg(array("type" => "error", "text" => $lang['commets:err.name']));
			return;
		}
		if (!$params['mail']) {
			msg(array("type" => "error", "text" => $lang['commets:err.mail']));
			return;
		}
		if (preg_match("/[^(\w)|(\x7F-\xFF)|(\s)]/", $params['user_name']) || strlen($params['user_name']) > 60) {
			msg(array("type" => "error", "text" => $lang['commets:err.badname']));
			return;
		}
		if (strlen($params['mail']) > 70 || !preg_match("/^[\.A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $params['mail'])) {
			msg(array("type" => "error", "text" => $lang['commets:err.badmail']));
			return;
		}
	}

	if (strlen($params['content']) > extra_get_param('comments', 'maxlen') || strlen($params['content']) < 2) {
		msg(array("type" => "error", "text" => str_replace('{maxlen}',extra_get_param('comments', 'maxlen') ,$lang['comments:err.badtext'])));
		return;
	}

	if ($config['flood_time']) {
		if (Flooder($ip)) {
			msg(array("type" => "error", "text" => str_replace('{timeout}',$config['flood_time'] ,$lang['commets:err.flood'])));
			return;
		}
	}

	/*
	if ($ban_row = $mysql->record("select * from ".prefix."_ipban where ip=".db_squote($ip))) {
		$mysql->query("update ".prefix."_ipban set counter=counter+1 where ip=".db_squote($ip));
		msg(array("type" => "error", "text" => $lang['msge_ip'], "info" => sprintf($lang['msgi_ip'], $ban_row['descr'])));
		return;
	}
	*/

	// Locate news
	if ($news_row = $mysql->record("select * from ".prefix."_news where id = ".db_squote($params['newsid']))) {
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
		if (is_array($lpost = $mysql->record("select author_id, author, ip, mail from ".prefix."_comments where post=".db_squote($params['newsid'])." order by id desc limit 1"))) {
			// Check for post from the same user
			if (is_array($userROW)) {
				 if ($userROW['id'] == $lpost['author_id']) {
					msg(array("type" => "error", "text" => $lang['comments:err.multilock']));
					return;
				}
			} else {
				//print "Last post: ".$lpost['id']."<br>\n";
				if (($lpost['author'] == $params['user_name'])||($lpost['mail'] == $params['mail'])) {
					msg(array("type" => "error", "text" => $lang['comments:err.multilock']));
					return;
				}
			}
		}
	}

	/*
	*** TEMPORALLY DISABLE INTERCEPTORS ***
	//
	// Run interceptors
	//
	exec_acts('addcomment','', &$params);

	// Break if interceptor blocks comment adding
	if ($params['stop'])
		return;

	*/

	$time = time() + ($config['date_adjust'] * 60);

	if (extra_get_param('comments', 'maxwlen') > 1){
		$params['content'] = preg_replace('/(\S{'.intval(extra_get_param('comments', 'maxwlen')).'})(?!\s)/', '$1 ', $params['content']);

		if (strlen($params['user_name']) > extra_get_param('comments', 'maxwlen')) {
			$params['user_name'] = substr($params['user_name'], 0, extra_get_param('comments', 'maxwlen'))." ...";
		}
	}
	$params['content'] = str_replace("\r\n", "<br />", $params['content']);

	// Create comment
	$mysql->query("insert into ".prefix."_comments (`postdate`, `post`, `author`, `author_id`, `mail`, `text`, `ip`, `reg`) VALUES (".db_squote($time).", ".db_squote($params['newsid']).", ".db_squote($params['user_name']).", ".db_squote($params['author_id']).", ".db_squote($params['mail']).", ".db_squote($params['content']).", ".db_squote($ip).", '".(($is_member) ? '1' : '0')."')");

	// Retrieve comment ID
	$comment_id = $mysql->result("select LAST_INSERT_ID() as id");

	// Update comment counter in news
	$mysql->query("update ".prefix."_news set com=com+1 where id=".db_squote($params['newsid']));
	$comment_no = $new_row['com']+1;

	// Update counter for user
	if ($params['author_id']) {
		$mysql->query("update ".prefix."_users set com=com+1 where id = ".db_squote($params['author_id']));
	}

	// Update flood protect database
	if ($mysql->rows($mysql->query("SELECT id FROM ".prefix."_flood WHERE ip = ".db_squote($ip))) > "0") {
		$mysql->query("UPDATE ".prefix."_flood SET id=".db_squote($time)." WHERE ip = ".db_squote($ip));
	} else {
		$mysql->query("INSERT INTO ".prefix."_flood (`ip`, `id`) VALUES (".db_squote($ip).", ".db_squote($time).")");
	}

	// Email informer
	if (extra_get_param('comments', 'inform_author') || extra_get_param('comments', 'inform_admin')) {
		$body = str_replace(
			array(	'{username}',
					'[userlink]',
					'[/userlink]',
					'{comment}',
					'{newslink}',
					'{newstitle}'),
			array(	$params['user_name'],
					($params['author_id'])?'<a href="'.GetLink('user', array('author' => $params['user_name'])).'">':'',
					($params['author_id'])?'</a>':'',
					$parse->bbcodes($parse->smilies(secure_html($params['content']))),
					GetLink('full', $news_row),
					$news_row['title'],
					),
			$lang['notice']
		);

		zzMail($config['admin_mail'], $lang['newcomment'], $body, 'html');
	}

	@setcookie("com_username", urlencode($params['user_name']), 0, '/');
	@setcookie("com_usermail", urlencode($params['mail']), 0, '/');

	return array($news_row, $comment_id);
}

/*
	// Check if we need to override news template
	$callingCommentsParams = array();

	// Set default template path
	$templatePath = tpl_dir.$config['theme'];

	// Find first category
	$fcat = array_shift(explode(",", $news_row['catid']));
	// Check if there is a custom mapping
	if ($fcat && $catmap[$fcat] && ($ctname = $catz[$catmap[$fcat]]['tpl'])) {
		// Check if directory exists
		if (is_dir($templatePath.'/ncustom/'.$ctname))
			$callingCommentsParams['overrideTemplatePath'] = $templatePath.'/ncustom/'.$ctname;
	}

	include root.'/includes/comments.show.php';
	comments_show($params['newsid'], $comment_id, $news_row['com']+1, $callingCommentsParams);
	return 1;
}

if (!comments_add()) {
		$tpl -> template('error', tpl_site);
		$tpl -> vars('error', array( 'vars' => array('content' => $template['vars']['mainblock'])));
		echo $tpl -> show('error');
}

*/
