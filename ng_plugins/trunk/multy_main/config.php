<?php
if(!defined('NGCMS')) exit('HAL');

plugins_load_config();
LoadPluginLang('multy_main', 'config', '', '', ':');

switch ($_REQUEST['action']) {
	case 'list_menu': showlist(); break;
	case 'add_form': add(); break;
	case 'move_up': move('up'); showlist(); break;
	case 'move_down': move('down'); showlist(); break;
	case 'dell': delete(); break;
	case 'general_submit': general_submit(); main(); break;
	case 'clear_cash': clear_cash();
	default: main();
}


function validate($string){
	$chars = 'abcdefghijklmnopqrstuvwxyz_.0123456789';
	if ($string == '') return true;
	foreach(str_split($string) as $char)
		if (stripos($chars, $char) === false)
			return false;
	return true;
}

function general_submit(){
	global $lang;
	$if_error = false;
	if (!validate($_POST['main'])) {
		msg(array('type' => 'error', 'info' => sprintf($lang['multy_main:error_validate'], $lang['multy_main:label_main']), 'text' => $lang['multy_main:error_val_title']));
		$if_error = true;
	}
	if (!validate($_POST['guest'])) {
		msg(array('type' => 'error', 'info' => sprintf($lang['multy_main:error_validate'], $lang['multy_main:label_guest']), 'text' => $lang['multy_main:error_val_title']));
		$if_error = true;
	}
	if (!validate($_POST['coment'])) {
		msg(array('type' => 'error', 'info' => sprintf($lang['multy_main:error_validate'], $lang['multy_main:label_coment']), 'text' => $lang['multy_main:error_val_title']));
		$if_error = true;
	}
	if (!validate($_POST['journ'])) {
		msg(array('type' => 'error', 'info' => sprintf($lang['multy_main:error_validate'], $lang['multy_main:label_journ']), 'text' => $lang['multy_main:error_val_title']));
		$if_error = true;
	}
	if (!validate($_POST['moder'])) {
		msg(array('type' => 'error', 'info' => sprintf($lang['multy_main:error_validate'], $lang['multy_main:label_moder']), 'text' => $lang['multy_main:error_val_title']));
		$if_error = true;
	}
	if (!validate($_POST['admin'])) {
		msg(array('type' => 'error', 'info' => sprintf($lang['multy_main:error_validate'], $lang['multy_main:label_admin']), 'text' => $lang['multy_main:error_val_title']));
		$if_error = true;
	}
	if (!$if_error){
		pluginSetVariable('multy_main', 'main', $_POST['main']);
		pluginSetVariable('multy_main', 'guest', $_POST['guest']);
		pluginSetVariable('multy_main', 'coment', $_POST['coment']);
		pluginSetVariable('multy_main', 'journ', $_POST['journ']);
		pluginSetVariable('multy_main', 'moder', $_POST['moder']);
		pluginSetVariable('multy_main', 'admin', $_POST['admin']);
		pluginsSaveConfig();
		msg(array('type' => 'info', 'info' => $lang['multy_main:info_save_general']));
	}
}

