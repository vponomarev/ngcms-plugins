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
	
	if(checkLinkAvailable('forum', 'out')){
		if($CurrentHandler['handlerParams']['value']['pluginName'] == 'core')
			return redirect_forum(link_out());
	}
	
	if(is_array($userROW)){
		$auth_db->drop_auth();
		return $output = announcement_forum('�� ����� �� ������', str_replace( array( '{url}' ), array( link_home() ), $lang['forum']['link_out'] ), 2, true);
	} else return $output = announcement_forum('������� �� ������ ���������������� ����� �����!', link_login(), 2);