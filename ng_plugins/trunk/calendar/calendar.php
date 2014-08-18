<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

// Check execution mode
if (!pluginGetVariable('calendar', 'mode')) {
	add_act('index', 'plugin_calendar');
} else {
	global $template;
	$template['vars']['plugin_calendar'] = '';
}

function plugin_calendar() {
	global $CurrentHandler, $template;

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
    $template['vars']['plugin_calendar'] = plug_calgen($month, $year, false, array(), pluginGetVariable('calendar','cache')?pluginGetVariable('calendar','cacheExpire'):0);
}


function plug_calgen($month, $year, $overrideTemplateName = false, $categoryList = array(), $cacheExpire = 0, $flagAJAX = false) {
	global $config, $lang, $mysql, $tpl, $template, $langMonths, $twig, $twigLoader;

	// Add leading zeroes to month (if needed)
	$month = sprintf('%02s', $month);

	// Generate cache file name [ we should take into account SWITCHER plugin ]
	$cacheFileName = md5('calendar'.$config['theme'].'|'.join(",",$categoryList).$overrideTemplateName.$config['default_lang'].$year.$month).'.txt';

	if ($cacheExpire > 0) {
		$cacheData = cacheRetrieveFile($cacheFileName, $cacheExpire, 'calendar');
		if ($cacheData != false) {
			// We got data from cache. Return it and stop
			return $cacheData;
		}
	}

	LoadPluginLang('calendar', 'main');

	// –азные запросы в зависимости от указани€ категорий
	if (!is_array($categoryList)) {
		$categoryList = intval($categoryList)?array(intval($categoryList)):array();

	}

	if (!count($categoryList)) {
		$sql = "SELECT day(from_unixtime(postdate)) as day, count(id) as count FROM ".prefix."_news WHERE approve = '1' AND postdate >= unix_timestamp('".$year."-".$month."-01 00:00:00') AND postdate < unix_timestamp(date_add('".$year."-".$month."-01 00:00:00', interval 1 month)) group by to_days(from_unixtime(postdate))";
	} else {

		$sqlList = array();
		foreach ($categoryList as $c) {
			if (intval($c) > 0)
				$sqlList []= intval($c);
		}


		$sql = "SELECT day(dt) as day, count(newsID) as count FROM ".prefix."_news_map WHERE categoryID in (".join(",", $sqlList).") AND (dt >= '".$year."-".$month."-01 00:00:00') AND dt < (date_add('".$year."-".$month."-01 00:00:00', interval 1 month)) group by to_days(dt)";
	}

	foreach ($mysql->select($sql) as $row) {
	        $counters[$row['day']] = $row['count'];
	}

	// Determine paths for all template files
	$tpath = locatePluginTemplates(array('entries', 'calendar'), 'calendar', pluginGetVariable('calendar', 'localsource'));

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

	$tVars = array('weeks' => array());

	for ($i = 0; $i < $weeks; $i++) {
		$tDays = array();

		unset($tvars);
		for ($j = 1; $j <= 7; $j++) {
			$dayno = ($i*7+$j)-$offset;
			if (($dayno>0)&&($dayno <= $days)) {
				$day_link = checkLinkAvailable('news', 'by.day')?
						generateLink('news', 'by.day', array('year' => $year, 'month' => $month, 'day' => sprintf('%02u', $dayno))):
						generateLink('core', 'plugin', array('plugin' => 'news', 'handler' => 'by.day'), array('year' => $year, 'month' => $month, 'day' => $dayno));

				$tDays[$j] = array(
					'dayNo'		=> $dayno,
					'countNews'	=> isset($counters[$dayno])?$counters[$dayno]:0,
					'link'		=> $day_link,
					'className'	=> $lang['calendar_class_'.(($flagCurrentMonth && ($localTime[3] == $dayno))?'today_':'').'week'.(($j<6)?'day':'end')],
					'isToday'	=> ($localTime[3] == $dayno)?true:false,
					'isWeekDay'	=> ($j<6)?true:false,
					'isWeekEnd'	=> ($j == 7)?true:false,
				);
			}
		}
		$tVars['weeks'][$i+1] = $tDays;
	}
	unset($tvars);

    $month_link = checkLinkAvailable('news', 'by.month')?
				generateLink('news', 'by.month', array('year' => $year, 'month' => $month)):
				generateLink('core', 'plugin', array('plugin' => 'news', 'handler' => 'by.month'), array('year' => $year, 'month' => $month));


    $prev_cd   = localtime($prevdt, true);
	$prev_link = checkLinkAvailable('news', 'by.month')?
				generateLink('news', 'by.month', array('year' => ($prev_cd['tm_year']+1900), 'month' => sprintf('%02u', ($prev_cd['tm_mon']+1)))):
				generateLink('core', 'plugin', array('plugin' => 'news', 'handler' => 'by.month'), array('year' => ($prev_cd['tm_year']+1900), 'month' => sprintf('%02u', ($prev_cd['tm_mon']+1))));

    $next_cd   = localtime($nextdt, true);
	$next_link = checkLinkAvailable('news', 'by.month')?
				generateLink('news', 'by.month', array('year' => ($next_cd['tm_year']+1900), 'month' => sprintf('%02u', ($next_cd['tm_mon']+1)))):
				generateLink('core', 'plugin', array('plugin' => 'news', 'handler' => 'by.month'), array('year' => ($next_cd['tm_year']+1900), 'month' => sprintf('%02u', ($next_cd['tm_mon']+1))));

	$tVars['currentMonth'] = array(
		'name'		=> $langMonths[$month-1],
		'link'		=> $month_link,
	);

	$tVars['currentEntry'] = array(
		'month'		=> $month,
		'year'		=> $year,
		'template'	=> $overrideTemplateName,
		'categories'	=> join(",", $categoryList),
	);

	// If cache is activated - calculate MIN and MAX dates for news
	if ($cacheExpire > 0) {
		//
		$mmx = $mysql->record("select (select postdate from ".prefix."_news use key(news_postdate) where mainpage=1 order by postdate limit 1) as min, (select postdate from ".prefix."_news use key(news_postdate) where approve=1 order by postdate desc limit 1) as max", 1);

		// Prev link
		if ($prevdt<$mmx['min']) {
			// Lock
		} else {
			$tVars['prevMonth'] = array('link' => $prev_link);
			$tVars['flags']['havePrevMonth'] = true;
		}

		// Next link
		if ($nextdt>$mmx['max']) {
			// Lock
		} else {
			$tVars['nextMonth'] = array('link' => $next_link);
			$tVars['flags']['haveNextMonth'] = true;
		}
	} else {
		$tVars['prevMonth'] = array('link' => $prev_link);
		$tVars['nextMonth'] = array('link' => $next_link);

		$tVars['flags']['havePrevMonth'] = true;
		$tVars['flags']['haveNextMonth'] = true;
	}

	foreach (explode(",", $lang['short_weekdays']) as $k => $v) {
		$lang['weekday_'.$k] = $v;
		$tVars['weekdays'][$k?($k):7] = $v;
	}

	// Prepare conversion table
	$conversionConfig = array(
		'{entries}'			=> '{% for week in weeks %}{% include localPath(0) ~ "entries.tpl" %}{% endfor %}',
		'{current_link}'	=> '<a href="{{ currentMonth.link }}">{{ currentMonth.name }}</a>',
		'{tpl_url}'			=> '{{ tpl_url }}',
	);

	$conversionConfigRegex = array(
		'#\[prev_link\](.+?)\[/prev_link\]#is' => '{% if (flags.havePrevMonth) %}<a href="{{ prevMonth.link }}">$1</a>{% endif %}',
		'#\[next_link\](.+?)\[/next_link\]#is' => '{% if (flags.haveNextMonth) %}<a href="{{ nextMonth.link }}">$1</a>{% endif %}',
	);


	$conversionConfigE = array();

	for ($i=0; $i < 7; $i++) {
		$conversionConfig['{l_weekday_'.$i.'}']	= '{{ weekdays['.$i.'] }}';
	}

	for ($i=1; $i <= 7; $i++) {
		$conversionConfigE['{cl'.$i.'}']	= '{{ week['.$i.'].className }}';
		$conversionConfigE['{d'.$i.'}']		= '{% if (week['.$i.'].countNews>0) %}<a href="{{ week['.$i.'].link }}">{{ week['.$i.'].dayNo}}</a>{% else %}{{ week['.$i.'].dayNo }}{% endif %}';
	}

	$twigLoader->setConversion($tpath['calendar'].'calendar.tpl', $conversionConfig, $conversionConfigRegex);
	$twigLoader->setConversion($tpath['entries'].'entries.tpl', $conversionConfigE);

	// AJAX flag
	$tVars['flags']['ajax'] = $flagAJAX?1:0;

	// ѕредзагрузка шаблона entries [ чтобы отработал setConversion ] при его наличии
	if (isset($tpath['entries']))
		$twig->loadTemplate($tpath['entries'].'entries.tpl');

	$xt = $twig->loadTemplate($tpath['calendar'].'calendar.tpl');
	$output = $xt->render($tVars);

	if ($cacheExpire > 0) {
		cacheStoreFile($cacheFileName, $output, 'calendar');
	}

	return $output;
}

