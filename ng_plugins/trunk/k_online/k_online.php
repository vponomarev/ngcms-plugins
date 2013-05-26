<?php
/*
=====================================================
 K_Online v.0.1
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
if (!defined('NGCMS'))
	die ('HAL');

add_act('index', 'k_online');
LoadPluginLang('k_online', 'main', '', '', '#');

function k_online()
{global $config, $twig, $template, $userROW, $ip, $mysql, $lang;

$time = time() + ($config['date_adjust'] * 60 * 60);
$last_time = $time - 500;
$tpath = locatePluginTemplates(array('k_online'), 'k_online', pluginGetVariable('k_online', 'localsource'));
$xt = $twig->loadTemplate($tpath['k_online'].'k_online.tpl');

$bot = filter_bots($_SERVER['HTTP_USER_AGENT']);

if( is_array($userROW) ){
	$id = null;
	$ips = null;
	$users = $userROW['name'];
	$users_id = $userROW['id'];
	$users_status = $userROW['status'];
} elseif($bot){
	$id = md5($bot);
	$ips = $ip;
	$users = $bot;
	$users_id = 0;
	$users_status = 5;
} else {
	$id = session_id();
	$ips = $ip;
	$users = 'Гость';
	$users_id = 0;
	$users_status = 0;
}

$confdir = get_plugcfg_dir('k_online');

if(!file_exists($confdir.'/date.php'))
	file_put_contents($confdir.'/date.php', serialize(date('Y-m-d', strtotime('+1 day', $time))));

if(!file_exists($confdir.'/result.php'))
	file_put_contents($confdir.'/result.php', '');

$date = unserialize(file_get_contents($confdir.'/date.php'));
$date_today = date('Y-m-d', strtotime('now', $time));

$db = false;
if($date <= $date_today){
	file_put_contents($confdir.'/date.php', serialize(date('Y-m-d', strtotime('+1 day', $time))));
	$mysql->query('DELETE FROM '.prefix.'_k_online');
	file_put_contents($confdir.'/result.php', '');
}

if($db)
	$result = $mysql->select('SELECT * FROM '.prefix.'_k_online');
else
	$result = unserialize(file_get_contents($confdir.'/result.php'));

if($result){
	$user = array(); $sess = array(); $ipis = array(); $count = count($result);
	if(isset($count) && ($count > 20000)){
		if($db){
			$mysql->query('DELETE FROM '.prefix.'_k_online WHERE users_id = 0 ORDER BY last_time ASC LIMIT 10000');
		} else {
			$x = 1;
			while ($count >= 0){
				$res = $result[$count];
				if ((isset($res['users_id']) && ($res['users_id'] == 0)) && (isset($res['last_time']) && ($res['last_time'] < $last_time))){
					array_splice($result, $count, 1);
					if($x >= 10000) break;
					$x++;
				}
				$count--;
			}
			file_put_contents($confdir.'/result.php', serialize($result));
		}
	}
	
	foreach ($result as $row){
		$user[$row['users_id']] = $row['last_time'];
		$sess[$row['sess_id']] = $row['last_time'];
		$ipis[$row['ip']] = $row['last_time'];
	}
	if( is_array($userROW) ){
		if($user[$userROW['id']]){
			if($user[$userROW['id']] < $last_time){
				while ($count >= 0){
					$res = $result[$count];
					if (isset($res['users_id']) && ($res['users_id'] == $userROW['id'])){
						$result[$count] = array('sess_id' => $id, 'last_time' => $time, 'ip' => $ips, 'users' => $userROW['name'], 'users_id' => $userROW['id'], 'users_status' => $userROW['status']);
						if($db)
							$mysql->query('UPDATE '.prefix.'_k_online SET sess_id = '.db_squote($id).', last_time = '.db_squote($time).', ip = '.db_squote($ips).', users_status = '.db_squote($userROW['status']).' WHERE users_id = '.db_squote($userROW['id']).' LIMIT 1');
						else
							file_put_contents($confdir.'/result.php', serialize($result));
						break;
					}
					
					$count--;
				}
			}
		}elseif($ipis[$ip]){
				while ($count >= 0){
					$res = $result[$count];
					if (isset($res['ip']) && ($res['ip'] == $ip)){
						$result[$count] = array('sess_id' => $id, 'last_time' => $time, 'users' => $users, 'users_id' => $users_id, 'users_status' => $users_status, 'ip' => $ips); 
						if($db)
							$mysql->query('UPDATE '.prefix.'_k_online SET sess_id = '.db_squote($id).', last_time = '.intval($time).', users = '.db_squote($userROW['name']).', users_id = '.db_squote($userROW['id']).', users_status = '.db_squote($userROW['status']).', ip = '.db_squote($ips).' WHERE ip = '.db_squote($ip).' LIMIT 1');
						else
							file_put_contents($confdir.'/result.php', serialize($result));
						break;
					}
					$count--;
				}
		} else {
			$result[] = array('sess_id' => $id, 'last_time' => $time, 'ip' => $ips, 'users' => $userROW['name'], 'users_id' => $userROW['id'], 'users_status' => $userROW['status']); 
			if($db){
				$mysql->query('INSERT INTO '.prefix.'_k_online (sess_id, last_time, ip, users, users_id, users_status) 
				VALUES ('.db_squote($id).', '.intval($time).', '.db_squote($ips).', '.db_squote($userROW['name']).', '.db_squote($userROW['id']).', '.db_squote($userROW['status']).')');
			} else {
				file_put_contents($confdir.'/result.php', serialize($result));
			}
		}
	} else {
		if($sess[$id]){
			if($sess[$id] < $last_time)
				
				while ($count >= 0){
					$res = $result[$count];
					if (isset($res['sess_id']) && ($res['sess_id'] == $id)){
						$result[$count] = array('ip' => $ips, 'last_time' => $time, 'users' => $users, 'users_id' => $users_id);
						if($db)
							$mysql->query('UPDATE '.prefix.'_k_online SET last_time = '.intval($time).', ip = '.db_squote($ips).' WHERE sess_id = '.db_squote($id).' LIMIT 1');
						else
							file_put_contents($confdir.'/result.php', serialize($result));
						break;
					}
					$count--;
				}
		} else {
			if( $ipis[$ip] ){
				if($ipis[$ip] < $last_time){
					while ($count >= 0){
						$res = $result[$count];
						if (isset($res['ip']) && ($res['ip'] == $ip)){
							$result[$count] = array('sess_id' => $id, 'last_time' => $time, 'users' => $users, 'users_id' => $users_id); 
							if($db)
								$mysql->query('UPDATE '.prefix.'_k_online SET sess_id = '.db_squote($id).', last_time = '.intval($time).' WHERE ip = '.db_squote($ip).' LIMIT 1');
							else
								file_put_contents($confdir.'/result.php', serialize($result));
							break;
						}
						$count--;
					}
				}
			} else {
				$result[] = array('sess_id' => $id, 'last_time' => $time, 'ip' => $ips, 'users' => $users,'users_id' => $users_id,'users_status' => $users_status); 
				if($db){
					$mysql->query('INSERT INTO '.prefix.'_k_online (sess_id, last_time, ip, users, users_id, users_status) 
					VALUES ('.db_squote($id).', '.intval($time).', '.db_squote($ips).', '.db_squote($users).', '.db_squote($users_id).', '.db_squote($users_status).')');
				} else {
					file_put_contents($confdir.'/result.php', serialize($result));
				}
			}
		}
	}
} else {
	$result[] = array('sess_id' => $id, 'last_time' => $time, 'ip' => $ips, 'users' => $users, 'users_id' => $users_id, 'users_status' => $users_status); 
	if($db){
		$mysql->query('INSERT INTO '.prefix.'_k_online (sess_id, last_time, ip, users, users_id, users_status) 
		VALUES ('.db_squote($id).', '.intval($time).', '.db_squote($ips).', '.db_squote($users).', '.db_squote($users_id).', '.db_squote($users_status).')');
	} else {
		file_put_contents($confdir.'/result.php', serialize($result));
	}
}

/* $x = 0;
while ($x<10000){
	$mysql->query('INSERT INTO '.prefix.'_k_online (sess_id, last_time, ip, users, users_id, users_status) 
	VALUES ('.db_squote($id).', '.intval($time).', '.db_squote($ips).', '.db_squote($users).', '.db_squote($users_id).', '.db_squote($users_status).')');
	$result[] = array('sess_id' => $id, 'last_time' => $time, 'ip' => $ips, 'users' => $users, 'users_id' => $users_id, 'users_status' => $users_status); 
	$x++;
} */
//file_put_contents($confdir.'/result.php', serialize($result));
//print "<pre>".var_export($result, true)."</pre>";

