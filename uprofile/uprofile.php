<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//LoadPluginLang('uprofile', 'main', '', '', ':');
LoadPluginLang('uprofile', 'main', '', 'uprofile', '#');
register_plugin_page('uprofile', 'edit', 'uprofile_editProfile', 0);
register_plugin_page('uprofile', 'apply', 'uprofile_applyProfile', 0);
register_plugin_page('uprofile', 'show', 'uprofile_showProfile', 0);
LoadPluginLibrary('uprofile', 'lib');
// =============================================================
// External functions of plugin
// =============================================================
function uprofile_showProfile($params) {

	global $mysql, $userROW, $config, $lang, $twig, $twigLoader, $template, $SYSTEM_FLAGS, $PFILTERS;
	//LoadPluginLang('uprofile', 'users', '', '', ':');
	// Check if valid user identity is specified
	$urow = '';
	if (isset($params['id']) && (intval($params['id']) > 0)) {
		$urow = $mysql->record("select * from " . uprefix . "_users where id = " . intval($params['id']));
	} else if (isset($params['name'])) {
		$urow = $mysql->record("select * from " . uprefix . "_users where name = " . db_squote($params['name']));
	} else if (isset($_REQUEST['id'])) {
		$urow = $mysql->record("select * from " . uprefix . "_users where id = " . intval($_REQUEST['id']));
	} else if (isset($_REQUEST['name'])) {
		$urow = $mysql->record("select * from " . uprefix . "_users where name = " . db_squote($_REQUEST['name']));
	}
	if (!is_array($urow)) {
		error404();

		return;
	}
	if (is_array($PFILTERS['plugin.uprofile']))
		foreach ($PFILTERS['plugin.uprofile'] as $k => $v) {
			$v->showProfilePre($urow['id'], $urow);
		}
	// Determine paths for all template files
	$tpath = locatePluginTemplates(array('users'), 'uprofile', pluginGetVariable('uprofile', 'localsource'));
	// Make page title
	$SYSTEM_FLAGS['info']['title']['group'] = $lang['uprofile']['header.view'];
	$SYSTEM_FLAGS['info']['title']['item'] = $urow['name'];
	$status = (($urow['status'] >= 1) && ($urow['status'] <= 4)) ? $lang['uprofile']['st_' . $urow['status']] : $lang['uprofile:st_unknown'];
	// Get user's photo and avatar
	$userPhoto = userGetPhoto($urow);
	$userAvatar = userGetAvatar($urow);
	$tVars = array(
		'userRec' => $urow,
		'user'    => array(
			'id'          => $urow['id'],
			'name'        => $urow['name'],
			'news'        => $urow['news'],
			'com'         => $urow['com'],
			'status'      => $status,
			'last'        => ($urow['last'] > 0) ? LangDate("l, j Q Y - H:i", $urow['last']) : $lang['no_last'],
			'reg'         => langdate("j Q Y", $urow['reg']),
			'site'        => secure_html($urow['site']),
			'icq'         => secure_html($urow['icq']),
			'from'        => secure_html($urow['where_from']),
			'info'        => secure_html($urow['info']),
			'photo_thumb' => $userPhoto[1],
			'photo'       => $userPhoto[2],
			'avatar'      => $userAvatar[1],
			'flags'       => array(
				'hasPhoto'     => $config['use_photos'] && $userPhoto[0],
				'hasAvatar'    => $config['use_avatars'] && $userAvatar[0],
				'hasIcq'       => is_numeric($urow['icq']) ? 1 : 0,
				'isOwnProfile' => (isset($userROW) && is_array($userROW) && ($userROW['id'] == $urow['id'])) ? 1 : 0,
			),
		),
		'token'   => genUToken('uprofile.editForm'),
	);
	$conversionConfig = array(
		'{user}'       => '{{ user.name }}',
		'{news}'       => '{{ user.news }}',
		'{com}'        => '{{ user.com }}',
		'{status}'     => '{{ user.status }}',
		'{last}'       => '{{ user.last }}',
		'{reg}'        => '{{ user.reg }}',
		'{site}'       => '{{ user.site }}',
		'{icq}'        => '{{ user.icq }}',
		'{from}'       => '{{ user.from }}',
		'{info}'       => '{{ user.info }}',
		'{photo}'      => '{{ user.photo_thumb }}',
		'{photo_link}' => '{{ user.photo }}',
		'{avatar}'     => '{{ user.avatar }}',
		'{tpl_url}'    => '{{ tpl_url }}',
	);
	$conversionConfigRegex = array(
		'#{l_uprofile:(.+?)}#' => '{{ lang.uprofile[\'$1\'] }}',
	);
	if (is_array($PFILTERS['plugin.uprofile']))
		foreach ($PFILTERS['plugin.uprofile'] as $k => $v) {
			$v->showProfile($urow['id'], $urow, $tVars);
		}
	$twigLoader->setConversion($tpath['users'] . 'users.tpl', $conversionConfig, $conversionConfigRegex);
	$xt = $twig->loadTemplate($tpath['users'] . 'users.tpl');
	$template['vars']['mainblock'] .= $xt->render($tVars);
}

