<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuration file for plugin
//
// Preload config file
plugins_load_config();
loadPluginLang('voting', 'config', '', '', ':');
// Fill configuration parameters
$skList = array();
if ($skDir = opendir(extras_dir . '/voting/tpl/skins')) {
	while ($skFile = readdir($skDir)) {
		if (!preg_match('/^\./', $skFile)) {
			$skList[$skFile] = $skFile;
		}
	}
	closedir($skDir);
}
$cfg = array();
array_push($cfg, array('descr' => $lang['voting:desc']));
array_push($cfg, array('name' => 'rotate', 'title' => $lang['voting:rotate'], 'descr' => $lang['voting:rotate#desc'], 'type' => 'select', 'values' => array('1' => $lang['yesa'], '0' => $lang['noa']), 'value' => pluginGetVariable('voting', 'rotate')));
array_push($cfg, array('name' => 'active', 'title' => $lang['voting:active'], 'descr' => $lang['voting:active#desc'], 'type' => 'select', 'values' => (array('0' => ' -- ') + mkVoteList()), 'value' => pluginGetVariable('voting', 'active')));
array_push($cfg, array('name' => 'secure', 'title' => $lang['voting:secure'], 'descr' => $lang['voting:secure#desc'], 'type' => 'select', 'values' => array('1' => 'БД', '0' => 'Cookie'), 'value' => pluginGetVariable('voting', 'secure')));
array_push($cfg, array('name' => 'localsource', 'title' => $lang['voting:localsource'], 'descr' => $lang['voting:localsource#desc'], 'type' => 'select', 'values' => array('0' => $lang['voting:lsrc.site'], '1' => $lang['voting:lsrc.plugin']), 'value' => intval(pluginGetVariable($plugin, 'localsource'))));
array_push($cfg, array('name' => 'skin', 'title' => $lang['voting:skin'], 'descr' => $lang['voting:skin#desc'], 'type' => 'select', 'values' => $skList, 'value' => pluginGetVariable('voting', 'skin')));
array_push($cfg, array('name' => 'vpp', 'title' => $lang['voting:vpp'], 'descr' => $lang['voting:vpp#desc'], 'type' => 'input', 'value' => intval(pluginGetVariable('voting', 'vpp'))));
function mkVoteList() {

	global $mysql;
	$res = array();
	foreach ($mysql->select("select * from " . prefix . "_vote where active = 1") as $row) {
		$res[$row['id']] = $row['name'];
	}

	return $res;
}

function mkVoteSkinList() {

	$dir = opendir();
}

