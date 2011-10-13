<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

add_act('index_post', 'plugin_ads_pro');
$plugin_ads_pro_data = array('stat' => false, 'stat_id' => null, 'news' => false, 'news_id' => null, 'cat' => false, 'cat_id' => null, 'main' => false);
class ADSProStaticFilter extends StaticFilter {
	function showStatic($staticID, $SQLstatic, &$tvars, $mode) { 
		global $plugin_ads_pro_data;
		$plugin_ads_pro_data['stat'] = true;
		$plugin_ads_pro_data['stat_id'] = $staticID;
		return 1; 
	}
}
register_filter('static','ads_pro', new ADSProStaticFilter);

class ADSProNewsFilter extends NewsFilter {
	function showNews($newsID, $SQLnews, &$tvars, $mode = array()) {
		global $plugin_ads_pro_data;
		if ($mode['style'] == 'full') {
			$plugin_ads_pro_data['news'] = true;
			$plugin_ads_pro_data['news_id'] = $newsID;
		}
		return 1;				
	}
}
register_filter('news', 'ads_pro', new ADSProNewsFilter);

function plugin_ads_pro($params) {	
	global $template, $config, $CurrentHandler, $catmap, $mysql, $plugin_ads_pro_data;
	
	$var = pluginGetVariable('ads_pro', 'data');
	if (!is_array($var)) return;

	if ($CurrentHandler['params'][0] == '/') $plugin_ads_pro_data['main'] = true;
	if (isset($CurrentHandler['params']['catid'])) {
		$plugin_ads_pro_data['cat'] = true;
		$plugin_ads_pro_data['cat_id'] = $CurrentHandler['params']['catid'];
	} else if (isset($CurrentHandler['params']['category'])) {
		$plugin_ads_pro_data['cat'] = true;
		$plugin_ads_pro_data['cat_id'] = array_search($CurrentHandler['params']['category'], $catmap);
	}

	$t_time = time();
	foreach ($var as $k => $v) {
		if (!$k) continue;
		if (!isset($template['vars'][$k])) $template['vars'][$k] = '';
		foreach ($v as $kk => $vv) {
			if (!$vv['state']) continue;
			$if_view = true;
			if ($vv['state'] == 2) {
				if ($vv['start_view'] && $vv['start_view'] > $t_time)
					$if_view = false;
				if ($vv['end_view'] && $vv['end_view'] <= $t_time)
					$if_view = false;
			}
			if (!$if_view) continue;
			if (is_array($vv['location'])) {
				$if_view = false;
				$if_break = false;
				foreach ($vv['location'] as $kkk => $vvv) {
					switch ($vvv['mode']) {
						case 0:
							if ($vvv['view']) {$if_view = false; $if_break = true;} else $if_view = true;
							break;
						case 1:
							if ($plugin_ads_pro_data['main']) {
								if ($vvv['view']) {$if_view = false; $if_break = true;} else $if_view = true;
							}
							break;
						case 2:
							if (!$plugin_ads_pro_data['main']) {
								if ($vvv['view']) {$if_view = false; $if_break = true;} else $if_view = true;
							}
							break;
						case 3:
							if ($plugin_ads_pro_data['cat']) {
								if (!$vvv['id']) {
									if ($vvv['view']) {$if_view = false; $if_break = true;} else $if_view = true;
								} else if ($plugin_ads_pro_data['cat_id'] == $vvv['id']) {
									if ($vvv['view']) {$if_view = false; $if_break = true;} else $if_view = true;
								}
							}
							break;
						case 4:
							if ($plugin_ads_pro_data['stat']) {
								if (!$vvv['id']) {
									if ($vvv['view']) {$if_view = false; $if_break = true;} else $if_view = true;
								} else if ($plugin_ads_pro_data['stat_id'] == $vvv['id']) {
									if ($vvv['view']) {$if_view = false; $if_break = true;} else $if_view = true;
								}
							}
							break;
						case 5:
							if ($plugin_ads_pro_data['news']) {
								if (!$vvv['id']) {
									if ($vvv['view']) {$if_view = false; $if_break = true;} else $if_view = true;
								} else if ($plugin_ads_pro_data['news_id'] == $vvv['id']) {
									if ($vvv['view']) {$if_view = false; $if_break = true;} else $if_view = true;
								}
							}
							break;
					}
					if ($if_break) break;
				}
				if (!$if_view) continue;
			}

			if ($vv['type'] != 1)
			{
				$cacheFileName = md5('ads_pro'.$kk.$vv['type']).'.txt';
				$cacheData = cacheRetrieveFile($cacheFileName, 30000, 'ads_pro');
				if ($cacheData != false) {
					$template['vars'][$k] .= $cacheData;
					continue;
				}
				$description = '';
				if (is_array($row = $mysql->record('select ads_blok from '.prefix.'_ads_pro where id='.db_squote($kk)))) {
					$description = $vv['type']?nl2br(htmlspecialchars($row['ads_blok'])):$row['ads_blok'];
				}
				$template['vars'][$k] .= $description;
				cacheStoreFile($cacheFileName, $description, 'ads_pro');
			} else {
				$description = '';
				if (is_array($row = $mysql->record('select ads_blok from '.prefix.'_ads_pro where id='.db_squote($kk)))) {
					$description = $row['ads_blok'];
				}
				ob_start();
				@eval($description);
				$out2 = ob_get_contents();
				ob_end_clean();
				$template['vars'][$k] .= $out2;
			}
		}
		if ($if_brek)
			break;
	}
}