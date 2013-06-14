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
 � ��������� ����������� ������� �� ������ 
 ������������. ��, ��� �������� � ������, ������ 
 ���������� � ������. :))
-----------------------------------------------------
 ������ ��� ������� ���������� �������
=====================================================
*/

if (!defined('NGCMS')) die ('HAL');

lang_forum('forum');

#############################
## ��������������� ������� ##
#############################
function update_forum($topic_id, $topic_title, $int_topic, $post_date, $post_id, $user_name, $user_id, $forum_id)
{global $mysql;
	$mysql->query('UPDATE '.prefix.'_forum_forums SET int_post = int_post + 1, int_topic = int_topic + '.$int_topic.',  l_topic_id = '.securemysql($topic_id).', l_topic_title = '.securemysql($topic_title).', l_post='.securemysql($post_id).',l_date ='.securemysql($post_date).', l_author = '.securemysql($user_name).', l_author_id = '.securemysql($user_id).' WHERE id = '.securemysql($forum_id).' LIMIT 1');
}

function update_topic($post_date, $post_id,$user_name, $user_id, $topic_id)
{global $mysql;
	$mysql->query('UPDATE '.prefix.'_forum_topics SET l_date = '.securemysql($post_date).', l_post = '.securemysql($post_id).', l_author = '.securemysql($user_name).', l_author_id = '.securemysql($user_id).', int_post = int_post + 1 WHERE id = '.securemysql($topic_id).' LIMIT 1');
}

/* �������� */
/* function update_post_forum($topic_id, $topic_title, $topic_date, $user_name, $user_id, $forum_id)
{global $mysql;
	
	list($topic_sum, $post_sum) = $mysql->record('SELECT COUNT(*), SUM(int_post+1) FROM '.prefix.'_forum_topics WHERE id= '.securemysql($forum_id));
	
	$mysql->query('UPDATE '.prefix.'_forum_forums SET int_post = '.securemysql($post_sum).', int_topic = '.securemysql($topic_sum).',l_topic_id = '.securemysql($topic_id).', l_topic_title = '.securemysql($topic_title).', l_date ='.securemysql($topic_date).', l_author = '.securemysql($user_name).', l_author_id = '.securemysql($user_id).' WHERE fid = '.securemysql($forum_id).' LIMIT 1');
} */
/* � ���� ���� */
/* function update_topic($post_date, $user_name, $user_id, $topic_id)
{global $mysql;
	
	$count = $mysql->result('SELECT COUNT(*) FROM '.prefix.'_forum_posts WHERE topic_id='.securemysql($topic_id)) - 1;
	
	$mysql->query('UPDATE '.prefix.'_forum_topics SET last_post = '.securemysql($post_date).', last_poster = '.securemysql($user_name).', last_poster_id = '.securemysql($user_id).', num_replies = '.securemysql($count).' WHERE topic_id = '.securemysql($topic_id).' LIMIT 1');
} */


/*� �������� function update_deltopic_forum($topic_id)
{global $mysql;
	$mysql->query("UPDATE ".prefix."_topics SET num_replies = num_replies - 1 WHERE topic_id = '".intval($topic_id)."'");
} */

function delete_post($post_id, $topic_id)
{global $mysql;
	$mysql->query('DELETE FROM '.prefix.'_forum_posts WHERE id = '.securemysql($post_id).' LIMIT 1');
	
	$num_replies = $mysql->result('SELECT COUNT(*) FROM '.prefix.'_forum_posts WHERE tid = '.securemysql($topic_id).'') - 1;
	
	$result = $mysql->record('SELECT id, author, author_id, c_data FROM '.prefix.'_forum_posts WHERE tid = '.securemysql($topic_id).' ORDER BY c_data DESC LIMIT 1');
	
	$mysql->query('UPDATE '.prefix.'_forum_topics SET l_date='.securemysql($result['c_data']).', l_author = '.securemysql($result['author']).', l_author_id = '.securemysql($result['author_id']).', int_post='.securemysql($num_replies).' WHERE id='.securemysql($topic_id).' LIMIT 1');
	
	$mysql->query('UPDATE '.prefix.'_users SET int_post = int_post - 1 WHERE id = '.securemysql($result['author_id']).' LIMIT 1');
}

