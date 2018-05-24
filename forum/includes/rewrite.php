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
function link_lock_passwd($id, $act = '', $pid) {

	$id = intval($id);
	switch ($act) {
		case '':
			$url = checkLinkAvailable('forum', 'lock_passwd') ?
				generateLink('forum', 'lock_passwd', array('id' => $id)) :
				generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'lock_passwd'), array('id' => $id));
			break;
		case 'pid':
			$url = checkLinkAvailable('forum', 'lock_passwd') ?
				generateLink('forum', 'lock_passwd', array('id' => $id, 'pid' => $pid)) :
				generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'lock_passwd'), array('id' => $id, 'pid' => $pid));
			break;
	}

	return $url;
}

function link_topic_modify($id) {

	$id = intval($id);
	$url = checkLinkAvailable('forum', 'topic_modify') ?
		generateLink('forum', 'topic_modify', array('id' => $id)) :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'topic_modify'), array('id' => $id));

	return $url;
}

function link_edit_post($id) {

	$id = intval($id);
	$url = checkLinkAvailable('forum', 'edit') ?
		generateLink('forum', 'edit', array('id' => $id)) :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'edit'), array('id' => $id));

	return $url;
}

function link_new_post($id, $act = '', $pid = 0) {

	$id = intval($id);
	switch ($act) {
		case '':
			$url = checkLinkAvailable('forum', 'addreply') ?
				generateLink('forum', 'addreply', array('id' => $id)) :
				generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'addreply'), array('id' => $id));
			break;
		case 'pid':
			$url = checkLinkAvailable('forum', 'addreply') ?
				generateLink('forum', 'addreply', array('id' => $id, 'pid' => $pid)) :
				generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'addreply'), array('id' => $id, 'pid' => $pid));
			break;
	}

	return $url;
}

function link_del_post($id) {

	$id = intval($id);
	$url = checkLinkAvailable('forum', 'delpost') ?
		generateLink('forum', 'delpost', array('id' => $id)) :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'delpost'), array('id' => $id));

	return $url;
}

function link_add_topic($id) {

	$id = intval($id);
	$url = checkLinkAvailable('forum', 'newtopic') ?
		generateLink('forum', 'newtopic', array('id' => $id)) :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'newtopic'), array('id' => $id));

	return $url;
}

function link_forum($id) {

	$id = intval($id);
	$url = checkLinkAvailable('forum', 'showforum') ?
		generateLink('forum', 'showforum', array('id' => $id)) :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'showforum'), array('id' => $id));

	return $url;
}

function link_profile($id, $act = '', $name) {

	$id = intval($id);
	switch ($act) {
		case '':
			$url = checkLinkAvailable('forum', 'profile') ?
				generateLink('forum', 'profile', array('name' => $name, 'id' => $id)) :
				generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'profile'), array('id' => $id));
			break;
		case 'edit':
			$url = checkLinkAvailable('forum', 'profile') ?
				generateLink('forum', 'profile', array('name' => $name, 'id' => $id, 'act' => 'edit')) :
				generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'profile'), array('id' => $id, 'act' => 'edit'));
			break;
	}

	return $url;
}

function link_topic($id, $act = '') {

	$id = intval($id);
	switch ($act) {
		case '':
			$url = checkLinkAvailable('forum', 'showtopic') ?
				generateLink('forum', 'showtopic', array('id' => $id)) :
				generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'showtopic'), array('id' => $id));
			break;
		case 'last':
			$url = checkLinkAvailable('forum', 'showtopic') ?
				generateLink('forum', 'showtopic', array('id' => $id, 'act' => 'last')) :
				generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'showtopic'), array('id' => $id, 'act' => 'last'));
			break;
		case 'pid':
			$url = checkLinkAvailable('forum', 'showtopic') ?
				generateLink('forum', 'showtopic', array('pid' => $id)) :
				generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'showtopic'), array('pid' => $id));
			break;
	}

	return $url;
}

function link_post($id) {

	$id = intval($id);
	$url = checkLinkAvailable('forum', 'showtopic') ?
		generateLink('forum', 'showtopic', array('pid' => $id)) :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'showtopic'), array('pid' => $id));

	return $url;
}

function link_home() {

	$url = checkLinkAvailable('forum', '') ?
		generateLink('forum', '') :
		generateLink('core', 'plugin', array('plugin' => 'forum'));

	return $url;
}

function link_userslist() {

	$url = checkLinkAvailable('forum', 'userlist') ?
		generateLink('forum', 'userlist') :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'userlist'));

	return $url;
}

function link_search() {

	$url = checkLinkAvailable('forum', 'search') ?
		generateLink('forum', 'search') :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'search'));

	return $url;
}

function link_register() {

	$url = checkLinkAvailable('forum', 'register') ?
		generateLink('forum', 'register') :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'register'));

	return $url;
}

function link_login() {

	$url = checkLinkAvailable('forum', 'login') ?
		generateLink('forum', 'login') :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'login'));

	return $url;
}

function link_out() {

	$url = checkLinkAvailable('forum', 'out') ?
		generateLink('forum', 'out') :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'out'));

	return $url;
}

function link_rules() {

	$url = checkLinkAvailable('forum', 'rules') ?
		generateLink('forum', 'rules') :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'rules'));

	return $url;
}

function link_act($s) {

	$url = checkLinkAvailable('forum', 'act') ?
		generateLink('forum', 'act', array('s' => $s)) :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'act'), array('s' => $s));

	return $url;
}

