<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

LoadPluginLang('uprofile', 'main', '', '', ':');
register_plugin_page('uprofile','edit','uprofile_editProfile',0);
register_plugin_page('uprofile','apply','uprofile_applyProfile',0);
register_plugin_page('uprofile','show','uprofile_showProfile',0);



// =============================================================
// External functions of plugin
// =============================================================
function uprofile_list() {
}


function uprofile_showProfile($params) {
	global $mysql, $lang, $tpl, $template, $SYSTEM_FLAGS;

	$SYSTEM_FLAGS['info']['title']['group']		= $lang['uprofile:header.view'];
	//LoadPluginLang('uprofile', 'users', '', '', ':');

	// Check if valid user identity is specified
	$urow = '';
	if (isset($params['id']) && (intval($params['id']) > 0)) {
		$urow = $mysql->record("select * from ".uprefix."_users where id = ".intval($params['id']));
	} else if (isset($params['name'])) {
		$urow = $mysql->record("select * from ".uprefix."_users where name = ".db_squote($params['name']));
	} else if (isset($_REQUEST['id'])) {
		$urow = $mysql->record("select * from ".uprefix."_users where id = ".intval($_REQUEST['id']));
	} else if (isset($_REQUEST['name'])) {
		$urow = $mysql->record("select * from ".uprefix."_users where name = ".db_squote($_REQUEST['name']));
	}
	if (!is_array($urow)) {
		msg(array("type" => "error", "text" => $lang['uprofile:msge_no_user']));
		return;
	}

	// Determine paths for all template files
	$tpath = locatePluginTemplates(array('users'), 'uprofile', extra_get_param('uprofile', 'localsource'));

	// Make page title
	$SYSTEM_FLAGS['info']['title']['group']	= $lang['loc_userinfo'];
	$SYSTEM_FLAGS['info']['title']['item']	= $urow['name'];

	$status = (($urow['status'] >= 1)&&($urow['status'] <= 4))?$lang['uprofile:st_'.$urow['status']]:$lang['uprofile:st_unknown'];

	// Check for new style of photos storing
	if (preg_match('/^'.$urow['id'].'\./', $urow['photo'])) {
		$uphoto = $urow['photo'];
	} else {
		$uphoto = $urow['id'].'.'.$urow['photo'];
	}

	// Check for new style of avatars storing
	if (preg_match('/^'.$urow['id'].'\./', $urow['avatar'])) {
		$uavatar = $urow['avatar'];
	} else {
		$uavatar = $urow['id'].'.'.$urow['avatar'];
	}

	$photo	= photos_url.'/'.(($urow['photo'] != "")?'thumb/'.$uphoto:'nophoto.gif');

	// GRAVATAR.COM integration ** BEGIN **
	if ($urow['avatar'] != '') {
		$avatar	= avatars_url.'/'.$uavatar;
	} else {
		if ($config['avatars_gravatar']) {
			$avatar	= 'http://www.gravatar.com/avatar/'.md5(strtolower($userROW['mail'])).'.jpg?s='.$config['avatar_wh'].'&d='.urlencode(avatars_url."/noavatar.gif");
		} else {
			$avatar = avatars_url."/noavatar.gif";
		}
	}
	// GRAVATAR.COM integration ** END **


	$tpl -> template('users', $tpath['users']);
	$tvars['vars'] = array(
		'user'		=>	$urow['name'],
		'news'		=>	$urow['news'],
		'com'		=>	$urow['com'],
		'status'	=>	$status,
		'last'		=>	langdate("j Q Y", $urow['last']),
		'reg'		=>	langdate("j Q Y", $urow['reg']),
		'site'		=>	secure_html($urow['site']),
		'icq'		=>	is_numeric($urow['icq']) ? '<a target="_blank" href="http://www.icq.com/people/about_me.php?uin='.$urow['icq'].'">'.$urow['icq'].'</a>' : secure_html($urow['icq']),
		'icqimg'	=>	is_numeric($urow['icq']) ? '<img src="http://status.icq.com/online.gif?icq='.$urow['icq'].'&img=1" />' : '',
		'from'		=>	secure_html($urow['where_from']),
		'info'		=>	secure_html($urow['info']),
		'photo'		=>	$photo,
		'photo_link'=>	($urow['photo'] != "") ? photos_url.'/'.$uphoto:'',
		'avatar'	=>	$avatar
	);

	$tpl -> vars('users', $tvars);
	$template['vars']['mainblock'] .= $tpl -> show('users');
}

