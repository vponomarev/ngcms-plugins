<?php
/*
 * auth_loginza for NextGeneration CMS (http://ngcms.ru/)
 * Copyright (C) 2011 Alexey N. Zhukov (http://digitalplace.ru)
 * http://digitalplace.ru
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */
# protect against hack attempts
if (!defined('NGCMS')) die ('Galaxy in danger');
# preload required libraries
loadPluginLibrary('uprofile', 'lib');
loadPluginLibrary('comments', 'lib');
register_plugin_page('auth_loginza', '', 'loginzaAuth', 0);
register_plugin_page('auth_loginza', 'register', 'loginzaRegister', 0);
register_plugin_page('auth_loginza', 'delete', 'loginzaDelete', 0);
# get token from POST loginza responce and request JSON auth result
function loginzaAuth() {

	global $config, $template, $tpl, $mysql, $userROW, $AUTH_METHOD;
	if (empty($_POST['token'])) header('Location: ' . $config['home_url']);
	$url = 'http://loginza.ru/api/authinfo?token=' . $_POST['token'];
	# determine paths for all template files
	$tpath = LocatePluginTemplates(array('register', 'append.account.success', 'append.account.error'), 'auth_loginza', pluginGetVariable('auth_loginza', 'localsource'));
	if (function_exists('curl_init')) {
		$curl = curl_init($url);
		$user_agent = 'NextGeneration CMS. Plugin auth_loginza/ PHP ' . phpversion();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		$raw_data = curl_exec($curl);
		curl_close($curl);
		$responce = $raw_data;
	} else {
		$responce = file_get_contents($url);
	}
	$responce_array = arrayCharsetConvert(1, json_decode($responce, true));
	# if loginza returned some error
	if ($responce_array['error_type']) {
		msg(array("type" => "error", "text" => $responce_array['error_type'] . ' ' . $responce_array['error_message']));

		return 1;
	}
	$user = $mysql->record("SELECT * FROM " . uprefix . "_users WHERE loginza_id = " . db_squote($responce_array['identity']));
	# if user is authorized then append loginza account on his profile
	if (is_array($userROW)) {
		if (!is_array($user)) {
			$mysql->query("UPDATE `" . uprefix . "_users` SET `loginza_id` = " . db_squote($responce_array['identity']) . " WHERE id = " . db_squote($userROW['id']));
			$tpl->template('append.account.success', $tpath['append.account.success']);
			$tpl->vars('append.account.success', array('vars' => array('account' => $responce_array['identity'])));
			$template['vars']['mainblock'] = $tpl->show('append.account.success');

			return;
		} else {
			$tpl->template('append.account.error', $tpath['append.account.error']);
			$tpl->vars('append.account.error', array('vars' => array('account' => $responce_array['identity'])));
			$template['vars']['mainblock'] = $tpl->show('append.account.error');

			return;
		}
	}
	# if user is registered yet then authorize his
	if (is_array($user)) {
		$auth = $AUTH_METHOD[$config['auth_module']];
		$auth->save_auth($user);
		header('Location: ' . $config['home_url']);

		return;
	}
	# use this variable later in loginzaRegister()
	session_start();
	$_SESSION['loginza_id'] = $responce_array['identity'];
	$tvars['vars'] = array(
		'login'    => genNickname($responce_array),
		'password' => MakeRandomPassword(),
		'email'    => $responce_array['email']
	);
	# show register form
	$tpl->template('register', $tpath['register']);
	$tpl->vars('register', $tvars);
	$template['vars']['mainblock'] = $tpl->show('register');
}

# after confirm user data we register his
function loginzaRegister() {

	global $config, $template, $tpl, $mysql, $AUTH_METHOD;
	session_start();
	if (empty($_SESSION['loginza_id'])) header('Location: ' . $config['home_url'] . '');
	$auth = $AUTH_METHOD[$config['auth_module']];
	$params = array();
	array_push($params, array('name' => 'login', title => $lang['auth_login'], 'descr' => $lang['auth_login_descr'], 'type' => 'input'));
	if ($config['register_type'] >= 3) {
		array_push($params, array('name' => 'password', title => $lang['auth_pass'], 'descr' => $lang['auth_pass_descr'], 'type' => 'password'));
		array_push($params, array('name' => 'password2', title => $lang['auth_pass2'], 'descr' => $lang['auth_pass2_descr'], 'type' => 'password'));
	}
	array_push($params, array('name' => 'email', title => $lang['auth_email'], 'descr' => $lang['auth_email_descr'], 'type' => 'input'));
	$values = array();
	$values['login'] = $_POST['login'];
	if ($config['register_type'] >= 3) {
		$values['password'] = $_POST['password'];
		$values['password2'] = $_POST['password'];
	}
	$values['email'] = $_POST['email'] ? $_POST['email'] : 'noreply@digitalplace.ru';
	$tpath = LocatePluginTemplates(array('register.error', 'register.success'), 'auth_loginza', pluginGetVariable('auth_loginza', 'localsource'));
	# register, activate and authorize user
	if ($auth->register($params, $values, $msg)) {
		$mysql->query("UPDATE `" . uprefix . "_users` SET `activation` = '', `mail` = '', `loginza_id` = " . db_squote($_SESSION['loginza_id']) . " WHERE name = " . db_squote($values['login']));
		$user = $mysql->record("SELECT * FROM " . uprefix . "_users WHERE name = " . db_squote($values['login']));
		$auth->save_auth($user);
		$tpl->template('register.success', $tpath['register.success']);
		$tpl->vars('register.success', array('vars' => (array('username' => $values['login'], 'password' => $values['password']))));
		$template['vars']['mainblock'] = $tpl->show('register.success');
	} else {
		$tpl->template('register.error', $tpath['register.error']);
		$tpl->vars('register.error', array('vars' => array('error.msg' => $msg)));
		$template['vars']['mainblock'] = $tpl->show('register.error');
	}
	unset($_SESSION['loginza_id']);
}

