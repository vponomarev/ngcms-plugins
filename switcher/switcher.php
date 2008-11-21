<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

add_act('core', 'plugin_switcher');
add_act('index', 'plugin_switcher_menu');
register_plugin_page('switcher','','switcher_redirector',0);

function plugin_switcher(){
	global $config;

	// Get chosen template
	$sw_template = $_COOKIE['sw_template'];

	$sw_count = intval(extra_get_param('switcher','count'));
	if (!$sw_count) { $sw_count = 3; }

	// If template is not selected, we can show default value
	if (!$sw_template) {
		// Check if we have default profile for this domain
		for ($i = 0; $i <= $sw_count; $i++) {
			$dlist = extra_get_param('switcher','profile'.$i.'_domains');
			if (!$dlist) continue;
			if (!is_array($darr=explode("\n",$dlist))) continue;
			$is_catched = 0;
			foreach ($darr as $dname) {
				$dname = trim($dname);
				if (!$dname) continue;
				// Check if domain fits our domain
				if (($_SERVER['SERVER_NAME'] == $dname)||($_SERVER['HTTP_HOST'] == $dname)) {
				        $is_catched = 1;
				}
			}
			if ($is_catched) { $sw_template = $i; break; }
		}
	}

	if (($sw_template> 0) && ($sw_template <= $sw_count) && extra_get_param('switcher','profile'.$sw_template.'_active')) {
	        if (extra_get_param('switcher','profile'.$sw_template.'_template')) {
			$config['theme'] = extra_get_param('switcher','profile'.$sw_template.'_template');
		}
	        if (extra_get_param('switcher','profile'.$sw_template.'_lang')) {
			$config['default_lang'] = extra_get_param('switcher','profile'.$sw_template.'_lang');
		}
	}

}

function plugin_switcher_menu(){
	global $template, $tpl, $lang;

	$list = '';
	$sw_count = intval(extra_get_param('switcher','count'));
	if (!$sw_count) { $sw_count = 3; }

	for ($i=1; $i <= $sw_count ; $i++) {
		if (extra_get_param('switcher','profile'.$i.'_active')) {
			$list.="<option value='$i'>".extra_get_param('switcher', 'profile'.$i.'_name')."</option>\n";
		}
	}

	LoadPluginLang('switcher', 'main','','switcher');

	$tpl->template('switcher',extras_dir.'/switcher/tpl');

	$tvars['vars']['list'] = $list;
	$tpl->vars('switcher', $tvars);
	$template['vars']['switcher'] = $tpl->show('switcher');

}

function switcher_redirector(){
	$templateID = $_REQUEST['profile'];

	// Scan for template with this ID
	$sw_count = intval(extra_get_param('switcher','count'));
	if (!$sw_count) { $sw_count = 3; }

	$templateNum = 0;
	for ($i=1; $i <= $sw_count ; $i++) {
		if (extra_get_param('switcher','profile'.$i.'_id') == $templateID) {
			$templateNum = $i;
			break;
		}
	}

	// Set cookie with template ID
	@setcookie('sw_template', $templateNum, time() + 365 * 24 * 60 * 60, '/');

	// Redirect to the root directory of the site
	@header("Location: ".home);
}