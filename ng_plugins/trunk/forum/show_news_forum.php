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

include_once(dirname(__FILE__).'/includes/rewrite.php');

class ShowForumNewsFilter extends NewsFilter {
	function showNews($newsID, $SQLnews, &$tvars, $mode = array()){
		if(empty($SQLnews['tid'])){
			$tvars['vars']['topic_forum_url'] = '';
			$tvars['regx']["'\[topic_show\](.*?)\[/topic_show\]'si"] = '';
		} else {
			$tvars['regx']["'\[topic_show\](.*?)\[/topic_show\]'si"] = '$1';
			$tvars['vars']['topic_forum_url'] = link_topic($SQLnews['tid']);
		}
	}
}

register_filter('news','forum', new ShowForumNewsFilter);