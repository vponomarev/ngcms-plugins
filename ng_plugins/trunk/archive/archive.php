<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

add_act('index', 'plugin_archive');

// Load lang file only if text counter is enabled
if (pluginGetVariable('archive', 'tcounter'))
	LoadPluginLang('archive', 'main', '', '', ':');


function plugin_archive() {
	global $config, $mysql, $tpl, $template, $langMonths, $lang;

	$maxnum    = intval(pluginGetVariable('archive','maxnum'));
	$counter   = intval(pluginGetVariable('archive','counter'));

	if (($maxnum < 1) || ($maxnum > 50)) $maxnum = 12;

	// Generate cache file name [ we should take into account SWITCHER plugin ]
	$cacheFileName = md5('archive'.$config['theme'].$config['default_lang']).'.txt';

	if (pluginGetVariable('archive','cache')) {
		$cacheData = cacheRetrieveFile($cacheFileName, extra_get_param('archive','cacheExpire'), 'archive');
		if ($cacheData != false) {
			// We got data from cache. Return it and stop
			$template['vars']['plugin_archive'] = $cacheData;
			return;
		}
	}

	// Load list
	$caseList = explode(',', $lang['archive:counter.case']);

	// Determine paths for all template files
	$tpath = locatePluginTemplates(array('entries', 'archive'), 'archive', extra_get_param('archive', 'localsource'));

	$result = '';
	foreach($mysql->select("SELECT month(from_unixtime(postdate)) as month, year(from_unixtime(postdate)) as year, COUNT(id) AS cnt, postdate FROM ".prefix."_news WHERE approve = '1' GROUP BY year(from_unixtime(postdate)), month(from_unixtime(postdate)) ORDER BY postdate DESC limit $maxnum") as $row){
	    $month_link = checkLinkAvailable('news', 'by.month')?
					generateLink('news', 'by.month', array('year' => $row['year'], 'month' => sprintf('%02u', $row['month']))):
					generateLink('core', 'plugin', array('plugin' => 'news', 'handler' => 'by.month'), array('year' => $row['year'], 'month' => sprintf('%02u', $row['month'])));


		$tvars['vars'] = array(
			'link'		=>	$month_link,
			'title'		=>	$langMonths[$row['month']-1].' '.$row['year'],
			'cnt'		=>	$row['cnt'],
		);

		if (pluginGetVariable('archive', 'tcounter')) {
			// Determine current case
			$sCase = 99;
			$cnt = $row['cnt'];
			if ($cnt == 1) {
				$sCase = 1;
			} else if (($cnt >= 2) && ($cnt <= 4)) {
				$sCase = 2;
			} else if (($cnt >= 5) && ($cnt <= 20)) {
				$sCase = 4;
			} else {
				$tsCase = $sCase % 10;
				if ($tsCase == 0) {
					$sCase = 4;
				} else if ($tsCase == 1) {
					$sCase = 1;
				} else if (($tsCase >= 2) && ($tsCase <= 4)) {
					$sCase = 2;
				} else {
					$sCase = 4;
				}
			}
			$tvars['vars']['ctext'] = $caseList[$sCase-1];
		} else {
			$tvars['vars']['ctext'] = '';
		}

		$tvars['regx']["'\[counter\](.*?)\[/counter\]'si"] = $counter?'$1':'';

		$tpl -> template('entries', $tpath['entries']);
		$tpl -> vars('entries', $tvars);
		$result .= $tpl -> show('entries');
	}

	unset($tvars);
	$tvars['vars'] = array ( 'tpl_url' => tpl_url, 'archive' => $result);

	$tpl -> template('archive', $tpath['archive']);
	$tpl -> vars('archive', $tvars);

	$output = $tpl -> show('archive');
	$template['vars']['plugin_archive'] = $output;

	cacheStoreFile($cacheFileName, $output, 'archive');
}
