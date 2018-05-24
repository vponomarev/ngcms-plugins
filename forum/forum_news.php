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
include_once(dirname(__FILE__) . '/includes/constants.php');
include_once(dirname(__FILE__) . '/includes/security.php');
include_once(dirname(__FILE__) . '/includes/functions.php');
include_once(dirname(__FILE__) . '/includes/cache.php');

class CreateNewsFilter extends NewsFilter {

	function addNewsForm(&$tvars) {

		global $mysql, $plugin, $twig;
		$tpath = locatePluginTemplates(array('show_forum', 'show_entries'), 'forum', 1, '', 'news');
		$xt = $twig->loadTemplate($tpath['show_entries'] . 'show_entries.tpl');
		$xg = $twig->loadTemplate($tpath['show_forum'] . 'show_forum.tpl');
		$result = $mysql->select('SELECT * FROM ' . prefix . '_forum_forums ORDER BY position ASC');
		$entries = array();
		foreach ($result as $row_2) {
			if ($row_2['parent'] != 0) {
				$tVars = array(
					'forum_id'   => $row_2['id'],
					'forum_name' => $row_2['title'],
				);
				$entries[$row_2['parent']] .= $xt->render($tVars);
			}
		}
		$output = '';
		foreach ($result as $row) {
			if ($row['parent'] == '0') {
				$tVars = array(
					'forum_name' => $row['title'],
					'entries'    => array(
						'true'  => isset($entries[$row['id']]) ? 1 : 0,
						'print' => isset($entries[$row['id']]) ? $entries[$row['id']] : ''
					),
				);
				$output .= $xg->render($tVars);
			}
		}
		$tvars['options_forum'] = $output;
	}

	function addNews(&$tvars, &$SQL) {

		if (isset($_REQUEST['create_forum']) && $_REQUEST['create_forum']) {
			$forum_id = intval($_REQUEST['forum_id']);
			if (empty($forum_id))
				return false;
			else
				return true;
		}

		return true;
	}

	function addNewsNotify(&$tvars, $SQL, $newsid) {

		global $mysql, $userROW, $config, $ip;
		if (isset($_REQUEST['create_forum']) && $_REQUEST['create_forum']) {
			$subject = secureinput($_REQUEST['title']);
			$message = isset($_REQUEST['ng_news_content']) ? secureinput($_REQUEST['ng_news_content']) : secureinput($_REQUEST['ng_news_content_short']);
			$id = intval($_REQUEST['forum_id']);
			$time = time() + ($config['date_adjust'] * 60);
			$mysql->query('insert into ' . prefix . '_forum_topics (
					author,
					author_id,
					title,
					c_data,
					l_date,
					l_author_id,
					l_author,
					fid
				)values(
					' . securemysql($userROW['name']) . ', 
					' . securemysql($userROW['id']) . ', 
					' . securemysql($subject) . ', 
					' . securemysql($time) . ', 
					' . securemysql($time) . ', 
					' . securemysql($userROW['id']) . ', 
					' . securemysql($userROW['name']) . ', 
					' . securemysql($id) . '
				)
			');
			$topic_id = $mysql->lastid('forum_topics');
			$mysql->query('insert into ' . prefix . '_forum_posts (
					author, 
					author_id, 
					author_ip, 
					message, 
					c_data, 
					tid
				)values(
					' . securemysql($userROW['name']) . ', 
					' . securemysql($userROW['id']) . ', 
					' . securemysql($ip) . ', 
					' . securemysql($message) . ', 
					' . securemysql($time) . ', 
					' . securemysql($topic_id) . '
				)
			');
			$post_id = $mysql->lastid('forum_posts');
			$mysql->query('UPDATE ' . prefix . '_news SET tid = ' . intval($topic_id) . ' WHERE id = ' . securemysql($newsid) . ' LIMIT 1');
			update_forum($topic_id, $subject, 1, $time, $userROW['name'], $userROW['id'], $id);
			$mysql->query('UPDATE ' . prefix . '_forum_topics SET l_post = ' . securemysql($post_id) . ' WHERE id = ' . securemysql($topic_id) . ' LIMIT 1');
			$mysql->query('UPDATE ' . prefix . '_users SET int_post = int_post + 1, l_post = ' . securemysql($time) . ' WHERE id = ' . securemysql($userROW['id']) . ' LIMIT 1');
			generate_index_cache(true);
			generate_statistics_cache(true);
		}
	}

	function editNewsForm($newsID, $SQLold, &$tvars) {

		global $mysql;

		return true;
	}

	function editNews($newsID, $SQLold, &$SQLnew, &$tvars) {

		global $mysql, $config;

		return true;
	}
}

register_filter('news', 'forum', new CreateNewsFilter);