function uprofile_editProfile() {

	// Call editForm routine
	uprofile_editForm();
}

function uprofile_applyProfile() {

	global $template, $userROW, $lang;
	// Check if user is logged in
	if (!is_array($userROW)) {
		msg(array("type" => "error", "text" => $lang['uprofile']['msge_notlogged']));

		return;
	}
	// Call Apply changes routine
	uprofile_editApply();
	// Redirect back if we do not have any messages
	if (!$template['vars']['mainblock']) {
		@header("Location: " . generateLink('uprofile', 'edit', array(), array('editComplete' => 1)));
		exit;
	} else {
		// We have some messages. Don't affect it, print editForm.
		uprofile_editForm();
	}
}

// =============================================================
// Internal functions of plugin
// =============================================================
// Show EDIT FORM for current user's profile
function uprofile_editForm($ajaxMode = false) {

	global $mysql, $userROW, $lang, $config, $tpl, $template, $twig, $twigLoader, $SYSTEM_FLAGS, $PFILTERS, $DSlist;
	$SYSTEM_FLAGS['info']['title']['group'] = $lang['uprofile']['header.edit'];
	// Check if user is logged in
	if (!is_array($userROW)) {
		msg(array("type" => "error", "text" => $lang['uprofile']['msge_notlogged']));

		return;
	}
	// Notify about `EDIT COMPLETE` if editComplete parameter is passed
	if (isset($_GET['editComplete']) && $_GET['editComplete']) {
		msg(array("type" => "info", "info" => $lang['uprofile']['msgo_saved']));
	}
	//
	// Show profile
	// Save current user's parameters
	$urow = $userROW;
	// Load list of attached images/files
	//$currentUser['#files']	= $mysql->select("select *, date_format(from_unixtime(date), '%d.%m.%Y') as date from ".prefix."_files where (linked_ds = ".$DSlist['users'].") and (linked_id = ".db_squote($currentUser['id']).')', 1);
	$urow['#images'] = $mysql->select("select *, date_format(from_unixtime(date), '%d.%m.%Y') as date from " . prefix . "_images where (linked_ds = " . $DSlist['users'] . ") and (linked_id = " . db_squote($urow['id']) . ')', 1);
	// Manage profile data [if needed]
	if (is_array($PFILTERS['plugin.uprofile']))
		foreach ($PFILTERS['plugin.uprofile'] as $k => $v) {
			$v->editProfileFormPre($urow['id'], $urow);
		}
	// Determine paths for all template files
	$tpath = locatePluginTemplates(array('profile'), 'uprofile', pluginGetVariable('uprofile', 'localsource'));
	$status = ((($urow['status'] >= 1) && ($urow['status'] <= 4)) ? $lang['uprofile']['st_' . $urow['status']] : $lang['uprofile']['st_unknown']);
	// Get user's photo and avatar
	$userPhoto = userGetPhoto($urow);
	$userAvatar = userGetAvatar($urow);
	$tVars = array(
		'userRec'             => $urow,
		'user'                => array(
			'id'          => $urow['id'],
			'name'        => $urow['name'],
			'news'        => $urow['news'],
			'com'         => $urow['com'],
			'status'      => $status,
			'last'        => ($urow['last'] > 0) ? LangDate("l, j Q Y - H:i", $urow['last']) : $lang['no_last'],
			'reg'         => langdate("j Q Y", $urow['reg']),
			'email'       => secure_html($urow['mail']),
			'site'        => secure_html($urow['site']),
			'icq'         => secure_html($urow['icq']),
			'from'        => secure_html($urow['where_from']),
			'info'        => secure_html($urow['info']),
			'photo_thumb' => $userPhoto[1],
			'photo'       => $userPhoto[2],
			'avatar'      => $userAvatar[1],
			'php_self'    => $PHP_SELF,
			'flags'       => array(
				'hasPhoto'  => $config['use_photos'] && $userPhoto[0],
				'hasAvatar' => $config['use_avatars'] && $userAvatar[0],
				'hasIcq'    => is_numeric($urow['icq']) ? 1 : 0,
			),
		),
		'flags'               => array(
			'photoAllowed'  => $config['use_photos'] ? 1 : 0,
			'avatarAllowed' => $config['use_avatars'] ? 1 : 0,
		),
		'form_action'         => generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'apply')),
		'token'               => genUToken('uprofile.update'),
		'info_sizelimit_text' => str_replace('{limit}', intval($config['user_aboutsize']), $lang['uprofile']['about_sizelimit']),
		'info_sizelimit'      => intval($config['user_aboutsize']),
	);
	$conversionConfig = array(
		'{php_self}'             => '{{ php_self }}',
		'{name}'                 => '{{ user.name }}',
		'{regdate}'              => '{{ user.reg }}',
		'{last}'                 => '{{ user.last }}',
		'{status}'               => '{{ user.status }}',
		'{news}'                 => '{{ user.news }}',
		'{comments}'             => '{{ user.com }}',
		'{email}'                => '{{ user.email }}',
		'{from}'                 => '{{ user.from }}',
		'{site}'                 => '{{ user.site }}',
		'{icq}'                  => '{{ user.icq }}',
		'{about}'                => '{{ user.info }}',
		'{about_sizelimit_text}' => '{{ info_sizelimit_text }}',
		'{about_sizelimit}'      => '{{ info_sizelimit }}',
		'{photo}'                => '{% if (flags.photoAllowed) %}<input type="file" name="newphoto" size="40" /><br />{% if (user.flags.hasPhoto) %}<a href="{{ user.photo }}" target="_blank"><img src="{{ user.photo_thumb }}" style="margin: 5px; border: 0px; alt=""/></a><br/><input type="checkbox" name="delphoto" id="delphoto" class="check" />&nbsp;<label for="delphoto">{{ lang.uprofile[\'delete\'] }}</label>{% endif %}{% else %}{{ lang.uprofile[\'photos_denied\'] }}{% endif %}',
		'{avatar}'               => '{% if (flags.avatarAllowed) %}<input type="file" name="newavatar" size="40" /><br />{% if (user.flags.hasAvatar) %}<img src="{{ user.avatar }}" style="margin: 5px; border: 0px; alt=""/><br/><input type="checkbox" name="delavatar" id="delavatar" class="check" />&nbsp;<label for="delavatar">{{ lang.uprofile[\'delete\'] }}</label>{% endif %}{% else %}{{ lang.uprofile[\'avatars_denied\'] }}{% endif %}',
		'{form_action}'          => '{{ form_action }}',
		'{token}'                => '{{ token }}',
		'{tpl_url}'              => '{{ tpl_url }}',
	);
	$conversionConfigRegex = array(
		'#{l_uprofile:(.+?)}#'     => '{{ lang.uprofile[\'$1\'] }}',
		'#{plugin_xfields_(\d+)}#' => ' {{ p.xfields[$1] }}',
	);
	if (is_array($PFILTERS['plugin.uprofile']))
		foreach ($PFILTERS['plugin.uprofile'] as $k => $v) {
			$v->editProfileForm($urow['id'], $urow, $tVars);
		}
	$twigLoader->setConversion($tpath['profile'] . 'profile.tpl', $conversionConfig, $conversionConfigRegex);
	$xt = $twig->loadTemplate($tpath['profile'] . 'profile.tpl');
	$render = $xt->render($tVars);
	if ($ajaxMode)
		return $render;
	$template['vars']['mainblock'] .= $render;
}

