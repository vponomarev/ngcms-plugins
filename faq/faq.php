<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
register_plugin_page('faq', '', 'plugin_faq');
function plugin_faq() {

	global $catz, $twig, $catmap, $mysql, $config, $userROW, $tpl, $parse, $template, $lang, $PFILTERS, $SYSTEM_FLAGS, $CurrentHandler;
	$title_plg = 'Вопросы и ответы';
	$SYSTEM_FLAGS['info']['title']['group'] = isset($title_plg) ? $title_plg : $SYSTEM_FLAGS['info']['title']['group'];
	$tpath = locatePluginTemplates(array('faq_page'), 'faq', 1);
	$xt = $twig->loadTemplate($tpath['faq_page'] . 'faq_page.tpl');
	foreach ($mysql->select("SELECT *
				FROM " . prefix . "_faq WHERE (active = 1)
				ORDER BY id ASC") as $row) {
		$tEntry[] = array(
			'id'       => $row['id'],
			'question' => $row['question'],
			'answer'   => $row['answer'],
		);
	}
	$tVars = array(
		'entries' => isset($tEntry) ? $tEntry : '',
		'home'    => home,
	);
	$template['vars']['mainblock'] = $xt->render($tVars);
}

function plug_faq($maxnum, $overrideTemplateName, $order, $cacheExpire) {

	global $config, $mysql, $tpl, $template, $twig, $twigLoader, $langMonths, $lang;
	if (($maxnum < 1) || ($maxnum > 50)) $maxnum = 12;
	if ($overrideTemplateName) {
		$templateName = $overrideTemplateName;
	} else {
		$templateName = 'faq_block';
	}
	if ($order != 'ASC' && $order != 'DESC') {
		$order = 'DESC';
	}
	// Generate cache file name [ we should take into account SWITCHER plugin ]
	$cacheFileName = md5('faq' . $config['theme'] . $templateName . $config['default_lang']) . '.txt';
	if ($cacheExpire > 0) {
		$cacheData = cacheRetrieveFile($cacheFileName, $cacheExpire, 'faq');
		if ($cacheData != false) {
			// We got data from cache. Return it and stop
			return $cacheData;
		}
	}
	foreach ($mysql->select("SELECT * FROM " . prefix . "_faq WHERE active = '1' ORDER BY id " . $order . " limit $maxnum") as $row) {
		$tEntries [] = array(
			'id'       => $row['cnt'],
			'question' => $row['question'],
			'answer'   => $row['answer'],
		);
	}
	$tVars['entries'] = $tEntries;
	$tVars['tpl_url'] = tpl_url;
	// Determine paths for all template files
	$tpath = locatePluginTemplates(array($templateName), 'faq', pluginGetVariable('faq', 'localsource'));
	$xt = $twig->loadTemplate($tpath[$templateName] . $templateName . '.tpl');
	$output = $xt->render($tVars);
	if ($cacheExpire > 0) {
		cacheStoreFile($cacheFileName, $output, 'archive');
	}

	return $output;
}

// Params:
// * maxnum		- Max num entries
// * template	- Personal template for plugin
// * cacheExpire		- age of cache [in seconds]
function plugin_faq_showTwig($params) {

	global $CurrentHandler, $config;

	return plug_faq($params['maxnum'], isset($params['template']) ? $params['template'] : false, isset($params['order']) ? $params['order'] : 'DESC', isset($params['cacheExpire']) ? $params['cacheExpire'] : 0);
}

twigRegisterFunction('faq', 'show', plugin_faq_showTwig);