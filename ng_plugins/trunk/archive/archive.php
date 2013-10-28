<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');


// Check execution mode
if (!pluginGetVariable('archive', 'mode')) {
	add_act('index', 'plugin_archive'); // auto
} else {
	global $template;
	$template['vars']['plugin_archive'] = ''; // twig
}

// Load lang file
LoadPluginLang('archive', 'main', '', '', ':');


function plugin_archive() {
	global $template, $config;

    $template['vars']['plugin_archive'] = plug_arch(pluginGetVariable('archive','maxnum')?pluginGetVariable('archive','maxnum'):12, pluginGetVariable('archive','counter')?pluginGetVariable('archive','counter'):0, pluginGetVariable('archive','tcounter')?pluginGetVariable('archive','tcounter'):0, false, pluginGetVariable('archive','cache')?pluginGetVariable('archive','cacheExpire'):0);
}

function plug_arch($maxnum, $counter, $tcounter, $overrideTemplateName, $cacheExpire) {
	global $config, $mysql, $tpl, $template, $twig, $twigLoader, $langMonths, $lang;

//	$maxnum    = intval(pluginGetVariable('archive','maxnum'));
//	$counter   = intval(pluginGetVariable('archive','counter'));

	if (($maxnum < 1) || ($maxnum > 50)) $maxnum = 12;
	
	if ($overrideTemplateName) {
        $templateName = $overrideTemplateName;
    } else {
         $templateName = 'archive';
    }

	// Generate cache file name [ we should take into account SWITCHER plugin ]
	$cacheFileName = md5('archive'.$config['theme'].$templateName.$config['default_lang']).'.txt';

	if ($cacheExpire > 0) {
		$cacheData = cacheRetrieveFile($cacheFileName, $cacheExpire, 'archive');
		if ($cacheData != false) {
			// We got data from cache. Return it and stop
			return $cacheData;
		}
	}
	

	
	// Determine paths for all template files
	$tpath = locatePluginTemplates(array($templateName, 'entries'), 'archive', pluginGetVariable('archive', 'localsource'));


	// Load list
	$caseList = explode(',', $lang['archive:counter.case']);

	foreach($mysql->select("SELECT month(from_unixtime(postdate)) as month, year(from_unixtime(postdate)) as year, COUNT(id) AS cnt, postdate FROM ".prefix."_news WHERE approve = '1' GROUP BY year(from_unixtime(postdate)), month(from_unixtime(postdate)) ORDER BY postdate DESC limit $maxnum") as $row){
	    $month_link = checkLinkAvailable('news', 'by.month')?
					generateLink('news', 'by.month', array('year' => $row['year'], 'month' => sprintf('%02u', $row['month']))):
					generateLink('core', 'plugin', array('plugin' => 'news', 'handler' => 'by.month'), array('year' => $row['year'], 'month' => sprintf('%02u', $row['month'])));

		if ($tcounter) {
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
			$ctext  = $caseList[$sCase-1];
		} else {
			$ctext = '';
		}
		
		
		
		$tEntries [] = array(
			'link'		=>	$month_link,
			'title'		=>	$langMonths[$row['month']-1].' '.$row['year'],
			'cnt'		=>	$row['cnt'],
			'counter' 	=> 	$counter,
			'ctext' 	=>	$ctext,
		);

	}
	$tVars['entries']	= $tEntries;
	$tVars['tpl_url'] = tpl_url;
	

	// Prepare conversion table
	$conversionConfig = array(
		'{archive}'			=> '{% for entry in entries %}{% include localPath(0) ~ "entries.tpl" %}{% endfor %}',
		'{tpl_url}'			=> '{{ tpl_url }}',
	);
	
	$conversionConfigE = array(
		'{link}'			=> '{{ entry.link }}',
		'{title}'			=> '{{ entry.title }}',
		'{cnt}'			=> '{{ entry.cnt }}',
		'{ctext}'			=> '{{ entry.ctext }}',
	);

	$conversionConfigRegex = array(
		'#\[counter\](.+?)\[/counter\]#is' => '{% if (entry.counter) %}$1{% endif %}',
	);
	
	$twigLoader->setConversion($tpath['archive'].'archive.tpl', $conversionConfig);
	$twigLoader->setConversion($tpath['entries'].'entries.tpl', $conversionConfigE, $conversionConfigRegex);
	
	// Предзагрузка шаблона entries [ чтобы отработал setConversion ] при его наличии
	if (isset($tpath['entries']))
		$twig->loadTemplate($tpath['entries'].'entries.tpl');		
		
	$xt = $twig->loadTemplate($tpath[$templateName].$templateName.'.tpl');
	$output = $xt->render($tVars);
	
	if ($cacheExpire > 0) {
		cacheStoreFile($cacheFileName, $output, 'archive');
	}

	return $output;
}


//
// Show data block for xnews plugin
// Params:
// * maxnum		- Max num entries for archive
// * counter		- Show counter in the entries
// * tcounter		- Show text counter in the entries
// * template	- Personal template for plugin
// * cacheExpire		- age of cache [in seconds]
function plugin_archive_showTwig($params) {
	global $CurrentHandler, $config;

	return	plug_arch(isset($params['maxnum'])?$params['maxnum']:pluginGetVariable('archive','maxnum'), isset($params['counter'])?$params['counter']:false, isset($params['tcounter'])?$params['tcounter']:false, isset($params['template'])?$params['template']:false, isset($params['cacheExpire'])?$params['cacheExpire']:0);
}

twigRegisterFunction('archive', 'show', plugin_archive_showTwig);
