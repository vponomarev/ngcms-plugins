<?php
/*
 * Plugin "Private message" for NextGeneration CMS (http://ngcms.ru/)
 * Copyright (C) 2010-2011 Alexey N. Zhukov (http://digitalplace.ru), Alexey Zinchenko
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
register_plugin_page('pm', '', 'pm', 0);
loadPluginLang('pm', 'main', '', '', ':');
add_act('usermenu', 'new_pm');
LoadPluginLibrary('pm', 'lib');
define('INBOX_LINK', generatePluginLink('pm', null, ($_GET['location'] ? array('action' => $_GET['location']) : array())));

/* 
  fill variables in usermenu.tpl
  + {{ p.pm.pm_unread }} - кол-во новых входящих сообщений
  + {{ p.pm.pm_all }} - общее кол-во входящих сообщений
  + {{ p.pm.link }} - URL на страницу со входящими сообщениями
*/

class PMCoreFilter extends CoreFilter {

	function showUserMenu(&$tVars) {

		global $mysql, $userROW, $lang;
		if (!$userROW['id']) return 0;
		# if 'sync' then fill varaiables without SQL query from user's table
		if ($userROW['pm_sync']) {
			$tVars['p']['pm']['pm_unread'] = !$userROW['pm_unread'] ? $userROW['pm_unread'] : '<span id="newpm">' . $userROW['pm_unread'] . '</span>';
			$tVars['p']['pm']['pm_all'] = $userROW['pm_all'];
			$tVars['p']['pm']['link'] = generatePluginLink('pm', null);

			return;
		}
		$notViewed = 0;
		$viewed = 0;
		foreach ($mysql->select("SELECT COUNT(viewed) AS pm_viewed, viewed FROM " . prefix . "_pm WHERE (`to_id` = " . db_squote($userROW['id']) . " AND `folder` = 'inbox') GROUP BY viewed") as $row) {
			if ($row['viewed'] == '1') {
				$viewed = $row['pm_viewed'];
				continue;
			}
			if ($row['viewed'] == '0') {
				$notViewed = $row['pm_viewed'];
			}
		}
		$viewed += $notViewed;
		# update pm counters
		$mysql->query('UPDATE ' . uprefix . '_users SET `pm_sync` = 1, `pm_all` = ' . $viewed . ', `pm_unread` = ' . $notViewed . ' WHERE `id` = ' . db_squote($userROW['id']));
		if ($notViewed != 0) $notViewed = '<span id="newpm">' . $notViewed . '</span>';
		$tVars['p']['pm']['pm_unread'] = $notViewed;
		$tVars['p']['pm']['pm_all'] = $viewed;
		$tVars['p']['pm']['link'] = generatePluginLink('pm', null);
	}
}

