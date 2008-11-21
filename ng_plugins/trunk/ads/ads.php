<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');


// Get content [ array - content and deferred elements ]
function ads_get_content($num) {
	// Check for deferred load
	$v = 'ads'.$num;
	if (extra_get_param('ads', $v.'_defer')) {
		$content = extra_get_param('ads', $v.'_deferblk');
		$insertBlock = '<div style="display: none;" id="adsSource'.$num.'">'.extra_get_param('ads', $v).'</div>'.
			'<script language="JavaScript">'."\n".'document.getElementById("adsTarget'.$num.'").innerHTML = document.getElementById("adsSource'.$num.'").innerHTML;'."\n".'</script>';
		if (!$content) $content = '<div id="adsTarget'.$num.'"></div>';
		return array ( $content, $insertBlock );
	} else {
		return array ( extra_get_param('ads', $v) );
	}
}

//
// ADS filtering functions for internals of STATIC pages
class AdsStaticFilter extends StaticFilter {
	function showStatic($staticID, $SQLnstatic, &$tvars) {
		global $template;

		$count = extra_get_param('ads','count');
		if ((intval($count) < 1)||(intval($count) > 20))
			$count = 3;

		for ( $i = 1; $i <= $count; $i++) {
			$v = 'ads'.$i;
			$mode = extra_get_param('ads',$v.'_type');
			if ($mode != 'static')
				continue;

			list ($content, $insertBlock) = ads_get_content($i);
			$tvars['vars'][$v] = $content;
			if ($insertBlock)
				$template['vars']['plugin_ads_defer'] .= $insertBlock;
		}
	}
}

register_filter('static','ads', new AdsStaticFilter);


add_act('index', 'plugin_ads');

function plugin_ads(){
	global $template, $action, $category, $cstart, $tvars, $year, $month;

	$template['vars']['plugin_ads_defer'] = '';
	$template['vars']['plugin_ads_rand']  = rand().rand();

	$count = extra_get_param('ads','count');
	if ((intval($count) < 1)||(intval($count) > 20))
		$count = 3;

	for ( $i = 1; $i <= $count; $i++) {
		$enableDisplay = 0;

		$v = 'ads'.$i;
		$mode = extra_get_param('ads',$v.'_type');

		if (
			// main page
			(($mode == 'root')&&(!$action)&&(!$category)&&(!$cstart)&&(!$year)&&(!$month)) ||
			// everywhere except main page
			(($mode == 'noroot')&&($action||$category)) ||
			// everywhere
			($mode == 'all')
		   ) {
		   	$enableDisplay = 1;
		}

		if ($enableDisplay) {
			list ($content, $insertBlock) = ads_get_content($i);
			$template['vars'][$v] = $content;
			if ($insertBlock)
				$template['vars']['plugin_ads_defer'] .= $insertBlock;
		} else {
			$template['vars'][$v] = '';
		}
	}
}