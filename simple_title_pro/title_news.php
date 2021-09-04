<?php
/*
=====================================================
 Добавление <title></title> с админки v 0.01
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

class TitleNewsFilter extends NewsFilter {

	function addNewsForm(&$tvars) {

		$tvars['titles'] = '';
	}

	function addNews(&$tvars, &$SQL) {

		return true;
	}

	function addNewsNotify(&$tvars, $SQL, $newsid) {

		global $mysql;
		$title = secure_html($_REQUEST['titles']);
		if (isset($title) && !empty($title)) {
			if (isset($newsid) && !empty($newsid)) {
				$mysql->query('INSERT INTO ' . prefix . '_simple_title_pro
									(	title,
										news_id
									) VALUES (
										' . db_squote($title) . ',
										' . db_squote($newsid) . '
									)
				');
			}
		}
	}

	function editNewsForm($newsID, $SQLold, &$tvars) {

		global $mysql;
		if ($row = $mysql->record('SELECT title FROM ' . prefix . '_simple_title_pro WHERE news_id = \'' . intval($newsID) . '\' LIMIT 1'))
			$tvars['titles'] = $row;
		else
			$tvars['titles'] = '';
	}

	function editNews($newsID, $SQLold, &$SQLnew, &$tvars) {

		global $mysql, $config;
		$title = isset($_REQUEST['titles']) ? secure_html($_REQUEST['titles']) : '';
		if (isset($newsID)) {
			if ($mysql->record('SELECT 1 FROM ' . prefix . '_simple_title_pro WHERE news_id = \'' . intval($newsID) . '\' LIMIT 1')) {
				$cacheFileName = md5('block_directory_sites_news' . $newsID . $config['default_lang']) . '.txt';
				cacheStoreFile($cacheFileName, $title, 'simple_title_pro');
				$mysql->query('UPDATE ' . prefix . '_simple_title_pro SET 
					title = ' . db_squote($title) . '
					WHERE news_id = \'' . intval($newsID) . '\'
				');
			} else {
				if (!empty($title)) {
					$mysql->query('INSERT INTO ' . prefix . '_simple_title_pro (title, news_id) 
							VALUES 
							(' . db_squote($title) . ',
							' . db_squote($newsID) . '
							)
					');
				}
			}
		}

		return true;
	}
}

register_filter('news', 'simple_title_pro', new TitleNewsFilter);