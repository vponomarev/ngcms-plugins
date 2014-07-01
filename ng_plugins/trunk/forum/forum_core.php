<?php

if (!defined('NGCMS')) die ('HAL');

function forum_core(){
	include_once( dirname(__FILE__) . '/includes/constants.php');
	include_once( dirname(__FILE__) . '/includes/rewrite.php');
}

function forum_group(){
global $userROW, $GROUP_PS, $FORUM_PS, $MODE_PERM, $GROUP_PERM;
	
	if(file_exists(FORUM_CACHE.'/forum_perm.php'))
		include(FORUM_CACHE.'/forum_perm.php');
	
	if(file_exists(FORUM_CACHE.'/group_perm.php'))
		include(FORUM_CACHE.'/group_perm.php');
	
	if(file_exists(FORUM_CACHE.'/mode_perm.php'))
		include(FORUM_CACHE.'/mode_perm.php');
	
	$bot = forum_filter_bots($_SERVER['HTTP_USER_AGENT']);
	
	if( is_array($userROW) ){
		$GROUP_STATUS = $userROW['status'];
	} elseif($bot){
		$GROUP_STATUS = 5;
	} else {
		$GROUP_STATUS = 0;
	}
	//print "<pre>".var_export($GROUP_PERM[$GROUP_STATUS], true)."</pre>";
	
	$GROUP_PS = $GROUP_PERM[$GROUP_STATUS];
	$FORUM_PS = $FORUM_PERM[$GROUP_STATUS];
	
	//print "<pre>".var_export($GROUP_PS, true)."</pre>";
	
	//print "<pre>".var_export($FORUM_PS, true)."</pre>";
}

function forum_browsing(){
global $viewers, $online, $CurrentHandler, $lang_forum, $GROUP_PERM;
	
	$last_time = time() + ($config['date_adjust'] * 60) - pluginGetVariable('forum', 'online_time');
	//print "<pre>".var_export($online, true)."</pre>";
	//print "<pre>".var_export($CurrentHandler['handlerName'], true)."</pre>";
	$viewers['num_guest_loc'] = 0; $viewers['num_user_loc'] = 0; $viewers['num_bot_loc'] = 0;
	if( is_array($online) ){
		foreach ($online as $row){
			if($row['last_time'] > $last_time){
				if($row['location'] == $CurrentHandler['handlerName']){
					if(isset($row['users_status']) && $row['users_status'] == 0){
						$viewers['num_guest_loc']++;
					}elseif(isset($row['users_status']) && $row['users_status'] == 1){
						$color_start = '<span style="color:'.$GROUP_PERM[$row['users_status']]['group_color'].';">'; $color_end = '</span>';
						$viewers['active_users_loc'][] = str_replace( array('{url}', '{name}', '{color_start}', '{color_end}'), array( link_profile($row['users_id'], '', $row['users']), $row['users'], $color_start, $color_end ), $lang_forum['admin_url'] );
						$viewers['num_user_loc']++;
					}elseif(isset($row['users_status']) && $row['users_status'] == 2){
						$color_start = '<span style="color:'.$GROUP_PERM[$row['users_status']]['group_color'].';">'; $color_end = '</span>';
						$viewers['active_users_loc'][] = str_replace( array('{url}', '{name}', '{color_start}', '{color_end}'), array( link_profile($row['users_id'], '', $row['users']), $row['users'], $color_start, $color_end ), $lang_forum['editor_url'] );
						$viewers['num_user_loc']++;
					}elseif(isset($row['users_status']) && $row['users_status'] == 3){
						$color_start = '<span style="color:'.$GROUP_PERM[$row['users_status']]['group_color'].';">'; $color_end = '</span>';
						$viewers['active_users_loc'][] = str_replace( array('{url}', '{name}', '{color_start}', '{color_end}'), array( link_profile($row['users_id'], '', $row['users']), $row['users'], $color_start, $color_end ), $lang_forum['publicist_url'] );
						$viewers['num_user_loc']++;
					}elseif(isset($row['users_status']) && $row['users_status'] == 4){
						$color_start = '<span style="color:'.$GROUP_PERM[$row['users_status']]['group_color'].';">'; $color_end = '</span>';
						$viewers['active_users_loc'][] = str_replace( array('{url}', '{name}', '{color_start}', '{color_end}'), array( link_profile($row['users_id'], '', $row['users']), $row['users'], $color_start, $color_end ), $lang_forum['com_url'] );
						$viewers['num_user_loc']++;
					}elseif(isset($row['users_status']) && $row['users_status'] == 5){
						$viewers['active_bot_loc'][] = $lang_forum[$row['users']];
						$viewers['num_bot_loc']++;
					}
				}
			}
		}
	}
	
	if( isset($viewers['active_users_loc']) && is_array($viewers['active_users_loc']) )
		$viewers['list_loc_user'] = implode(", ", $viewers['active_users_loc']);
	
	if( isset($viewers['active_bot_loc']) && is_array($viewers['active_bot_loc']) )
		$viewers['list_loc_bot'] = implode(", ", $viewers['active_bot_loc']);
}