function uprofile_editProfile(){

	// Call editForm routine
	uprofile_editForm();

}

function uprofile_applyProfile() {
	global $template, $userROW, $lang;

	// Check if user is logged in
	if (!is_array($userROW)) {
		msg(array("type" => "error", "text" => $lang['uprofile:msge_notlogged']));
		return;
	}

	// Call Apply changes routine
	uprofile_editApply();

	// Redirect back if we do not have any messages
	if (!$template['vars']['mainblock']) {
		@header("Location: ".generateLink('uprofile', 'edit', array()));
	} else {
		// We have some messages. Don't affect it, print editForm.
		uprofile_editForm();
	}
}



// =============================================================
// Internal functions of plugin
// =============================================================


// Show profile for specified user
function profile_show() {
	global $mysql;
}


// Show EDIT FORM for current user's profile
function uprofile_editForm(){
	global $mysql, $userROW, $lang, $config, $tpl, $template, $SYSTEM_FLAGS;

	$SYSTEM_FLAGS['info']['title']['group']		= $lang['uprofile:header.edit'];

	// Check if user is logged in
	if (!is_array($userROW)) {
		msg(array("type" => "error", "text" => $lang['uprofile:msge_notlogged']));
		return;
	}

	//
	// Show profile

	// Determine paths for all template files
	$tpath = locatePluginTemplates(array('profile'), 'uprofile', extra_get_param('uprofile', 'localsource'));

	// If AVATARs are enabled
	if ($config['use_avatars']) {
		if ($userROW['avatar'] !== "") {
			// Check for new style of avatar storing
			if (preg_match('/^'.$userROW['id'].'\./', $userROW['avatar'])) {
				$avatar = $userROW['avatar'];
			} else {
				$avatar = $userROW['id'].'.'.$userROW['avatar'];
			}

			$imgavatar = '<img src="'.avatars_url.'/'.$avatar.'" style="margin: 5px; border: 0px;" alt="" />';
			$delavatar = '<input type="checkbox" name="delavatar" id="delavatar" class="check" />&nbsp;<label for="delavatar">'.$lang["uprofile:delete"].'</label>';
		}
		$showrow_avatar = '<input type="file" name="newavatar" size="40" /><br />'.$imgavatar.'<br />'.$delavatar;
	} else {
		$showrow_avatar = $lang['uprofile:avatars_denied'];
	}

	// If PHOTOS are enabled
	if ($config['use_photos']) {
		if ($userROW['photo'] !== "") {
			// Check for new style of avatar storing
			if (preg_match('/^'.$userROW['id'].'\./', $userROW['photo'])) {
				$photo = $userROW['photo'];
			} else {
				$photo = $userROW['id'].'.'.$userROW['photo'];
			}
			$imgphoto = '<a href="'.photos_url.'/'.$photo.'" target="_blank"><img src="'.photos_url.'/thumb/'.$photo.'" style="margin: 5px; border: 0px;" alt="" /></a>';
			$delphoto = '<input type="checkbox" name="delphoto" id="delphoto" class="check" />&nbsp;<label for="delphoto">'.$lang["uprofile:delete"].'</label>';
		}
		$showrow_photo = '<input type="file" name="newphoto" size="40" /><br />'.$imgphoto.'<br />'.$delphoto;
	} else {
		$showrow_photo = $lang['uprofile:photos_denied'];
	}

	$status = (($userROW['status'] >= 1)&&($userROW['status'] <= 4))?$lang['uprofile:st_'.$userROW['status']]:$lang['uprofile:st_unknown'];

	$tvars['vars'] = array(
		'php_self'	=>	$PHP_SELF,
		'name'		=>	$userROW['name'],
		'regdate'	=>	LangDate("l, j Q Y - H:i", $userROW['reg']),
		'last'		=>	(empty($userROW['last'])) ? $lang['no_last'] : LangDate("l, j Q Y - H:i", $userROW['last']),
		'status'	=>	$status,
		'news'		=>	$userROW['news'],
		'comments'	=>	$userROW['com'],
		'email'		=>	secure_html($userROW['mail']),
		'ifchecked'	=>	$ifchecked,
		'site'		=>	secure_html($userROW['site']),
		'icq'		=>	secure_html($userROW['icq']),
		'from'		=>	secure_html($userROW['where_from']),
		'about'		=>	secure_html($userROW['info']),
		'about_sizelimit_text'	=> str_replace('{limit}', intval($config['user_aboutsize']), $lang['uprofile:about_sizelimit']),
		'about_sizelimit'	=> intval($config['user_aboutsize']),
		'avatar'	=>	$showrow_avatar,
		'photo'		=>	$showrow_photo,
		'form_action'	=>	generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'apply')),
	);

	$tpl -> template('profile', $tpath['profile']);
	$tpl -> vars('profile', $tvars);
	$template['vars']['mainblock'] .= $tpl -> show('profile');
}


