<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

function plugin_top_active_users($number, $mode, $overrideTemplateName, $cacheExpire) {
	global $config, $mysql, $tpl, $template, $twig, $twigLoader, $langMonths, $lang, $TemplateCache;

	// Prepare keys for cacheing
	$cacheKeys = array();
	$cacheDisabled = false;
	
	if (($number < 1) || ($number > 100))
		$number = 5;
		
	switch ($mode) {
		case 'news':	$sql = "SELECT id, name, com, news, avatar, mail, last, reg FROM ".uprefix."_users ORDER BY news DESC";
						break;
		case 'com':    	$sql = "SELECT id, name, com, news, avatar, mail, last, reg FROM ".uprefix."_users ORDER BY com DESC";
						break;
		case 'last':	$sql = "SELECT id, name, com, news, avatar, mail, last, reg FROM ".uprefix."_users ORDER BY reg DESC";
						break;
		case 'rnd':		$cacheDisabled = true;
						$sql = "SELECT id, name, com, news, avatar, mail, last, reg FROM ".uprefix."_users ORDER BY RAND() DESC";
						break;
		default:		$mode = 'news';
						$sql = "SELECT id, name, com, news, avatar, mail, last, reg FROM ".uprefix."_users ORDER BY news DESC";
						break;
	}
	$sql .= " limit ".$number;


	if ($overrideTemplateName) {
        $templateName = $overrideTemplateName;
    } else {
         $templateName = 'top_active_users';
    }
	
	// Determine paths for all template files
	$tpath = locatePluginTemplates(array($templateName), 'top_active_users', pluginGetVariable('top_active_users', 'localsource'));

	
	// Preload template configuration variables
	@templateLoadVariables();

	// Use default <noavatar> file
	// - Check if noavatar is defined on template level
	$tplVars = $TemplateCache['site']['#variables'];
	$noAvatarURL = (isset($tplVars['configuration']) && is_array($tplVars['configuration']) && isset($tplVars['configuration']['noAvatarImage']) &&$tplVars['configuration']['noAvatarImage'])?(tpl_url."/".$tplVars['configuration']['noAvatarImage']):(avatars_url."/noavatar.gif");
	
	$cacheKeys []= '|number='.$number;
	$cacheKeys []= '|mode='.$mode;
	$cacheKeys []= '|templateName='.$templateName;

	// Generate cache file name [ we should take into account SWITCHER plugin ]
	$cacheFileName = md5('top_active_users'.$config['theme'].$templateName.$config['default_lang'].join('', $cacheKeys)).'.txt';

	if (!$cacheDisabled && ($cacheExpire > 0)) {
		$cacheData = cacheRetrieveFile($cacheFileName, $cacheExpire, 'top_active_users');
		if ($cacheData != false) {
			// We got data from cache. Return it and stop
			return $cacheData;
		}
	}
	

	foreach ($mysql->select($sql) as $row) {
		$alink = checkLinkAvailable('uprofile', 'show')?
					generateLink('uprofile', 'show', array('name' => $row['name'], 'id' => $row['id'])):
					generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('name' => $row['name'], 'id' => $row['id']));
		$ublog_link = generatePluginLink('ublog', null, array('uid' => $row['id'], 'uname' => $row['name']));
		
		$use_avatars = $config['use_avatars'];
		
		// Generate avatar link
		if ($config['use_avatars']) {
			if ($row['avatar']) {
				$avatars = avatars_url."/".$row['avatar'];
			} else {
				// If gravatar integration is active, show avatar from GRAVATAR.COM
				if ($config['avatars_gravatar']) {
					$avatars = 'http://www.gravatar.com/avatar/'.md5(strtolower($row['mail'])).'.jpg?s='.$config['avatar_wh'].'&d='.urlencode($noAvatarURL);
				} else {
					$avatars = $noAvatarURL;
				}
			}
		} else {
			$avatars = '';
		}
		
		$tEntries [] = array(
			'name'			=>	$row['name'],
			'link'			=>	$alink,
			'ulink' 		=> $ublog_link,
			'avatar_url'	=>	$avatars,
			'mail'			=> $row['mail'],
			'last'			=> $row['last'],
			'reg'			=> $row['reg'],
			'use_avatars' 		=> 	$use_avatars,
			'news' 		=>	$row['news'],
			'com' 		=>	$row['com'],
		);

	}
	
	$tVars['entries']	= $tEntries;
	$tVars['tpl_url'] = tpl_url;

	$xt = $twig->loadTemplate($tpath[$templateName].$templateName.'.tpl');
	$output = $xt->render($tVars);
	
	if (!$cacheDisabled && ($cacheExpire > 0)) {
		cacheStoreFile($cacheFileName, $output, 'top_active_users');
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
function plugin_top_active_users_showTwig($params) {
	global $CurrentHandler, $config;

	return	plugin_top_active_users($params['number'], $params['mode'], $params['template'], isset($params['cacheExpire'])?$params['cacheExpire']:0);
}

twigRegisterFunction('top_active_users', 'show', plugin_top_active_users_showTwig);
