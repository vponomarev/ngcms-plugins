<?php

if (!defined('2z')) { die("Don't you figure you're so cool?"); }

add_act('index_post', 'plugin_rss_show');

function plugin_rss_show(){
	global $template;
	$template['vars']['feed'] = 'нет данных';
	$rows = 0;

	$feed = extra_get_param('rss_show', 'feed');
	if (!$feed) {
		return;
	}

	// Generate cache file name [ we should take into account SWITCHER plugin ]
	$cacheFileName = md5('rss_show'.$feed).'.txt';

	if (1) {
		$cacheData = cacheRetrieveFile($cacheFileName, extra_get_param('rss_show','cacheExpire'), 'rss_show');
		if ($cacheData != false) {
			// We got data from cache. Return it and stop
			$template['vars']['feed'] = $cacheData;
			return;
		}
	}

	@include_once "inc/parserss.class.php";
	$rss = new parseRSS(array('outgoing_encoding' => 'windows-1251'));
	$result = $rss->scanURL(extra_get_param('rss_show','feed'));

	if ($result['error']) {
		return;
	}

	$skip = extra_get_param('rss_show','skip');
	$count = extra_get_param('rss_show','count');
	$delim = extra_get_param('rss_show', 'templatedelim');
	if ((!is_numeric($skip))||($skip <=0)) { $skip = 0; }
	if ((!is_numeric($count))||($count <=0)) { $count = 10; }
	if (!$delim) { $delim = '<br>'; }


	for ($i = $skip; $i < $skip + $count; $i++) {
		$mynews = $result['items'][$i];
		if (!is_array($mynews)) {
			break;
		}

		$tpl = (extra_get_param('rss_show','mantemplate') == '1')?extra_get_param('rss_show','template'):'<b><a href="{link}">{title}</a></b><br>{description}<br>';
		$tpl = str_replace('{link}', $mynews['link'], $tpl);
		$tpl = str_replace('{title}', $mynews['title'], $tpl);
		$tpl = str_replace('{description}', $mynews['description'], $tpl);


		if ($rows) {
			$template['vars']['feed'] .= $delim.$tpl;
		} else {
			$template['vars']['feed'] = $tpl;
		}
		$rows++;
	}

	cacheStoreFile($cacheFileName, $template['vars']['feed'], 'rss_show');
}