# show inbox messages list
function pm_inbox() {

	global $mysql, $config, $lang, $userROW, $tpl, $template, $TemplateCache, $twig;
	$tpath = locatePluginTemplates(array('inbox'), 'pm', intval(pluginGetVariable('pm', 'localsource')));
	# messages per page
	$msg_per_page = intval(pluginGetVariable('pm', 'msg_per_page')) <= 0 ? 10 : intval(pluginGetVariable('pm', 'msg_per_page'));
	$page = 1;
	if (isset($_REQUEST['page'])) $page = intval($_REQUEST['page']);
	# range of messages
	$limit = 'LIMIT ' . ($page - 1) * $msg_per_page . ', ' . $msg_per_page;
	# count all inbox messages
	$countMsg = $mysql->result("SELECT COUNT(*) FROM " . prefix . "_pm WHERE `to_id` = " . db_squote($userROW['id']) . " AND `folder` = 'inbox'");
	foreach ($mysql->select("SELECT pm.*, u.id as uid, u.name as uname FROM " . prefix . "_pm pm LEFT JOIN " . uprefix . "_users u ON pm.from_id=u.id WHERE pm.to_id = " . db_squote($userROW['id']) . " AND folder='inbox' ORDER BY viewed ASC, date DESC " . $limit) as $row) {
		$author = '';
		if ($row['from_id'] && $row['uid']) {
			$alink = checkLinkAvailable('uprofile', 'show') ?
				generateLink('uprofile', 'show', array('name' => $row['uname'], 'id' => $row['uid'])) :
				generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('name' => $row['uname'], 'id' => $row['uid']));
			$author = '<a href="' . $alink . '">' . $row['uname'] . '</a>';
		} else if ($row['from_id']) {
			$author = $lang['pm:udeleted'];
		} else {
			$author = $lang['pm:messaging'];
		}
		$tEntries [] = array(
			'php_self' => $PHP_SELF,
			'pmid'     => $row['id'],
			'pmdate'   => $row['date'],
			'subject'  => $row['subject'],
			'link'     => $author,
			'viewed'   => $row['viewed'],
		);
	}
	$tVars = array(
		'php_self' => $PHP_SELF,
		'entries'  => $tEntries,
		'tpl_url'  => tpl_url,
	);
	$pages_count = ceil($countMsg / $msg_per_page);
	$paginationParams = array('pluginName' => 'pm', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false));
	# generate pagination if count of pages > 1
	if ($pages_count > 1) {
		templateLoadVariables(true);
		$navigations = $TemplateCache['site']['#variables']['navigation'];
		$tVars['pagination'] = generatePagination($page, 1, $pages_count, 9, $paginationParams, $navigations);
	} else $tVars['pagination'] = '';
	$xt = $twig->loadTemplate($tpath['inbox'] . 'inbox.tpl');
	$template['vars']['mainblock'] = $xt->render($tVars);
}

# show outbox messages list
function pm_outbox() {

	global $mysql, $lang, $userROW, $tpl, $template, $TemplateCache, $twig;
	$tpath = locatePluginTemplates(array('outbox'), 'pm', intval(pluginGetVariable('pm', 'localsource')));
	# messages per page
	$msg_per_page = intval(pluginGetVariable('pm', 'msg_per_page')) <= 0 ? 10 : intval(pluginGetVariable('pm', 'msg_per_page'));
	$page = 1;
	if (isset($_REQUEST['page'])) $page = intval($_REQUEST['page']);
	# range of messages
	$limit = 'LIMIT ' . ($page - 1) * $msg_per_page . ', ' . $msg_per_page;
	# count all outbox messages
	$countMsg = $mysql->result("SELECT COUNT(*) FROM " . prefix . "_pm WHERE `from_id` = " . db_squote($userROW['id']) . " AND `folder` = 'outbox'");
	foreach ($mysql->select("SELECT pm.*, u.id as uid, u.name as uname FROM " . prefix . "_pm pm LEFT JOIN " . uprefix . "_users u ON pm.to_id=u.id WHERE pm.from_id = " . db_squote($userROW['id']) . " AND folder='outbox' ORDER BY date DESC " . $limit) as $row) {
		$author = '';
		if ($row['to_id'] && $row['uid']) {
			$alink = checkLinkAvailable('uprofile', 'show') ?
				generateLink('uprofile', 'show', array('name' => $row['uname'], 'id' => $row['uid'])) :
				generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('name' => $row['uname'], 'id' => $row['uid']));
			$author = '<a href="' . $alink . '">' . $row['uname'] . '</a>';
		} else if ($row['to_id']) {
			$author = $lang['pm:udeleted'];
		} else {
			$author = $lang['pm:messaging'];
		}
		$tEntries [] = array(
			'php_self' => $PHP_SELF,
			'pmid'     => $row['id'],
			'pmdate'   => $row['date'],
			'subject'  => $row['subject'],
			'link'     => $author
		);
	}
	$tVars = array(
		'php_self' => $PHP_SELF,
		'entries'  => $tEntries,
		'tpl_url'  => tpl_url,
	);
	$pages_count = ceil($countMsg / $msg_per_page);
	$paginationParams = array('pluginName' => 'pm', 'params' => array(), 'xparams' => array('action' => 'outbox'), 'paginator' => array('page', 0, false));
	# generate pagination if count of pages > 1
	if ($pages_count > 1) {
		templateLoadVariables(true);
		$navigations = $TemplateCache['site']['#variables']['navigation'];
		$tVars['pagination'] = generatePagination($page, 1, $pages_count, 9, $paginationParams, $navigations);
	} else $tVars['pagination'] = '';
	$xt = $twig->loadTemplate($tpath['outbox'] . 'outbox.tpl');
	$template['vars']['mainblock'] = $xt->render($tVars);
}