function uprofile_editApply(){
	global $mysql, $tpl, $lang, $template, $userROW, $auth_db, $config;

	// Load required library
	@include_once root.'includes/classes/upload.class.php';

	// Check if user is logged in
	if (!is_array($userROW)) {
		msg(array("type" => "error", "text" => $lang['uprofile:msge_notlogged']));
		return;
	}

	// Delete avatar if requested
	if ($_REQUEST['delavatar']) {
		uprofile_manageDelete('avatar', $userROW['id']);
	} else {
		$avatar = $userROW['avatar'];
	}

	// Delete photo if requested
	if ($_REQUEST['delphoto']) {
		uprofile_manageDelete('photo', $userROW['id']);
	} else {
		$photo = $userROW['photo'];
	}

	// UPLOAD AVATAR
	if ($_FILES['newavatar']['name']) {

		// Delete an avatar if user already has it
		uprofile_manageDelete('avatar', $userROW['id']);

		$fmanage = new file_managment();
		$imanage = new image_managment();
		$up = $fmanage->file_upload(array('type' => 'avatar', 'http_var' => 'newavatar', 'replace' => 1, 'manualfile' => $userROW['id'].'.'.strtolower($_FILES['newavatar']['name'])));

		if (is_array($up)) {
			// Now fetch information about size and prepare to write info into DB
			if (is_array($sz = $imanage->get_size($config['avatars_dir'].$up[1]))) {
				$fmanage->get_limits('avatar');

				// Check avatar size limit (!!!)
				$lwh = intval($config['avatar_wh']);
				if ($lwh && (($sz[1] > $lwh)||($sz[2] > $lwh))) {
					// Fatal: uploaded avatar mismatch size limits !
					msg(array("type" => "error", "text" => $lang['uprofile:msge_size'], "info" => sprintf($lang['uprofile:msgi_size'], $lwh.'x'.$lwh)));
					$fmanage->file_delete(array('type' => 'avatar', 'id' => $up[0]));
				} else {
					$mysql->query("update ".prefix."_".$fmanage->tname." set width=".db_squote($sz[1]).", height=".db_squote($sz[2])." where id = ".db_squote($up[0]));
					$avatar = $up[1];
				}
			} else {
				// We were unable to fetch image size. Damaged file, delete it!
				msg(array("type" => "error", "text" => $lang['uprofile:msge_damaged']));
				$fmanage->file_delete(array('type' => 'avatar', 'id' => $up[0]));
			}
		}
	}

	// UPLOAD PHOTO
	if ($_FILES['newphoto']['name']) {

		// Delete a photo if user already has it
		uprofile_manageDelete('photo', $userROW['id']);

		$fmanage = new file_managment();
		$imanage = new image_managment();
		$up = $fmanage->file_upload(array('type' => 'photo', 'http_var' => 'newphoto', 'replace' => 1, 'manualfile' => $userROW['id'].'.'.strtolower($_FILES['newphoto']['name'])));
		if (is_array($up)) {
			// Now write info about image into DB
			if (is_array($sz = $imanage->get_size($config['photos_dir'].$subdirectory.'/'.$up[1]))) {
				$fmanage->get_limits('photo');

				// Create preview for photo
				$tsz = intval($config['photos_thumb_size']);
				if (($tsz < 10)||($tsz > 1000)) $tsz = 150;
				$thumb = $imanage->create_thumb($config['photos_dir'].$subdirectory, $up[1], $tsz,$tsz);

				// If we were unable to create thumb - delete photo, it's damaged!
				if (!$thumb) {
					msg(array("type" => "error", "text" => $lang['uprofile:msge_damaged']));
					$fmanage->file_delete(array('type' => 'avatar', 'id' => $up[0]));
				} else {
					$mysql->query("update ".prefix."_".$fmanage->tname." set width=".db_squote($sz[1]).", height=".db_squote($sz[2]).", preview=1 where id = ".db_squote($up[0]));
					$photo = $up[1];
				}
			} else {
				// We were unable to fetch image size. Damaged file, delete it!
				msg(array("type" => "error", "text" => $lang['uprofile:msge_damaged']));
				$fmanage->file_delete(array('type' => 'avatar', 'id' => $up[0]));
			}
		}
	}

	$sqlFields = array ( 'avatar' => $avatar, 'photo' => $photo, 'mail' => $_REQUEST['editmail'], 'site' => $_REQUEST['editsite'], 'icq' => is_numeric($_REQUEST['editicq'])?$_REQUEST['editicq']:'', 'where_from' => $_REQUEST['editfrom'], 'info' => (intval($config['user_aboutsize'])?substr($_REQUEST['editabout'],0,$config['user_aboutsize']):$_REQUEST['editabout']));
	if ($_REQUEST['editpassword'] != '') {
		if (method_exists($auth_db, 'save_profile')) {
			$auth_db->save_profile($userROW['id'], array('password' => $_REQUEST['editpassword']));
		}
		$sqlFields['pass'] = EncodePassword($_REQUEST['editpassword']);
	}


	// Prepare SQL line
	$sqlF = array();
	foreach ($sqlFields as $f => $v)
		array_push($sqlF, $f . " = " . db_squote($v));

	$sqlUpdate = "update ".uprefix."_users set ".join(", ",$sqlF)." where id = ".db_squote($userROW['id']);
	$mysql->query($sqlUpdate);

	return true;
}



function uprofile_manageDelete($type, $userID){
	global $mysql, $userROW;

	// Load required library
	@include_once root.'includes/classes/upload.class.php';

	$localUpdate = 0;
	$userID = intval($userID);

	if ($userID != $userROW['id']) {
		if (!is_array($uRow = $mysql->record("select * from ".uprefix."_users where id = ".$userID)))
		 return;
	} else {
		$uRow = $userROW;
		$localUpdate = 1;
	}

	// Search for avatar record in mySQL table
	if (is_array($imageRow = $mysql->record("select * from ".prefix."_images where owner_id = ".$userID." and category = ".($type=='avatar'?1:2)))) {
		// Info was found in SQL table
		$fmanager = new file_managment();
		$fmanager->file_delete(array('type' => $type, 'id' => $imageRow['id']));
		//unlink(avatars_dir.$imageRow['name']);
	} else if ($uRow[$type]) {
		// Try to delete all avatars of this user
		@unlink($avatar_dir.$uRow['id'].'.*');
	}
	$mysql->query("update ".uprefix."_users set ".($type=='photo'?'photo':'avatar')." = '' where id = ".$userID);
	if ($localUpdate) $userROW[$type] = '';
}