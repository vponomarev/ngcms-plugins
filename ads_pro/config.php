<?php
if (!defined('NGCMS')) exit('HAL');
plugins_load_config();
LoadPluginLang('ads_pro', 'config', '', '', ':');
//pluginSetVariable('ads_pro', 'data', array());
//pluginsSaveConfig();
switch ($_REQUEST['action']) {
	case 'list':
		showlist();
		break;
	case 'add':
		add();
		break;
	case 'edit':
		add();
		break;
	case 'add_submit':
		add_submit();
		break;
	case 'edit_submit':
		add_submit();
		break;
	case 'move_up':
		move('up');
		break;
	case 'move_down':
		move('down');
		break;
	case 'dell':
		delete();
		break;
	case 'main_submit':
		main_submit();
		break;
	case 'clear_cash':
		clear_cash();
	default:
		main();
}
function main() {

	global $tpl, $lang;
	$tpath = locatePluginTemplates(array('conf.main', 'conf.general'), 'ads_pro', 1);
	$s_news = pluginGetVariable('ads_pro', 'support_news');
	$s_news_sort = pluginGetVariable('ads_pro', 'news_cfg_sort');
	$s_multidisplay = pluginGetVariable('ads_pro', 'multidisplay_mode');
	$ttvars = array();
	$ttvars['vars'] = array(
		'action'              => $lang['ads_pro:button_general'],
		's_news0'             => ($s_news ? '' : ' selected'),
		's_news1'             => ($s_news ? ' selected' : ''),
		's_news_sort0'        => ($s_news_sort ? '' : ' selected'),
		's_news_sort1'        => ($s_news_sort ? ' selected' : ''),
		'multidisplay_mode_0' => (($s_multidisplay == 0) ? ' selected' : ''),
		'multidisplay_mode_1' => (($s_multidisplay == 1) ? ' selected' : ''),
		'multidisplay_mode_2' => (($s_multidisplay == 2) ? ' selected' : ''),
	);
	$tpl->template('conf.general', $tpath['conf.general']);
	$tpl->vars('conf.general', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.general');
	$tvars['vars']['action'] = $lang['ads_pro:button_general'];
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	print $tpl->show('conf.main');
}

function main_submit() {

	global $tpl, $lang;
	$chg = 0;
	$nv = intval($_REQUEST['support_news']);
	if ($nv != pluginGetVariable('ads_pro', 'support_news')) {
		pluginSetVariable('ads_pro', 'support_news', $nv);
		$chg++;
	}
	$ns = intval($_REQUEST['news_cfg_sort']);
	if ($ns != pluginGetVariable('ads_pro', 'news_cfg_sort')) {
		pluginSetVariable('ads_pro', 'news_cfg_sort', $ns);
		$chg++;
	}
	$nm = intval($_REQUEST['multidisplay_mode']);
	if ($nm != pluginGetVariable('ads_pro', 'multidisplay_mode')) {
		pluginSetVariable('ads_pro', 'multidisplay_mode', $nm);
		$chg++;
	}
	if ($chg) {
		pluginsSaveConfig();
	}
	main();
}

function showlist() {

	global $tpl, $lang;
	$tpath = locatePluginTemplates(array('conf.main', 'conf.list', 'conf.list.row'), 'ads_pro', 1);
	$var = pluginGetVariable('ads_pro', 'data');
	$output = '';
	$t_time = time();
	$t_state = array(0 => $lang['ads_pro:label_off'], 1 => $lang['ads_pro:label_on'], 2 => $lang['ads_pro:label_sched']);
	$t_type = array(0 => $lang['ads_pro:html'], 1 => $lang['ads_pro:php'], 2 => $lang['ads_pro:text']);
	foreach ($var as $k => $v) {
		foreach ($v as $kk => $vv) {
			$pvars['vars']['name'] = $k ? $k : $lang['ads_pro:error_name'];
			$pvars['vars']['id'] = $kk;
			$pvars['vars']['description'] = $vv['description'];
			$if_view = $vv['state'] ? true : false;
			if ($vv['start_view'] && $vv['start_view'] > $t_time)
				$if_view = false;
			if ($vv['end_view'] && $vv['end_view'] <= $t_time)
				$if_view = false;
			$pvars['vars']['online'] = ($if_view || $vv['state'] == 1) ? $lang['ads_pro:online_on'] : $lang['ads_pro:online_off'];
			$pvars['vars']['state'] = $t_state[$vv['state']];
			$pvars['vars']['type'] = $t_type[$vv['type']];
			$tpl->template('conf.list.row', $tpath['conf.list.row']);
			$tpl->vars('conf.list.row', $pvars);
			$output .= $tpl->show('conf.list.row');
		}
	}
	$ttvars['vars']['entries'] = $output;
	$tpl->template('conf.list', $tpath['conf.list']);
	$tpl->vars('conf.list', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.list');
	$tvars['vars']['action'] = $lang['ads_pro:button_list'];
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	print $tpl->show('conf.main');
}

function add() {

	global $mysql, $tpl, $lang;
	$PluginsList = getPluginsActiveList();
	// Load config
	$pConfig = pluginGetVariable('ads_pro', 'data');
	//print "<pre>".var_export($pConfig, true)."</pre>";
	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
	$var = '';
	$name = '';
	if ($id) {
		foreach ($pConfig as $k => $v) {
			foreach ($v as $kk => $vv) {
				if ($id == $kk) {
					$var = $vv;
					$name = $k ? $k : '';
					break(2);
				}
			}
		}
	}
	$tpath = locatePluginTemplates(array('conf.main', 'conf.add_edit.form'), 'ads_pro', 1);
	$ttvars['vars']['plugins_list'] = "\n";
	$ttvars['vars']['plugins_list'] .= "\n\t\t\t" . 'subsubel = document.createElement("option");' . "\n";
	$ttvars['vars']['plugins_list'] .= "\t\t\t" . 'subsubel.setAttribute("value", "0");' . "\n";
	$ttvars['vars']['plugins_list'] .= "\t\t\t" . 'subsubel.appendChild(document.createTextNode("' . $lang['ads_pro:all'] . '"));' . "\n";
	$ttvars['vars']['plugins_list'] .= "\t\t\t" . 'subel.appendChild(subsubel);' . "\n";
	$t_plugin_list = array(0 => $lang['ads_pro:all']);
	foreach ($PluginsList['actions']['ppages'] as $key => $value) {
		$t_plugin_list[$key] = $key;
		$ttvars['vars']['plugins_list'] .= "\n\t\t\t" . 'subsubel = document.createElement("option");' . "\n";
		$ttvars['vars']['plugins_list'] .= "\t\t\t" . 'subsubel.setAttribute("value", "' . $key . '");' . "\n";
		$ttvars['vars']['plugins_list'] .= "\t\t\t" . 'subsubel.appendChild(document.createTextNode("' . htmlspecialchars($key) . '"));' . "\n";
		$ttvars['vars']['plugins_list'] .= "\t\t\t" . 'subel.appendChild(subsubel);' . "\n";
	}
	$ttvars['vars']['category_list'] = "\n";
	$ttvars['vars']['category_list'] .= "\t\t\t" . 'subsubel = document.createElement("option");' . "\n";
	$ttvars['vars']['category_list'] .= "\t\t\t" . 'subsubel.setAttribute("value", "0");' . "\n";
	$ttvars['vars']['category_list'] .= "\t\t\t" . 'subsubel.appendChild(document.createTextNode("' . $lang['ads_pro:all'] . '"));' . "\n";
	$ttvars['vars']['category_list'] .= "\t\t\t" . 'subel.appendChild(subsubel);' . "\n";
	$t_category_list = array(0 => $lang['ads_pro:all']);
	foreach ($mysql->select("select id, name from " . prefix . "_category") as $row) {
		$t_category_list[$row['id']] = $row['name'];
		$ttvars['vars']['category_list'] .= "\n\t\t\t" . 'subsubel = document.createElement("option");' . "\n";
		$ttvars['vars']['category_list'] .= "\t\t\t" . 'subsubel.setAttribute("value", "' . $row['id'] . '");' . "\n";
		$ttvars['vars']['category_list'] .= "\t\t\t" . 'subsubel.appendChild(document.createTextNode("' . htmlspecialchars($row['name']) . '"));' . "\n";
		$ttvars['vars']['category_list'] .= "\t\t\t" . 'subel.appendChild(subsubel);' . "\n";
	}
	$ttvars['vars']['static_list'] = "\n";
	$ttvars['vars']['static_list'] .= "\n\t\t\t" . 'subsubel = document.createElement("option");' . "\n";
	$ttvars['vars']['static_list'] .= "\t\t\t" . 'subsubel.setAttribute("value", "0");' . "\n";
	$ttvars['vars']['static_list'] .= "\t\t\t" . 'subsubel.appendChild(document.createTextNode("' . $lang['ads_pro:all'] . '"));' . "\n";
	$ttvars['vars']['static_list'] .= "\t\t\t" . 'subel.appendChild(subsubel);' . "\n";
	$t_static_list = array(0 => $lang['ads_pro:all']);
	foreach ($mysql->select("select id, title from " . prefix . "_static") as $row) {
		$t_static_list[$row['id']] = $row['title'];
		$ttvars['vars']['static_list'] .= "\n\t\t\t" . 'subsubel = document.createElement("option");' . "\n";
		$ttvars['vars']['static_list'] .= "\t\t\t" . 'subsubel.setAttribute("value", "' . $row['id'] . '");' . "\n";
		$ttvars['vars']['static_list'] .= "\t\t\t" . 'subsubel.appendChild(document.createTextNode("' . htmlspecialchars($row['title']) . '"));' . "\n";
		$ttvars['vars']['static_list'] .= "\t\t\t" . 'subel.appendChild(subsubel);' . "\n";
	}
	if (pluginGetVariable('ads_pro', 'support_news')) {
		$ttvars['vars']['news_list'] = "\n";
		$ttvars['vars']['news_list'] .= "\n\t\t\t" . 'subsubel = document.createElement("option");' . "\n";
		$ttvars['vars']['news_list'] .= "\t\t\t" . 'subsubel.setAttribute("value", "0");' . "\n";
		$ttvars['vars']['news_list'] .= "\t\t\t" . 'subsubel.appendChild(document.createTextNode("' . $lang['ads_pro:all'] . '"));' . "\n";
		$ttvars['vars']['news_list'] .= "\t\t\t" . 'subel.appendChild(subsubel);' . "\n";
		$t_news_list = array(0 => $lang['ads_pro:all']);
		foreach ($mysql->select("select id, title from " . prefix . "_news order by " . (pluginGetVariable('ads_pro', 'news_cfg_sort') ? 'title' : 'id')) as $row) {
			$t_news_list[$row['id']] = $row['title'];
			$ttvars['vars']['news_list'] .= "\n\t\t\t" . 'subsubel = document.createElement("option");' . "\n";
			$ttvars['vars']['news_list'] .= "\t\t\t" . 'subsubel.setAttribute("value", "' . $row['id'] . '");' . "\n";
			$ttvars['vars']['news_list'] .= "\t\t\t" . 'subsubel.appendChild(document.createTextNode("' . htmlspecialchars((pluginGetVariable('ads_pro', 'news_cfg_sort') ? '' : ($row['id'] . ' :: ')) . $row['title']) . '"));' . "\n";
			$ttvars['vars']['news_list'] .= "\t\t\t" . 'subel.appendChild(subsubel);' . "\n";
		}
		$ttvars['regx']['#\[support_news\](.*?)\[\/support_news\]#si'] = '$1';
	} else {
		$ttvars['regx']['#\[support_news\](.*?)\[\/support_news\]#si'] = '';
	}
	if ($id) {
		$ttvars['vars']['id'] = $id;
		$ttvars['vars']['name'] = $name;
		$ttvars['vars']['description'] = $var['description'];
		$ttvars['vars']['start_view'] = $var['start_view'] ? date('Y.m.d H:i', $var['start_view']) : '';
		$ttvars['vars']['end_view'] = $var['end_view'] ? date('Y.m.d H:i', $var['end_view']) : '';
		$ttvars['vars']['location_list'] = '';
		foreach ($var['location'] as $k => $v) {
			$ttvars['vars']['location_list'] .= '<tr><td>' . ($k) . ': </td><td align="left">';
			$ttvars['vars']['location_list'] .= MakeDropDown(array(0 => $lang['ads_pro:around'], 1 => $lang['ads_pro:main'], 2 => $lang['ads_pro:not_main'], 3 => $lang['ads_pro:category'], 4 => $lang['ads_pro:static'], 5 => $lang['ads_pro:news'], 6 => $lang['ads_pro:plugins']), 'location[' . ($k) . '][mode]" onchange="AddSubBlok(this, ' . ($k) . ');', $v['mode']);
			if ($v['mode'] == 3) $ttvars['vars']['location_list'] .= MakeDropDown($t_category_list, 'location[' . ($k) . '][id]', $v['id']);
			if ($v['mode'] == 4) $ttvars['vars']['location_list'] .= MakeDropDown($t_static_list, 'location[' . ($k) . '][id]', $v['id']);
			if ($v['mode'] == 5) $ttvars['vars']['location_list'] .= MakeDropDown($t_news_list, 'location[' . ($k) . '][id]', $v['id']);
			if ($v['mode'] == 6) $ttvars['vars']['location_list'] .= MakeDropDown($t_plugin_list, 'location[' . ($k) . '][id]', $v['id']);
			$ttvars['vars']['location_list'] .= MakeDropDown(array(0 => $lang['ads_pro:view'], 1 => $lang['ads_pro:not_view']), 'location[' . ($k) . '][view]', $v['view']);
			$ttvars['vars']['location_list'] .= '</td></tr>';
		}
		$ttvars['vars']['ads_blok'] = '';
		foreach ($mysql->select("select ads_blok from " . prefix . "_ads_pro where id=" . db_squote($id) . " limit 1") as $row) $ttvars['vars']['ads_blok'] = $row['ads_blok'];
	}
	$ttvars['vars']['type_list'] = MakeDropDown(array(0 => $lang['ads_pro:html'], 1 => $lang['ads_pro:php'], 2 => $lang['ads_pro:text']), 'type', ($id) ? $var['type'] : 0);
	$ttvars['vars']['state_list'] = MakeDropDown(array(0 => $lang['ads_pro:label_off'], 1 => $lang['ads_pro:label_on'], 2 => $lang['ads_pro:label_sched']), 'state', ($id) ? $var['state'] : 0);
	$ttvars['regx']['/\[add\](.*?)\[\/add\]/si'] = ($id) ? '' : '$1';
	$ttvars['regx']['/\[edit\](.*?)\[\/edit\]/si'] = ($id) ? '$1' : '';
	$tpl->template('conf.add_edit.form', $tpath['conf.add_edit.form']);
	$tpl->vars('conf.add_edit.form', $ttvars);
	$tvars['vars']['entries'] = $tpl->show('conf.add_edit.form');
	$tvars['vars']['action'] = $id ? $lang['ads_pro:button_edit'] : $lang['ads_pro:button_add'];
	$tpl->template('conf.main', $tpath['conf.main']);
	$tpl->vars('conf.main', $tvars);
	print $tpl->show('conf.main');
}

function add_submit() {

	global $mysql, $parse, $lang;
	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
	$name = $parse->translit(trim(secure_html(convert($_REQUEST['name']))));
	if (!$name) $name = 0;
	$description = trim(secure_html(convert($_REQUEST['description'])));
	$type = intval($_REQUEST['type']);
	$location = $_REQUEST['location'];
	array_walk_recursive($location, intval);
	$state = intval($_REQUEST['state']);
	$start_view = GetTimeStamp(trim(secure_html(convert($_REQUEST['start_view']))));
	$end_view = GetTimeStamp(trim(secure_html(convert($_REQUEST['end_view']))));
	$ads_blok = $_REQUEST['ads_blok'];
	$var = pluginGetVariable('ads_pro', 'data');
	if (!$id) {
		$mysql->query("insert into " . prefix . "_ads_pro (ads_blok) values (" . db_squote($ads_blok) . ")");
		$id = intval($mysql->lastid("ads_pro"));
	} else {
		$t_update = $mysql->query("update " . prefix . "_ads_pro set ads_blok=" . db_squote($ads_blok) . " where id=" . db_squote($id) . " limit 1");
		$t_name = 0;
		$if_brek = false;
		foreach ($var as $k => $v) {
			foreach ($v as $kk => $vv) {
				if ($id == $kk) {
					$t_name = $k;
					$if_brek = true;
					break;
				}
			}
			if ($if_brek)
				break;
		}
		if ($t_name !== $name) {
			unset($var[$t_name][$id]);
			if (!count($var[$t_name])) unset($var[$t_name]);
		}
	}
	$var[$name][$id]['description'] = $description;
	$var[$name][$id]['type'] = $type;
	$var[$name][$id]['state'] = $state;
	$var[$name][$id]['start_view'] = $start_view;
	$var[$name][$id]['end_view'] = $end_view;
	$var[$name][$id]['location'] = $location;
	pluginSetVariable('ads_pro', 'data', $var);
	pluginsSaveConfig();
	clear_cash();
	showlist();
}

function move($action) {

	$id = intval($_REQUEST['id']);
	$var = pluginGetVariable('ads_pro', 'data');
	$keys = array_keys($var);
	$values = array_values($var);
	$count = count($keys);
	$if_break = false;
	for ($i = 0; $i < $count; $i++) {
		$sub_keys = array_keys($var[$keys[$i]]);
		$sub_values = array_values($var[$keys[$i]]);
		$sub_count = count($sub_keys);
		for ($j = 0; $j < $sub_count; $j++) {
			if ($id == $sub_keys[$j]) {
				$if_break = true;
				if ($action == 'up') {
					if ($j == 0 && $i != 0) {
						array_splice($keys, $i - 1, 2, array($keys[$i], $keys[$i - 1]));
						array_splice($values, $i - 1, 2, array($values[$i], $values[$i - 1]));
						$var = array_combine($keys, $values);
						break;
					} else if ($j != 0) {
						array_splice($sub_keys, $j - 1, 2, array($sub_keys[$j], $sub_keys[$j - 1]));
						array_splice($sub_values, $j - 1, 2, array($sub_values[$j], $sub_values[$j - 1]));
						$var[$keys[$i]] = array_combine($sub_keys, $sub_values);
						break;
					}
				} else if ($action == 'down') {
					if ($j == $sub_count - 1 && $i != $count - 1) {
						array_splice($keys, $i, 2, array($keys[$i + 1], $keys[$i]));
						array_splice($values, $i, 2, array($values[$i + 1], $values[$i]));
						$var = array_combine($keys, $values);
						break;
					} else if ($j != $sub_count - 1) {
						array_splice($sub_keys, $j, 2, array($sub_keys[$j + 1], $sub_keys[$j]));
						array_splice($sub_values, $j, 2, array($sub_values[$j + 1], $sub_values[$j]));
						$var[$keys[$i]] = array_combine($sub_keys, $sub_values);
						break;
					}
				}
			}
		}
		if ($if_break)
			break;
	}
	pluginSetVariable('ads_pro', 'data', $var);
	pluginsSaveConfig();
	showlist();
}

function GetTimeStamp($date) {

	$stamp = explode(' ', $date);
	$tdate = null;
	$ttime = null;
	switch (count($stamp)) {
		case 1:
			$tdate = explode('.', $stamp[0]);
			break;
		case 2:
			$tdate = explode('.', $stamp[0]);
			$ttime = explode(':', $stamp[1]);
			break;
		default:
			return null;
			break;
	}
	if (!is_array($tdate) && count($tdate) != 3)
		$tdate = null;
	if (!is_array($ttime) && count($ttime) != 2)
		$ttime = null;
	if ($tdate === null && $ttime === null)
		return null;
	if ($tdate === null) $tdate = array(0, 0, 0);
	if ($ttime === null) $ttime = array(0, 0);
	$tstamp = mktime($ttime[0], $ttime[1], 0, $tdate[1], $tdate[2], $tdate[0]);
	if ($tstamp < 0) return null;

	return $tstamp;
}

function delete() {

	global $mysql, $lang;
	$id = intval($_REQUEST['id']);
	$mysql->query("delete from " . prefix . "_ads_pro where id=" . db_squote($id));
	$var = pluginGetVariable('ads_pro', 'data');
	$if_brek = false;
	$name = '';
	$title = '';
	foreach ($var as $k => $v) {
		foreach ($v as $kk => $vv) {
			if ($id == $kk) {
				$title = $vv['description'];
				$name = $k;
				$if_brek = true;
				break;
			}
		}
		if ($if_brek)
			break;
	}
	unset($var[$name][$id]);
	if (!count($var[$name])) unset($var[$name]);
	pluginSetVariable('ads_pro', 'data', $var);
	pluginsSaveConfig();
	msg(array('type' => 'info', 'info' => sprintf($lang['ads_pro:info_delete'], $title)));
	clear_cash();
	showlist();
}

function clear_cash() {

	if (($dir = get_plugcache_dir('ads_pro'))) {
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				if ($file == "." || $file == "..")
					continue;
				unlink($dir . $file);
			}
			closedir($handle);
		}
	}
}