# show read message form
function pm_read() {

	global $mysql, $config, $lang, $userROW, $tpl, $mod, $parse, $template, $twig;
	$tpath = locatePluginTemplates(array('read'), 'pm', intval(pluginGetVariable('pm', 'localsource')));
	$pmid = intval($_REQUEST['pmid']);
	if ($row = $mysql->record("SELECT * FROM " . prefix . "_pm WHERE id = " . db_squote($pmid) . " AND ((`from_id`=" . db_squote($userROW['id']) . " AND `folder`='outbox') OR (`to_id`=" . db_squote($userROW['id']) . ") AND `folder`='inbox')")) {
		$tVars = array(
			'php_self' => $PHP_SELF,
			'pmid'     => $row['id'],
			'subject'  => $row['subject'],
			'location' => $row['folder'],
			'pmdate'   => $row['date'],
			'content'  => $parse->htmlformatter($parse->smilies($parse->bbcodes($row['message'])))
		);
		$author = '';
		$authorID = $row['folder'] == 'inbox' ? $row['from_id'] : $row['to_id'];
		$row_user = $mysql->record("SELECT id, name FROM " . uprefix . "_users WHERE id = " . $authorID);
		if ($row_user['id']) {
			$alink = checkLinkAvailable('uprofile', 'show') ?
				generateLink('uprofile', 'show', array('name' => $row_user['name'], 'id' => $row_user['id'])) :
				generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('name' => $row_user['name'], 'id' => $row_user['id']));
			$author = '<a href="' . $alink . '">' . $row_user['name'] . '</a>';
		} else
			$author = $lang['pm:udeleted'];
		$tVars['author'] = $author;
		$tVars['ifinbox'] = ($row['folder'] == 'inbox') ? 1 : 0;
		$xt = $twig->loadTemplate($tpath['read'] . 'read.tpl');
		$template['vars']['mainblock'] = $xt->render($tVars);
		# update pm counters
		if ((!$row['viewed']) && ($row['to_id'] == $userROW['id']) && ($row['folder'] == 'inbox')) {
			$mysql->query("UPDATE " . prefix . "_pm SET `viewed` = '1' WHERE `id` = " . db_squote($row['id']));
			$mysql->query("UPDATE " . uprefix . "_users SET `pm_unread` = `pm_unread` - 1 WHERE `id` = " . db_squote($userROW['id']));
		}
	} else {
		msg(array("type" => "error", "text" => $lang['pm:msge_bad'] . str_replace('{url}', INBOX_LINK, $lang['pm:html_reload'])));
	}
}