function main(){
	global $tpl, $lang;
	$tpath = locatePluginTemplates(array('conf.main', 'conf.general.form'), 'multy_main', 1);
	$ttvars['vars']['main'] = isset($_POST['main'])?$_POST['main']:pluginGetVariable('multy_main', 'main');
	$ttvars['vars']['guest'] = isset($_POST['guest'])?$_POST['guest']:pluginGetVariable('multy_main', 'guest');
	$ttvars['vars']['coment'] = isset($_POST['coment'])?$_POST['coment']:pluginGetVariable('multy_main', 'coment');
	$ttvars['vars']['journ'] = isset($_POST['journ'])?$_POST['journ']:pluginGetVariable('multy_main', 'journ');
	$ttvars['vars']['moder'] = isset($_POST['moder'])?$_POST['moder']:pluginGetVariable('multy_main', 'moder');
	$ttvars['vars']['admin'] = isset($_POST['admin'])?$_POST['admin']:pluginGetVariable('multy_main', 'admin');
	$ttvars['vars']['action'] = $lang['multy_main:button_general'];
	$tpl->template('conf.general.form', $tpath['conf.general.form']);
	$tpl->vars('conf.general.form', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.general.form');
	$tvars['vars']['action'] = $lang['multy_main:button_general'];
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	print $tpl->show('conf.main');
}

function showlist(){
	global $tpl, $lang, $catz;
	$tpath = locatePluginTemplates(array('conf.main', 'conf.list', 'conf.list.row'), 'multy_main', 1);
	$category = pluginGetVariable('multy_main', 'category');
	$output = '';
	foreach ($category as $cat=>$tpll) {
		$pvars['vars']['cat'] = $cat;
		$pvars['vars']['cat_name'] = $catz[$cat]['name'];
		$pvars['vars']['tpl'] = $tpll;
		$tpl->template('conf.list.row', $tpath['conf.list.row']);
		$tpl->vars('conf.list.row', $pvars);
		$output .= $tpl->show('conf.list.row');
	}
	$ttvars['vars']['entries'] = $output;
	$tpl->template('conf.list', $tpath['conf.list']);
	$tpl->vars('conf.list', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.list');
	$tvars['vars']['action'] = $lang['multy_main:button_list'];
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	print $tpl->show('conf.main');
}

function add(){
	global $tpl, $lang, $catz;
	$category = pluginGetVariable('multy_main', 'category');
	$if_add = true;
	$cat = '';
	$tpll = '';
	if (isset($_GET['cat'])){
		if (!array_key_exists($_GET['cat'], $category)){
			msg(array('type' => 'error', 'info' => $lang['multy_main:error_not_exists'], 'text' => $lang['multy_main:error_val_title']));
			showlist();
			return;
		}
		$cat = $_GET['cat'];
		$tpll = $category[$cat];
		$if_add = false;
	}
	if (isset($_POST['cat']) && isset($_POST['tpl']))
	{
		$cat = $_POST['cat'];
		$tpll = $_POST['tpl'];
		$if_error = false;
		if (!$cat || !validate($cat)){
			msg(array('type' => 'error', 'info' => sprintf($lang['multy_main:error_validate'], $lang['multy_main:label_cat']), 'text' => $lang['multy_main:error_val_title']));
			$if_error = true;
		}
		if (!$tpll || !validate($tpll)){
			msg(array('type' => 'error', 'info' => sprintf($lang['multy_main:error_validate'], $lang['multy_main:label_tpl']), 'text' => $lang['multy_main:error_val_title']));
			$if_error = true;
		}
		if ($if_add && array_key_exists($cat, $category)){
			msg(array('type' => 'error', 'info' => sprintf($lang['multy_main:error_exists'], $catz[$cat]['name']), 'text' => $lang['multy_main:error_val_title']));
			$if_error = true;
		}
		if (!$if_error){
			$category[$cat] = $tpll;
			pluginSetVariable('multy_main', 'category', $category);
			pluginsSaveConfig();
			msg(array('type' => 'info', 'info' => $lang['multy_main:info_save_general']));
			showlist();
			return;
		}
	}
	$cat_list = array();
	foreach($catz as $key=>$val){
		if(array_key_exists($key, $category) && ($if_add || $key != $cat)) continue;
		$cat_list[$key] = $val['name'];
	}
	
	$tpath = locatePluginTemplates(array('conf.main', 'conf.add_edit.form'), 'multy_main', 1);
	$ttvars['vars']['cat'] = $cat;
	$ttvars['vars']['cat_list'] = MakeDropDown($cat_list, 'cat', $cat);
	$ttvars['vars']['tpl'] = $tpll;		
	$ttvars['regx']['/\[add\](.*?)\[\/add\]/si'] = $if_add?'$1':'';
	$ttvars['regx']['/\[edit\](.*?)\[\/edit\]/si'] = $if_add?'':'$1';
	$tpl->template('conf.add_edit.form', $tpath['conf.add_edit.form']);
	$tpl->vars('conf.add_edit.form', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.add_edit.form');
	$tvars['vars']['action'] = $if_add?$lang['multy_main:button_add_submit']:$lang['multy_main:button_edit_submit'];
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	print $tpl->show('conf.main');
}

function delete()
{
	global $tpl, $lang, $catz;
	$category = pluginGetVariable('multy_main', 'category');

	if (!isset($_REQUEST['cat']) || !array_key_exists($_REQUEST['cat'], $category)) {
		msg(array('type' => 'error', 'info' => $lang['multy_main:error_not_exists'], 'text' => $lang['multy_main:error_val_title']));
		showlist();
		return;
	}
	$cat = $_REQUEST['cat'];
	if (isset($_POST['commit'])) {
		if ($_POST['commit'] == 'yes'){
			unset($category[$cat]);
			pluginSetVariable('multy_main', 'category', $category);
			pluginsSaveConfig();
			msg(array('type' => 'info', 'info' => $lang['multy_main:info_save_general']));
		}
		showlist();
		return;
	}
	
	$tpath = locatePluginTemplates(array('conf.main', 'conf.commit.form'), 'multy_main', 1);
	$tvars['vars']['cat'] = $cat;
	$tvars['vars']['commit'] = sprintf($lang['multy_main:desc_commit'], $catz[$cat]['name']);
	$tpl->template('conf.commit.form', $tpath['conf.commit.form']);
	$tpl->vars('conf.commit.form', $tvars);
	$tvars['vars']['entries'] = $tpl->show('conf.commit.form');
	$tvars['vars']['action'] = $lang['multy_main:title_commit'];
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	print $tpl->show('conf.main');
}