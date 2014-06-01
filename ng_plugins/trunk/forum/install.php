<?php
if (!defined('NGCMS')) die ('HAL');

function plugin_forum_install($action) {
	global $mysql;
	$install = true;
	$db_update = array(
	array(
		'table'		=> 'forum_complaints',
		'action'	=> 'cmodify',
		'engine'	=> 'MyISAM',
		'key'		=> 'primary key(id)',
		'fields'	=> array(
			array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL AUTO_INCREMENT'),
			array('action'	=> 'cmodify', 'name' => 'pid', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'author', 'type' => 'varchar(30)', 'params' => 'NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'author_id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'who_author', 'type' => 'varchar(30)', 'params' => 'NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'who_author_id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
			array('action'	=> 'cmodify', 'name' => 'message', 'type' => 'text', 'params' => 'NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'c_data', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'viewed', 'type' => 'tinyint(1)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
		)
	),
	array(
		'table'		=> 'forum_attach',
		'action'	=> 'cmodify',
		'engine'	=> 'MyISAM',
		'key'		=> 'primary key(id), KEY (tid), KEY (pid)',
		'fields'	=> array(
			array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL AUTO_INCREMENT'),
			array('action'	=> 'cmodify', 'name' => 'tid', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'pid', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'c_data', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'file', 'type' => 'varchar(100)', 'params' => 'NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'size', 'type' => 'varchar(10)', 'params' => 'NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'downloads', 'type' => 'INT(10)', 'params' => 'NOT NULL DEFAULT "0"'),
			array('action'	=> 'cmodify', 'name' => 'location', 'type' => 'varchar(25)', 'params' => 'NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'author', 'type' => 'varchar(30)', 'params' => 'NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'author_id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
		)
	),
	array(
		'table'		=> 'forum_thank',
		'action'	=> 'cmodify',
		'engine'	=> 'MyISAM',
		'key'		=> 'primary key(id), KEY(`tid`), KEY(`pid`), KEY(`to_author_id`)',
		'fields'	=> array(
			array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL AUTO_INCREMENT'),
			array('action'	=> 'cmodify', 'name' => 'tid', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'pid', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'c_data', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'message', 'type' => 'TEXT', 'params' => 'NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'to_author_id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'author', 'type' => 'varchar(30)', 'params' => 'NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'author_id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
		)
	),
	array(
		'table'		=> 'forum_news',
		'action'	=> 'cmodify',
		'engine'	=> 'MyISAM',
		'key'		=> 'primary key(id), KEY(c_data)',
		'fields'	=> array(
			array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL AUTO_INCREMENT'),
			array('action'	=> 'cmodify', 'name' => 'title', 'type' => 'varchar(80)', 'params' => 'NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'content', 'type' => 'TEXT', 'params' => 'NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'c_data', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
		)
	),
	array(
		'table'		=> 'forum_forums',
		'action'	=> 'cmodify',
		'engine'	=> 'MyISAM',
		'key'		=> 'primary key(id), KEY `count` (`int_topic`, `int_post`), KEY (position)',
		'fields'	=> array(
			array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL AUTO_INCREMENT'),
			array('action'	=> 'cmodify', 'name' => 'parent', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
			array('action'	=> 'cmodify', 'name' => 'title', 'type' => 'varchar(80)', 'params' => 'NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'description', 'type' => 'TEXT', 'params' => 'NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'keywords', 'type' => 'TEXT', 'params' => 'NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'lock_passwd', 'type' => 'varchar(30)', 'params' => 'NOT NULL DEFAULT ""'),
			array('action'	=> 'cmodify', 'name' => 'redirect_url', 'type' => 'varchar(100)', 'params' => 'NULL'),
			array('action'	=> 'cmodify', 'name' => 'moderators', 'type' => 'TEXT', 'params' => 'NULL'),
			array('action'	=> 'cmodify', 'name' => 'int_topic', 'type' => 'mediumINT(8)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
			array('action'	=> 'cmodify', 'name' => 'int_post', 'type' => 'mediumINT(8)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
			array('action'	=> 'cmodify', 'name' => 'l_post', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
			array('action'	=> 'cmodify', 'name' => 'l_date', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
			array('action'	=> 'cmodify', 'name' => 'l_topic_id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
			array('action'	=> 'cmodify', 'name' => 'l_topic_title', 'type' => 'varchar(200)', 'params' => 'NOT NULL DEFAULT ""'),
			array('action'	=> 'cmodify', 'name' => 'l_author', 'type' => 'varchar(30)', 'params' => 'NOT NULL DEFAULT ""'),
			array('action'	=> 'cmodify', 'name' => 'l_author_id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
			array('action'	=> 'cmodify', 'name' => 'position', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
		)
	),
	array(
		'table'  => 'forum_topics',
		'action' => 'cmodify',
		'engine'	=> 'MyISAM',
		'key'    => 'primary key(id), KEY (l_date), KEY (fid), KEY `showforum`(`fid`,`l_date`), KEY `int_post`(`fid`,`int_post`), FULLTEXT (title)',
		'fields' => array(
			array('action' => 'cmodify', 'name' => 'id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL AUTO_INCREMENT'),
			array('action' => 'cmodify', 'name' => 'fid', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
			array('action' => 'cmodify', 'name' => 'state', 'type' => 'enum("closed", "open")', 'params' => 'NOT NULL default "open"'),
			array('action' => 'cmodify', 'name' => 'author', 'type' => 'varchar(30)', 'params' => 'NOT NULL'),
			array('action' => 'cmodify', 'name' => 'author_id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
			array('action' => 'cmodify', 'name' => 'title', 'type' => 'varchar(255)', 'params' => 'NOT NULL'),
			array('action' => 'cmodify', 'name' => 'c_data', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
			array('action' => 'cmodify', 'name' => 'l_post', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
			array('action' => 'cmodify', 'name' => 'l_date', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
			array('action' => 'cmodify', 'name' => 'l_author', 'type' => 'varchar(30)', 'params' => 'NOT NULL'),
			array('action' => 'cmodify', 'name' => 'l_author_id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
			array('action' => 'cmodify', 'name' => 'int_views', 'type' => 'MEDIUMINT(8)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
			array('action' => 'cmodify', 'name' => 'int_post', 'type' => 'MEDIUMINT(8)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
			array('action' => 'cmodify', 'name' => 'pinned', 'type' => 'tinyint(1)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
			
			
		)
	),
	array(
		'table'  => 'forum_posts',
		'action' => 'cmodify',
		'engine'	=> 'MyISAM',
		'key'    => 'primary key(id), KEY(tid), KEY `showtopic` (`tid`, `c_data`), KEY `delpost` (`tid`, `author_id`), FULLTEXT (message)',
		'fields' => array(
			array('action' => 'cmodify', 'name' => 'id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL AUTO_INCREMENT'),
			array('action' => 'cmodify', 'name' => 'tid', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
			array('action' => 'cmodify', 'name' => 'author', 'type' => 'varchar(200)', 'params' => 'NOT NULL'),
			array('action' => 'cmodify', 'name' => 'author_id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
			array('action' => 'cmodify', 'name' => 'author_ip', 'type' => 'varchar(15)', 'params' => 'NOT NULL'),
			array('action' => 'cmodify', 'name' => 'message', 'type' => 'text', 'params' => 'NOT NULL'),
			array('action' => 'cmodify', 'name' => 'c_data', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
			array('action' => 'cmodify', 'name' => 'who_e_author', 'type' => 'varchar(30)', 'params' => 'NOT NULL DEFAULT ""'),
			array('action' => 'cmodify', 'name' => 'e_date', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
		)
	),
	array(
		'table'		=>	'forum_online',
		'action'	=>	'cmodify',
		'engine'	=> 'MyISAM',
		'fields'	=>	array(
			array('action' => 'cmodify', 'name' => 'sess_id', 'type' => 'char(255)', 'params' => 'DEFAULT \'\''),
			array('action' => 'cmodify', 'name' => 'last_time', 'type' => 'char(255)', 'params' => 'DEFAULT \'0\''),
			array('action' => 'cmodify', 'name' => 'ip', 'type' => 'varchar(15)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'users_name', 'type' => 'varchar(100)', 'params' => 'DEFAULT \'\''),
			array('action' => 'cmodify', 'name' => 'users_id', 'type' => 'int(11)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'users_status', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
		)
	),
	array(
		'table'		=>	'forum_group',
		'action'	=>	'cmodify',
		'engine'	=> 'MyISAM',
		'key'    => 'primary key(id)',
		'fields'	=>	array(
			array('action' => 'cmodify', 'name' => 'id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL AUTO_INCREMENT'),
			array('action' => 'cmodify', 'name' => 'group_id', 'type' => 'int(11)', 'params' => 'UNSIGNED NOT NULL'),
			array('action' => 'cmodify', 'name' => 'group_name', 'type' => 'varchar(100)', 'params' => 'NOT NULL'),
			array('action' => 'cmodify', 'name' => 'group_color', 'type' => 'varchar(15)', 'params' => 'NOT NULL'),
			array('action' => 'cmodify', 'name' => 'group_read', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'group_news', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'group_search', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'group_pm', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
		)
	),
	array(
		'table'		=>	'forum_moderators',
		'action'	=>	'cmodify',
		'engine'	=> 'MyISAM',
		'key'    => 'primary key(id), UNIQUE(m_forum_id)',
		'fields'	=>	array(
			array('action' => 'cmodify', 'name' => 'id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL AUTO_INCREMENT'),
			array('action' => 'cmodify', 'name' => 'm_forum_id', 'type' => 'int(11)', 'params' => 'NOT NULL'),
			array('action' => 'cmodify', 'name' => 'm_topic_send', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'm_topic_modify', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'm_topic_closed', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'm_topic_remove', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'm_post_send', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'm_post_modify', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'm_post_remove', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
		)
	),
	array(
		'table'		=>	'forum_permission',
		'action'	=>	'cmodify',
		'engine'	=> 'MyISAM',
		'key'    => 'primary key(id)',
		'fields'	=>	array(
			array('action' => 'cmodify', 'name' => 'id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL AUTO_INCREMENT'),
			array('action' => 'cmodify', 'name' => 'group_id', 'type' => 'int(11)', 'params' => 'UNSIGNED NOT NULL'),
			array('action' => 'cmodify', 'name' => 'forum_id', 'type' => 'int(11)', 'params' => 'UNSIGNED NOT NULL'),
			array('action' => 'cmodify', 'name' => 'forum_read', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'topic_read', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'topic_send', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'topic_modify', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'topic_modify_your', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'topic_closed', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'topic_closed_your', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'topic_remove', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'topic_remove_your', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'post_send', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'post_modify', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'post_modify_your', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'post_remove', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'post_remove_your', 'type' => 'tinyint(1)', 'params' => 'DEFAULT 0'),
		)
	),
	array(
		'table'		=>	'forum_subscriptions',
		'action'	=>	'cmodify',
		'engine'	=> 'MyISAM',
		'key'    => 'KEY `search` (`uid`, `tid`)',
		'fields'	=>	array(
			array('action' => 'cmodify', 'name' => 'uid', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
			array('action' => 'cmodify', 'name' => 'tid', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
		)
	),
	array(
		'table'		=>	'forum_reputation',
		'action'	=>	'cmodify',
		'engine'	=> 'MyISAM',
		'key'    => 'primary key(id), KEY(to_author_id)',
		'fields'	=>	array(
			array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL AUTO_INCREMENT'),
			array('action'	=> 'cmodify', 'name' => 'tid', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'pid', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'c_data', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'plus', 'type' => 'TINYINT(1)', 'params' => 'UNSIGNED NOT NULL DEFAULT 0'),
			array('action'	=> 'cmodify', 'name' => 'minus', 'type' => 'TINYINT(1)', 'params' => 'UNSIGNED NOT NULL DEFAULT 0'),
			array('action'	=> 'cmodify', 'name' => 'message', 'type' => 'TEXT', 'params' => 'NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'to_author_id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'author', 'type' => 'varchar(30)', 'params' => 'NOT NULL'),
			array('action'	=> 'cmodify', 'name' => 'author_id', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL'),
		)
	),
	
	array(
		'table' => 'pm',
		'action' => 'cmodify',
		'engine'	=> 'MyISAM',
		'key' => 'primary key (`id`), KEY `count_pm` (`to_id`, `viewed`, `folder`)',
		'fields' => array(
			array('action' => 'cmodify', 'name' => 'id', 'type' => 'int(10)', 'params' => 'UNSIGNED NOT NULL AUTO_INCREMENT'),
			array('action' => 'cmodify', 'name' => 'subject', 'type' => 'varchar(255)', 'params' => 'NOT NULL'),
			array('action' => 'cmodify', 'name' => 'message', 'type' => 'text', 'params' => 'NOT NULL'),
			array('action' => 'cmodify', 'name' => 'from_id', 'type' => 'int(10)', 'params' => 'NOT NULL'),
			array('action' => 'cmodify', 'name' => 'to_id', 'type' => 'int(10)', 'params' => 'NOT NULL'),
			array('action' => 'cmodify', 'name' => 'date', 'type' => 'int(10)', 'params' => 'NOT NULL'),
			array('action' => 'cmodify', 'name' => 'viewed', 'type' => 'tinyint(1)', 'params' => 'NOT NULL'),
			array('action' => 'cmodify', 'name' => 'folder', 'type' => 'enum(\'inbox\', \'outbox\')', 'params' => 'DEFAULT \'inbox\'')
		)
	),
	
	array(
		'table'		=>	'users',
		'action'	=>	'cmodify',
		'key'    => 'KEY(reg), KEY (int_post), KEY (l_post)',
		'fields'	=>	array(
			array('action' => 'cmodify', 'name' => 'timezone', 'type' => 'float', 'params' => 'NOT NULL DEFAULT "0"'),
			array('action' => 'cmodify', 'name' => 'signature', 'type' => 'text'),
			array('action' => 'cmodify', 'name' => 'int_post', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
			array('action' => 'cmodify', 'name' => 'l_post', 'type' => 'INT(10)', 'params' => 'UNSIGNED NOT NULL DEFAULT "0"'),
			array('action' => 'cmodify', 'name' => 'reputation', 'type' => 'INT(10)', 'params' => 'UNSIGNED DEFAULT 0'),
			array('action' => 'cmodify', 'name' => 'int_thank', 'type' => 'INT(10)', 'params' => 'UNSIGNED DEFAULT 0'),
		)
	),
	
	array(
		'table'		=>	'news',
		'action'	=>	'cmodify',
		'engine'	=> 'MyISAM',
		'key'    => '',
		'fields'	=>	array(
			array('action'	=> 'cmodify', 'name' => 'tid', 'type' => 'INT(10)', 'params' => 'UNSIGNED DEFAULT "0"'),
		)
	),
);
	
	switch ($action){
		case 'confirm':generate_install_page('forum', '������������ � ���������'); break;
		case 'apply':
			/* if(!file_exists(files_dir . 'forum')){
					if(!@mkdir(files_dir . 'forum', 0777)){
						msg(array("type" => "error", "text" => "����������� ������ <br /> �� ������� ������� ����� ".files_dir . 'forum'), 1);
						$install = false;
				}
			} */
			
			if($install){
				if (fixdb_plugin_install('forum', $db_update, 'install', ($action=='autoapply')?true:false)) {
					
					if(!$mysql->result('SELECT 1 FROM '.prefix.'_forum_group')){
						$mysql->query('INSERT INTO '.prefix.'_forum_group (group_id, group_name, group_color, group_read, group_news, group_search, group_pm) VALUES (\'0\', \'�����\', \'red\', \'1\', \'1\', \'1\', \'1\')');
						$mysql->query('INSERT INTO '.prefix.'_forum_group (group_id, group_name, group_color, group_read, group_news, group_search, group_pm) VALUES (\'1\', \'�������������\', \'red\', \'1\', \'1\', \'1\', \'1\')');
						$mysql->query('INSERT INTO '.prefix.'_forum_group (group_id, group_name, group_color, group_read, group_news, group_search, group_pm) VALUES (\'2\', \'��������\', \'red\', \'1\', \'1\', \'1\', \'1\')');
						$mysql->query('INSERT INTO '.prefix.'_forum_group (group_id, group_name, group_color, group_read, group_news, group_search, group_pm) VALUES (\'3\', \'���������\', \'blue\', \'1\', \'1\', \'1\', \'1\')');
						$mysql->query('INSERT INTO '.prefix.'_forum_group (group_id, group_name, group_color, group_read, group_news, group_search, group_pm) VALUES (\'4\', \'�����������\', \'gold\', \'1\', \'1\', \'1\', \'1\')');
						$mysql->query('INSERT INTO '.prefix.'_forum_group (group_id, group_name, group_color, group_read, group_news, group_search, group_pm) VALUES (\'5\', \'���\', \'red\', \'1\', \'1\', \'1\', \'1\')');
					}
					
					if(!$mysql->record('SHOW INDEX FROM '.prefix.'_pm WHERE Key_name = \'count_pm\''))
						$mysql->query('alter table '.prefix.'_pm add index count_pm (`to_id`, `viewed`, `folder`)');
					
					if(!$mysql->record('SHOW INDEX FROM '.prefix.'_users WHERE Key_name = \'int_post\''))
						$mysql->query('alter table '.prefix.'_users add index int_post (int_post)');
					
					if(!$mysql->record('SHOW INDEX FROM '.prefix.'_users WHERE Key_name = \'l_post\''))
						$mysql->query('alter table '.prefix.'_users add index l_post (l_post)');
					
					if(!$mysql->record('SHOW INDEX FROM '.prefix.'_forum_posts WHERE Key_name = \'message\''))
						$mysql->query('alter table '.prefix.'_forum_posts add FULLTEXT (message)');
					
					if(!$mysql->record('SHOW INDEX FROM '.prefix.'_forum_topics WHERE Key_name = \'title\''))
						$mysql->query('alter table '.prefix.'_forum_topics add FULLTEXT (title)');
					
					$r =$mysql->query('SELECT * FROM `'.prefix.'_forum_forums` LIMIT 1');
					if($r['l_author_avatar'])
						$mysql->query('ALTER TABLE `'.prefix.'_forum_forums` DROP `l_author_avatar`');
					
					$r = $mysql->record('SELECT * FROM `'.prefix.'_forum_topics` LIMIT 1');
					if($r['l_author_avatar'])
						$mysql->query('ALTER TABLE `'.prefix.'_forum_topics` DROP `l_author_avatar`');
					
					foreach ($mysql->select('SELECT MAX(id) as pid, tid FROM '.prefix.'_forum_posts GROUP BY tid') as $row){
						$mysql->query('UPDATE '.prefix.'_forum_topics SET l_post = '.$row['pid'].' WHERE id = '.$row['tid'].' LIMIT 1');
					}
					
					foreach ($mysql->select('SELECT MAX(id) as tid, fid FROM '.prefix.'_forum_topics GROUP BY fid') as $row){
						$mysql->query('UPDATE '.prefix.'_forum_forums SET l_post = '.$row['tid'].' WHERE id = '.$row['fid'].' LIMIT 1');
					}
					
					plugin_mark_installed('forum');
					mkdir(files_dir . 'forum', 0777);
				} else return false;
			} else return false;
 			$params = array(
				'localsource' => 1,
				'online' => 1,
				'online_time' => 900,
				'redirect_time' => 5,
				'forum_title' => '�������� ������',
				'forum_description' => '�������� ������',
				'forum_keywords' => '�������� �����',
				'localskin' => 'flux',
				'edit_del_time' => 5,
				'display_main' => 1,
				
				'topic_per_page' => 20,
				'search_per_page' => 20,
				'user_per_page' => 20,
				'forum_per_page' => 20,
				'reput_per_page' => 20,
				'thank_per_page' => 20,
				'newpost_per_page' => 20,
				'news_per_page' => 20,
				'rss_per_page' => 20,
				'list_pm_per_page' => 20,
				'act_per_page' => 20,
				
				'home_title' => '%name_forum%',
				'forums_title' => '%cat_forum% / %name_forum% [/ %num%]',
				'topic_title' => '%name_topic% / %cat_forum% [/ %num%]',
				'userlist_title' => '������ ������������� / %name_forum%',
				'search_title' => '����� / %name_forum%',
				'register_title' => '����������� / %name_forum%',
				'login_title' => '����� �� ���� / %name_forum%',
				'profile_title' => '%others% / %name_forum%',
				'out_title' => '����� / %name_forum%',
				'addreply_title' => '�������� ��������� / %name_forum%',
				'newtopic_title' => '�������� ���� / %name_forum%',
				'delpost_title' => '������� ��������� / %name_forum%',
				'edit_title' => '������������� / %name_forum%',
				'rules_title' => '������� / %name_forum%',
				'show_new_title' => '��������� ��������� / %name_forum%',
				'markread_title' => '�� ��������� / %name_forum%',
				'rep_title' => '��������� ��������� %others% / %name_forum%',
				'addr_title' => '�������� ��������� / %name_forum%',
				'news_title' => '%name_news% / ������� / %name_forum%',
				'news_feed_title' => '��� ����� / %name_forum% [/ %num%]',
				'act_title' => '%others% / %name_forum%',
				'thank_title' => '������� �������������� ��������� %others% / %name_forum%',
				'complaints_title' => '�������� ���������� / %name_forum%',
				'send_pm_title' => '����� ��������� / %name_forum%',
				'list_pm_title' => '������ ��������� / %name_forum%',
				'del_pm_title' => '������� ��������� / %name_forum%',
				'downloads_title' => '�������� ����� / %name_forum%',
				'erro404_title' => '���������� / %name_forum%',
				'num_title' => '�������� %count%',
			);
			foreach ($params as $k => $v) {
				extra_set_param('forum', $k, $v);
			}
			extra_commit_changes();
			break;
	}
	return true;
}