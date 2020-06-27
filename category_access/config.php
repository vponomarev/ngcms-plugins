<?php
if (!defined('NGCMS')) exit('HAL');
plugins_load_config();
LoadPluginLang('category_access', 'config', '', '', ':');
switch ($_REQUEST['action']) {
	case 'list_user':
		show_list_user();
		break;
	case 'list_category':
		show_list_category();
		break;
	case 'add_user':
		add_user();
		break;
	case 'add_category':
		add_category();
		break;
	case 'move_up':
		move('up');
		showlist();
		break;
	case 'move_down':
		move('down');
		showlist();
		break;
	case 'dell_user':
		delete_user();
		break;
	case 'dell_category':
		delete_category();
		break;
	case 'general_submit':
		general_submit();
		main();
		break;
	case 'clear_cash':
		clear_cash();
	default:
		main();
}
function validate($string) {

	$chars = 'abcdefghijklmnopqrstuvwxyz_.0123456789';
	if ($string == '') return true;
	foreach (str_split($string) as $char)
		if (stripos($chars, $char) === false)
			return false;

	return true;
}

function general_submit() {

	global $lang;
	$guest = isset($_POST['guest']) ? intval($_POST['guest']) : 0;
	$coment = isset($_POST['coment']) ? intval($_POST['coment']) : 0;
	$journ = isset($_POST['journ']) ? intval($_POST['journ']) : 0;
	$moder = isset($_POST['moder']) ? intval($_POST['moder']) : 0;
	$admin = isset($_POST['admin']) ? intval($_POST['admin']) : 0;
	$message = isset($_POST['message']) ? $_POST['message'] : '';
	if (!$if_error) {
		pluginSetVariable('category_access', 'guest', $guest);
		pluginSetVariable('category_access', 'coment', $coment);
		pluginSetVariable('category_access', 'journ', $journ);
		pluginSetVariable('category_access', 'moder', $moder);
		pluginSetVariable('category_access', 'admin', $admin);
		pluginSetVariable('category_access', 'message', $message);
		pluginsSaveConfig();
		msg(array('type' => 'info', 'info' => $lang['category_access:info_save_general']));
	}
}

