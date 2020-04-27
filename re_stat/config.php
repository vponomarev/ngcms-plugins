<?php
if(!defined('NGCMS')) exit('HAL');

plugins_load_config();
ver_ver();

switch ($_REQUEST['action']) {
	case 'edit': case 'add': editform(); break;
	case 'confirm': editform(); break;
	case 'delete': delete(); break;
	case 're_map': re_map(); showlist(); break;
	default: showlist();
}

function showlist()
{
	global $tpl, $mysql;
	$static_page = $mysql->select('select `id`, `title` from '.prefix.'_static order by `title`, `id`');
	$tpath = locatePluginTemplates(array('conf.list', 'conf.list.row'), 're_stat');
	$output = ''; $no = 1; $t_values = array(); $values = pluginGetVariable('re_stat', 'values');
	foreach($values as $key => $row) {
		$title = '';
		foreach ($static_page as $page) if (intval($page['id']) == $row['id']){$title = $page['title']; break;}
		$pvars['vars'] = array (
			'id' => $key,
			'no' => $no ++,
			'code' => $row['code'],
			'title' => ($title?$title:'<font color="red">Такой страницы не сущуствует</font>'),
			'error' => '',
			);
		if (in_array($row['code'], $t_values, true)) $pvars['vars']['error'] = '<font color="red">Повторяющийся код</font>';
		$t_values[] = $row['code'];
		$tpl->template('conf.list.row', $tpath['conf.list.row']);
		$tpl -> vars('conf.list.row', $pvars);
		$output .= $tpl->show('conf.list.row');
	}
	$tvars['vars']['entries'] = $output;
	$tpl->template('conf.list', $tpath['conf.list']);
	$tpl->vars('conf.list', $tvars);
	print $tpl->show('conf.list');
}

function editform()
{
	global $mysql, $tpl, $config;
	if (!isset($_REQUEST['id'])) {
		msg(array('type' => 'info', 'info' => '<font color="red">Значение для редактирования/добавления не определено!!!</font>'));
		showlist();	return false; }
	$id = intval($_REQUEST['id']);
	$values = pluginGetVariable('re_stat', 'values');
	if ($id != -1 && !is_array($values)) {
		msg(array('type' => 'info', 'info' => '<font color="red">В базе отсутствуют значения, редактировать нечего!!!</font>'));
		showlist();	return false; }
	if ($id != -1 && !array_key_exists($id, $values)) {
		msg(array('type' => 'info', 'info' => '<font color="red">Ключ id='.$id.' отсутствует в базе</font>'));
		showlist(); return false; } 
	$if_error = false; $idstat = 0; $code = '';
	if (isset($_REQUEST['code']) && isset($_REQUEST['idstat'])){
		$code = secure_html(convert($_REQUEST['code']));
		if (!$code) { 
			msg(array('type' => 'info', 'info' => '<font color="red">Значение <b>код</b> не может быть пустым</font>'));
			$if_error = true; }
		foreach ($values as $key => $row) if ($row['code'] === $code && $key != $id){
			msg(array('type' => 'info', 'info' => '<font color="red">Такое значение <b>код</b> уже присутствует в списке</font>'));
			$if_error = true; }
		if (!$if_error){
			$idstat = intval($_REQUEST['idstat']);
			$ULIB = new urlLibrary();
			$ULIB->loadConfig();
			if ($id == -1) {
				$values[] = array('code' => $code, 'id' => $idstat);
			} else {
				$ULIB->removeCommand('re_stat', $values[$id]['code']);
				$values[$id]['code'] = $code;
				$values[$id]['id'] = $idstat;}
			pluginSetVariable('re_stat', 'values', $values);
			pluginsSaveConfig();
			$title = '<font color="red">Такой страницы не сущуствует</font>';
			foreach ($mysql->select('select `title` from '.prefix.'_static where `id`='.$idstat.' limit 1') as $page) $title = $page['title'];
			$ULIB->registerCommand('re_stat', $code, array('vars' => array(), 'descr' => array ($config['default_lang'] => $title)));
			$ULIB->saveConfig();
			showlist();
			return;
		}
	}
	$static_page = $mysql->select('select `id`, `title` from '.prefix.'_static order by `title`, `id`');
	$tpath = locatePluginTemplates(array('conf.edit'), 're_stat');
	$statlist = array();
	foreach ($static_page as $row)
		$statlist[$row['id']] = $row['title'];
	$tvars['vars']['statlist'] = MakeDropDown($statlist, 'idstat', ($if_error?$idstat:(isset($values[$id]['id'])?$values[$id]['id']:-1)));
	$tvars['vars']['code'] = ($if_error?$code:(isset($values[$id]['code'])?$values[$id]['code']:''));
	$tvars['vars']['id'] = $id;
	$tvars['regx']['/\[add\](.*?)\[\/add\]/si'] = '';
	$tvars['regx']['/\[edit\](.*?)\[\/edit\]/si'] = '';
	if ($id == -1) $tvars['regx']['/\[add\](.*?)\[\/add\]/si'] = '$1'; else $tvars['regx']['/\[edit\](.*?)\[\/edit\]/si'] = '$1';
	$tpl->template('conf.edit', $tpath['conf.edit']);
	$tpl->vars('conf.edit', $tvars);
	print $tpl->show('conf.edit');
}

