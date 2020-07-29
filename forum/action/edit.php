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
$tpath = locatePluginTemplates(array('editform', 'preview'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum', 'localskin'));
$xt = $twig->loadTemplate($tpath['editform'] . 'editform.tpl');
if (isset($params['id']))
	$id = isset($params['id']) ? intval($params['id']) : '';
else
	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : '';
if (empty($id))
	return $output = information('id сообщения не указан не передан', $title = 'Информация');
$time = time() + ($config['date_adjust'] * 60);
$sql = 'SELECT p.message, p.author_id, p.tid, p.id, p.c_data, t.title, t.fid FROM ' . prefix . '_forum_posts AS p LEFT JOIN ' . prefix . '_forum_topics AS t ON t.id = p.tid WHERE p.id = ' . securemysql($id) . ' LIMIT 1';
$row = $mysql->record($sql);
if (!(($userROW['id'] == $row['author_id'] && (($time - $row['c_data']) < (pluginGetVariable('forum', 'edit_del_time') * 60) or (pluginGetVariable('forum', 'edit_del_time') == 0))) or ($userROW['status'] == 1)))
	return $output = information('Вы не можете редактировать', $title = 'Информация');
$sql = "SELECT id as fid, moderators FROM " . prefix . "_forum_forums WHERE id = " . securemysql("{$row['fid']}") . " LIMIT 1";
$rows = $mysql->record($sql);
$sql_2 = 'SELECT `id` FROM `' . prefix . '_forum_posts` WHERE `tid` = ' . securemysql($row['tid']) . ' ORDER BY id LIMIT 1';
$row_2 = $mysql->record($sql_2);
$edit_subject = ($id == $row_2['id']) ? 1 : 0;
if (($edit_subject && (($userROW['id'] == $row['author_id'] && $FORUM_PS[$rows['fid']]['topic_modify_your']) || $FORUM_PS[$rows['fid']]['topic_modify'] || moder_perm($rows['fid'], 'topic_modify', $rows['moderators']))) ||
	(!$edit_subject && (($userROW['id'] == $row['author_id'] && $FORUM_PS[$rows['fid']]['post_modify_your']) || $FORUM_PS[$rows['fid']]['post_modify'] || moder_perm($rows['fid'], 'post_modify', $rows['moderators'])))
) {
	$message = isset($_REQUEST['message']) ? secureinput($_REQUEST['message']) : '';
	$subject = isset($_REQUEST['subject']) ? secureinput($_REQUEST['subject']) : '';
	$del['id']['attach_delete'] = isset($_REQUEST['attach_delete']) ? secureinput($_REQUEST['attach_delete']) : array();
	if (isset($_REQUEST['submit'])) {
		delete_attach($del);
		if ($edit_subject) {
			if (empty($subject)) $error_text[] = 'Вы не добавили заголовок';
			if (empty($error_text)) {
				$mysql->query('UPDATE ' . prefix . '_forum_topics SET title = ' . securemysql($subject) . ' 
					WHERE id = ' . securemysql($row['tid']) . ' LIMIT 1');
			}
		}
		if ($_FILES['files']['name'])
			if ($mysql->result('SELECT COUNT(*) FROM `' . prefix . '_forum_attach` WHERE `pid` = ' . securemysql($id)) < 3)
				$file = forum_upload_files();
			else
				$error_text[] = 'Вы превысили лимит количества файлов к одному сообщения';
		if (empty($message)) $error_text[] = 'Вы не добавили сообщение';
		if (empty($error_text)) {
			$mysql->query('UPDATE ' . prefix . '_forum_posts SET 
						message = ' . securemysql($message) . ', 
						e_date = ' . securemysql($time) . ', 
						who_e_author = ' . securemysql($userROW['name']) . ' 
					WHERE id = ' . db_squote($id) . ' LIMIT 1
				');
			if (isset($_REQUEST['subscribe']))
				subscribe($userROW['id'], $id);
			generate_index_cache(true);
			if (isset($file) && $file)
				$mysql->query('INSERT INTO ' . prefix . '_forum_attach (
							tid,
							pid,
							c_data,
							file,
							size,
							location,
							author,
							author_id
						) values (
							' . securemysql($row['tid']) . ',
							' . securemysql($id) . ',
							' . securemysql($time) . ',
							' . securemysql($file[0]) . ',
							' . securemysql($file[1]) . ',
							' . securemysql($file[2]) . ',
							' . securemysql($userROW['name']) . ',
							' . securemysql($userROW['id']) . '
						)
					');

			return $output = announcement_forum('Данные внесены', link_topic($id, 'pid') . '#' . $id, 2);
		}
	}
	$error_input = '';
	if (isset($error_text) && is_array($error_text))
		foreach ($error_text as $error)
			$error_input .= msg(array("type" => "error", "text" => $error), 0, 2);
	else $error_input = '';
	foreach ($mysql->select('SELECT * FROM ' . prefix . '_forum_attach WHERE pid = ' . securemysql($id) . ' ORDER BY id') as $row_4) {
		$list_attach[] = array(
			'file_id'   => $row_4['id'],
			'file'      => $row_4['file'],
			'file_link' => link_downloads($row_4['id']),
			'size'      => round($row_4['size'] / 1024, 2),
			'int_file'  => $row_4['downloads'],
		);
	}
	$tVars = array(
		'list_attach' => $list_attach,
		'subject'     => array(
			'true'  => $edit_subject,
			'print' => ($subject) ? $subject : $row['title']
		),
		'message'     => array(
			'true'  => ($message) ? 1 : 0,
			'print' => ($message) ? $message : $row['message']
		),
		'preview'     => array(
			'true'  => isset($_REQUEST['preview']) ? 1 : 0,
			'print' => bb_codes($message)
		),
		'error'       => array(
			'true'  => ($error_input) ? 1 : 0,
			'print' => $error_input
		)
	);
	$output = $xt->render($tVars);
} else {
	return $output = information('Вы не можете редактировать', $title = 'Информация');
}