if ($_REQUEST['action'] == 'newvote') {
	$mysql->query("insert into " . prefix . "_vote (name) values (" . db_squote('** новое голосование **') . ")");
	print "Новый опрос создан. <a href='$PHP_SELF?mod=extra-config&plugin=voting'>переход к редактированию</a>";
} else if ($_REQUEST['action'] == 'delvote') {
	$voteid = intval($_REQUEST['id']);
	if ($row = $mysql->record("select * from " . prefix . "_vote where id = $voteid")) {
		$mysql->query("delete from " . prefix . "_voteline where voteid = $voteid");
		$mysql->query("delete from " . prefix . "_vote where id = $voteid");
		print "Опрос удалён. ";
	} else {
		print "Такого опроса не существует. ";
	}
	print "<a href='$PHP_SELF?mod=extra-config&plugin=voting'>переход к редактированию</a>";
} else if ($_REQUEST['action'] == 'commit') {
	// Let's look what do we need to do.
	// First - process voteline updates/deletes
	foreach ($_REQUEST as $rq => $rv) {
		if (preg_match('/^vename_(\d+)$/', $rq, $match)) {
			$lid = $match[1];
			$vecnt = (strlen($_REQUEST['vecount_' . $lid])) ? ", cnt = " . intval($_REQUEST['vecount_' . $lid]) : '';
			$vename = $_REQUEST['vename_' . $lid];
			$veactive = intval($_REQUEST['veactive_' . $lid]);
			$vedel = $_REQUEST['vedel_' . $lid];
			if ($vedel) {
				$mysql->query("delete from " . prefix . "_voteline where id = $lid");
			} else {
				$mysql->query("update " . prefix . "_voteline set name = " . db_squote($vename) . " $vecnt, active = $veactive where id = $lid");
			}
		}
	}
	// Next, process voteline inserts
	foreach ($_REQUEST as $rq => $rv) {
		if (preg_match('/^viname_(\d+)_(\d+)$/', $rq, $match)) {
			$vid = $match[1];
			$lid = $vid . '_' . $match[2];
			$vecnt = intval($_REQUEST['vicount_' . $lid]);
			$vename = $_REQUEST['viname_' . $lid];
			$veactive = intval($_REQUEST['viactive_' . $lid]);
			$vedel = $_REQUEST['videl_' . $lid];
			if (!$vedel) {
				$mysql->query("insert into " . prefix . "_voteline(voteid, name, cnt, active) values($vid," . db_squote($vename) . ",$vecnt, $veactive)");
			}
		}
	}
	// Next, process vote updates
	foreach ($_REQUEST as $rq => $rv) {
		if (preg_match('/^vname_(\d+)$/', $rq, $match)) {
			$lid = $match[1];
			$vname = $_REQUEST['vname_' . $lid];
			$vdescr = $_REQUEST['vdescr_' . $lid];
			$vactive = intval($_REQUEST['vactive_' . $lid]);
			$vclosed = intval($_REQUEST['vclosed_' . $lid]);
			$vregonly = intval($_REQUEST['vregonly_' . $lid]);
			$mysql->query("update " . prefix . "_vote set name=" . db_squote($vname) . ", descr=" . db_squote($vdescr) . ", active=$vactive, closed=$vclosed, regonly=$vregonly where id =  $lid");
		}
	}
	// Next, process inserts
	//var_dump($_REQUEST);
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	array_push($cfg, array('type' => 'flat', 'input' => '<tr><td colspan=2 style="background-color: #FFFF00;font : normal 14px verdana, sans-serif; padding: 4px;">' . $lang['voting:hdr.votelist'] . '</td></tr>'));
	array_push($cfg, array('type' => 'flat', 'input' => '<tr><td align=left style="padding-left: 14px;"><input type=button value="' . $lang['voting:button.create'] . '" style="width:343px;" onclick="document.location=' . "'" . $PHP_SELF . "?mod=extra-config&plugin=voting&action=newvote'" . ';"/></td><td align=right style="padding-top: 8px; padding-bottom: 8px;"> <input type=button value="' . $lang['voting:button.show_all'] . '" style="width:170px;" onclick="showHide(1);"/> <input type=button value="' . $lang['voting:button.hide_all'] . '" style="width:170px;" onclick="showHide(0);"/>'));
	$tpl->template('sheader', extras_dir . '/voting/tpl');
	$tpl->vars('sheader', array());
	array_push($cfg, array('type' => 'flat', 'input' => $tpl->show('sheader')));
	$tpl->template('vote', extras_dir . '/voting/tpl');
	$tpl->template('ventry', extras_dir . '/voting/tpl');
	$flag_nonactive = 0;
	$flag_active = 0;
	$flag_closed = 0;
	foreach ($mysql->select("select * from " . prefix . "_vote order by active,closed") as $vrow) {
		$cfgX = array();
		if (!$vrow['active'] && !$vrow['closed'] && !$flag_nonactive) {
			$flag_nonactive = 1;
			array_push($cfg, array('type' => 'flat', 'input' => '<tr><td colspan=2 style="background-color: #AAAAAA;font : normal 10px verdana, sans-serif; padding: 4px;"><b>' . $lang['voting:hdr.inactive'] . '</b></td></tr>'));
		}
		if ($vrow['active'] && !$vrow['closed'] && !$flag_active) {
			$flag_active = 1;
			array_push($cfg, array('type' => 'flat', 'input' => '<tr><td colspan=2 style="background-color: #99BB88;font : normal 10px verdana, sans-serif; padding: 4px;"><b>' . $lang['voting:hdr.active'] . '</b></td></tr>'));
		}
		if ($vrow['active'] && $vrow['closed'] && !$flag_closed) {
			$flag_closed = 1;
			array_push($cfg, array('type' => 'flat', 'input' => '<tr><td colspan=2 style="background-color: #77BB44;font : normal 10px verdana, sans-serif; padding: 4px;"><b>' . $lang['voting:hdr.closed'] . '</b></td></tr>'));
		}
		$ll = '';
		$allcnt = 0;
		foreach ($mysql->select("select * from " . prefix . "_voteline where voteid=" . $vrow['id'] . " order by id") as $row) {
			$tvars['vars'] = array(
				'name'     => secure_html($row['name']),
				'count'    => $row['cnt'],
				'id'       => $row['id'],
				'veactive' => $row['active'] ? 'checked' : ''
			);
			$allcnt += $row['cnt'];
			$tpl->vars('ventry', $tvars);
			$ll .= $tpl->show('ventry');
		}
		$tvars['vars'] = array(
			'entries'  => $ll,
			'name'     => secure_html($vrow['name']),
			'allcnt'   => $allcnt,
			'descr'    => secure_html($vrow['descr']),
			'voteid'   => $vrow['id'],
			'vactive'  => $vrow['active'] ? 'checked' : '',
			'vclosed'  => $vrow['closed'] ? 'checked' : '',
			'vregonly' => $vrow['regonly'] ? 'checked' : '',
			'fregonly' => $vrow['regonly'] ? '[<b>' . $lang['voting:hdr.regflag'] . '</b>]' : '',
			'php_self' => $PHP_SELF
		);
		$tpl->vars('vote', $tvars);
		array_push($cfgX, array('type' => 'flat', 'input' => $tpl->show('vote')));
		array_push($cfg, array('mode' => 'group', 'title' => '[ ' . $lang['voting:hdr.voting'] . ': <b><font color=blue>' . $vrow['name'] . '</font></b> ]', 'entries' => $cfgX));
	}
	generate_config_page($plugin, $cfg);
}