//
// Show data block for xnews plugin
// Params:
// * month		- Month for calendar
// * year		- Year for calendar
// * offset		- 'prev', 'next', '' [ show previous/next/selected month ]
// * template	- Personal template for plugin
// * category	- List of categories [will be generated only for specific categories if specified]
// * cache		- Cache expiration
// * flagAJAX	- flag if function was called from AJAX function call
function plugin_calendar_showTwig($params) {
	global $CurrentHandler;

	// Default values for month/year - current month
	$today = time();
	$month = date('m', $today);
	$year  = date('Y', $today);

	// Check if month/year are set
	if (isset($params['year']) && isset($params['month'])) {
		$month	= intval($params['month']);
		$year	= intval($params['year']);
	} else {
		// Month/year is not set. Try to check current month/year from URL

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

		}
	}

	// Check offset
	if (isset($params['offset']) && ($params['offset'] == 'prev')) {	$month--;	}
	if (isset($params['offset']) && ($params['offset'] == 'next')) {	$month++;	}

	// Update month for offset fix
	if ($month > 12) {
		$month = 1;
		$year++;
	}

	if ($month < 1) {
		$month = 12;
		$year--;
	}

	return	plug_calgen($month, $year, isset($params['template'])?$params['template']:false, array(), 0, $params['flagAJAX']);
}

twigRegisterFunction('calendar', 'show', plugin_calendar_showTwig);



function calendar_rpc_manage($params){
	$params['flagAJAX'] = true;
	$calendarOutput = plugin_calendar_showTwig($params);

	return array('status' => 1, 'errorCode' => 0, 'data' => arrayCharsetConvert(0, $calendarOutput));
}

rpcRegisterFunction('plugin.calendar.show', 'calendar_rpc_manage');