function loginzaDelete() {

	global $userROW, $mysql, $config, $tpl, $template;
	if (!is_array($userROW) || !$userROW['loginza_id']) header('Location: ' . $config['home_url'] . '');
	$mysql->query("UPDATE `" . uprefix . "_users` SET `loginza_id` = '' WHERE id = " . db_squote($userROW['id']));
	$tpath = LocatePluginTemplates(array('account.delete'), 'auth_loginza', pluginGetVariable('auth_loginza', 'localsource'));
	$tpl->template('account.delete', $tpath['account.delete']);
	$tpl->vars('account.delete', array('vars' => array()));
	$template['vars']['mainblock'] = $tpl->show('account.delete');
}

# parse identity and show provider icon in comments
class loginzaFilterComments extends FilterComments {

	# request field 'loginza_id' from table _users for use it $commRec['users_loginza_id'] in showComments
	function commentsJoinFilter() {

		return array('users' => array('fields' => array('loginza_id')));
	}

	function showComments($newsID, $commRec, $comnum, &$tvars) {

		global $TemplateCache;
		if (empty($commRec['users_loginza_id'])) {
			$tvars['vars']['loginza_icon'] = '';

			return;
		}
		# format: facebook.com	= facebook.png
		$tpath = LocatePluginTemplates(array(':providers.ini'), 'auth_loginza', pluginGetVariable('auth_loginza', 'localsource'));
		if (!isset($TemplateCache['plugin']['auth_loginza']['#providers']))
			$TemplateCache['plugin']['auth_loginza'] = parse_ini_file($tpath[':providers.ini'] . 'providers.ini', true);
		if (preg_match('/^https?:\/\/([^\.]+\.)?([a-z0-9\-\.]+\.[a-z]{2,5})/i', $commRec['users_loginza_id'], $matches)) {
			$icon_dir = $tpath['url::providers.ini'] . '/img/';
			$provider_key = $matches[2];
			if (array_key_exists($provider_key, $TemplateCache['plugin']['auth_loginza']['providers'])) {
				$tvars['vars']['loginza_icon'] = str_replace(array('{icon_dir}', '{provider_img}'), array($icon_dir, $TemplateCache['plugin']['auth_loginza']['providers'][$provider_key]), $TemplateCache['plugin']['auth_loginza']['icon']['iffound']);

				return 0;
			}
		}
		$tvars['vars']['loginza_icon'] = str_replace('{icon_dir}', $icon_dir, $TemplateCache['plugin']['auth_loginza']['icon']['default']);
	}
}

register_filter('comments', 'auth_loginza', new loginzaFilterComments);

# show loginza identity in user's profile
class uLoginzaFilter extends p_uprofileFilter {

	function showProfile($userID, $SQLrow, &$tvars) {

		if (empty($SQLrow['loginza_id'])) {
			$tvars['regx']['/\[if-loginza\](.*?)\[\/if-loginza\]/si'] = '';
			$tvars['vars']['loginza_account'] = '';
		} else {
			$tvars['regx']['/\[if-loginza\](.*?)\[\/if-loginza\]/si'] = '$1';
			$tvars['vars']['loginza_account'] = $SQLrow['loginza_id'];
		}
	}

	function editProfileForm($userID, $SQLrow, &$tvars) {

		if (empty($SQLrow['loginza_id'])) {
			$tvars['regx']['/\[if-loginza\](.*?)\[\/if-loginza\]/si'] = '';
			$tvars['regx']['/\[if-not-loginza\](.*?)\[\/if-not-loginza\]/si'] = '$1';
			$tvars['vars']['loginza_account'] = '';
		} else {
			$tvars['regx']['/\[if-loginza\](.*?)\[\/if-loginza\]/si'] = '$1';
			$tvars['regx']['/\[if-not-loginza\](.*?)\[\/if-not-loginza\]/si'] = '';
			$tvars['vars']['loginza_account'] = $SQLrow['loginza_id'];
		}
	}
}

register_filter('plugin.uprofile', 'auth_loginza', new uLoginzaFilter);
function genNickname($responce) {

	if ($responce['nickname']) {
		return $responce['nickname'];
	} elseif (!empty($responce['email']) && preg_match('/^(.+)\@/i', $responce['email'], $nickname)) {
		return $nickname[1];
	} elseif (($fullname = genFullName($responce))) {
		return $fullname;
	}
	$patterns = array(
		'([^\.]+)\.ya\.ru',
		'openid\.mail\.ru\/[^\/]+\/([^\/?]+)',
		'openid\.yandex\.ru\/([^\/?]+)',
		'([^\.]+)\.myopenid\.com'
	);
	foreach ($patterns as $pattern) {
		if (preg_match('/^https?\:\/\/' . $pattern . '/i', $responce['identity'], $result)) {
			return $result[1];
		}
	}

	return false;
}

function genFullName(&$responce) {

	if ($responce['name']['full_name']) {
		return $responce['name']['full_name'];
	} elseif ($responce['name']['first_name'] || $responce['name']['last_name']) {
		return trim($responce['name']['first_name'] . ' ' . $responce['name']['last_name']);
	}

	return false;
}