# delete message(s)
function pm_delete() {

	global $mysql, $config, $lang, $userROW, $tpl;
	$selected_pm = $_REQUEST['selected_pm'];
	$pmid = intval($_REQUEST['pmid']);
	if (!$pmid) {
		if (!$selected_pm) {
			msg(array("type" => "error", "text" => $lang['pm:msge_select'] . str_replace('{url}', INBOX_LINK, $lang['pm:html_reload'])));

			return;
		}
		$mysql->query("DELETE FROM " . prefix . "_pm WHERE `id` IN (" . join(',', $selected_pm) . ") AND ((`from_id`=" . db_squote($userROW['id']) . " AND `folder`='outbox') OR (`to_id`=" . db_squote($userROW['id']) . ") AND `folder`='inbox')");
		$mysql->query("UPDATE " . uprefix . "_users SET `pm_sync` = 0 WHERE `id` = " . db_squote($userROW['id']));
		msg(array("text" => $lang['pm:msgo_deleted'] . str_replace('{url}', INBOX_LINK, $lang['pm:html_reload'])));
	} else {
		$row = $mysql->record("SELECT id, viewed, folder FROM " . prefix . "_pm WHERE `id`=" . db_squote($pmid) . " AND ((`from_id`=" . db_squote($userROW['id']) . " AND `folder`='outbox') OR (`to_id`=" . db_squote($userROW['id']) . ") AND `folder`='inbox')");
		if ($row) {
			$mysql->query("DELETE FROM " . prefix . "_pm WHERE `id`=" . db_squote($pmid));
			#update pm counter
			if ($row['folder'] == 'inbox') {
				if ($row['viewed'])
					$mysql->query("UPDATE " . uprefix . "_users SET `pm_all` = `pm_all` - 1 WHERE `id` = " . db_squote($userROW['id']));
				else
					$mysql->query("UPDATE " . uprefix . "_users SET `pm_all` = `pm_all` - 1, `pm_unread` = `pm_unread` - 1 WHERE `id` = " . db_squote($userROW['id']));
			}
			msg(array("text" => $lang['pm:msgo_deleted_one'] . str_replace('{url}', INBOX_LINK, $lang['pm:html_reload'])));
		} else
			msg(array("type" => "error", "text" => $lang['pm:msge_bad_del'] . str_replace('{url}', INBOX_LINK, $lang['pm:html_reload'])));
	}
}

# show write message form
function pm_write() {

	global $config, $lang, $tpl, $template, $twig;
	$tpath = locatePluginTemplates(array('write'), 'pm', intval(pluginGetVariable('pm', 'localsource')));
	$tVars = array(
		'php_self'  => $PHP_SELF,
		'username'  => trim($_REQUEST['name']),
		'quicktags' => BBCodes('pm_content')
	);
	$tVars['smilies'] = ($config['use_smilies'] == "1") ? InsertSmilies('', 10, 'pm_content') : '';
	$xt = $twig->loadTemplate($tpath['write'] . 'write.tpl');
	$template['vars']['mainblock'] = $xt->render($tVars);
}

# send message
function pm_send() {

	global $mysql, $config, $lang, $userROW;
	$pm = new pm();
	$status = $pm->sendMsg($_POST['to_username'], $userROW['id'], $_POST['title'], $_POST['content'], false, $_POST['saveoutbox']);
	# if all right
	if (!$status) {
		msg(array("text" => $lang['pm:msgo_sent'] . str_replace('{url}', INBOX_LINK, $lang['pm:html_reload'])));

		return 0;
	}
	# if some error
	switch ($status) {
		case -1:
			msg(array(
				"type" => "error",
				"text" => $lang['pm:msge_title'],
				"info" => str_replace('{length}', pluginGetVariable('pm', 'title_length'), $lang['pm:msgi_title']) .
					$lang['pm:html_back']
			));
			break;
		case -2:
			msg(array(
				"type" => "error",
				"text" => $lang['pm:msge_title'],
				"info" => $lang['pm:msgi_no_title'] .
					$lang['pm:html_back']
			));
			break;
		case -3:
			msg(array(
				"type" => "error",
				"text" => $lang['pm:msge_message'],
				"info" => str_replace('{length}', pluginGetVariable('pm', 'message_length'), $lang['pm:msgi_message']) .
					$lang['pm:html_back']
			));
			break;
		case -4:
			msg(array(
				"type" => "error",
				"text" => $lang['pm:msge_message'],
				"info" => $lang['pm:msgi_no_message'] .
					$lang['pm:html_back']
			));
			break;
		case -5:
			msg(array(
				"type" => "error",
				"text" => $lang['pm:msge_nouser'],
				"info" => $lang['pm:msgi_nouser'] .
					$lang['pm:html_back']
			));
			break;
	}
}