$num_guest = 0; $num_user = 0; $num_admin = 0; $num_auth = 0; $num_editor = 0; $num_publicist = 0; $com_url = 0; $num_bot = 0; $num_today = 0; $num_guest_today = 0; $num_team = 0; $num_users = 0;
if(is_array($result)){
	foreach ($result as $row){
		$profile_link = checkLinkAvailable('uprofile', 'show')?
			generateLink('uprofile', 'show', array('name' => $row['users'], 'id' => $row['users_id'])):
			generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('id' => $row['users_id']));
		if($row['last_time'] > $last_time){
			
			if($row['users_status'] == 0){
				$num_guest++;
			}elseif($row['users_status'] == 1){
				$color_start = '<span style="color:red;">'; $color_end = '</span>';
				$admin_url[] = str_replace( array('{url}', '{name}', '{color_start}', '{color_end}'), array($profile_link, $row['users'], $color_start, $color_end), $lang['k_online']['admin_url']);
				$team_url[] = str_replace( array('{url}', '{name}', '{color_start}', '{color_end}'), array($profile_link, $row['users'], $color_start, $color_end ), $lang['k_online']['team_url']);
				$num_team++;
				$num_admin++;
				$num_auth++;
			}elseif($row['users_status'] == 2){
				$color_start = '<span style="color:green;">'; $color_end = '</span>';
				$editor_url[] = str_replace( array('{url}', '{name}', '{color_start}', '{color_end}'), array($profile_link, $row['users'], $color_start, $color_end), $lang['k_online']['editor_url']);
				$team_url[] = str_replace( array('{url}', '{name}', '{color_start}', '{color_end}'), array($profile_link, $row['users'], $color_start, $color_end ), $lang['k_online']['team_url']);
				$num_team++;
				$num_editor++;
				$num_user++;
				$num_auth++;
			}elseif($row['users_status'] == 3){
				$color_start = '<span style="color:blue;">'; $color_end = '</span>';
				$publicist_url[] = str_replace( array('{url}', '{name}', '{color_start}', '{color_end}'), array($profile_link, $row['users'], $color_start, $color_end), $lang['k_online']['publicist_url']);
				$team_url[] = str_replace( array('{url}', '{name}', '{color_start}', '{color_end}'), array($profile_link, $row['users'], $color_start, $color_end ), $lang['k_online']['team_url']);
				$num_team++;
				$num_publicist++;
				$num_user++;
				$num_auth++;
				
			}elseif($row['users_status'] == 4){
				$color_start = ''; $color_end = '';
				$com_url[] = str_replace( array('{url}', '{name}', '{color_start}', '{color_end}'), array($profile_link, $row['users'], $color_start, $color_end), $lang['k_online']['com_url']);
				$user_url[] = str_replace( array('{url}', '{name}'), array($profile_link, $row['users']), $lang['k_online']['user_url']);
				$num_users++;
				$num_com++;
				$num_user++;
				$num_auth++;
			}elseif($row['users_status'] == 5){
				$bot_url[] = str_replace( array('{name}'), array($lang['k_online'][$row['users']]), $lang['k_online']['bot_url']);
				$num_bot++;
			}
		}
		if($row['users_status'] == 0){
			$num_guest_today++;
		}elseif($row['users_status'] == 1 or $row['users_status'] == 2 or $row['users_status'] == 3 or $row['users_status'] == 4){
			switch($row['users_status']){
				case 1: $color_start = '<span style="color:red;">'; $color_end = '</span>'; break;
				case 2: $color_start = '<span style="color:green;">'; $color_end = '</span>'; break;
				case 3: $color_start = '<span style="color:blue;">'; $color_end = '</span>'; break;
				default: $color_start = ''; $color_end = '';
			}
			$last_date = date('H:i:s', intval($row['last_time']));
			$num_today++;
			$today_users[] = str_replace( array('{url}', '{name}', '{date}', '{color_start}', '{color_end}'), array($profile_link, $row['users'], $last_date, $color_start, $color_end), $lang['k_online']['today_url']);
		}elseif($row['users_status']==5){
			$last_date = date('H:i:s', intval($row['last_time']));
			$num_today++;
			$today_users[] = str_replace( array('{name}', '{date}'), array($lang['k_online'][$row['users']], $last_date), $lang['k_online']['today_bot_url']);
		}
	}
}

