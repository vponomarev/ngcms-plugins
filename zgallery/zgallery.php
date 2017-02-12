<?php
if (!defined('NGCMS'))
	die ('HAL');
function plugin_block_zgallery($number, $mode, $cat, $overrideTemplateName, $cacheExpire) {

	global $config, $mysql, $tpl, $template, $twig, $twigLoader, $langMonths, $lang, $TemplateCache, $userROW;
	@include_once root . 'includes/classes/upload.class.php';
	$fmanager = new file_managment();
	$type = 'image';
	$fmanager->get_limits($type);
	$filter = array();
	// Filter category if we work with images
	if ($type == 'image') {
		$filter = array('category = 0');
	} else {
		$filter = array();
	}
	if (isset($cat) && !empty($cat)) {
		$cc_str = "";
		$cat_array = explode(",", $cat);
		foreach ($cat_array as $cc_i => $cc_v) {
			$cat_array[$cc_i] = db_squote(trim($cc_v));
		}
		$cc_str = implode($cat_array, ', ');
		array_push($filter, "folder IN (" . $cc_str . ")");
	}
	// Show only images, that are not linked to any DataStorage
	array_push($filter, 'linked_ds = 0');
	$filter_where = (count($filter) ? "where " . join(" and ", $filter) : '');
	if (intval($number) > 0 || intval($number) < 1000) {
		$limit = "limit " . $number;
	} else {
		$limit = "";
	}
	if (!isset($mode)) {
		$mode = "date desc";
	}
	$sql = "select * from " . prefix . "_" . $fmanager->tname . " " . $filter_where . " order by " . $mode . " " . $limit;
	//var_dump($sql);
	// Prepare keys for cacheing
	$cacheKeys = array();
	$cacheDisabled = false;
	if ($overrideTemplateName) {
		$templateName = 'block/' . $overrideTemplateName;
	} else {
		$templateName = 'block/block';
	}
	// Determine paths for all template files
	$tpath = locatePluginTemplates(array($templateName), 'zgallery', pluginGetVariable('zgallery', 'localsource'));
	// Preload template configuration variables
	@templateLoadVariables();
	$cacheKeys [] = '|number=' . $number;
	$cacheKeys [] = '|mode=' . $mode;
	$cacheKeys [] = '|cat=' . $cat;
	$cacheKeys [] = '|templateName=' . $templateName;
	// Generate cache file name [ we should take into account SWITCHER plugin ]
	$cacheFileName = md5('zgallery' . $config['theme'] . $templateName . $config['default_lang'] . join('', $cacheKeys)) . '.txt';
	if (!$cacheDisabled && ($cacheExpire > 0)) {
		$cacheData = cacheRetrieveFile($cacheFileName, $cacheExpire, 'zgallery');
		if ($cacheData != false) {
			// We got data from cache. Return it and stop
			return $cacheData;
		}
	}
	$tEntries = array();
	$nCount = 0;
	foreach ($mysql->select($sql) as $row) {
		$nCount++;
		$folder = $row['folder'] ? $row['folder'] . '/' : '';
		$fname = $fmanager->dname . $folder . $row['name'];
		$fileurl = $fmanager->uname . '/' . $folder . $row['name'];
		$thumburl = $fmanager->uname . '/' . $folder . 'thumb/' . $row['name'];
		$row['nCount'] = $nCount;
		$row['folder'] = $folder;
		$row['fname'] = $fname;
		$row['fileurl'] = $fileurl;
		$row['thumburl'] = $thumburl;
		$tEntries [] = $row;
	}
	$tVars['entries'] = $tEntries;
	$tVars['tpl_url'] = tpl_url;
	$tVars['home'] = home;
	$xt = $twig->loadTemplate($tpath[$templateName] . $templateName . '.tpl');
	$output = $xt->render($tVars);
	if (!$cacheDisabled && ($cacheExpire > 0)) {
		cacheStoreFile($cacheFileName, $output, 'zgallery');
	}

	return $output;
}

class ShowZgallery extends NewsFilter {

	function showNews($newsID, $SQLnews, &$tvars, $mode = array()) {

		global $mysql, $tpl, $config, $twig;
		/*
		// [TWIG]..[/TWIG]
		if (preg_match_all('/\[TWIG\](.+?)\[\/TWIG\]/is', $SQLnews['content'], $parr)) {
			//var_dump($parr);
			foreach ($parr[0] as $k => $v) {
				$scode = $parr[1][$k];
				preg_match_all('/\{\{ callPlugin\((.+?),(.+?)\}\}/is', $parr[1][$k], $parse_callplug);
				$funct_name = str_replace("'","",$parse_callplug[1][0]);
				$funct_params = str_replace(")", "", trim($parse_callplug[2][0]));
				$funct_params = str_replace("'", '"', $funct_params);
				var_dump($funct_params);
				$out = plugin_block_zgallery_showTwig($funct_params);
				//var_dump($out);
				$SQLnews['content'] = str_replace($v, $result, $SQLnews['content']);
			}
		}
		*/
		if (preg_match_all('/\[TWIG\](.+?)\[\/TWIG\]/is', $tvars['vars']['news']['short'], $parr)) {
			foreach ($parr[0] as $k => $v) {
				$scode = $parr[1][$k];
				$cacheFileName = md5($scode) . '.txt';
				$cacheFile = cacheRetrieveFile($cacheFileName, 3600, '_templates');
				if ($cacheFile === false) {
					cacheStoreFile($cacheFileName, $scode, '_templates');
				}
				$tx = $twig->loadTemplate(get_plugcache_dir('_templates') . $cacheFileName);
				$result = $tx->render($tvars['vars']);
				$tvars['vars']['news']['short'] = str_replace($v, $result, $tvars['vars']['news']['short']);
			}
		}
	}
}

function plugin_block_zgallery_showTwig($params) {

	global $CurrentHandler, $config;

	return plugin_block_zgallery($params['number'], $params['mode'], $params['cat'], $params['template'], isset($params['cacheExpire']) ? $params['cacheExpire'] : 0);
}

twigRegisterFunction('zgallery', 'show', plugin_block_zgallery_showTwig);
register_filter('news', 'zgallery', new ShowZgallery);