function delete_topic($topic_id)
{global $mysql;
	$mysql->query('DELETE FROM '.prefix.'_forum_topics where id = '.securemysql($topic_id).' LIMIT 1');
	
	//print "<pre>".var_export($result, true)."</pre>";
	$mysql->query('DELETE FROM '.prefix.'_forum_posts WHERE tid = '.securemysql($topic_id).'');
	$mysql->query('DELETE FROM '.prefix.'_forum_subscriptions WHERE tid = '.securemysql($topic_id).'');
	
	foreach ($mysql->select('SELECT COUNT(*), author_id FROM '.prefix.'_forum_posts WHERE tid = '.securemysql($topic_id).' GROUP BY author_id') as $row){
		$mysql->query('UPDATE '.prefix.'_users SET int_post = int_post - '.securemysql($row[0]).' WHERE id = '.securemysql($row[1]).' LIMIT 1');
	}
}

function global_update_forum($forum_id)
{global $mysql, $userROW, $config;
	
	$record = $mysql->record('SELECT COUNT(*) as num_topic, SUM(int_post) as num_post FROM '.prefix.'_forum_topics WHERE fid = '.securemysql($forum_id).'');
	
	$num_post = $record['num_post'] + $record['num_topic'];
	
	$result = $mysql->record('SELECT title, l_date, l_post, id, l_author, l_author_id FROM '.prefix.'_forum_topics WHERE fid = '.securemysql($forum_id).' ORDER BY l_date DESC LIMIT 1');
	if ($result){
		$mysql->query('UPDATE '.prefix.'_forum_forums SET int_topic = \''.$record['num_topic'].'\', int_post =\''.$num_post.'\', l_topic_id = \''.$result['id'].'\', l_date = \''.$result['l_date'].'\', l_post = \''.$result['l_post'].'\', l_author_id = \''.$result['l_author_id'].'\', l_author= \''.$result['l_author'].'\', l_topic_title = \''.$result['title'].'\' WHERE id = \''.$forum_id.'\' LIMIT 1');
	} else {
		$mysql->query('UPDATE '.prefix.'_forum_forums SET int_topic = \'0\', int_post = \'0\', l_date = NULL, l_post = NULL, l_author_id = NULL, l_author = NULL, l_topic_title = NULL WHERE id = \''.$forum_id.'\' LIMIT 1');
	}
}

function delete_thank($rows){
global $mysql;
	
	if(isset($rows['tid']) ){
		$mysql->query('DELETE FROM '.prefix.'_forum_thank  where tid = '.securemysql($rows['tid']));
	} elseif($rows['pid']){
		$mysql->query('DELETE FROM '.prefix.'_forum_thank  where pid = '.securemysql($rows['pid']));
	}
	
}

function delete_attach($rows)
{global $mysql, $userROW;
	
	if(isset($rows['id'])){
		foreach ($rows['id']['attach_delete'] as $id){
			$row = $mysql->record('SELECT * FROM '.prefix.'_forum_attach WHERE id = '.securemysql($id).' LIMIT 1');
			if(($row['author_id'] == $userROW['id']) OR $userROW['status'] == 1){
				unlink(files_dir . 'forum/' . $row['location'] . '/' . $row['file']);
				rmdir(files_dir . 'forum/' . $row['location']);
				$mysql->query('DELETE FROM '.prefix.'_forum_attach where id = '.securemysql($id).' LIMIT 1');
			}
		}
	}elseif(isset($rows['pid'])){
		foreach ($mysql->select('SELECT * FROM '.prefix.'_forum_attach WHERE pid = '.securemysql($rows['pid']).'') as $row){
			if(($row['author_id'] == $userROW['id']) OR $userROW['status'] == 1){
				unlink(files_dir . 'forum/' . $row['location'] . '/' . $row['file']);
				rmdir(files_dir . 'forum/' . $row['location']);
				$mysql->query('DELETE FROM '.prefix.'_forum_attach where pid = '.securemysql($rows['pid']).' LIMIT 1');
			}
		}
		
	}elseif(isset($rows['tid'])){
		if($userROW['status'] == 1){
			foreach ($mysql->select('SELECT * FROM '.prefix.'_forum_attach WHERE tid = '.securemysql($rows['tid']).'') as $row){
				//print "<pre>".var_export($row, true)."</pre>";
				unlink(files_dir . 'forum/' . $row['location'] . '/' . $row['file']);
				rmdir(files_dir . 'forum/' . $row['location']);
				$mysql->query('DELETE FROM '.prefix.'_forum_attach where tid = '.securemysql($rows['tid']).' LIMIT 1');
			}
		}
	}
}