if( is_array($admin_url) )
	$activ_adm = str_replace( array('{name}'), array(implode(", ", $admin_url)), $lang['k_online']['admin_list']);
if( is_array($team_url) )
	$activ_team = str_replace( array('{name}'), array(implode(", ", $team_url)), $lang['k_online']['team_list']);
if( is_array($editor_url) )
	$activ_red = str_replace( array('{name}'), array(implode(", ", $editor_url)), $lang['k_online']['editor_list']);
if( is_array($publicist_url) )
	$activ_pub = str_replace( array('{name}'), array(implode(", ", $publicist_url)), $lang['k_online']['publicist_list']);
if( is_array($com_url) )
	$activ_com = str_replace( array('{name}'), array(implode(", ", $com_url)), $lang['k_online']['com_list']);
if( is_array($user_url) )
	$activ_user = str_replace( array('{name}'), array(implode(", ", $user_url)), $lang['k_online']['user_list']);
if( is_array($bot_url) )
	$activ_bot = str_replace( array('{name}'), array(implode(", ", $bot_url)), $lang['k_online']['bot_list']);
if(is_array($today_users))
	$was_now = str_replace( array('{name}', '{num_today}'), array(implode(", ", $today_users), $num_today), $lang['k_online']['today_list']);

$tVars = array (
	'num_users' => $num_user,
	'num_guest' => $num_guest,
	'num_admin' => $num_admin,
	'num_auth' => $num_auth,
	'num_team' => $num_team,
	'num_users' => $num_users,
	'num_bot' => $num_bot,
	'num_today' => $num_today,
	'num_guest_today' => $num_guest_today,
	'all' => $num_guest+$num_user+$num_admin+$num_bot,
	'entries_admin' => array(
		'true' => empty($activ_adm)?0:1,
		'print'=> $activ_adm
	),
	'entries_comm' => array(
		'true' => empty($activ_com)?0:1,
		'print'=> $activ_com
	),
	'entries_red' => array(
		'true' => empty($activ_red)?0:1,
		'print'=> $activ_red
	),
	'entries_pub' => array(
		'true' => empty($activ_pub)?0:1,
		'print'=> $activ_pub
	),
	'entries_team' => array(
		'true' => empty($activ_team)?0:1,
		'print'=> $activ_team
	),
	'entries_user' => array(
		'true' => empty($activ_user)?0:1,
		'print'=> $activ_user
	),
	'entries_bot' => array(
		'true' => empty($activ_bot)?0:1,
		'print'=> $activ_bot
	),
	'today' => array(
		'true' => empty($was_now)?0:1,
		'print'=> $was_now
	),
);

	$template['vars']['k_online'] = $xt->render($tVars);
}

function filter_bots($u_agent){
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