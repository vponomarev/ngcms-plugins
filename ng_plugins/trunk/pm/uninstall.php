<?php

/*
 * Uninstall plugin "Private message" for NextGeneration CMS (http://ngcms.ru/)
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
if (!defined('NGCMS')) die ('Galaxy in danger');

global $lang;

loadPluginLang('pm', 'config', '', '', ':');

$db_update = array(
	array(
		'table'		=>	'pm',
		'action'	=>	'drop',
	),
	
	array(
		'table'  => 'users',
		'action' => 'modify',
		'fields' => array(
					array('action' => 'drop', 'name' => 'pm_unread'),
					array('action' => 'drop', 'name' => 'pm_all'),
					array('action' => 'drop', 'name' => 'pm_sync'),
					array('action' => 'drop', 'name' => 'pm_email'),
		)
	),
);

if ($_REQUEST['action'] == 'commit') {
	if (fixdb_plugin_install('pm', $db_update, 'deinstall')) {
		plugin_mark_deinstalled('pm');
	}
} else {
	generate_install_page('pm', $lang['pm:uninstall'], 'deinstall');
}