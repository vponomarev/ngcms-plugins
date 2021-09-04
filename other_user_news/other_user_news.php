<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
function plugin_other_user_news($number, $mode, $overrideTemplateName, $cacheExpire) {

	global $config, $mysql, $tpl, $template, $twig, $twigLoader, $langMonths, $lang, $TemplateCache, $CurrentHandler, $SYSTEM_FLAGS, $parse;
	// Prepare keys for cacheing
	$cacheKeys = array();
	$cacheDisabled = false;
	if (($number < 1) || ($number > 100))
		$number = 5;
	$current_news_id = $SYSTEM_FLAGS['news']['db.record']['id'];
	$current_author = $SYSTEM_FLAGS['news']['db.record']['author'];
	$current_author_id = $SYSTEM_FLAGS['news']['db.record']['author_id'];
	$sql = "SELECT * FROM " . prefix . "_news WHERE id != " . $current_news_id . " AND author_id = " . $current_author_id . " AND approve = 1 ";
	switch ($mode) {
		case 'view':
			$sql .= "ORDER BY views DESC";
			break;
		case 'com':
			$sql .= "ORDER BY com DESC";
			break;
		case 'dt':
			$sql .= "ORDER BY postdate DESC";
			break;
		case 'rnd':
			$cacheDisabled = true;
			$sql .= "ORDER BY RAND() DESC";
			break;
		default:
			$mode = 'dt';
			$sql .= "ORDER BY postdate DESC";
			break;
	}
	$sql .= " LIMIT " . $number;
	//var_dump($sql);
	if ($overrideTemplateName) {
		$templateName = $overrideTemplateName;
	} else {
		$templateName = 'other_user_news';
	}
	// Determine paths for all template files
	$tpath = locatePluginTemplates(array($templateName), 'other_user_news', pluginGetVariable('other_user_news', 'localsource'));
	$cacheKeys [] = '|current_news_id=' . $current_news_id;
	$cacheKeys [] = '|current_author_id=' . $current_author_id;
	$cacheKeys [] = '|number=' . $number;
	$cacheKeys [] = '|mode=' . $mode;
	$cacheKeys [] = '|templateName=' . $templateName;
	// Generate cache file name [ we should take into account SWITCHER plugin ]
	$cacheFileName = md5('other_user_news' . $config['theme'] . $templateName . $config['default_lang'] . join('', $cacheKeys)) . '.txt';
	if (!$cacheDisabled && ($cacheExpire > 0)) {
		$cacheData = cacheRetrieveFile($cacheFileName, $cacheExpire, 'other_user_news');
		if ($cacheData != false) {
			// We got data from cache. Return it and stop
			return $cacheData;
		}
	}
	$author_link = checkLinkAvailable('uprofile', 'show') ?
		generateLink('uprofile', 'show', array('name' => $current_author, 'id' => $current_author_id)) :
		generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('name' => $current_author, 'id' => $current_author_id));
	$ublog_link = generatePluginLink('ublog', null, array('uid' => $current_author_id, 'uname' => $current_author));
	foreach ($mysql->select($sql) as $row) {
		$news_link = newsGenerateLink($row);
		$categories = GetCategories($row['catid']);
		$short_news = '';
		list ($short_news, $full_news) = explode('<!--more-->', $row['content'], 2);
		if ($config['blocks_for_reg']) $short_news = $parse->userblocks($short_news);
		if ($config['use_htmlformatter']) $short_news = $parse->htmlformatter($short_news);
		if ($config['use_bbcodes']) $short_news = $parse->bbcodes($short_news);
		if ($config['use_smilies']) $short_news = $parse->smilies($short_news);
		//if (strlen($short_news) > $newslength) $short_news = $parse -> truncateHTML($short_news, $newslength);
		$row['news_link'] = $news_link;
		$row['categories'] = $categories;
		$row['short_news'] = $short_news;
		$tEntries [] = $row;
	}
	$tVars['entries'] = $tEntries;
	$tVars['author'] = $current_author;
	$tVars['author_id'] = $current_author_id;
	$tVars['author_link'] = $author_link;
	$tVars['ublog_link'] = $ublog_link;
	$xt = $twig->loadTemplate($tpath[$templateName] . $templateName . '.tpl');
	$output = $xt->render($tVars);
	if (!$cacheDisabled && ($cacheExpire > 0)) {
		cacheStoreFile($cacheFileName, $output, 'other_user_news');
	}

	return $output;
}

//
// Show data block for plugin
// Params:
// * number			- Max num entries for top_active_users
// * mode			- Mode for show
// * template		- Personal template for plugin
// * cacheExpire	- age of cache [in seconds]
function plugin_other_user_news_showTwig($params) {

	global $CurrentHandler, $config;

	return plugin_other_user_news($params['number'], $params['mode'], $params['template'], isset($params['cacheExpire']) ? $params['cacheExpire'] : 0);
}

twigRegisterFunction('other_user_news', 'show', 'plugin_other_user_news_showTwig');