function link_moderate($params) {

	$tid = $params['tid'] ? $params['tid'] : 0;
	$metod = $params['metod'] ? $params['metod'] : '';
	$url = checkLinkAvailable('forum', 'moderate') ?
		generateLink('forum', 'moderate', array('tid' => $tid, 'metod' => $metod)) :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'moderate'), array('tid' => $tid, 'metod' => $metod));

	return $url;
}

function link_inf_subs($id, $metod) {

	$id = intval($id);
	$metod = intval($metod);
	$url = checkLinkAvailable('forum', 'uns') ?
		generateLink('forum', 'uns', array('id' => $id, 'metod' => $metod)) :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'uns'), array('id' => $id, 'metod' => $metod));

	return $url;
}

function link_markread() {

	$url = checkLinkAvailable('forum', 'markread') ?
		generateLink('forum', 'markread') :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'markread'));

	return $url;
}

function link_reputation($id) {

	$id = intval($id);
	$url = checkLinkAvailable('forum', 'rep') ?
		generateLink('forum', 'rep', array('id' => $id)) :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'rep'), array('id' => $id));

	return $url;
}

function link_news($id) {

	$id = intval($id);
	$url = checkLinkAvailable('forum', 'news') ?
		generateLink('forum', 'rep', array('id' => $id)) :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'news'), array('id' => $id));

	return $url;
}

function link_news_feed() {

	$url = checkLinkAvailable('forum', 'news_feed') ?
		generateLink('forum', 'news_feed') :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'news_feed'));

	return $url;
}

function link_rss_feed() {

	$url = checkLinkAvailable('forum', 'rss_feed') ?
		generateLink('forum', 'rss_feed') :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'rss_feed'));

	return $url;
}

function link_rss($id) {

	$id = intval($id);
	$url = checkLinkAvailable('forum', 'rss') ?
		generateLink('forum', 'rss', array('id' => $id)) :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'rss'), array('id' => $id));

	return $url;
}

function link_add_rep($pid, $metod) {

	$pid = intval($pid);
	$metod = intval($metod);
	$url = checkLinkAvailable('forum', 'addr') ?
		generateLink('forum', 'addr', array('pid' => $pid, 'metod' => $metod)) :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'addr'), array('pid' => $pid, 'metod' => $metod));

	return $url;
}

function link_add_thank($pid) {

	$pid = intval($pid);
	$url = checkLinkAvailable('forum', 'add_thank') ?
		generateLink('forum', 'add_thank', array('pid' => $pid)) :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'add_thank'), array('pid' => $pid));

	return $url;
}

function link_thank($id) {

	$id = intval($id);
	$url = checkLinkAvailable('forum', 'thank') ?
		generateLink('forum', 'thank', array('id' => $id)) :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'thank'), array('id' => $id));

	return $url;
}

function link_complaints($id) {

	$id = intval($id);
	$url = checkLinkAvailable('forum', 'complaints') ?
		generateLink('forum', 'complaints', array('id' => $id)) :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'complaints'), array('id' => $id));

	return $url;
}

function link_list_pm($id = 0, $page = 0, $folder = '') {

	$id = intval($id);
	if ($id && $page)
		$url = checkLinkAvailable('forum', 'list_pm') ?
			generateLink('forum', 'list_pm', array('id' => $id, 'folder' => $folder, 'page' => $page)) :
			generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'list_pm'), array('id' => $id, 'folder' => $folder, 'page' => $page));
	elseif ($id)
		$url = checkLinkAvailable('forum', 'list_pm') ?
			generateLink('forum', 'list_pm', array('id' => $id, 'folder' => $folder)) :
			generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'list_pm'), array('id' => $id, 'folder' => $folder));
	else
		$url = checkLinkAvailable('forum', 'list_pm') ?
			generateLink('forum', 'list_pm', array('folder' => $folder)) :
			generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'list_pm'), array('folder' => $folder));

	return $url;
}

function link_send_pm($id, $reply = '', $quote = '') {

	$id = intval($id);
	$reply = intval($reply);
	$quote = intval($quote);
	if ($id && $reply)
		$url = checkLinkAvailable('forum', 'send_pm') ?
			generateLink('forum', 'send_pm', array('id' => $id, 'reply' => $reply)) :
			generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'send_pm'), array('id' => $id, 'reply' => $reply));
	elseif ($id && $quote)
		$url = checkLinkAvailable('forum', 'send_pm') ?
			generateLink('forum', 'send_pm', array('id' => $id, 'quote' => $quote)) :
			generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'send_pm'), array('id' => $id, 'quote' => $quote));
	elseif ($id)
		$url = checkLinkAvailable('forum', 'send_pm') ?
			generateLink('forum', 'send_pm', array('id' => $id)) :
			generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'send_pm'), array('id' => $id));
	else
		$url = checkLinkAvailable('forum', 'send_pm') ?
			generateLink('forum', 'send_pm') :
			generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'send_pm'));

	return $url;
}

function link_del_pm($id, $folder = '') {

	$id = intval($id);
	$url = checkLinkAvailable('forum', 'del_pm') ?
		generateLink('forum', 'del_pm', array('id' => $id, 'folder' => $folder)) :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'del_pm'), array('id' => $id, 'folder' => $folder));

	return $url;
}

function link_downloads($id) {

	$id = intval($id);
	$url = checkLinkAvailable('forum', 'downloads') ?
		generateLink('forum', 'downloads', array('id' => $id)) :
		generateLink('core', 'plugin', array('plugin' => 'forum', 'handler' => 'downloads'), array('id' => $id));

	return $url;
}

twigRegisterFunction('forum', 'link_moderate', link_moderate);