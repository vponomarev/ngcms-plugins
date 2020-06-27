<?php

if (!defined('NGCMS'))
	die ('HAL');
	
	
include_once(dirname(__FILE__).'/cache.php');
	
function plugin_block_zboard($number, $mode, $cat, $overrideTemplateName, $cacheExpire) {
	global $config, $mysql, $tpl, $template, $twig, $twigLoader, $langMonths, $lang, $TemplateCache;

	// Prepare keys for cacheing
	$cacheKeys = array();
	$cacheDisabled = false;
	
	
	if(isset($cat) && !empty($cat))
	{
		$cat_id = ' and n.cat_id IN ( '.$cat.' ) ';
	} else {
		$cat_id = '';
	}
	$sorting = $cat_id;
	
	if (($number < 1) || ($number > 100))
		$number = 5;
		
	switch ($mode) {
		case 'view':    $sql = 'SELECT *, c.id as cid, n.id as nid FROM '.prefix.'_zboard n LEFT JOIN '.prefix.'_zboard_cat c ON n.cat_id = c.id LEFT JOIN '.prefix.'_zboard_images i ON n.id = i.zid WHERE n.active = \'1\' '.$sorting.' GROUP BY n.id ORDER BY n.views DESC';
						break;
		case 'last':	$sql = 'SELECT *, c.id as cid, n.id as nid FROM '.prefix.'_zboard n LEFT JOIN '.prefix.'_zboard_cat c ON n.cat_id = c.id LEFT JOIN '.prefix.'_zboard_images i ON n.id = i.zid WHERE n.active = \'1\' '.$sorting.' GROUP BY n.id ORDER BY editdate DESC';
						break;
		case 'rnd':		$cacheDisabled = true;
						$sql = 'SELECT *, c.id as cid, n.id as nid FROM '.prefix.'_zboard n LEFT JOIN '.prefix.'_zboard_cat c ON n.cat_id = c.id LEFT JOIN '.prefix.'_zboard_images i ON n.id = i.zid WHERE n.active = \'1\' '.$sorting.' GROUP BY n.id ORDER BY RAND() DESC';
						break;
        case 'vip':		$sql = 'SELECT *, c.id as cid, n.id as nid FROM '.prefix.'_zboard n LEFT JOIN '.prefix.'_zboard_cat c ON n.cat_id = c.id LEFT JOIN '.prefix.'_zboard_images i ON n.id = i.zid WHERE n.active = \'1\' '.$sorting.' AND n.vip_expired != "" ORDER BY n.vip_expired DESC';
						break;
		default:		$mode = 'last';
						$sql = 'SELECT *, c.id as cid, n.id as nid FROM '.prefix.'_zboard n LEFT JOIN '.prefix.'_zboard_cat c ON n.cat_id = c.id LEFT JOIN '.prefix.'_zboard_images i ON n.id = i.zid WHERE n.active = \'1\' '.$sorting.' GROUP BY n.id ORDER BY editdate DESC';
						break;
	}
	$sql .= " limit ".$number;


	if ($overrideTemplateName) {
        $templateName = 'block/'.$overrideTemplateName;
    } else {
         $templateName = 'block/block_zboard';
    }
	
	// Determine paths for all template files
	$tpath = locatePluginTemplates(array($templateName), 'zboard', pluginGetVariable('zboard', 'localsource'));

	
	// Preload template configuration variables
	@templateLoadVariables();
	
	$cacheKeys []= '|number='.$number;
	$cacheKeys []= '|mode='.$mode;
	$cacheKeys []= '|cat='.$cat;
	$cacheKeys []= '|templateName='.$templateName;

	// Generate cache file name [ we should take into account SWITCHER plugin ]
	$cacheFileName = md5('zboard'.$config['theme'].$templateName.$config['default_lang'].join('', $cacheKeys)).'.txt';

	if (!$cacheDisabled && ($cacheExpire > 0)) {
		$cacheData = cacheRetrieveFile($cacheFileName, $cacheExpire, 'zboard');
		if ($cacheData != false) {
			// We got data from cache. Return it and stop
			return $cacheData;
		}
	}
	

	foreach ($mysql->select($sql) as $row) {
		
		if($row['author_id'] != 0) {
			$alink = checkLinkAvailable('uprofile', 'show')?
						generateLink('uprofile', 'show', array('id' => $row['author_id'])):
						generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('id' => $row['author_id']));
		}
		else { $alink = ''; }
		
		$fulllink = checkLinkAvailable('zboard', 'show')?
			generateLink('zboard', 'show', array('id' => $row['nid'])):
			generateLink('core', 'plugin', array('plugin' => 'zboard', 'handler' => 'show'), array('id' => $row['nid']));
		
		$catlink = checkLinkAvailable('zboard', '')?
			generateLink('zboard', '', array('cat' => $row['cid'])):
			generateLink('core', 'plugin', array('plugin' => 'zboard'), array('cat' => $row['cid']));
		
		$tEntries [] = array(
			'nid'					=>	$row['nid'],
			'date'					=>	$row['date'],
			'editdate'				=>	$row['editdate'],
			'views'					=>	$row['views'],
			'announce_name'			=>	$row['announce_name'],
			'author'				=>	$row['author'],
			'author_id'				=>	$row['author_id'],
			'author_email'			=>	$row['author_email'],
			'announce_period'		=>	$row['announce_period'],
			'announce_description'	=>	$row['announce_description'],
			'announce_contacts'		=>	$row['announce_contacts'],
            'vip_added'				=>	$row['vip_added'],
            'vip_expired'			=>	$row['vip_expired'],
			'fulllink'				=>	$fulllink,
			'catlink'				=>	$catlink,
			'cat_name'				=>	$row['cat_name'],
			'pid'					=>	$row['pid'],
			'filepath'				=>	$row['filepath'],
			'alink' 		=> $alink,
		);
		//var_dump($row);
	}
	
	$tVars['entries']	= $tEntries;
	$tVars['tpl_url'] = tpl_url;
	$tVars['home'] = home;

	$xt = $twig->loadTemplate($tpath[$templateName].$templateName.'.tpl');
	$output = $xt->render($tVars);
	
	if (!$cacheDisabled && ($cacheExpire > 0)) {
		cacheStoreFile($cacheFileName, $output, 'zboard');
	}
	
	return $output;
}

