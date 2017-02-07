<?php

/*
 * Plugin's "Private message" API for NextGeneration CMS (http://ngcms.ru/)
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

class pm {

	function __construct() {

		LoadPluginLang('pm', 'main', '', '', ':');
	}

	/* params:
	 *
	 *		$to_user: ID or NAME
	 *		$from_username: ID
	 *
	 * return:
	 *		-1: if length of  message's title ($title) > title_length (conf param)
	 *		-2:	if length of message's title ($title) = 0
	 *		-3:	if length of message ($message) > message_length (conf param)
	 *		-4: if length of mesasge ($message) = 0
	 *		-5: if user not found
	 *		 0: all rigth, message was send
	 */
	function sendMsg($to_user, $from_username, $title, $message, $mail_from = false, $saveoutbox = 0) {

		global $lang, $mysql, $config;
		if (strlen($title) > pluginGetVariable('pm', 'title_length'))
			return -1;
		if (!$title)
			return -2;
		if (strlen($message) > pluginGetVariable('pm', 'message_length'))
			return -3;
		if (!$message)
			return -4;
		$to_user = trim($to_user);
		if (!$to_user || (!$torow = $mysql->record("SELECT * FROM " . uprefix . "_users WHERE " . (is_numeric($to_user) ? "id = " . db_squote($to_user) : "name = " . db_squote($to_user)))))
			return -5;
		$title = secure_html($title);
		$message = secure_html($message);
		$time = time() + ($config['date_adjust'] * 60);
		# if all right
		$mysql->query("INSERT INTO " . prefix . "_pm (from_id, to_id, date, subject, message, folder) 
					   VALUES (" . db_squote($from_username) . ", " . db_squote($torow['id']) . ", " . db_squote($time) . ", " . db_squote($title) . ", " . db_squote($message) . ", 'inbox')");
		$id = $mysql->result("SELECT LAST_INSERT_ID() as id");
		# save message in outbox if needed
		if ($saveoutbox)
			$mysql->query("INSERT INTO " . prefix . "_pm (from_id, to_id, date, subject, message, folder) 
					   VALUES (" . db_squote($from_username) . ", " . db_squote($torow['id']) . ", " . db_squote($time) . ", " . db_squote($title) . ", " . db_squote($message) . ", 'outbox')");
		# update pm counters
		$mysql->query("UPDATE " . uprefix . "_users SET `pm_all` = `pm_all` + 1, `pm_unread` = `pm_unread` + 1 WHERE `id` = " . db_squote($torow['id']));
		# send email if needed
		if ($torow['pm_email'] && $torow['mail']) {
			$msg_link = generatePluginLink('pm', null, array('pmid' => $id, 'action' => 'read'), array(), false, true);
			$set_link = generatePluginLink('pm', null, array('action' => 'set'), array(), false, true);
			sendEmailMessage($torow['mail'],
				$lang['pm:email_subject'],
				str_replace(array('{message}', '{url}', '{url-2}'), array($message, $msg_link, $set_link), $lang['pm:email_body']),
				false, $mail_from);
		}

		return 0;
	}
}