function forum_online(){
global $mysql, $config, $userROW, $ip, $online, $ip_user, $CurrentHandler;

	if(!pluginGetVariable('forum','online')) return;
	
	$time = time() + ($config['date_adjust'] * 60);
	$last_time = time() + ($config['date_adjust'] * 60) - pluginGetVariable('forum', 'online_time');
	
	$bot = forum_filter_bots($_SERVER['HTTP_USER_AGENT']);
	
	if( is_array($userROW) ){
		$id = null;
		$ips = null;
		$users = $userROW['name'];
		$users_id = $userROW['id'];
		$users_status = $userROW['status'];
		$location = $CurrentHandler['handlerName'];
	} elseif($bot){
		$id = md5($bot);
		$ips = $ip;
		$users = $bot;
		$users_id = 0;
		$users_status = 5;
		$location = $CurrentHandler['handlerName'];
	} else {
		$id = session_id();
		$ips = $ip;
		$users = 'Гость';
		$users_id = 0;
		$users_status = 0;
		$location = $CurrentHandler['handlerName'];
	}
	
	$date_today = date('Y-m-d', $time);
	$online = array();
	if(file_exists(FORUM_CACHE.'/online.php'))
		include(FORUM_CACHE.'/online.php');
	else
		file_put_contents(FORUM_CACHE.'/online.php', '<?php'."\n\n".'$online = '.var_export(array(), true).';'."\n\n".'$date_update = \''.$date_today.'\';');
	
	//print "<pre>".var_export($CurrentHandler, true)."</pre>";
	
	//print "<pre>".var_export($location, true)."</pre>";
	
	if(is_array($online)){
		$user = array(); $sess = array(); $ip_user = array(); $count = count($online);
		if(isset($count) && ($count > 20000)){
			$x = 1;
			while ($count >= 0){
				$res = $online[$count];
				if ((isset($res['users_id']) && ($res['users_id'] == 0)) && (isset($res['last_time']) && ($res['last_time'] < $last_time))){
					array_splice($online, $count, 1);
					if($x >= 10000) break;
					$x++;
				}
				$count--;
			}
			array_splice($online, $count, 1);
			file_put_contents(FORUM_CACHE.'/online.php', '<?php'."\n\n".'$online = '.var_export($online, true).';'."\n\n".'$date_update = \''.$date_today.'\';');
		}
		
		if($date_update < $date_today){
			while ($count >= 0){
				$res = $online[$count];
				if ((isset($res['users_id']) && ($res['users_id'] == 0))){
					array_splice($online, $count, 1);
				}
				$count--;
			}
			file_put_contents(FORUM_CACHE.'/online.php', '<?php'."\n\n".'$online = '.var_export($online, true).';'."\n\n".'$date_update = \''.$date_today.'\';');
		}
		
		foreach ($online as $row){
			$user[$row['users_id']] = $row['last_time'];
			$sess[$row['sess_id']] = $row['last_time'];
			$ip_user[$row['ip']] = $row['last_time'];
		}
		
		if( is_array($userROW) ){
			if($user[$userROW['id']]){
				while ($count >= 0){
					$res = $online[$count];
					if (isset($res['users_id']) && ($res['users_id'] == $userROW['id'])){
						$online[$count] = array('sess_id' => $res['sess_id'], 'last_time' => $res['last_time'], 'ip' => $res['ip'], 'users' => $res['users'], 'users_id' => $res['users_id'], 'users_status' => $res['users_status'], 'location' => $location);
						file_put_contents(FORUM_CACHE.'/online.php', '<?php'."\n\n".'$online = '.var_export($online, true).';'."\n\n".'$date_update = \''.$date_today.'\';'); break;
					}
					$count--;
				}
				if($user[$userROW['id']] < $last_time){
					$mysql->query('UPDATE '.prefix.'_users SET last = '.securemysql($user[$userROW['id']]).' WHERE id = '.securemysql($userROW['id']));
					while ($count >= 0){
						$res = $online[$count];
						if (isset($res['users_id']) && ($res['users_id'] == $userROW['id'])){
							$online[$count] = array('sess_id' => $id, 'last_time' => $time, 'ip' => $ips, 'users' => $users, 'users_id' => $users_id, 'users_status' => $users_status, 'location' => $location);
							file_put_contents(FORUM_CACHE.'/online.php', '<?php'."\n\n".'$online = '.var_export($online, true).';'."\n\n".'$date_update = \''.$date_today.'\';'); break;
						}
						$count--;
					}
				}
			}elseif($ip_user[$ip]){
				while($count >= 0){
					$res = $online[$count];
					if (isset($res['ip']) && ($res['ip'] == $ip)){
						$online[$count] = array('sess_id' => $id, 'last_time' => $time, 'ip' => $ips, 'users' => $users, 'users_id' => $users_id, 'users_status' => $users_status, 'location' => $location);
						file_put_contents(FORUM_CACHE.'/online.php', '<?php'."\n\n".'$online = '.var_export($online, true).';'."\n\n".'$date_update = \''.$date_today.'\';'); break;
					}
					$count--;
				}
			} else {
				$online[] = array('sess_id' => $id, 'last_time' => $time, 'ip' => $ips, 'users' => $users, 'users_id' => $users_id, 'users_status' => $users_status, 'location' => $location);
				file_put_contents(FORUM_CACHE.'/online.php', '<?php'."\n\n".'$online = '.var_export($online, true).';'."\n\n".'$date_update = \''.$date_today.'\';');
			}
		} else {
			if($sess[$id]){
				while ($count >= 0){
					$res = $online[$count];
					if (isset($res['sess_id']) && ($res['sess_id'] == $id)){
						$online[$count] = array('sess_id' => $res['sess_id'], 'last_time' => $res['last_time'], 'ip' => $res['ip'], 'users' => $res['users'], 'users_id' => $res['users_id'], 'users_status' => $res['users_status'], 'location' => $location);
						file_put_contents(FORUM_CACHE.'/online.php', '<?php'."\n\n".'$online = '.var_export($online, true).';'."\n\n".'$date_update = \''.$date_today.'\';'); break;
					}
					$count--;
				}
				if($sess[$id] < $last_time){
					while ($count >= 0){
						$res = $online[$count];
						if (isset($res['sess_id']) && ($res['sess_id'] == $id)){
							$online[$count] = array('sess_id' => $id, 'last_time' => $time, 'ip' => $ips, 'users' => $users, 'users_id' => $users_id, 'users_status' => $users_status, 'location' => $location);
							file_put_contents(FORUM_CACHE.'/online.php', '<?php'."\n\n".'$online = '.var_export($online, true).';'."\n\n".'$date_update = \''.$date_today.'\';'); break;
						}
						$count--;
					}
				}
			} else {
				if( $ip_user[$ip] ){
					while ($count >= 0){
						$res = $online[$count];
						if (isset($res['ip']) && ($res['ip'] == $ip)){
							$online[$count] = array('sess_id' => $res['sess_id'], 'last_time' => $res['last_time'], 'ip' => $res['ip'], 'users' => $res['users'], 'users_id' => $res['users_id'], 'users_status' => $res['users_status'], 'location' => $location);
							file_put_contents(FORUM_CACHE.'/online.php', '<?php'."\n\n".'$online = '.var_export($online, true).';'."\n\n".'$date_update = \''.$date_today.'\';'); break;
						}
						$count--;
					}
					if($ip_user[$ip] < $last_time){
						while ($count >= 0){
							$res = $online[$count];
							if (isset($res['ip']) && ($res['ip'] == $ip)){
								$online[$count] = array('sess_id' => $id, 'last_time' => $time, 'ip' => $ips, 'users' => $users, 'users_id' => $users_id, 'users_status' => $users_status, 'location' => $location);
								file_put_contents(FORUM_CACHE.'/online.php', '<?php'."\n\n".'$online = '.var_export($online, true).';'."\n\n".'$date_update = \''.$date_today.'\';'); break;
							}
							$count--;
						}
					}
				} else {
					$online[] = array('sess_id' => $id, 'last_time' => $time, 'ip' => $ips, 'users' => $users, 'users_id' => $users_id, 'users_status' => $users_status, 'location' => $location);
					file_put_contents(FORUM_CACHE.'/online.php', '<?php'."\n\n".'$online = '.var_export($online, true).';'."\n\n".'$date_update = \''.$date_today.'\';');
				}
			}
		}
	} else {
		$online[] = array('sess_id' => $id, 'last_time' => $time, 'ip' => $ips, 'users' => $users, 'users_id' => $users_id, 'users_status' => $users_status, 'location' => $location);
		file_put_contents(FORUM_CACHE.'/online.php', '<?php'."\n\n".'$online = '.var_export($online, true).';'."\n\n".'$date_update = \''.$date_today.'\';');
	}
}

function forum_filter_bots($u_agent){
	$engines = array(
		'YandexBot' => 'YandexBot',
		'YandexDirect' => 'YandexDirect',
		'Googlebot' => 'Googlebot',
	);
	
	foreach ($engines as $key => $value){
		if (stripos($u_agent, $key)){
			return($value);
		}
	}
	
	return false;
}

add_act('forum:core', 'forum_core');
add_act('index_post', 'forum_core');

add_act('forum:core', 'forum_group');
add_act('index_post', 'forum_group');

add_act('forum:function', 'forum_online');
add_act('forum:function', 'forum_browsing');