//��� ��������
function update_users_mes()
{global $mysql, $config, $userROW;
	
	$time = time() + ($config['date_adjust'] * 60);
	
	if(is_array($userROW)){
		$num = $mysql->result('SELECT COUNT(*) FROM '.prefix.'_forum_posts WHERE author_id = '.securemysql($userROW['id']));
		$mysql->query('UPDATE '.prefix.'_users SET int_post = '.securemysql($num).', l_post = '.securemysql($time).' WHERE id = '.securemysql($userROW['id']).' LIMIT 1');
	}
}

function subscribe($user_id, $topic_id)
{global $mysql, $userROW;
	
	$result = $mysql->result('SELECT 1 FROM '.prefix.'_forum_subscriptions WHERE tid = '.securemysql($topic_id).' and uid = '.securemysql($user_id).' LIMIT 1');
	
	if(empty($result))
		$mysql->query('INSERT INTO '.prefix.'_forum_subscriptions (
				uid,
				tid
			) VALUES (
				'.securemysql($user_id).',
				'.securemysql($topic_id).'
			)
		');
}

function send_subscribe($topic_id, $last_post_id, $name_topic, $message)
{global $mysql, $userROW, $config, $SYSTEM_FLAGS, $twig;
	
	$link_pos = link_post($last_post_id);
	
	$tpath = locatePluginTemplates(array('htmail'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	$xt = $twig->loadTemplate($tpath['htmail'].'htmail.tpl');
	
	foreach ($mysql->select('SELECT u.id, u.mail, u.name FROM '.prefix.'_users AS u LEFT JOIN '.prefix.'_forum_subscriptions AS s ON s.uid = u.id WHERE s.tid = '.securemysql($topic_id).' and s.uid != '.securemysql($userROW['id']).'', 1) as $row){
		
		$profile_link = link_profile($row['id'], '', $row['name']);
		$fromprofile_link = link_profile($userROW['id'], '', $userROW['name']);
		
		$tVars = array(
			'from_user' => '<a href=\'http://'.$SYSTEM_FLAGS['mydomains'][0].$fromprofile_link.'\'>'.$userROW['name'].'</a>',
			'user' => '<a href=\'http://'.$SYSTEM_FLAGS['mydomains'][0].$profile_link.'\'>'.$row['name'].'</a>',
			'url' => '<a href=\'http://'.$SYSTEM_FLAGS['mydomains'][0].$link_pos.'#'.$last_post_id.'\'>'.$name_topic.'</a>',
			'message' => bb_codes($message),
		
		);
		
		if( filter_var($row['mail'], FILTER_VALIDATE_EMAIL) )
			zzMail($row['mail'], '����� ��������� �� ������', $xt->render($tVars), '', false, 'text/html');
	}
}

function LoadVariables(){
	$tpath = locatePluginTemplates(array(':'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	
	return parse_ini_file($tpath[':'].'/variables.ini', true);
}

function generatePagination_forum($countPages, $pageNo, $paginationParams, $navigations, $intlink = false){
	$pages = array();
	$link_to_all = false;
	
	if (empty($pageNo)){
		$pageNo = 1;
		$link_to_all = true;
	}
	
	if ($pageNo > 5){
		$pages[] = str_replace( array('%link%', '%page%'), array(generatePageLink($paginationParams, 1, $intlink), 1), $navigations['link_page']);
		if ($pageNo > 5) $pages[] = $navigations['dots'];
	}
	for ($current = ($pageNo == 5) ? $pageNo - 3 : $pageNo - 2, $stop = ($pageNo + 4 == $countPages) ? $pageNo + 4 : $pageNo + 3; $current < $stop; ++$current){
		if ($current < 1 || $current > $countPages) continue;
		else if ($current != $pageNo || $link_to_all)
			$pages[] = str_replace( array('%link%', '%page%'), array(generatePageLink($paginationParams, $current, $intlink), $current), $navigations['link_page']);
		else
			$pages[] = str_replace('%page%',$current, $navigations['current_page']);
	}
	if ($pageNo <= ($countPages-3)){
		if ($pageNo != ($countPages-3) && $pageNo != ($countPages-4))
			$pages[] = $navigations['dots'];
		$pages[] = str_replace( array('%link%', '%page%'), array(generatePageLink($paginationParams, $countPages, $intlink), $countPages), $navigations['link_page']);
	}
	
	return implode(' ', $pages);
}

function status_forum($date)
{global $userROW;
	
	if(is_array($userROW)){
		if($date > $userROW['last'])
			return 1;
		else
			return 0;
	} return 0;
}
function update_review($id)
{global $mysql;
	if(empty($_SESSION["myform_{$id}"]))
		$mysql->query("UPDATE `".prefix."_forum_topics` set `int_views` = `int_views` + 1 WHERE `id` = ".securemysql("{$id}")." LIMIT 1");
	
	$_SESSION["myform_{$id}"] = "ubdate";
}

function update_lastdate_user($update = false)
{global $mysql, $config, $userROW;
	
	$time = time() + ($config['date_adjust'] * 60);
	
	$last_time = time() + ($config['date_adjust'] * 60) - pluginGetVariable('forum', 'online_time');
	
	if($userROW['last'] < $last_time or $update = true)
		$mysql->query('UPDATE '.prefix.'_users SET last = '.intval($time).' WHERE id = '.intval($userROW['id']));
}

function check_online_forum()
{global $mysql, $config, $userROW, $ip, $online, $ipis, $CurrentHandler;
	
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
		$users = '�����';
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
	
	//print "<pre>".var_export($location, true)."</pre>";
	
	if(is_array($online)){
		$user = array(); $sess = array(); $ipis = array(); $count = count($online);
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
			$ipis[$row['ip']] = $row['last_time'];
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
			}elseif($ipis[$ip]){
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
				if( $ipis[$ip] ){
					while ($count >= 0){
						$res = $online[$count];
						if (isset($res['ip']) && ($res['ip'] == $ip)){
							$online[$count] = array('sess_id' => $res['sess_id'], 'last_time' => $res['last_time'], 'ip' => $res['ip'], 'users' => $res['users'], 'users_id' => $res['users_id'], 'users_status' => $res['users_status'], 'location' => $location);
							file_put_contents(FORUM_CACHE.'/online.php', '<?php'."\n\n".'$online = '.var_export($online, true).';'."\n\n".'$date_update = \''.$date_today.'\';'); break;
						}
						$count--;
					}
					if($ipis[$ip] < $last_time){
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

function rss_export_generate_forum($tid = ''){
global $config, $mysql, $show_main, $twig;
	
	$show_main = true;
	
	$tpath = locatePluginTemplates(array('rss_feed'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	
	$limitCount = intval(pluginGetVariable('forum', 'rss_per_page'));
	
	if (($limitCount < 2) or ($limitCount > 2000)) $limitCount = 2;
	
	if(empty($tid))
		$where = '';
	else
		$where = 'WHERE t.fid = '.securemysql($tid);
	
	$select = $mysql->select('SELECT p.author_id, p.author, p.message, p.id, p.c_data, t.title as Ttitle, t.l_date  FROM '.prefix.'_forum_posts AS p
		LEFT JOIN '.prefix.'_forum_topics AS t ON t.id = p.tid 
		'.$where.'
		ORDER BY p.id DESC LIMIT '.$limitCount);
	
	if(empty($select)){
		$show_main = true;
		return $output = information('RSS ���� ���� �� ��������', $title = '����������');
	}
	
	foreach ($select as $row){
		$tEntry[] = array(
			'profile_link' => link_profile($row['author_id'], '', $row['author']),
			'profile' => $row['author'],
			'content' => bb_codes($row['message']),
			'pid' => $row['id'],
			'topic_link' => $config['home_url'].link_topic($row['id'], 'pid'),
			'Ttitle' => $row['Ttitle'],
			'c_data' => $row['c_data'],
		);
	}
	
	$xt = $twig->loadTemplate($tpath['rss_feed'].'rss_feed.tpl');
	$tVars = array(
		'entries' => $tEntry,
		'title' => pluginGetVariable('forum', 'forum_title'),
		'home' => $config['home_url'].link_home(),
		'entries' => $tEntry,
		'date' => $row['l_date'],
	);
	
	echo $xt->render($tVars);

}

function suppress_show()
{global $SUPRESS_TEMPLATE_SHOW, $SUPRESS_MAINBLOCK_SHOW;
	$SUPRESS_TEMPLATE_SHOW = pluginGetVariable('forum','display_main');
	$SUPRESS_MAINBLOCK_SHOW = 0;
	
	if($SUPRESS_TEMPLATE_SHOW){
		actionDisable('index');
		actionDisable('usermenu');
		actionDisable('index_post');
		actionDisable('maintenance');
	}
}

function banned_users()
{global $ip, $ban;
	
	if(file_exists(FORUM_CACHE.'/ban.php'))
		include(FORUM_CACHE.'/ban.php');
	else
		file_put_contents(FORUM_CACHE.'/ban.php', '<?php'."\n\n".'$ban = '.var_export(array(), true).';'."\n\n");
}

function lang_forum($lang){
	global $config, $lang_forum, $twig;
	$tpath = locatePluginTemplates(array(':'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	$lang_forum = parse_ini_file($tpath[':'].'lang/'.$config['default_lang'].'/'.$lang.'.ini', true);
	$twig->addGlobal('lang_forum', $lang_forum);
}

function add_banned_users()
{global $ban, $ip;
	$ban[$ip] = $ban[$ip] + 1;
	
	if(file_exists(FORUM_CACHE.'/ban.php'))
		file_put_contents(FORUM_CACHE.'/ban.php', '<?php'."\n\n".'$ban = '.var_export($ban, true).';'."\n\n");
}

function checkPermission_forum($moder_array = ''){
	global $userROW, $group_perm;
	
	$group = array('1' => 
		array(
			'read' => true, 
			'replies' => true, 
			'modify' => true, 
			'modify_your' => true, 
			'remove' => true,
			'remove_your' => true,
			'modunit' => true,
			'topic_closed' => true,
			'topic_remove' => true,
			'topic_move' => true,
		),'2' => 
		array(
			'read' => true, 
			'replies' => true, 
			'modify' => true, 
			'modify_your' => true, 
			'remove' => true,
			'remove_your' => true,
		),'3' => 
		array(
			'read' => true, 
			'replies' => true, 
			'modify' => true, 
			'modify_your' => true, 
			'remove' => true,
			'remove_your' => true,
		),'4' => 
		array(
			'read' => true, 
			'replies' => true, 
			'modify' => true, 
			'modify_your' => true, 
			'remove' => true,
			'remove_your' => true,
		),'5' => 
		array(
			'read' => true, 
			'replies' => false, 
			'modify' => false, 
			'modify_your' => false, 
			'remove' => false,
			'remove_your' => false,
		)
	); 
	
	if(isset($moder_array) && $moder_array)
		foreach (unserialize($moder_array) as $row){
			$moder[] = $row['id'];
		}
	
	if(is_array($userROW) && array_key_exists($userROW['id'], $moder)){
		$group[$userROW['status']]['modunit'] = true;
		$group[$userROW['status']]['topic_closed'] = true;
		$group[$userROW['status']]['topic_remove'] = true;
		$group[$userROW['status']]['topic_move'] = true;
	} else {
		$group[$userROW['status']]['modunit'] = false;
		$group[$userROW['status']]['topic_closed'] = false;
		$group[$userROW['status']]['topic_remove'] = false;
		$group[$userROW['status']]['topic_move'] = false;
	}
	
	$group_perm = $group[$userROW['status']];
	//print "<pre>".var_export($group, true)."</pre>";
}

//������� � ������
/* function show_date($row)
{global $config;
	
	$time = time() + ($config['date_adjust'] * 60);
	
	if(empty($row)) return;
	
	$date = date('Y-m-d', $row);
	$today = date('Y-m-d', $time);
	$yesterday = date('Y-m-d', $time-86400);
	
	if ($date == $today)
		$date = '������� '.date('H:i', $row);
	elseif ($date == $yesterday)
		$date = '����� '.date('H:i', $row);
	else
		$date = date('Y-m-d H:i', $row);
	
	return $date;
} */

function forum_upload_files(){
	$max_file_size = 7 * 1024 * 1024;
	$extensions = array_map('trim', explode(',', 'zip, rar, 7zip, 7z, gif, jpg, png, jpe, jpeg'));
	
	if(empty($_FILES['files']['name']))
		return 0;
	
	if (!is_uploaded_file($_FILES['files']['tmp_name']))
		return 0;
	
	$ext = pathinfo($_FILES['files']['name'], PATHINFO_EXTENSION);
	
	if(!in_array($ext, $extensions))
		return 0;
	
	if ($_FILES['files']['size'] > $max_file_size)
		return 0;
	
	if(!is_writable(files_dir . 'forum/'))
		return 0;
	
	$name_file = forum_translit($_FILES['files']['name']);
	$name_file = basename($name_file, '.'.$ext);
	$name_file = preg_replace("/[^\w\x7F-\xFF]/", '', $name_file);
	
	$Ffile = $name_file . '.' . $ext;
	
	$new = date('Ymd').rand(1000,9999);
	mkdir(files_dir . 'forum/' . $new, 0777);
	
	if(move_uploaded_file($_FILES['files']['tmp_name'], files_dir . 'forum/' . $new . '/' . $Ffile))
		chmod(files_dir . 'forum/' . $new . '/' . $Ffile, 0644);
	else
		return 0;
	
	return array($Ffile, $_FILES['files']['size'], $new);
}

function forum_translit($string){
	$converter = array(
		'�' => 'a',		'�' => 'b',		'�' => 'v',
		'�' => 'g',		'�' => 'd',		'�' => 'e',
		'�' => 'e',		'�' => 'zh',	'�' => 'z',
		'�' => 'i',		'�' => 'y',		'�' => 'k',
		'�' => 'l',		'�' => 'm',		'�' => 'n',
		'�' => 'o',		'�' => 'p',		'�' => 'r',
		'�' => 's',		'�' => 't',		'�' => 'u',
		'�' => 'f',		'�' => 'h',		'�' => 'c',
		'�' => 'ch',	'�' => 'sh',	'�' => 'sch',
		'�' => "'",		'�' => 'y',		'�' => "'",
		'�' => 'e',		'�' => 'yu',	'�' => 'ya',
		
		'�' => 'A',		'�' => 'B',		'�' => 'V',
		'�' => 'G',		'�' => 'D',		'�' => 'E',
		'�' => 'E',		'�' => 'Zh',	'�' => 'Z',
		'�' => 'I',		'�' => 'Y',		'�' => 'K',
		'�' => 'L',		'�' => 'M',		'�' => 'N',
		'�' => 'O',		'�' => 'P',		'�' => 'R',
		'�' => 'S',		'�' => 'T',		'�' => 'U',
		'�' => 'F',		'�' => 'H',		'�' => 'C',
		'�' => 'Ch',	'�' => 'Sh',	'�' => 'Sch',
		'�' => "'",		'�' => 'Y',		'�' => "'",
		'�' => 'E',		'�' => 'Yu',	'�' => 'Ya',
		' ' => '_',
	);
	return strtr($string, $converter);
}

function forum_memory_usage(){
	if ( function_exists('memory_get_usage') )
		return round( memory_get_usage() / 1024  / 1024, 2) . 'MB';
	else
		return '<b>����������</b>';
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

function redirect_forum($url){
	if (headers_sent()){
		echo "<script>document.location.href='{$url}';</script>\n";
	} else {
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: {$url}");
	}
	exit;
}

function my_error_handler($in_errno, $in_errstr, $in_errfile, $in_errline, $in_errcontext){
	$errs = array(
		2 => 'E_WARNING',
		8 => 'E_NOTICE',
		256 => 'E_USER_ERROR',
		512 => 'E_USER_WARNING',
		1024 => 'E_USER_NOTICE',
		2048 => 'E_STRICT'
	);
	
	$err_type = '';
	foreach ($errs as $val => $errstr){
		if (($in_errno & $val) != 0)
				$err_type .= $errstr;
	}
	
	echo <<<EOTABLE
	<div style='font: 12px verdana; background-color: #EEEEEE; border: #ABCDEF 1px solid; margin: 1px; padding: 3px;'>
		<span style='color: red;'>��������� ������!</span><br />
		<span style=\"font: 9px arial;\"><b>{$err_type}:</b>({$in_errfile}, line {$in_errline})</span><br />
		<span style=\"font: 9px arial;\">{$in_errstr}</span>
	</div>
EOTABLE;
	if($in_errno == E_USER_ERROR)
		exit;
	
}