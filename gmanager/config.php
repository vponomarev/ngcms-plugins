<?php
if (!defined('NGCMS')) exit('HAL');
plugins_load_config();
LoadPluginLang('gmanager', 'config', '', '', ':');
switch ($_REQUEST['action']) {
	case 'list':
		showlist();
		break;
	case 'update':
		update();
		showlist();
		break;
	case 'edit':
		edit();
		break;
	case 'edit_submit':
		edit_submit();
		showlist();
		break;
	case 'dell':
		delete();
		break;
	case 'move_up':
		move('up');
		showlist();
		break;
	case 'move_down':
		move('down');
		showlist();
		break;
	case 'clear_cash':
		clear_cash();
		main();
		break;
	case 'general_submit':
		general_submit();
		main();
		break;
	default:
		main();
}
function main() {

	global $tpl, $lang, $main_admin;
	$tpath = locatePluginTemplates(array('conf.main', 'conf.general.form'), 'gmanager', 1);
	$tvars['vars']['locate_tpl_list'] = MakeDropDown(array(0 => $lang['gmanager:label_site'], 1 => $lang['gmanager:label_plugin']), 'locate_tpl', pluginGetVariable('gmanager', 'locate_tpl'));
	$tvars['vars']['if_auto_cash_list'] = MakeDropDown(array(0 => $lang['gmanager:label_no'], 1 => $lang['gmanager:label_yes']), 'if_auto_cash', pluginGetVariable('gmanager', 'if_auto_cash'));
	$tvars['vars']['if_description_list'] = MakeDropDown(array(0 => $lang['gmanager:label_no'], 1 => $lang['gmanager:label_yes']), 'if_description', pluginGetVariable('gmanager', 'if_description'));
	$tvars['vars']['if_keywords_list'] = MakeDropDown(array(0 => $lang['gmanager:label_no'], 1 => $lang['gmanager:label_yes']), 'if_keywords', pluginGetVariable('gmanager', 'if_keywords'));
	$tvars['vars']['main_row'] = pluginGetVariable('gmanager', 'main_row');
	$tvars['vars']['main_cell'] = pluginGetVariable('gmanager', 'main_cell');
	$tvars['vars']['main_page_list'] = MakeDropDown(array(0 => $lang['gmanager:label_no'], 1 => $lang['gmanager:label_yes']), 'main_page', pluginGetVariable('gmanager', 'main_page'));
	$tvars['vars']['one_row'] = pluginGetVariable('gmanager', 'one_row');
	$tvars['vars']['one_cell'] = pluginGetVariable('gmanager', 'one_cell');
	$tvars['vars']['one_page_list'] = MakeDropDown(array(0 => $lang['gmanager:label_no'], 1 => $lang['gmanager:label_yes']), 'one_page', pluginGetVariable('gmanager', 'one_page'));
	$tpl->template('conf.general.form', $tpath['conf.general.form']);
	$tpl->vars('conf.general.form', $tvars);
	$tvars['vars']['entries'] = $tpl->show('conf.general.form');
	$tvars['vars']['action'] = $lang['gmanager:button_general'];
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	$main_admin = $tpl->show('conf.main');
}

function general_submit() {

	global $lang;
	pluginSetVariable('gmanager', 'locate_tpl', intval($_POST['locate_tpl']));
	pluginSetVariable('gmanager', 'if_auto_cash', intval($_POST['if_auto_cash']));
	pluginSetVariable('gmanager', 'if_description', intval($_POST['if_description']));
	pluginSetVariable('gmanager', 'if_keywords', intval($_POST['if_keywords']));
	pluginSetVariable('gmanager', 'main_row', intval($_POST['main_row']));
	pluginSetVariable('gmanager', 'main_cell', intval($_POST['main_cell']));
	pluginSetVariable('gmanager', 'main_page', intval($_POST['main_page']));
	pluginSetVariable('gmanager', 'one_row', intval($_POST['one_row']));
	pluginSetVariable('gmanager', 'one_cell', intval($_POST['one_cell']));
	pluginSetVariable('gmanager', 'one_page', intval($_POST['one_page']));
	pluginsSaveConfig();
	if (pluginGetVariable('gmanager', 'if_auto_cash')) clear_cash();
	msg(array('type' => 'info', 'info' => $lang['gmanager:info_save_general']));
}

