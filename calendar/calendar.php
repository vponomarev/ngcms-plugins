<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

add_act('index', 'plugin_calendar');

function plugin_calendar() {
	global $CurrentHandler;

	// Determine MONTH and YEAR for current show process
	if (($CurrentHandler['pluginName'] == 'news')&&
		(in_array($CurrentHandler['handlerName'], array('by.day','by.month', 'by.year')))) {
		$year = isset($CurrentHandler['params']['year'])?$CurrentHandler['params']['year']:$_REQUEST['year'];
		$month = isset($CurrentHandler['params']['month'])?$CurrentHandler['params']['month']:$_REQUEST['month'];

		if (($month < 1)||($month > 12))
			$month = 1;

		if (($year < 1970)||($year > 2100)) {
			$tm = localtime();
			$year = $tm[5];
		}
	} else {
		$lt = time();
		$month = date('m', $lt);
		$year  = date('Y', $lt);
	}
    return plug_calgen($month, $year);
}


function plug_calgen($month, $year) {
	global $config, $lang, $mysql, $tpl, $template, $langMonths;

	// Generate cache file name [ we should take into account SWITCHER plugin ]
	$cacheFileName = md5('calendar'.$config['theme'].$config['default_lang'].$year.$month).'.txt';

	if (extra_get_param('calendar','cache')) {
		$cacheData = cacheRetrieveFile($cacheFileName, extra_get_param('calendar','cacheExpire'), 'calendar');
		if ($cacheData != false) {
			// We got data from cache. Return it and stop
			$template['vars']['plugin_calendar'] = $cacheData;
			return;
		}
	}

	LoadPluginLang('calendar', 'main');

	$sql = "SELECT day(from_unixtime(postdate)) as day, count(id) as count FROM ".prefix."_news WHERE approve = '1' AND postdate >= unix_timestamp('".$year."-".$month."-01 00:00:00') AND postdate < unix_timestamp(date_add('".$year."-".$month."-01 00:00:00', interval 1 month)) group by to_days(from_unixtime(postdate))";
	foreach ($mysql->select($sql) as $row) {
	        $counters[$row['day']] = $row['count'];
	}

	// Determine paths for all template files
	$tpath = locatePluginTemplates(array('entries', 'calendar'), 'calendar', extra_get_param('calendar', 'localsource'));

	$dt	= mktime(0,0,0,$month  , 1, $year);
	$prevdt	= mktime(0,0,0,$month-1, 1, $year);
	$nextdt	= mktime(0,0,0,$month+1, 1, $year);

	$days	= date("t", $dt);
	$offset	= (date("w", $dt)) ? date("w", $dt)-1 : 6;
	$weeks	= ceil(($days+$offset) / 7);
	$result = '';

	// Determine current date [ needed to set different class for current day ]
	$localTime = localtime();
	$flagCurrentMonth = ((($localTime[4]+1) == $month)&&(($localTime[5]+1900) == $year))?1:0;

	for ($i = 0; $i < $weeks; $i++) {
	 unset($tvars);
	 for ($j = 1; $j <= 7; $j++) {
	  $dayno = ($i*7+$j)-$offset;
	  if (($dayno>0)&&($dayno <= $days)) {
		  $day_link = checkLinkAvailable('news', 'by.day')?
						generateLink('news', 'by.day', array('year' => $year, 'month' => $month, 'day' => sprintf('%02u', $dayno))):
						generateLink('core', 'plugin', array('plugin' => 'news', 'handler' => 'by.day'), array('year' => $year, 'month' => $month, 'day' => $dayno));

		  $tvars['vars']['d'.$j]	= ($counters[$dayno]?('<a href="'.$day_link.'">'.$dayno.'</a>'):$dayno);
		  $tvars['vars']['cl'.$j]	= $lang['calendar_class_'.(($flagCurrentMonth && ($localTime[3] == $dayno))?'today_':'').'week'.(($j<6)?'day':'end')];
	  } else {
	  	$tvars['vars']['d'.$j]		= '';
	  	$tvars['vars']['cl'.$j]		= '';
	  }
	 }
	 $tpl -> template('entries', $tpath['entries']);
	 $tpl -> vars('entries', $tvars);
	 $result .= $tpl -> show('entries');
	}
	unset($tvars);

    $month_link = checkLinkAvailable('news', 'by.month')?
				generateLink('news', 'by.month', array('year' => $year, 'month' => $month)):
				generateLink('core', 'plugin', array('plugin' => 'news', 'handler' => 'by.month'), array('year' => $year, 'month' => $month));

	$tvars['vars'] = array (
		'tpl_url' => tpl_url,
		'entries' => $result,
		'current_link' => '<a href="'.$month_link.'">'.$langMonths[$month-1].' '.$year.'</a>'
	);

    $prev_cd   = localtime($prevdt, true);
	$prev_link = checkLinkAvailable('news', 'by.month')?
				generateLink('news', 'by.month', array('year' => ($prev_cd['tm_year']+1900), 'month' => sprintf('%02u', ($prev_cd['tm_mon']+1)))):
				generateLink('core', 'plugin', array('plugin' => 'news', 'handler' => 'by.month'), array('year' => ($prev_cd['tm_year']+1900), 'month' => sprintf('%02u', ($prev_cd['tm_mon']+1))));

    $next_cd   = localtime($nextdt, true);
	$next_link = checkLinkAvailable('news', 'by.month')?
				generateLink('news', 'by.month', array('year' => ($next_cd['tm_year']+1900), 'month' => sprintf('%02u', ($next_cd['tm_mon']+1)))):
				generateLink('core', 'plugin', array('plugin' => 'news', 'handler' => 'by.month'), array('year' => ($next_cd['tm_year']+1900), 'month' => sprintf('%02u', ($next_cd['tm_mon']+1))));

	// If cache is activated - calculate MIN and MAX dates for news
	if (extra_get_param('calendar','cache')) {
		//
		$mmx = $mysql->record("select (select postdate from ".prefix."_news use key(news_postdate) where mainpage=1 order by postdate limit 1) as min, (select postdate from ".prefix."_news use key(news_postdate) where approve=1 order by postdate desc limit 1) as max", 1);

		// Prev link
		if ($prevdt<$mmx['min']) {
			// Lock
			$tvars['regx']['#\[prev_link\].+?\[/prev_link\]#is'] = '&nbsp;';
		} else {

			$tvars['vars']['[prev_link]']  = '<a href="'.$prev_link.'">';
			$tvars['vars']['[/prev_link]'] = '</a>';
		}

		// Next link
		if ($nextdt>$mmx['max']) {
			// Lock
			$tvars['regx']['#\[next_link\].+?\[/next_link\]#is'] = '&nbsp;';
		} else {
			$tvars['vars']['[next_link]']  = '<a href="'.$next_link.'">';
			$tvars['vars']['[/next_link]'] = '</a>';
		}
	} else {
		$tvars['vars']['[prev_link]']  = '<a href="'.$prev_link.'">';
		$tvars['vars']['[/prev_link]'] = '</a>';
		$tvars['vars']['[next_link]']  = '<a href="'.$next_link.'">';
		$tvars['vars']['[/next_link]'] = '</a>';
	}

	foreach (explode(",", $lang['short_weekdays']) as $k => $v)
		$lang['weekday_'.$k] = $v;

	$tpl -> template('calendar', $tpath['calendar']);
	$tpl -> vars('calendar', $tvars);

	$output = $tpl -> show('calendar');
	$template['vars']['plugin_calendar'] = $output;

	if (extra_get_param('calendar','cache')) {
		cacheStoreFile($cacheFileName, $output, 'calendar');
	}
}
