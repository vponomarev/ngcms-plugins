<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

add_act('index', 'plugin_lastnews');


function plugin_lastnews(){
	global $template;
	$template['vars']['plugin_lastnews'] = plugin_lastnewsGenerator('', array(), array('number' => pluginGetVariable('lastnews','number'), 'maxlength' => pluginGetVariable('lastnews','maxlength')));
}

//
// $orderby			- param for news order in show (SQL expression, NO SECURITY CHECK !!!)
// $categories		- a list of categories - array of elements, each element may be a category ID or an array of categories
// $overrideParams	- a list of overriding params
//   * number		- number of news to show
//   * offset		- news number offset
//   * dateformat	- manually set date format for display [default: "{day0}:{month0}:{year}"]
//	* {day}		- day number
//	* {day0}	- day number with leading zero
//	* {month}       - month number
//	* {month0}      - month number with leading zero
//	* {year}        - year (in 4 digit format)
//	* {year2}       - year (in 2 digit format)
//	* {month_s}     - month name, short
//	* {month_l}     - month name, long
//	* {hour}        - hour
//	* {hour0}       - hour with leading zero
//	* {minute0}     - minute with leading zero
//
//   * maxlength	- maximum length of news title (cut)
//   * overrideTemplatePath - path for template
function plugin_lastnewsGenerator($orderby = '', $categories = array(), $overrideParams = array()) {
	global $config, $mysql, $tpl, $lang, $langShortMonths, $langMonths, $PFILTERS, $userROW;

	// Generate cache file name [ we should take into account SWITCHER plugin & calling parameters ]
	$cacheFileName = md5('lastnews'.$config['theme'].$config['default_lang'].var_export($categories, true).var_export($overrideParams, true)).'.txt';

	if (pluginGetVariable('lastnews','cache')) {
		$cacheData = cacheRetrieveFile($cacheFileName, pluginGetVariable('lastnews','cacheExpire'), 'lastnews');
		if ($cacheData != false) {
			// We got data from cache. Return it and stop
			return $cacheData;
		}
	}

	if (intval($overrideParams['number']) <= 1) {
		$number = 10;
	} else {
		$number = intval($overrideParams['number']);
	}
	$offset = isset($overrideParams['offset'])?intval($overrideParams['offset']):0;

	if (intval($overrideParams['maxlength']) <= 1) {
		$maxlength = 100;
	} else {
		$maxlength = intval($overrideParams['maxlength']);
	}

	// Determine paths for all template files
	if (isset($overrideParams['overrideTemplatePath']) && $overrideParams['overrideTemplatePath']) {
		$tpath = array('entries' => $overrideParams['overrideTemplatePath'], 'lastnews' => $overrideParams['overrideTemplatePath']);
	} else {
		$tpath = locatePluginTemplates(array('entries', 'lastnews'), 'lastnews', pluginGetVariable('lastnews', 'localsource'));
	}

	$filter = array ('approve = 1');

	//
	$catfilter = array();
	foreach ($categories as $cat) {
		if (is_array($cat)) {
			$catsubfilter = array();
			foreach ($cat as $subcat)
				$catsubfilter [] = "(catid regexp '[[:<:]](".$subcat.")[[:>:]]')";
			$catfilter [] = '('.join (' AND ', $catsubfilter).')';
		} else {
			$catfilter [] = "(catid regexp '[[:<:]](".$cat.")[[:>:]]')";
		}
	}
	if (count($catfilter))
		$filter [] = '('.join(' OR ', $catfilter).')';

	// Preparation for plugin integration [if needed]
	$callingParams = array();
	if (pluginGetVariable('lastnews', 'pcall')) {
		$callingParams['plugin'] = 'lastnews';
		switch (intval(pluginGetVariable('lastnews', 'pcall_mode'))) {
			case 1: $callingParams['style'] = 'short';
					break;
			case 2: $callingParams['style'] = 'full';
					break;
			default: $callingParams['style'] = 'export';
		}

		// Preload plugins
		load_extras('news:show');
		load_extras('news:show:one');
	}

	$result = '';
	foreach ($mysql->select("select * from ".prefix."_news where ".join(" AND ", $filter)." order by ".($orderby?$orderby:"id desc")." limit ".$offset.",".$number) as $row) {
		// Execute filters [ if requested ]
		if (pluginGetVariable('lastnews', 'pcall') && is_array($PFILTERS['news']))
				foreach ($PFILTERS['news'] as $k => $v) { $v->showNewsPre($row['id'], $row, $callingParams); }

		$tvars['vars'] = array(
			'link'		=>	newsGenerateLink($row),
			'views'		=>	$row['views']
		);

		// Show edit/detele news buttons
		if (is_array($userROW) && ($row['author_id'] == $userROW['id'] || $userROW['status'] == "1" || $userROW['status'] == "2")) {
			$tvars['vars']['[edit-news]'] = "<a href=\"".admin_url."/admin.php?mod=editnews&amp;action=editnews&amp;id=".$row['id']."\" target=\"_blank\">";
			$tvars['vars']['[/edit-news]'] = "</a>";
		} else {
			$tvars['regx']["#\[edit-news\].*?\[/edit-news\]#si"] = "";
		}

		// Set formatted date
		$dformat = (isset($overrideParams['dateformat']))?$overrideParams['dateformat']:(pluginGetVariable('lastnews','dateformat')?pluginGetVariable('lastnews','dateformat'):'{day0}.{month0}.{year}');
		$tvars['vars']['date'] = str_replace(array('{day}', '{day0}', '{month}', '{month0}', '{year}', '{year2}', '{month_s}', '{month_l}', '{hour}', '{hour0}', '{minute0}'),
						array(date('j',$row['postdate']), date('d',$row['postdate']), date('n',$row['postdate']), date('m',$row['postdate']), date('y',$row['postdate']), date('Y',$row['postdate']), $langShortMonths[date('n',$row['postdate'])-1], $langMonths[date('n',$row['postdate'])-1], date('G', $row['postdate']), date('H', $row['postdate']), date('i', $row['postdate'])), $dformat);

		if (strlen($row['title']) > $maxlength) {
			$tvars['vars']['title'] = substr(secure_html($row['title']), 0, $maxlength)."...";
		} else {
			$tvars['vars']['title'] = secure_html($row['title']);
		}

		// Execute filters [ if requested ]
		if (pluginGetVariable('lastnews', 'pcall') && is_array($PFILTERS['news']))
			foreach ($PFILTERS['news'] as $k => $v) { $v->showNews($row['id'], $row, $tvars, $callingParams); }

		$tpl -> template('entries', $tpath['entries']);
		$tpl -> vars('entries', $tvars);
		$result .= $tpl -> show('entries');
	}

	unset($tvars);
	$tvars['vars'] = array ( 'tpl_url' => tpl_url, 'entries' => $result);

	$tpl -> template('lastnews', $tpath['lastnews']);
	$tpl -> vars('lastnews', $tvars);

	$output = $tpl -> show('lastnews');

	if (pluginGetVariable('lastnews','cache'))
		cacheStoreFile($cacheFileName, $output, 'lastnews');

	return $output;
}