function main() {

	global $tpl, $lang, $main_admin;
	$tpath = locatePluginTemplates(array('conf.main', 'conf.general.form'), 'category_access', 1);
	$guest = pluginGetVariable('category_access', 'guest');
	$coment = pluginGetVariable('category_access', 'coment');
	$journ = pluginGetVariable('category_access', 'journ');
	$moder = pluginGetVariable('category_access', 'moder');
	$admin = pluginGetVariable('category_access', 'admin');
	$message = pluginGetVariable('category_access', 'message');
	$ttvars['vars']['guest_list'] = MakeDropDown(array(0 => $lang['category_access:label_close'], 1 => $lang['category_access:label_protect'], 2 => $lang['category_access:label_open']), 'guest', $guest);
	$ttvars['vars']['coment_list'] = MakeDropDown(array(0 => $lang['category_access:label_close'], 1 => $lang['category_access:label_protect'], 2 => $lang['category_access:label_open']), 'coment', $coment);
	$ttvars['vars']['journ_list'] = MakeDropDown(array(0 => $lang['category_access:label_close'], 1 => $lang['category_access:label_protect'], 2 => $lang['category_access:label_open']), 'journ', $journ);
	$ttvars['vars']['moder_list'] = MakeDropDown(array(0 => $lang['category_access:label_close'], 1 => $lang['category_access:label_protect'], 2 => $lang['category_access:label_open']), 'moder', $moder);
	$ttvars['vars']['admin_list'] = MakeDropDown(array(0 => $lang['category_access:label_close'], 1 => $lang['category_access:label_protect'], 2 => $lang['category_access:label_open']), 'admin', $admin);
	$ttvars['vars']['message'] = $message;
	$ttvars['vars']['action'] = $lang['category_access:button_general'];
	$tpl->template('conf.general.form', $tpath['conf.general.form']);
	$tpl->vars('conf.general.form', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.general.form');
	$tvars['vars']['action'] = $lang['category_access:button_general'];
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	$main_admin = $tpl->show('conf.main');
}

function show_list_user() {

	global $tpl, $lang, $catz, $catmap, $main_admin;
	$tpath = locatePluginTemplates(array('conf.main', 'conf.list.user', 'conf.list.user.row'), 'category_access', 1);
	$users = pluginGetVariable('category_access', 'users');
	$output = '';
	foreach ($users as $user => $category) {
		$pvars['vars']['user'] = $user;
		$pvars['vars']['category'] = $catz[$catmap[$category]]['name'];
		$tpl->template('conf.list.user.row', $tpath['conf.list.user.row']);
		$tpl->vars('conf.list.user.row', $pvars);
		$output .= $tpl->show('conf.list.user.row');
	}
	$ttvars['vars']['entries'] = $output;
	$tpl->template('conf.list.user', $tpath['conf.list.user']);
	$tpl->vars('conf.list.user', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.list.user');
	$tvars['vars']['action'] = $lang['category_access:button_list_user'];
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	$main_admin = $tpl->show('conf.main');
}

function show_list_category() {

	global $tpl, $lang, $catz, $catmap, $main_admin;
	$tpath = locatePluginTemplates(array('conf.main', 'conf.list', 'conf.list.row'), 'category_access', 1);
	$categorys = pluginGetVariable('category_access', 'categorys');
	$output = '';
	foreach ($categorys as $cat) {
		$pvars['vars']['category'] = $cat;
		$pvars['vars']['category_name'] = $catz[$catmap[$cat]]['name'];
		$tpl->template('conf.list.row', $tpath['conf.list.row']);
		$tpl->vars('conf.list.row', $pvars);
		$output .= $tpl->show('conf.list.row');
	}
	$ttvars['vars']['entries'] = $output;
	$tpl->template('conf.list', $tpath['conf.list']);
	$tpl->vars('conf.list', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.list');
	$tvars['vars']['action'] = $lang['category_access:button_list_category'];
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	$main_admin = $tpl->show('conf.main');
}

function add_user() {

	global $tpl, $lang, $catz, $catmap, $mysql, $main_admin;
	$users = pluginGetVariable('category_access', 'users');
	$if_add = true;
	$user = '';
	$category = 0;
	if (isset($_GET['user'])) {
		if (!array_key_exists($_GET['user'], $users)) {
			msg(array('type' => 'error', 'info' => $lang['category_access:error_not_exists'], 'text' => $lang['category_access:error_val_title']));
			show_list_user();

			return;
		}
		$user = $_GET['user'];
		$category = $users[$user];
		$if_add = false;
	}
	if (isset($_POST['user']) && isset($_POST['category'])) {
		$user = $_POST['user'];
		$category = $_POST['category'];
		$if_error = false;
		if (!$user || !validate($user)) {
			msg(array('type' => 'error', 'info' => sprintf($lang['category_access:error_validate'], $lang['category_access:label_user_name']), 'text' => $lang['category_access:error_val_title']));
			$if_error = true;
		}
		if (!array_key_exists($category, $catmap)) {
			msg(array('type' => 'error', 'info' => $lang['category_access:error_category'], 'text' => $lang['category_access:error_val_title']));
			$if_error = true;
		}
		if ($if_add && array_key_exists($user, $users)) {
			msg(array('type' => 'error', 'info' => sprintf($lang['category_access:error_exists'], $user), 'text' => $lang['category_access:error_val_title']));
			$if_error = true;
		}
		if (!$if_error) {
			$users[$user] = $category;
			pluginSetVariable('category_access', 'users', $users);
			pluginsSaveConfig();
			msg(array('type' => 'info', 'info' => $lang['category_access:info_save_general']));
			show_list_user();

			return;
		}
	}
	$category_list = array();
	foreach ($catmap as $key => $val) {
		if (array_key_exists($key, $category) && ($if_add || $key != $cat)) continue;
		$category_list[$key] = $catz[$val]['name'];
	}
	$user_list = array();
	foreach ($mysql->select('select ' . prefix . '_users.name  from ' . prefix . '_users order by ' . prefix . '_users.name asc') as $row) {
		if (array_key_exists($row['name'], $users) && ($if_add || $row['name'] != $user)) continue;
		$user_list[$row['name']] = $row['name'];
	}
	$tpath = locatePluginTemplates(array('conf.main', 'conf.add_edit_user.form'), 'category_access', 1);
	$ttvars['vars']['user'] = $user;
	$ttvars['vars']['user_list'] = MakeDropDown($user_list, 'user', $user);
	$ttvars['vars']['category_list'] = MakeDropDown($category_list, 'category', $category);
	$ttvars['regx']['/\[add\](.*?)\[\/add\]/si'] = $if_add ? '$1' : '';
	$ttvars['regx']['/\[edit\](.*?)\[\/edit\]/si'] = $if_add ? '' : '$1';
	$tpl->template('conf.add_edit_user.form', $tpath['conf.add_edit_user.form']);
	$tpl->vars('conf.add_edit_user.form', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.add_edit_user.form');
	$tvars['vars']['action'] = $if_add ? $lang['category_access:button_add_user'] : $lang['category_access:button_edit_user'];
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	$main_admin = $tpl->show('conf.main');
}

function add_category() {

	global $tpl, $lang, $catz, $catmap, $main_admin;
	$categorys = pluginGetVariable('category_access', 'categorys');
	if (isset($_POST['category']) && is_array($_POST['category'])) {
		foreach ($_POST['category'] as $category) {
			if (!array_key_exists($category, $catmap)) {
				msg(array('type' => 'error', 'info' => sprintf($lang['category_access:error_category_not_add'], $category), 'text' => $lang['category_access:error_val_title']));
				continue;
			}
			if (in_array($category, $categorys)) {
				msg(array('type' => 'error', 'info' => sprintf($lang['category_access:error_category_not_add'], $catz[$catmap[$category]]['name']), 'text' => $lang['category_access:error_val_title']));
				continue;
			}
			$categorys[] = $category;
		}
		pluginSetVariable('category_access', 'categorys', $categorys);
		pluginsSaveConfig();
		msg(array('type' => 'info', 'info' => $lang['category_access:info_save_general']));
		show_list_category();

		return;
	}
	$tpath = locatePluginTemplates(array('conf.main', 'conf.add_edit.category', 'conf.add_edit.category.row'), 'category_access', 1);
	$entries = '';
	foreach ($catmap as $key => $val) {
		if (in_array($key, $categorys)) continue;
		$pvars['vars']['category'] = $key;
		$pvars['vars']['category_name'] = $catz[$val]['name'];
		$tpl->template('conf.add_edit.category.row', $tpath['conf.add_edit.category.row']);
		$tpl->vars('conf.add_edit.category.row', $pvars);
		$entries .= $tpl->show('conf.add_edit.category.row');
	}
	$ttvars['vars']['entries'] = $entries;
	$tpl->template('conf.add_edit.category', $tpath['conf.add_edit.category']);
	$tpl->vars('conf.add_edit.category', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.add_edit.category');
	$tvars['vars']['action'] = $lang['category_access:button_add_category'];
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	$main_admin = $tpl->show('conf.main');
}

function delete_user() {

	global $tpl, $lang, $main_admin;
	$users = pluginGetVariable('category_access', 'users');
	if (!isset($_REQUEST['user']) || !array_key_exists($_REQUEST['user'], $users)) {
		msg(array('type' => 'error', 'info' => $lang['category_access:error_not_exists_user'], 'text' => $lang['category_access:error_val_title']));
		show_list_user();

		return;
	}
	$user = $_REQUEST['user'];
	if (isset($_POST['commit'])) {
		if ($_POST['commit'] == 'yes') {
			unset($users[$user]);
			pluginSetVariable('category_access', 'users', $users);
			pluginsSaveConfig();
			msg(array('type' => 'info', 'info' => $lang['category_access:info_save_general']));
		}
		show_list_user();

		return;
	}
	$tpath = locatePluginTemplates(array('conf.main', 'conf.commit.user'), 'category_access', 1);
	$tvars['vars']['user'] = $user;
	$tvars['vars']['commit'] = sprintf($lang['category_access:desc_commit_user'], $user);
	$tpl->template('conf.commit.user', $tpath['conf.commit.user']);
	$tpl->vars('conf.commit.user', $tvars);
	$tvars['vars']['entries'] = $tpl->show('conf.commit.user');
	$tvars['vars']['action'] = $lang['category_access:title_commit'];
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	$main_admin = $tpl->show('conf.main');
}

function delete_category() {

	global $tpl, $lang, $catz, $catmap, $main_admin;
	$categorys = pluginGetVariable('category_access', 'categorys');
	if (!isset($_REQUEST['category']) || !in_array($_REQUEST['category'], $categorys)) {
		msg(array('type' => 'error', 'info' => $lang['category_access:error_not_exists'], 'text' => $lang['category_access:error_val_title']));
		show_list_category();

		return;
	}
	$category = $_REQUEST['category'];
	if (isset($_POST['commit'])) {
		if ($_POST['commit'] == 'yes') {
			unset($categorys[array_search($category, $categorys)]);
			pluginSetVariable('category_access', 'categorys', $categorys);
			pluginsSaveConfig();
			msg(array('type' => 'info', 'info' => $lang['category_access:info_save_general']));
		}
		show_list_category();

		return;
	}
	$tpath = locatePluginTemplates(array('conf.main', 'conf.commit.form'), 'category_access', 1);
	$tvars['vars']['category'] = $category;
	$tvars['vars']['commit'] = sprintf($lang['category_access:desc_commit_category'], $catz[$catmap[$category]]['name']);
	$tpl->template('conf.commit.form', $tpath['conf.commit.form']);
	$tpl->vars('conf.commit.form', $tvars);
	$tvars['vars']['entries'] = $tpl->show('conf.commit.form');
	$tvars['vars']['action'] = $lang['category_access:title_commit'];
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	$main_admin = $tpl->show('conf.main');
}
