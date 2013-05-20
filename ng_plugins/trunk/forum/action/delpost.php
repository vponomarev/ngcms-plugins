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
	
	if(isset($params['id']))
		$id = isset($params['id'])?intval($params['id']):0;
	else
		$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
	
	if(empty($id ))
		return $output = information('id ����� �� ������ �� �������', $title = '����������');
	
	$sql = "SELECT p.author_id, p.id as pid, p.c_data, t.fid, t.id as tid FROM ".prefix."_forum_posts AS p LEFT JOIN ".prefix."_forum_topics AS t ON t.id = p.tid WHERE p.id = ".securemysql( "{$id}" )." LIMIT 1";
	$row = $mysql->record($sql);
	
	if(!(($userROW['id'] == $row['author_id'] && (($time - $row['c_data']) < (pluginGetVariable('forum','edit_del_time')*60) or (pluginGetVariable('forum','edit_del_time') == 0))) or ($userROW['status'] == 1)))
		return $output = information('�� �� ������ �������', $title = '����������');
	
	$sql_2 = "SELECT id FROM `".prefix."_forum_posts` WHERE `tid` = '{$row['tid']}' ORDER BY c_data ASC LIMIT 1";
	$row_2 = $mysql->record($sql_2);
	
	$delet_subject = ($id == $row_2['id']) ? true : false;
	
	$sql_3 = 'SELECT `id` FROM `'.prefix.'_forum_posts` WHERE `tid` = '.$row['tid'].' AND id < '.db_squote($id).' ORDER BY c_data DESC LIMIT 1';
	$row_3 = $mysql->record($sql_3);
	
	if( ($row['author_id'] == $userROW['id']) or ($userROW['status'] == 1)){
		if($delet_subject){
			delete_topic($row['tid']);
			$mysql->query('UPDATE '.prefix.'_news SET tid = 0 WHERE tid = '.securemysql($row['tid']).' LIMIT 1');
			//delete_thank($row['tid']);
			delete_attach($row);
			global_update_forum($row['fid']);
			generate_index_cache(true);
			return $output = announcement_forum('������ �������', link_forum($row['fid']), 2);
		} else {
			delete_post($id, $row['tid']);
			//delete_thank($row['pid']);
			delete_attach($row);
			global_update_forum($row['fid']);
			generate_index_cache(true);
			return $output = announcement_forum('������ �������', link_topic($row_3['id'], 'pid').'#'.$row_3['id'], 2);
		}
	} else {
		return $output = information('�� �� ������ ����� ������� ���������', $title = '����������');
	}