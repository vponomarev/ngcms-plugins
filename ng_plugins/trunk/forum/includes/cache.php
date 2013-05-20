<?php
	/*
	=====================================================
	 NG FORUM v.alfa
	-----------------------------------------------------
	 Author: Nail' R. Davydov (ROZARD)
	-----------------------------------------------------
	 Jabber: ROZARD@ya.ru
	 E-mail: ROZARD@list.ru
	-----------------------------------------------------
	 © Настоящий программист никогда не ставит 
	 комментариев. То, что писалось с трудом, должно 
	 пониматься с трудом. :))
	-----------------------------------------------------
	 Данный код защищен авторскими правами
	=====================================================
	*/
if (!defined('NGCMS')) die ('HAL');

function generate_index_cache($load = false)
{global $mysql, $config;
	
	$time = time() + ($config['date_adjust'] * 60);
	$date_today = date('Y-m-d', strtotime('now'));
	$date = array();
	if(file_exists(FORUM_CACHE.'/date.php')){
		$date = unserialize(file_get_contents(FORUM_CACHE.'/date.php'));
	} else {
		$date['index'] = date('Y-m-d', strtotime('now'));
		file_put_contents(FORUM_CACHE.'/date.php', serialize($date));
	}
	
	if(!file_exists(FORUM_CACHE.'/cache_index.php') or $date['index'] <= $date_today or $load){
		//if(true){
		
		$result = $mysql->select('SELECT f.*, u.avatar FROM '.prefix.'_forum_forums as f
			LEFT JOIN '.prefix.'_users AS u ON u.id = f.l_author_id
			ORDER BY f.position ASC');
		$date['index'] = date('Y-m-d', strtotime('+1 day'));
		
		file_put_contents(FORUM_CACHE.'/cache_index.php', '<?php'."\n\n".'if (!defined(\'NGCMS\')) die (\'HAL\');'."\n\n".'$result = '.var_export($result, true).';'."\n\n");
		file_put_contents(FORUM_CACHE.'/date.php', serialize($date));
	}
}

function generate_statistics_cache($load = false)
{global $mysql, $config;
	
	$time = time() + ($config['date_adjust'] * 60);
	$date_today = date('Y-m-d', strtotime('now'));
	$date = array();
	if(file_exists(FORUM_CACHE.'/date.php')){
		$date = unserialize(file_get_contents(FORUM_CACHE.'/date.php'));
	} else {
		$date['stat'] = date('Y-m-d', strtotime('now'));
		file_put_contents(FORUM_CACHE.'/date.php', serialize($date));
	}
	
	if(!file_exists(FORUM_CACHE.'/cache_statistics.php') or $date['stat'] <= $date_today or $load){
		//if(true){
		$result_users = $mysql->result('SELECT COUNT(*) FROM `'.prefix.'_users`');
		$result_last_users = $mysql->record('SELECT `id`, `name` FROM '.prefix.'_users ORDER BY `reg` DESC LIMIT 1');
		$SUM = $mysql->record('SELECT SUM(`int_topic`) as int_topic, SUM(`int_post`) as int_post FROM `'.prefix.'_forum_forums`');
		$date['stat'] = date('Y-m-d', strtotime('+1 day'));
		file_put_contents(FORUM_CACHE.'/cache_statistics.php', '<?php'."\n\n".'if (!defined(\'NGCMS\')) die (\'HAL\');'."\n\n".'$result_users = '.var_export($result_users, true).';'."\n\n".'$result_last_users = '.var_export($result_last_users, true).';'."\n\n".'$topic_sum = '.var_export($SUM['int_topic'], true).';'."\n\n".'$post_sum = '.var_export($SUM['int_post'], true).';'."\n\n");
		file_put_contents(FORUM_CACHE.'/date.php', serialize($date));
	}
}