<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

add_act('index', 'plugin_calendar');

function plugin_calendar() {
        global $month, $year;

	if (is_int($year) && is_int($month) && ($month >= 1) && ($month <= 12) && ($year >= 1970) && ($year <= 2100)) {
		$this_month	=	$month;
		$this_year	=	$year;
	} else {
		$time		=	time();
		$this_month	=	date('m', $time);
		$this_year	=	date('Y', $time);
	}

        return plug_calgen($this_month, $this_year);
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
		  $tvars['vars']['d'.$j]	= ($counters[$dayno]?('<a href="'.GetLink('date', array('postdate' => mktime(0,0,0,$month,$dayno,$year))).'">'.$dayno.'</a>'):$dayno);
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
	$tvars['vars'] = array (
		'tpl_url' => tpl_url,
		'entries' => $result,
		'current_link' => '<a href="'.GetLink('month', array('postdate' => $dt)).'">'.$langMonths[$month-1].' '.$year.'</a>',
		'[prev_link]' => '<a href="'.GetLink('month', array('postdate' => $prevdt)).'">',
		'[/prev_link]' => '</a>',
		'[next_link]' => '<a href="'.GetLink('month', array('postdate' => $nextdt)).'">',
		'[/next_link]' => '</a>',
	);

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