# show reply form
function pm_reply() {

	global $mysql, $config, $lang, $userROW, $tpl, $parse, $template, $twig;
	$tpath = locatePluginTemplates(array('reply'), 'pm', intval(pluginGetVariable('pm', 'localsource')));
	$pmid = $_REQUEST['pmid'];
	$save = $_REQUEST['saveoutbox'];
	if ($row = $mysql->record("SELECT * FROM " . prefix . "_pm WHERE id = " . db_squote($pmid) . " AND (to_id = " . db_squote($userROW['id']) . " OR from_id=" . db_squote($userROW['id']) . ")")) {
		if ($row['folder'] == 'outbox') {
			msg(array("type" => "error", "text" => $lang['pm:msge_notreply'] . $lang['pm:html_back']));

			return 0;
		}
		if (!$row['from_id']) {
			msg(array("type" => "error", "text" => $lang['pm:msge_reply'] . $lang['pm:html_back']));

			return;
		}
		$tVars = array(
			'php_self'    => $PHP_SELF,
			'pmid'        => $row['id'],
			'title'       => 'Re:' . $row['subject'],
			'to_username' => $row['from_id'],
			'quicktags'   => BBCodes('pm_content')
		);
		$tVars['smilies'] = ($config['use_smilies'] == "1") ? InsertSmilies('', 10, 'pm_content') : '';
		$xt = $twig->loadTemplate($tpath['reply'] . 'reply.tpl');
		$template['vars']['mainblock'] = $xt->render($tVars);
	} else {
		msg(array("type" => "error", "text" => $lang['pm:msge_bad'] . $lang['pm:html_back']));
	}
}

# user settings
function pm_set() {

	global $userROW, $template, $tpl, $mysql, $twig;
	$checked = $userROW['pm_email'];
	if (isset($_POST['check'])) {
		if ($_POST['email']) {
			$mysql->query('UPDATE ' . uprefix . '_users SET pm_email = 1 WHERE id = ' . $userROW['id']);
			$checked = true;
		} else {
			$mysql->query('UPDATE ' . uprefix . '_users SET pm_email = 0 WHERE id = ' . $userROW['id']);
			$checked = false;
		}
	}
	$tpath = locatePluginTemplates(array('set'), 'pm', intval(pluginGetVariable('pm', 'localsource')));
	$tVars = array(
		'php_self' => $PHP_SELF,
		'checked'  => $checked ? 'checked="checked"' : ''
	);
	$xt = $twig->loadTemplate($tpath['set'] . 'set.tpl');
	$template['vars']['mainblock'] = $xt->render($tVars);
}

function pm() {

	global $userROW, $template, $lang, $SYSTEM_FLAGS;
	$SYSTEM_FLAGS['info']['title']['group'] = $lang['pm:pm'];
	if (!$userROW['id']) {
		msg(array("type" => "info", "info" => $lang['pm:err.noAuthorization']));;

		return 1;
	}
	$tpath = locatePluginTemplates(array(':pm.css'), 'pm', intval(pluginGetVariable('pm', 'localsource')));
	register_stylesheet($tpath['url::pm.css'] . '/pm.css');
	switch ($_REQUEST['action']) {
		case "read"   :
			pm_read();
			break;
		case "reply"  :
			pm_reply();
			break;
		case "send"   :
			pm_send();
			break;
		case "write"  :
			pm_write();
			break;
		case "delete" :
			pm_delete();
			break;
		case "outbox" :
			pm_outbox();
			break;
		case "set"      :
			pm_set();
			break;
		default       :
			pm_inbox();
	}

	return 0;
}

register_filter('core.userMenu', 'pm', new PMCoreFilter);