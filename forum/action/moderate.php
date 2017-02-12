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
$tpath = locatePluginTemplates(array('show_moderate', 'entries', ':'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum', 'localskin'), 'moderate');
if (isset($params['tid']))
	$tid = isset($params['tid']) ? intval($params['tid']) : 0;
else
	$tid = isset($_REQUEST['tid']) ? intval($_REQUEST['tid']) : 0;
if (isset($params['metod']))
	$metod = isset($params['metod']) ? secureinput($params['metod']) : '';
else
	$metod = isset($_REQUEST['metod']) ? secureinput($_REQUEST['metod']) : '';
if (empty($tid)) {
	return redirect_forum(link_home());
}
if (empty($metod)) {
	return redirect_forum(link_topic($tid));
}
$xt = $twig->loadTemplate($tpath['show_moderate'] . 'show_moderate.tpl');
$xm = $twig->loadTemplate($tpath['entries'] . 'entries.tpl');
$sql = "SELECT f.id as fid, f.moderators, t.fid, t.id as tid, t.author, t.author_id, t.title, t.c_data FROM " . prefix . "_forum_topics AS t INNER JOIN " . prefix . "_forum_forums AS f ON f.id = t.fid WHERE t.id = " . securemysql("{$tid}") . " LIMIT 1";
$rows = $mysql->record($sql);
//print "<pre>".var_export($rows, true)."</pre>";
//print "<pre>".var_export(moder_perm($rows['fid'], 'topic_move', $moderators), true)."</pre>";
switch ($metod) {
	case 'delete':
		if (moder_perm($rows['tid'], 'topic_remove', $row_['moderators']) || $userROW['status'] == 1) {
			delete_topic($rows['tid']);
		}

		return redirect_forum(link_forum($rows['fid']));
		break;
	case 'move':
		if (isset($_REQUEST['submit'])) {
			$move_to_forum = isset($_REQUEST['move_to_forum']) ? intval($_REQUEST['move_to_forum']) : 0;
			if (empty($metod))
				return redirect_forum(link_topic($tid));
			$row_ = $mysql->record("SELECT * FROM " . prefix . "_forum_forums WHERE id = " . securemysql("{$move_to_forum}") . " LIMIT 1");
			if (moder_perm($row_['id'], 'topic_move', $row_['moderators']) || $userROW['status'] == 1) {
				$mysql->query('UPDATE ' . prefix . '_forum_topics SET fid = \'' . $move_to_forum . '\' WHERE id = ' . securemysql($tid) . ' LIMIT 1');
				global_update_forum($move_to_forum);
				global_update_forum($rows['fid']);
				generate_index_cache(true);
			}

			return redirect_forum(link_topic($tid));
		}
		if (file_exists(FORUM_CACHE . '/cache_index.php'))
			include(FORUM_CACHE . '/cache_index.php');
		if (isset($result) && is_array($result)) {
			foreach ($result as $row) {
				if ($row['parent'] <> 0 and $row['id'] <> $rows['fid'] && moder_perm($row['id'], 'topic_move', $row['moderators'])) {
					$entries[$row['parent']][] = array(
						'forum_name' => $row['title'],
						'forum_id'   => $row['id'],
					);
				}
			}
			//print "<pre>".var_export($entries, true)."</pre>";
			$output = '';
			foreach ($result as $row_2) {
				if ($row_2['parent'] == '0') {
					$tVars = array(
						'cat_id'   => $row_2['id'],
						'cat_name' => $row_2['title'],
						'cat_desc' => $row_2['description'],
						'entries'  => isset($entries[$row_2['id']]) ? $entries[$row_2['id']] : ''
					);
					$entries_2 .= $xm->render($tVars);
				}
			}
		}
		$tVars = array(
			'entries' => $entries_2
		);
		$output = $xt->render($tVars);
		break;
	case 'open':
		if (moder_perm($rows['tid'], 'topic_move', $row_['moderators']) || $userROW['status'] == 1) {
			$mysql->query('UPDATE ' . prefix . '_forum_topics SET state = \'open\' WHERE id = ' . securemysql($tid) . ' LIMIT 1');
		}

		return redirect_forum(link_topic($tid));
		break;
	case 'close':
		if (moder_perm($rows['tid'], 'topic_move', $row_['moderators']) || $userROW['status'] == 1) {
			$mysql->query('UPDATE ' . prefix . '_forum_topics SET state = \'closed\' WHERE id = ' . securemysql($tid) . ' LIMIT 1');
		}

		return redirect_forum(link_topic($tid));
		break;
	case 'stick':
		if (moder_perm($rows['tid'], 'topic_move', $row_['moderators']) || $userROW['status'] == 1) {
			$mysql->query('UPDATE ' . prefix . '_forum_topics SET pinned = \'1\' WHERE id = ' . securemysql($tid) . ' LIMIT 1');
		}

		return redirect_forum(link_topic($tid));
		break;
	case 'unstick':
		if (moder_perm($rows['tid'], 'topic_move', $row_['moderators']) || $userROW['status'] == 1) {
			$mysql->query('UPDATE ' . prefix . '_forum_topics SET pinned = \'0\' WHERE id = ' . securemysql($tid) . ' LIMIT 1');
		}

		return redirect_forum(link_topic($tid));
		break;
	default:
		return redirect_forum(link_topic($tid));
}


