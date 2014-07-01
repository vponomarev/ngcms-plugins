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
	if (!defined('NGCMS')) die ('HAL'); global $twig;
	$tpath = locatePluginTemplates(array(':'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'));
	
	@define('FORUM_VERSION', '0.1 RC9');
	@define('FORUM_AVATAR_DIR', dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/uploads/avatars');
	@define('FORUM_AVATAR_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/uploads/avatars');
	@define('FORUM_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/engine/plugins/forum/');
	@define('FORUM_DIR', dirname(dirname(__FILE__)));
	@define('FORUM_CACHE', get_plugcfg_dir('forum'));
	
	$twig->addGlobal('forum_tpl', $tpath['url::']);