function showlist() {

	global $tpl, $lang, $mysql, $main_admin;
	$tpath = locatePluginTemplates(array('conf.main', 'conf.list', 'conf.list.row'), 'gmanager', 1);
	$output = '';
	foreach ($mysql->select('select * from ' . prefix . '_gmanager order by `order`') as $row) {
		$pvars['regx']['/\[if_active\](.*?)\[\/if_active\]/si'] = $row['if_active'] ? '$1' : '';
		$pvars['regx']['/\[if_not_active\](.*?)\[\/if_not_active\]/si'] = $row['if_active'] ? '' : '$1';
		$pvars['vars']['id'] = $row['id'];
		$pvars['vars']['name'] = $row['name'];
		$pvars['vars']['title'] = $row['title'];
		$tpl->template('conf.list.row', $tpath['conf.list.row']);
		$tpl->vars('conf.list.row', $pvars);
		$output .= $tpl->show('conf.list.row');
	}
	$ttvars['vars']['entries'] = $output;
	$tpl->template('conf.list', $tpath['conf.list']);
	$tpl->vars('conf.list', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.list');
	$tvars['vars']['action'] = $lang['gmanager:button_list'];
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	$main_admin = $tpl->show('conf.main');
}

function update() {

	global $mysql;
	$galery = $mysql->select('select name from ' . prefix . '_gmanager');
	$next_order = count($galery) + 1;
	$dir = opendir(images_dir);
	if ($dir = opendir(images_dir)) {
		while ($file = readdir($dir)) {
			if (!is_dir(images_dir . "/" . $file) || $file == "." || $file == ".." || GetKeyFromName($file, $galery) !== false)
				continue;
			$mysql->query('insert ' . prefix . '_gmanager ' .
				'(`name`, `order`) values ' .
				'(' . db_squote($file) . ', ' . db_squote($next_order) . ')');
			$next_order++;
		}
		closedir($dir);
	}
}

function GetKeyFromName($name, $array) {

	$count = count($array);
	for ($i = 0; $i < $count; $i++)
		if ($array[$i]['name'] == $name)
			return $i;

	return false;
}

function edit() {

	global $mysql, $tpl, $lang, $main_admin;
	if (!isset($_REQUEST['id'])) return;
	$id = intval($_REQUEST['id']);
	$galery = $mysql->record('select * from ' . prefix . '_gmanager where `id`=' . db_squote($id) . ' limit 1');
	if (!$galery) return;
	$tpath = locatePluginTemplates(array('conf.main', 'conf.edit.form'), 'gmanager', 1);
	$icon_list = array();
	foreach ($mysql->select('select id, name from ' . prefix . '_images where folder=' . db_squote($galery['name'])) as $row)
		$icon_list[$row['id']] = $row['name'];
	$pvars['vars']['id'] = $galery['id'];
	$pvars['vars']['id_icon_list'] = MakeDropDown($icon_list, 'id_icon', $galery['id_icon']);
	$pvars['vars']['if_active_list'] = MakeDropDown(array(0 => $lang['gmanager:label_off'], 1 => $lang['gmanager:label_on']), 'if_active', $galery['if_active']);
	$pvars['vars']['name'] = $galery['name'];
	$pvars['vars']['title'] = $galery['title'];
	$pvars['vars']['description'] = $galery['description'];
	$pvars['vars']['keywords'] = $galery['keywords'];
	$tpl->template('conf.edit.form', $tpath['conf.edit.form']);
	$tpl->vars('conf.edit.form', $pvars);
	$output .= $tpl->show('conf.edit.form');
	$tvars['vars']['entries'] = $output;
	$tvars['vars']['action'] = $lang['gmanager:button_edit'];
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	$main_admin = $tpl->show('conf.main');
}

function edit_submit() {

	global $mysql, $lang;
	if (!isset($_REQUEST['id']) || !isset($_POST['title']) || !isset($_POST['if_active']) || !isset($_POST['id_icon']) || !isset($_POST['description']) || !isset($_POST['keywords'])) return;
	$id = intval($_REQUEST['id']);
	$galery = $mysql->record('select * from ' . prefix . '_gmanager where `id`=' . db_squote($id) . ' limit 1');
	if (!$galery) return;
	$title = trim(secure_html(convert($_POST['title'])));
	$if_active = intval($_POST['if_active']);
	$id_icon = intval($_POST['id_icon']);
	$description = trim(convert($_POST['description']));
	$keywords = trim(secure_html(convert($_POST['keywords'])));
	$t_update = '';
	if ($title != $row['title'])
		$t_update .= (($t_update ? ', ' : '') . '`title`=' . db_squote($title));
	if ($if_active != $row['if_active'])
		$t_update .= (($t_update ? ', ' : '') . '`if_active`=' . db_squote($if_active));
	if ($id_icon != $row['id_icon'])
		$t_update .= (($t_update ? ', ' : '') . '`id_icon`=' . db_squote($id_icon));
	if ($description != $row['description'])
		$t_update .= (($t_update ? ', ' : '') . '`description`=' . db_squote($description));
	if ($keywords != $row['keywords'])
		$t_update .= (($t_update ? ', ' : '') . '`keywords`=' . db_squote($keywords));
	if ($t_update) {
		$mysql->query('update ' . prefix . '_gmanager set ' . $t_update . ' where id = ' . db_squote($id) . ' limit 1');
		msg(array('type' => 'info', 'info' => $lang['gmanager:info_update_record']));
	}
	if (pluginGetVariable('gmanager', 'if_auto_cash')) clear_cash();
}

function move($action) {

	global $mysql, $lang;
	if (!isset($_REQUEST['id'])) return;
	$id = intval($_REQUEST['id']);
	$galery = $mysql->record('select id, `order` from ' . prefix . '_gmanager where `id`=' . db_squote($id) . ' limit 1');
	if (!$galery) return;
	$count = 0;
	if (is_array($pcnt = $mysql->record('select count(*) as cnt from ' . prefix . '_gmanager')))
		$count = $pcnt['cnt'];
	if ($action == 'up') {
		if ($galery['order'] == 1)
			return;
		$galery2 = $mysql->record('select id, `order` from ' . prefix . '_gmanager where `order`=' . db_squote($galery['order'] - 1) . ' limit 1');
		$mysql->query('update ' . prefix . '_gmanager set `order`=' . db_squote($galery['order']) . 'where `id`=' . db_squote($galery2['id']) . ' limit 1');
		$mysql->query('update ' . prefix . '_gmanager set `order`=' . db_squote($galery2['order']) . 'where `id`=' . db_squote($galery['id']) . ' limit 1');
	} else if ($action == 'down') {
		if ($galery['order'] == $count)
			return;
		$galery2 = $mysql->record('select id, `order` from ' . prefix . '_gmanager where `order`=' . db_squote($galery['order'] + 1) . ' limit 1');
		$mysql->query('update ' . prefix . '_gmanager set `order`=' . db_squote($galery['order']) . 'where `id`=' . db_squote($galery2['id']) . ' limit 1');
		$mysql->query('update ' . prefix . '_gmanager set `order`=' . db_squote($galery2['order']) . 'where `id`=' . db_squote($galery['id']) . ' limit 1');
	}
	if (pluginGetVariable('gmanager', 'if_auto_cash')) clear_cash();
}

function delete() {

	global $mysql, $tpl, $lang, $main_admin;
	if (!isset($_REQUEST['id'])) return;
	$id = intval($_REQUEST['id']);
	$galery = $mysql->record('select `title` from ' . prefix . '_gmanager where `id`=' . db_squote($id) . ' limit 1');
	if (isset($_POST['commit'])) {
		if ($_POST['commit'] == 'yes') {
			$mysql->query('delete from ' . prefix . '_gmanager where `id`=' . db_squote($id));
			$next_order = 1;
			foreach ($mysql->select('select id from ' . prefix . '_gmanager order by `order`') as $row) {
				$dir = opendir(images_dir);
				$mysql->query('update ' . prefix . '_gmanager set `order`=' . db_squote($next_order) . 'where `id`=' . db_squote($row['id']) . ' limit 1');
				$next_order++;
			}
			msg(array('type' => 'info', 'info' => $lang['gmanager:info_delete']));
			if (pluginGetVariable('gmanager', 'if_auto_cash')) clear_cash();
		}
		showlist();

		return true;
	}
	$tpath = locatePluginTemplates(array('conf.main', 'conf.commit.form'), 'gmanager', 1);
	$tvars['vars']['id'] = $id;
	$tvars['vars']['commit'] = sprintf($lang['gmanager:desc_commit'], $galery['title']);
	$tpl->template('conf.commit.form', $tpath['conf.commit.form']);
	$tpl->vars('conf.commit.form', $tvars);
	$tvars['vars']['entries'] = $tpl->show('conf.commit.form');
	$tvars['vars']['action'] = $lang['gmanager:title_commit'];
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	$main_admin = $tpl->show('conf.main');
}

function clear_cash() {

	global $lang;
	if (($dir = get_plugcache_dir('gmanager'))) {
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				if ($file == "." || $file == "..")
					continue;
				unlink($dir . $file);
			}
			closedir($handle);
		}
		msg(array('type' => 'info', 'info' => $lang['gmanager:info_cash_clear']));
	}
}
