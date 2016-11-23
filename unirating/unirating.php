<?php

// #====================================================================================#
// # Наименование плагина: unirating [ Univeral rating ]                                #
// # Разрешено к использованию с: Next Generation CMS                                   #
// # Автор: Vitaly A Ponomarev, vp7@mail.ru                                             #
// #====================================================================================#

// #====================================================================================#
// # Ядро плагина                                                                       #
// #====================================================================================#
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Фильтр новостей (для отображения новостей)
//
class UNRatingNewsFilter extends NewsFilter {
	function showNews($newsID, $SQLnews, &$tvars, $mode) {
		global $tpl, $mysql, $userROW;

		LoadPluginLang('unirating', 'site');
		$localskin = extra_get_param('unirating', 'news_localskin');
		if (!$localskin) $localskin='news/basic';

		$tpath = locatePluginTemplates(array('rating', 'rating.form', ':rating.css'), 'unirating', extra_get_param('unirating', 'news_localsource'), $localskin);
		register_stylesheet($tpath['url::rating.css'].'/rating.css');

		$trvars = array();
		$trvars['vars']['tpl_url'] = $tpath['url::rating.css'];
		$trvars['vars']['home'] = home;
		$trvars['vars']['post_id'] = $newsID;
		$trvars['vars']['rating'] = (!$rating || !$votes) ? 0 : round(($rating / $votes), 0);
		$trvars['vars']['votes'] = $votes;

		if ($_COOKIE['rating'.$newsID] || (extra_get_param('unirating','regonly') && !is_array($userROW))) {
			// Show
			$tpl -> template('rating', $tpath['rating']);
			$tpl -> vars('rating', $trvars);
			$tvars['vars']['unirating_news'] = $tpl -> show('rating');
		} else {
			// Edit
			$tpl -> template('rating.form', $tpath['rating.form']);
			$tpl -> vars('rating.form', $trvars);
			$tvars['vars']['unirating_news'] = $tpl -> show('rating.form');
		}
		return;
	}
}

register_filter('news','unirating', new UNRatingNewsFilter);
//register_plugin_page('unirating','','plugin_finance_screen',0);

