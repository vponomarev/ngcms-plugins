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
	
	if(isset($params['id']))
		$id = isset($params['id'])?intval($params['id']):0;
	else
		$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
	
	if(empty($id))
		return $output = information('id сообщения не передан', $title = 'Информация');
	
	if(!is_array($userROW))
		return $output = information('У вас нет прав доступа', $title = 'Информация');
	
	$mysql->query('DELETE FROM '.prefix.'_pm WHERE ((`from_id`='.securemysql($userROW['id']).' AND `folder`=\'outbox\') OR (`to_id`='.securemysql($userROW['id']).') AND `folder`=\'inbox\') AND id = \''.intval($id).'\' LIMIT 1');
	
	return $output = announcement_forum('Сообщение удалено', link_list_pm(0,0,'inbox'), 2);