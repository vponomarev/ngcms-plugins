<?php
/*
=====================================================
 Создание тем с админки v 0.01
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
	exit('HAL');
LoadPluginLibrary('comments', 'lib');
include_once(dirname(__FILE__) . '/includes/constants.php');
include_once(dirname(__FILE__) . '/includes/security.php');
include_once(dirname(__FILE__) . '/includes/functions.php');
include_once(dirname(__FILE__) . '/includes/cache.php');
include_once(dirname(__FILE__) . '/includes/rewrite.php');

class CreatePostFilter extends FilterComments {

	// Form generator
	function addCommentsForm($newsID, &$tvars) {

		return 1;
	}

	function addComments($userRec, $newsRec, &$tvars, &$SQL) {

		return 1;
	}

	// Adding notificator [ after successful adding ]
	function addCommentsNotify($userRec, $newsRec, &$tvars, $SQL, $commID) {

		global $mysql, $userROW, $ip;
		if (isset($newsRec['tid']) && $newsRec['tid'] && is_array($userROW)) {
			$row = $mysql->record('SELECT * FROM ' . prefix . '_forum_posts WHERE tid = ' . securemysql($newsRec['tid']) . ' ORDER BY id DESC LIMIT 1');
			if ($row['author_id'] == $userROW['id']) {
				$mysql->query('UPDATE ' . prefix . '_forum_posts SET 
					message = ' . securemysql($row['message'] . "\n" . '[color=red]Добавлено ' . date("j-m-Y, H:i", $SQL['postdate']) . "[/color]\n" . $SQL['text']) . '
					WHERE id = ' . securemysql($row['id']) . ' LIMIT 1
				');
				$post_id = $row['id'];
			} else {
				$mysql->query('INSERT INTO ' . prefix . '_forum_posts (
							author,
							author_id,
							message,
							author_ip,
							c_data,
							tid
						) values (
							' . securemysql($userROW['name']) . ',
							' . securemysql($userROW['id']) . ',
							' . securemysql($SQL['text']) . ',
							' . securemysql($ip) . ',
							' . securemysql($SQL['postdate']) . ',
							' . securemysql($newsRec['tid']) . ')
						');
				$post_id = $mysql->lastid('forum_posts');
				$result = $mysql->record('SELECT fid, title FROM ' . prefix . '_forum_topics WHERE id = ' . securemysql($newsRec['tid']) . ' LIMIT 1');
				update_users_mes();
				update_topic($SQL['postdate'], $userROW['name'], $userROW['id'], $newsRec['tid']);
				update_forum($newsRec['tid'], $result['title'], 0, $SQL['postdate'], $userROW['name'], $userROW['id'], $result['fid']);
			}
			send_subscribe($newsRec['tid'], $post_id, $result['title'], $SQL['text'], $userROW['name']);
		}

		/*
		print_r ($newsRec['tid']);
		print_r ($SQL); */

		return 1;
	}
}

register_filter('comments', 'forum', new CreatePostFilter);