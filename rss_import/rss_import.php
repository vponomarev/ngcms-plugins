<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
add_act('index', 'rss_import_block');
function rss_import_block() {

	global $config, $tpl, $template, $parse;
	$count = extra_get_param('rss_import', 'count');
	if ((intval($count) < 1) || (intval($count) > 20))
		$count = 1;
	for ($i = 1; $i <= $count; $i++) {
		$vv = 'rss' . $i;
		$number = intval(extra_get_param('rss_import', $vv . '_number'));
		$maxlength = intval(extra_get_param('rss_import', $vv . '_maxlength'));
		$newslength = intval(extra_get_param('rss_import', $vv . '_newslength'));
		// Determine paths for all template files
		if (intval(extra_get_param('rss_import', 'localsource')) == 1) $overrideTemplatePath = root . '/plugins/rss_import/tpl/' . $vv . '';
		else $overrideTemplatePath = tpl_site . 'plugins/rss_import/' . $vv . '';
		$tpath = array('entries' => $overrideTemplatePath, 'rss' => $overrideTemplatePath);
		// Generate cache file name [ we should take into account SWITCHER plugin ]
		$cacheFileName = md5($vv . $config['theme'] . $config['default_lang']) . '.txt';
		if (extra_get_param('rss_import', 'cache')) {
			$cacheData = cacheRetrieveFile($cacheFileName, extra_get_param('rss_import', 'cacheExpire'), 'rss_import');
			if ($cacheData != false) {
				// We got data from cache. Return it and stop
				$template['vars'][$vv] = $cacheData;

				return;
			}
		}
		if (!$number) {
			$number = 10;
		}
		if (!$maxlength) {
			$maxlength = 100;
		}
		if (!$newslength) {
			$newslength = 100;
		}
		$url = extra_get_param('rss_import', $vv . '_url');       //адрес RSS ленты
		$rss = simplexml_load_file($url);       //Интерпретирует XML-файл в объект
		if (empty($rss))
			return $template['vars'][$vv] = 'RSS не доступен';
		//цикл для обхода всей RSS ленты
		$j = 1;
		foreach ($rss->xpath('//item') as $item) {
			$title = $item->title;       //выводим заголовок статьи
			if (strlen($title) > $maxlength) $tvars['vars']['title'] = iconv('utf-8', 'windows-1251', substr(secure_html($title), 0, $maxlength) . "");
			else $tvars['vars']['title'] = iconv('utf-8', 'windows-1251', secure_html($title));
			if (extra_get_param('rss_import', $vv . '_content')) {
				$short_news = strip_tags(iconv('utf-8', 'windows-1251', $item->description));        //выводим текст статьи	
				if ($config['blocks_for_reg']) $short_news = $parse->userblocks($short_news);
				//if ($config['use_htmlformatter']) $short_news = $parse -> htmlformatter($short_news);
				if ($config['use_bbcodes']) $short_news = $parse->bbcodes($short_news);
				if ($config['use_smilies']) $short_news = $parse->smilies($short_news);
				if (strlen($short_news) > $newslength) $short_news = substr($short_news, 0, $newslength);
				if (extra_get_param('rss_import', $vv . '_img')) $short_news = preg_replace('%<[^<>]*?img\\s+src\\s*="?[^"]+"?[^<>]*>\\s*(?:</img>)?%i', "", $short_news);
				$tvars['vars']['short_news'] = $short_news;
			}
			$tvars['vars']['link'] = $item->link;
			$tpl->template('entries', $tpath['entries']);
			$tpl->vars('entries', $tvars);
			$result .= $tpl->show('entries');
			if ($j == $number) break;
			$j++;
		}
		$tpl->template('rss', $tpath['rss']);
		$tpl->vars('rss', array('vars' => array('entries' => $result, 'author' => extra_get_param('rss_import', $vv . '_name'))));
		$template['vars'][$vv] = $tpl->show('rss');
		$result = '';
		if (extra_get_param('rss_import', 'cache')) {
			cacheStoreFile($cacheFileName, $output, 'rss_import');
		}
	}
}