function uprofile_editApply() {

	global $mysql, $tpl, $lang, $template, $userROW, $auth_db, $config, $PFILTERS, $DSlist;
	// Load required library
	@include_once root . 'includes/classes/upload.class.php';
	// Check if user is logged in
	if (!is_array($userROW)) {
		msg(array("type" => "error", "text" => $lang['uprofile']['msge_notlogged']));

		return;
	}
	$currentUser = $userROW;
	// Check if correct AUTH params are presented:
	// * for all activities [except PW change] - token or password
	// * for PW change - password
	// If we want to change password
	if ($_REQUEST['editpassword'] != '') {
		// Correct OLD password must be presented
		if (!isset($_POST['oldpass']) || (EncodePassword($_POST['oldpass']) != $currentUser['pass'])) {
			msg(array("type" => "error", "text" => $lang['uprofile']['msge_needoldpass']));

			return;
		}
	} else {
		// Token or correct OLD password must be presented
		if ((!isset($_POST['token']) || ($_POST['token'] != genUToken('uprofile.update'))) &&
			(!isset($_POST['oldpass']) || (EncodePassword($_POST['oldpass']) != $currentUser['pass']))
		) {
			msg(array("type" => "error", "text" => $lang['uprofile']['msge_needoldpass']));

			return;
		}
	}
	// Delete avatar if requested
	if ($_REQUEST['delavatar']) {
		uprofile_manageDelete('avatar', $currentUser['id']);
	} else {
		$avatar = $currentUser['avatar'];
	}
	// Delete photo if requested
	if ($_REQUEST['delphoto']) {
		uprofile_manageDelete('photo', $currentUser['id']);
	} else {
		$photo = $currentUser['photo'];
	}
	// UPLOAD AVATAR
	if ($_FILES['newavatar']['name']) {
		// Delete an avatar if user already has it
		uprofile_manageDelete('avatar', $currentUser['id']);
		$fmanage = new file_managment();
		$imanage = new image_managment();
		$up = $fmanage->file_upload(array('type' => 'avatar', 'http_var' => 'newavatar', 'replace' => 1, 'manualfile' => $currentUser['id'] . '.' . strtolower($_FILES['newavatar']['name'])));
		if (is_array($up)) {
			// Now fetch information about size and prepare to write info into DB
			if (is_array($sz = $imanage->get_size($config['avatars_dir'] . $up[1]))) {
				$fmanage->get_limits('avatar');
				// Check avatar size limit (!!!)
				$lwh = intval($config['avatar_wh']);
				if ($lwh && (($sz[1] > $lwh) || ($sz[2] > $lwh))) {
					// Fatal: uploaded avatar mismatch size limits !
					msg(array("type" => "error", "text" => $lang['uprofile']['msge_size'], "info" => sprintf($lang['uprofile']['msgi_size'], $lwh . 'x' . $lwh)));
					$fmanage->file_delete(array('type' => 'avatar', 'id' => $up[0]));
				} else {
					$mysql->query("update " . prefix . "_" . $fmanage->tname . " set width=" . db_squote($sz[1]) . ", height=" . db_squote($sz[2]) . " where id = " . db_squote($up[0]));
					$avatar = $up[1];
				}
			} else {
				// We were unable to fetch image size. Damaged file, delete it!
				msg(array("type" => "error", "text" => $lang['uprofile']['msge_damaged']));
				$fmanage->file_delete(array('type' => 'avatar', 'id' => $up[0]));
			}
		}
	}
	// UPLOAD PHOTO
	if ($_FILES['newphoto']['name']) {
		// Delete a photo if user already has it
		uprofile_manageDelete('photo', $currentUser['id']);
		$fmanage = new file_managment();
		$imanage = new image_managment();
		$up = $fmanage->file_upload(array('type' => 'photo', 'http_var' => 'newphoto', 'replace' => 1, 'manualfile' => $currentUser['id'] . '.' . strtolower($_FILES['newphoto']['name'])));
		if (is_array($up)) {
			// Now write info about image into DB
			if (is_array($sz = $imanage->get_size($config['photos_dir'] . $subdirectory . '/' . $up[1]))) {
				$fmanage->get_limits('photo');
				// Create preview for photo
				$tsx = intval($config['photos_thumb_size_x']) ? intval($config['photos_thumb_size_x']) : intval($config['photos_thumb_size']);
				$tsy = intval($config['photos_thumb_size_y']) ? intval($config['photos_thumb_size_y']) : intval($config['photos_thumb_size']);
				if (($tsx < 10) || ($tsx > 1000)) $tsx = 150;
				if (($tsy < 10) || ($tsy > 1000)) $tsy = 150;
				$thumb = $imanage->create_thumb($config['photos_dir'] . $subdirectory, $up[1], $tsx, $tsy);
				// If we were unable to create thumb - delete photo, it's damaged!
				if (!$thumb) {
					msg(array("type" => "error", "text" => $lang['uprofile']['msge_damaged']));
					$fmanage->file_delete(array('type' => 'avatar', 'id' => $up[0]));
				} else {
					$mysql->query("update " . prefix . "_" . $fmanage->tname . " set width=" . db_squote($sz[1]) . ", height=" . db_squote($sz[2]) . ", preview=1 where id = " . db_squote($up[0]));
					$photo = $up[1];
				}
			} else {
				// We were unable to fetch image size. Damaged file, delete it!
				msg(array("type" => "error", "text" => $lang['uprofile']['msge_damaged']));
				$fmanage->file_delete(array('type' => 'avatar', 'id' => $up[0]));
			}
		}
	}
	$sqlFields = array(
		'avatar'     => $avatar,
		'photo'      => $photo,
		'mail'       => $_REQUEST['editmail'],
		'site'       => $_REQUEST['editsite'],
		'icq'        => is_numeric($_REQUEST['editicq']) ? $_REQUEST['editicq'] : '',
		'where_from' => $_REQUEST['editfrom'],
		'info'       => (intval($config['user_aboutsize']) ? mb_substr($_REQUEST['editabout'], 0, $config['user_aboutsize']) : $_REQUEST['editabout'])
	);
	if ($_REQUEST['editpassword'] != '') {
		if (method_exists($auth_db, 'save_profile')) {
			$auth_db->save_profile($currentUser['id'], array('password' => $_REQUEST['editpassword']));
		}
		$sqlFields['pass'] = EncodePassword($_REQUEST['editpassword']);
	}
	// Get list of attached images
	$currentUser['#images'] = $mysql->select("select *, date_format(from_unixtime(date), '%d.%m.%Y') as date from " . prefix . "_images where (linked_ds = " . $DSlist['users'] . ") and (linked_id = " . db_squote($currentUser['id']) . ')', 1);
	// Call external plugins for request processing
	if (is_array($PFILTERS['plugin.uprofile']))
		foreach ($PFILTERS['plugin.uprofile'] as $k => $v) {
			$v->editProfile($currentUser['id'], $currentUser, $sqlFields);
		}
	// Prepare SQL line
	$sqlF = array();
	foreach ($sqlFields as $f => $v)
		array_push($sqlF, $f . " = " . db_squote($v));
	$sqlUpdate = "update " . uprefix . "_users set " . join(", ", $sqlF) . " where id = " . db_squote($currentUser['id']);
	$mysql->query($sqlUpdate);
	// Call external plugins for request processing
	if (is_array($PFILTERS['plugin.uprofile']))
		foreach ($PFILTERS['plugin.uprofile'] as $k => $v) {
			$v->editProfileNotify($currentUser['id'], $currentUser, $sqlFields);
		}

	return true;
}

