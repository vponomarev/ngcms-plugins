<?php

/*
 * Install plugin "Private message" for NextGeneration CMS (http://ngcms.ru/)
 * Copyright (C) 2010 Alexey N. Zhukov (http://digitalplace.ru)
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
if (!defined('NGCMS'))die ('Galaxy in danger');

function plugin_pm_install($action) {
	global $lang;
	
	if ($action != 'autoapply')
		loadPluginLang('pm', 'config', '', '', ':');
		
	$db_create = array(
		array(
			'table' => 'pm',
			'action' => 'cmodify',
			'key' => 'primary key (`id`)',
			'fields' => array(
				array('action' => 'cmodify', 'name' => '`id`', 'type' => 'int(10)', 'params' => 'UNSIGNED NOT NULL AUTO_INCREMENT'),
				array('action' => 'cmodify', 'name' => '`subject`', 'type' => 'varchar(255)', 'params' => 'NOT NULL'),
				array('action' => 'cmodify', 'name' => '`message`', 'type' => 'text', 'params' => 'NOT NULL'),
				array('action' => 'cmodify', 'name' => '`from_id`', 'type' => 'int(10)', 'params' => 'NOT NULL'),
				array('action' => 'cmodify', 'name' => '`to_id`', 'type' => 'int(10)', 'params' => 'NOT NULL'),
				array('action' => 'cmodify', 'name' => '`date`', 'type' => 'int(10)', 'params' => 'NOT NULL'),
				array('action' => 'cmodify', 'name' => '`viewed`', 'type' => 'tinyint(1)', 'params' => 'NOT NULL'),
				array('action' => 'cmodify', 'name' => '`folder`', 'type' => 'varchar(10)', 'params' => 'NOT NULL')
			)
		),

		array(
			 'table'  => 'users',
			 'action' => 'cmodify',
			 'fields' => array(
				array('action' => 'cmodify', 'name' => 'pm_all', 'type' => 'smallint(5)', 'params' => "default '0'"),
				array('action' => 'cmodify', 'name' => 'pm_unread', 'type' => 'smallint(5)', 'params' => "default '0'"),
				array('action' => 'cmodify', 'name' => 'pm_sync', 'type' => 'tinyint(1)', 'params' => "default '0'"),
				array('action' => 'cmodify', 'name' => 'pm_email', 'type' => 'tinyint(1)', 'params' => "default '1'"),
			)
		),
	);

	switch ($action) {
		case 'confirm': 
			 generate_install_page('pm', $lang['pm:install']);
			 break;
		case 'autoapply':
		case 'apply':
			if (fixdb_plugin_install('pm', $db_create, 'install', ($action=='autoapply')?true:false)) {
				plugin_mark_installed('pm');
			} else {
				return false;
			}
			break;
	}
	return true;
}