function delete()
{
	if (!isset($_REQUEST['id'])) {
		msg(array('type' => 'info', 'info' => '<font color="red">Значение для удаления не определено, удалять нечего!!!</font>'));
		showlist();	return false; }
	$id = intval($_REQUEST['id']);
	$values = pluginGetVariable('re_stat', 'values');
	if (!is_array($values)) {
		msg(array('type' => 'info', 'info' => '<font color="red">В базе отсутствуют значения, удалять нечего!!!</font>'));
		showlist();	return false; }
	if (!array_key_exists($id, $values)) {
		msg(array('type' => 'info', 'info' => '<font color="red">Ключ id='.$id.' отсутствует в базе</font>'));
		showlist(); return false; }

	$ULIB = new urlLibrary();
	$ULIB->loadConfig();
	$ULIB->removeCommand('re_stat', $values[$id]['code']);
	$ULIB->saveConfig();	
	
	unset($values[$id]);
	pluginSetVariable('re_stat', 'values', $values);
	pluginsSaveConfig();
	showlist();
}

function re_map()
{
	global $mysql, $config;
	$ULIB = new urlLibrary();
	$ULIB->loadConfig();
	if (isset($ULIB->CMD['re_stat']))
		unset($ULIB->CMD['re_stat']);
	$values = pluginGetVariable('re_stat', 'values');
	foreach ($values as $key => $row){
		$title = '<font color="red">Такой страницы не сущуствует</font>';
		foreach ($mysql->select('select `title` from '.prefix.'_static where `id`='.$row['id'].' limit 1') as $page) $title = $page['title'];
		$ULIB->registerCommand('re_stat', $row['code'], array('vars' => array(), 'descr' => array ($config['default_lang'] => $title)));
	}
	$ULIB->saveConfig();
	msg(array('type' => 'info', 'info' => '<font color="green">Карта ссылок успешно перестроена</font>'));
}

function ver_ver()
{
	global $mysql, $PLUGINS;
	$versionbase = pluginGetVariable('re_stat', 'version');
	if (isset($PLUGINS['config']['re_stat']) && !$versionbase) $versionbase = '0.01';
	else if (!$versionbase) {$versionbase = '0.02'; pluginSetVariable('re_stat', 'version', $versionbase); pluginsSaveConfig();}
	switch ($versionbase) {
	case '0.01':
		$count = 0; $values = array();
		if (isset($PLUGINS['config']['re_stat'])) $count = count($PLUGINS['config']['re_stat']) / 2;
		$static_page = $mysql->select('select `id`, `alt_name` from '.prefix.'_static');
		for ($i = 0; $i < $count; $i ++){
			$id = 0;
			foreach ($static_page as $page) 
				if ($page['alt_name'] == pluginGetVariable('re_stat', 'altstat'.$i))
					{$id = intval($page['id']); break;}
			$values[] = array('id' => $id, 'code' => pluginGetVariable('re_stat', 'code'.$i));
			unset($PLUGINS['config']['re_stat']['code'.$i]); 
			unset($PLUGINS['config']['re_stat']['altstat'.$i]);
		}
		pluginSetVariable('re_stat', 'values', $values);
		pluginSetVariable('re_stat', 'version', '0.02');
		pluginsSaveConfig();
		msg(array('type' => 'info', 'info' => '<font color="green">База настроек успешно обновленна до версии 0.02</font>'));
		re_map();
	}
}