function plugin_m_zboard() {
	global $config;

	$zboard_dir = get_plugcfg_dir('zboard');
	generate_entries_cnt_cache();
	
	if(file_exists($zboard_dir.'/cache_entries_cnt.php')){
		$output = unserialize(file_get_contents($zboard_dir.'/cache_entries_cnt.php'));
	} else {
		$output = '';
	}

	return $output;
}

function plugin_m_zboard_catz_tree() {
	global $config;

	$zboard_dir = get_plugcfg_dir('zboard');
	generate_catz_cache();
	
	if(file_exists($zboard_dir.'/cache_catz.php')){
		$output = unserialize(file_get_contents($zboard_dir.'/cache_catz.php'));
	} else {
		$output = '';
	}

	return $output;
}

//
// Show data block for xnews plugin
// Params:
// * number			- Max num entries for top_active_users
// * mode			- Mode for show
// * template		- Personal template for plugin
// * cacheExpire	- age of cache [in seconds]
function plugin_block_zboard_showTwig($params = []) {
	global $CurrentHandler, $config;

	return	plugin_block_zboard($params['number'], $params['mode'], $params['cat'], $params['template'], isset($params['cacheExpire'])?$params['cacheExpire']:0);
}

function plugin_m_zboard_showTwig($params = []) {
	global $CurrentHandler, $config;

	return plugin_m_zboard();
}

function plugin_m_zboard_catz_tree_showTwig($params = []) {
	global $CurrentHandler, $config;

	return plugin_m_zboard_catz_tree();
}

twigRegisterFunction('zboard', 'show', plugin_block_zboard_showTwig);
twigRegisterFunction('zboard', 'show_entries_cnt', plugin_m_zboard_showTwig);
twigRegisterFunction('zboard', 'show_catz_tree', plugin_m_zboard_catz_tree_showTwig);