function uprofile_manageDelete($type, $userID) {

	global $mysql, $userROW;
	// Load required library
	@include_once root . 'includes/classes/upload.class.php';
	$localUpdate = 0;
	$userID = intval($userID);
	if ($userID != $userROW['id']) {
		if (!is_array($uRow = $mysql->record("select * from " . uprefix . "_users where id = " . $userID)))
			return;
	} else {
		$uRow = $userROW;
		$localUpdate = 1;
	}
	// Search for avatar record in mySQL table
	if (is_array($imageRow = $mysql->record("select * from " . prefix . "_images where owner_id = " . $userID . " and category = " . ($type == 'avatar' ? 1 : 2)))) {
		// Info was found in SQL table
		$fmanager = new file_managment();
		$fmanager->file_delete(array('type' => $type, 'id' => $imageRow['id']));
		//unlink(avatars_dir.$imageRow['name']);
	} else if ($uRow[$type]) {
		// Try to delete all avatars of this user
		@unlink($avatar_dir . $uRow['id'] . '.*');
	}
	$mysql->query("update " . uprefix . "_users set " . ($type == 'photo' ? 'photo' : 'avatar') . " = '' where id = " . $userID);
	if ($localUpdate) $userROW[$type] = '';
}

function uprofile_rpc_manage($params) {

	$uprofileOutput = uprofile_editForm(true);

	return array('status' => 1, 'errorCode' => 0, 'data' => arrayCharsetConvert(0, $uprofileOutput));
}

rpcRegisterFunction('plugin.uprofile.editForm', 'uprofile_rpc_manage');
