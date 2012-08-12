<?php

/*
 * Plugin "Private message" for NextGeneration CMS (http://ngcms.ru/)
 * Copyright (C) 2010 Alexey N. Zhukov (http://digitalplace.ru), Alexey Zinchenko
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

 // Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

class PMCoreFilter extends CoreFilter {
	function showUserMenu(&$tVars) {
		global  $mysql, $userROW, $lang;

		$count = array();
		foreach ($mysql->select("SELECT COUNT(viewed) AS cnt, viewed FROM ".prefix."_pm WHERE (to_id = ".db_squote($userROW['id'])." AND folder='inbox') GROUP BY viewed") as $row){
			$count[$row['viewed']] = $row['cnt'];
		}

		$tVars['p']['pm']['new']	= intval($count[0]);
		$tVars['p']['pm']['total']	= intval($count[0]) + intval($count[1]);
		$tVars['p']['pm']['flags']['hasNew']	= intval($count[0])?1:0;
		$tVars['p']['pm']['link']	= generatePluginLink('pm', null);
	}
}

function pm_inbox (){
	global $mysql, $config, $lang, $userROW, $tpl, $template;

	$tpath = locatePluginTemplates(array('entries', 'inbox'), 'pm', intval(extra_get_param('pm', 'localsource')));

	foreach($mysql->select("SELECT pm.*, u.id as uid, u.name as uname FROM ".prefix."_pm pm LEFT JOIN ".uprefix."_users u ON pm.from_id=u.id WHERE pm.to_id = ".db_squote($userROW['id'])." AND folder='inbox' ORDER BY viewed ASC, date DESC") as $row)
	{
		$author = '';
		if ($row['from_id'] && $row['uid']) {
			$alink = checkLinkAvailable('uprofile', 'show')?
						generateLink('uprofile', 'show', array('name' => $row['uname'], 'id' => $row['uid'])):
						generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('name' => $row['uname'], 'id' => $row['uid']));
			$author = '<a href="'.$alink.'">'.$row['uname'].'</a>';
		} else if ($row['from_id']) {
			$author = $lang['pm:udeleted'];
		} else {
			$author = $lang['pm:messaging'];
		}

		$tvars['vars'] = array(
			'php_self'	=>	$PHP_SELF,
			'pmid'		=>	$row['id'],
			'pmdate'	=>	LangDate('j.m.Y - H:i', $row['date']),
			'subject'	=>	$row['subject'],
			'link'		=>	$author,
			'viewed'	=>	$row['viewed'] = ($row['viewed'] == 1 ? '<img src=/engine/plugins/pm/img/viewed.yes.gif>' : '<img src=/engine/plugins/pm/img/viewed.no.gif>')
		);
		$tpl -> template('entries', $tpath['entries']);
		$tpl -> vars('entries', $tvars);
		$entries .= $tpl -> show('entries');
	}

	$tpl -> template('inbox', $tpath['inbox']);
	$tvars['vars'] = array(
		'php_self'	=>	$PHP_SELF,
		'entries'	=>	$entries,
	);
	$tpl -> vars('inbox', $tvars);
	$template['vars']['mainblock'] = $tpl -> show('inbox');
}

function pm_outbox (){
	global $mysql, $lang, $userROW, $tpl, $template;

	$tpath = locatePluginTemplates(array('outbox_entries', 'outbox'), 'pm', intval(extra_get_param('pm', 'localsource')));

	foreach($mysql->select("SELECT pm.*, u.id as uid, u.name as uname FROM ".prefix."_pm pm LEFT JOIN ".uprefix."_users u ON pm.to_id=u.id WHERE pm.from_id = ".db_squote($userROW['id'])." AND folder='outbox' ORDER BY date DESC") as $row)
	{
		$author = '';
		if ($row['to_id'] && $row['uid']) {
			$alink = checkLinkAvailable('uprofile', 'show')?
						generateLink('uprofile', 'show', array('name' => $row['uname'], 'id' => $row['uid'])):
						generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('name' => $row['uname'], 'id' => $row['uid']));
			$author = '<a href="'.$alink.'">'.$row['uname'].'</a>';
		} else if ($row['from_id']) {
			$author = $lang['pm:udeleted'];
		} else {
			$author = $lang['pm:messaging'];
		}

		$tvars['vars'] = array(
			'php_self'	=>	$PHP_SELF,
			'pmid'		=>	$row['id'],
			'pmdate'	=>	LangDate('j.m.Y - H:i', $row['date']),
			'title'		=>	$row['subject'],
			'link'		=>	$author
		);
		$tpl -> template('outbox_entries', $tpath['outbox_entries']);
		$tpl -> vars('outbox_entries', $tvars);
		$entries .= $tpl -> show('outbox_entries');
	}

	$tpl -> template('outbox', $tpath['outbox']);
	$tvars['vars'] = array(
		'php_self'	=>	$PHP_SELF,
		'entries'	=>	$entries,
	);
	$tpl -> vars('outbox', $tvars);
	$template['vars']['mainblock'] = $tpl -> show('outbox');
}

function pm_read(){
	global $mysql, $config, $lang, $userROW, $tpl, $mod, $parse, $template;

	$tpath = locatePluginTemplates(array('read'), 'pm', intval(extra_get_param('pm', 'localsource')));

	$pmid = $_REQUEST['pmid'];

	if ($row = $mysql->record("SELECT * FROM ".prefix."_pm WHERE id = ".db_squote($pmid)." AND ((`from_id`=".db_squote($userROW['id'])." AND `folder`='outbox') OR (`to_id`=".db_squote($userROW['id']).") AND `folder`='inbox')")) {
		$tpl -> template('read', $tpath['read']);
		$tvars['vars'] = array(
			'php_self'		=>	$PHP_SELF,
			'pmid'			=>	$row['id'],
			'subject'		=>	$row['subject'],
			'from'			=>	$row['from_id'],
			'location'		=>	$row['folder'],
			'content'		=>	$parse->htmlformatter($parse->smilies($parse->bbcodes($row['message'])))
		);

		if($row['folder'] == 'inbox')
		$tvars['regx']['/\[if-inbox\](.*?)\[\/if-inbox\]/si'] = '$1';
		else
		$tvars['regx']['/\[if-inbox\](.*?)\[\/if-inbox\]/si'] = '';

		$tpl -> template('read', $tpath['read']);
		$tpl -> vars('read', $tvars);
		$template['vars']['mainblock'] = $tpl -> show('read');
		if ((!$row['viewed'])&&($row['to_id'] == $userROW['id'])) {
			$mysql->query("update ".uprefix."_pm set `viewed` = '1' WHERE `id` = ".db_squote($row['id']));
		}
	} else {
		msg(array("type" => "error", "text" => $lang['pm:msge_bad'].'<META HTTP-EQUIV="refresh" CONTENT="2;URL=/plugin/pm/">'));
	}
}

function pm_delete(){
	global $mysql, $config, $lang, $userROW, $tpl;

	$selected_pm = $_REQUEST['selected_pm'];
	$location	 = $_REQUEST['location'];
	$pmid 		 = $_REQUEST['pmid'];

	if(!$location) $location = "inbox";

	if(!$pmid){

		if(!$selected_pm) {
			msg(array("type" => "error", "text" => $lang['pm:msge_select'].'<META HTTP-EQUIV="refresh" CONTENT="2;URL=/plugin/pm/">'));
			return;
		}

		foreach ($selected_pm as $id) {
			$mysql->query("DELETE FROM ".prefix."_pm WHERE `id`=".db_squote($id)." AND ((`from_id`=".db_squote($userROW['id'])." AND `folder`='outbox') OR (`to_id`=".db_squote($userROW['id']).") AND `folder`='inbox')");
		}

		msg(array("text" => $lang['pm:msgo_deleted'].'<META HTTP-EQUIV="refresh" CONTENT="2;URL=/plugin/pm/">'));
	}
	else {
		$row = $mysql->select("SELECT id FROM ".prefix."_pm WHERE `id`=".db_squote($pmid)." AND ((`from_id`=".db_squote($userROW['id'])." AND `folder`='outbox') OR (`to_id`=".db_squote($userROW['id']).") AND `folder`='inbox')");
		if($row){
		$mysql->query("DELETE FROM ".prefix."_pm WHERE `id`=".db_squote($pmid)." AND (from_id=".db_squote($userROW['id'])." OR to_id=".db_squote($userROW['id']).")");
		msg(array("text" => $lang['pm:msgo_deleted_one'].'<META HTTP-EQUIV="refresh" CONTENT="2;URL=/plugin/pm/?action='.$location.'">'));
		}
		else
		msg(array("type" => "error", "text" => $lang['pm:msge_bad_del'].'<META HTTP-EQUIV="refresh" CONTENT="2;URL=/plugin/pm/?action='.$location.'">'));

	}
}

function pm_write(){
	global $config, $lang, $tpl, $template;

	$tpath = locatePluginTemplates(array('write'), 'pm', intval(extra_get_param('pm', 'localsource')));

	$tpl -> template('write', $tpath['write']);
	$tvars['vars'] = array(
		'php_self'	=>	$PHP_SELF,
		'username'	=>	trim($_REQUEST['name']),
		'quicktags'	=>	BBCodes()
	);
	$tvars['vars']['smilies'] = ($config['use_smilies'] == "1") ? InsertSmilies("content", 10) : '';

	$tpl -> vars('write', $tvars);
	$template['vars']['mainblock'] = $tpl -> show('write');
}

function pm_send() {
	global $mysql, $config, $lang, $userROW;

	$time = time() + ($config['date_adjust'] * 60);

	$sendto  = trim($_REQUEST['sendto']);
	$title   = secure_html($_REQUEST['title']);
	$content = $_REQUEST['content'];
	$save = $_REQUEST['saveoutbox'];

	if (!$title || strlen($title) > "50") {
		msg(array("type" => "error", "text" => $lang['pm:msge_title'], "info" => $lang['pm:msgi_title'].'<br /> <br /><center><a href="javascript:history.back()"><img src="/engine/plugins/pm/img/arrow_left.png"></a></center>'));
		return;
	}
	if (!$content || strlen($content) > "3000") {
		msg(array("type" => "error", "text" => $lang['pm:msge_content'], "info" => $lang['pm:msgi_content'].'<br /> <br /><center><a href="javascript:history.back()"><img src="/engine/plugins/pm/img/arrow_left.png"></a></center>'));
		return;
	}

	if ($sendto && ($torow = $mysql->record("select * from ".uprefix."_users where ".(is_numeric($sendto)?"id = ".db_squote($sendto):"name = ".db_squote($sendto))))) {
		$content = secure_html(trim($content));
		$mysql->query("insert into ".prefix."_pm (from_id, to_id, date, subject, message, folder) VALUES (".db_squote($userROW['id']).", ".db_squote($torow['id']).", ".db_squote($time).", ".db_squote($title).", ".db_squote($content).", 'inbox')");

		if($save)
		$mysql->query("insert into ".prefix."_pm (from_id, to_id, date, subject, message, folder) VALUES (".db_squote($userROW['id']).", ".db_squote($torow['id']).", ".db_squote($time).", ".db_squote($title).", ".db_squote($content).", 'outbox')");

		msg(array("text" => $lang['pm:msgo_sent'].'<META HTTP-EQUIV="refresh" CONTENT="2;URL=/plugin/pm/">'));
	} else {
		msg(array("type" => "error", "text" => $lang['pm:msge_nouser'], "info" => $lang['pm:msgi_nouser'].'<br /> <br /><center><a href="javascript:history.back()"><img src="/engine/plugins/pm/img/arrow_left.png"></a></center>'	));
	}
}

function pm_reply(){
	global $mysql, $config, $lang, $userROW, $tpl, $parse, $template;

	$tpath = locatePluginTemplates(array('reply'), 'pm', intval(extra_get_param('pm', 'localsource')));

	$pmid = $_REQUEST['pmid'];
	$save = $_REQUEST['saveoutbox'];

	if ($row = $mysql->record("select * from ".prefix."_pm where id = ".db_squote($pmid)."and (to_id = ".db_squote($userROW['id'])." or from_id=".db_squote($userROW['id']).")")) {

		if($row['folder'] == 'outbox'){
			msg(array("type" => "error", "text" => $lang['pm:msge_notreply'].'<br /> <br /><center><a href="javascript:history.back()"><img src="/engine/plugins/pm/img/arrow_left.png"></a></center>'));
			return 0;
		}

		if (!$row['from_id']) {
			msg(array("type" => "error", "text" => $lang['pm:msge_reply'].'<br /> <br /><center><a href="javascript:history.back()"><img src="/engine/plugins/pm/img/arrow_left.png"></a></center>'));
			return;
		}

		$tpl -> template('reply', $tpath['reply']);
		$tvars['vars'] = array(
			'php_self'	=>	$PHP_SELF,
			'pmid'		=>	$row['id'],
			'title'		=>	'Re:'.$row['subject'],
			'sendto'	=>	$row['from_id'],
			'quicktags'	=>	BBCodes()
		);
		$tvars['vars']['smilies'] = ($config['use_smilies'] == "1") ? InsertSmilies("content", 10) : '';
		exec_acts('pm_reply');
		$tpl -> vars('reply', $tvars);
		$template['vars']['mainblock'] = $tpl -> show('reply');
	} else {
		msg(array("type" => "error", "text" => $lang['pm:msge_bad'].'<br /> <br /><center><a href="javascript:history.back()"><img src="/engine/plugins/pm/img/arrow_left.png"></a></center>'));
	}
}

function pm(){
	global $userROW, $template, $lang, $SYSTEM_FLAGS;

	$SYSTEM_FLAGS['info']['title']['group'] = $lang['pm:pm'];

	if(!$userROW['id']){
		msg(array("type" => "info", "info" => $lang['pm:err.noAuthorization']));;
		return 1;
	}

	$tpath = locatePluginTemplates(array(':pm.css'), 'pm', intval(extra_get_param('pm', 'localsource')));
	register_stylesheet($tpath['url::pm.css'].'/pm.css');

	switch($_REQUEST['action']){
		case "read"   : pm_read();   break;
		case "reply"  : pm_reply();  break;
		case "send"   : pm_send();   break;
		case "write"  : pm_write();  break;
		case "delete" : pm_delete(); break;
		case "outbox" : pm_outbox(); break;
		default       : pm_inbox();
	}

	return 0;
}

register_filter('core.userMenu', 'pm', new PMCoreFilter);
register_plugin_page('pm', '', 'pm', 0);
loadPluginLang('pm', 'main', '', '', ':');




