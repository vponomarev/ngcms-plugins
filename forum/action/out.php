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
	
	if(checkLinkAvailable('forum', 'out')){
		if($CurrentHandler['handlerParams']['value']['pluginName'] == 'core')
			return redirect_forum(link_out());
	}
	
	if(is_array($userROW)){
		$auth_db->drop_auth();
		return $output = announcement_forum('Вы вышли из форума', link_home(), 2);
	} else return $output = announcement_forum('Сначала вы должны авторизироваться чтобы выйти!', link_login(), 2);