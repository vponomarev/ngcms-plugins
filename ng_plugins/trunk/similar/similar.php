<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

// Preload plugin tags
load_extras('core', 'tags');

include_once("inc/similar.php");

class SimilarNewsfilter extends NewsFilter {
	function addNewsNotify(&$tvars, $SQL, $newsid) {
		global $mysql;

		$scount = pluginGetVariable('similar', 'count');
		$scount = (($scount < 1)||($scount > 20))?5:$scount;

		// Make reset for all tags for new news
		plugin_similar_reset($newsid);

		return 1;
	}

	// Make changes in DB after EditNews was successfully executed
	function editNewsNotify($newsID, $SQLnews, &$SQLnew, &$tvars) {
		global $mysql;

		if (!$SQLnews['approve'])
			return 1;

		// Reset linked news
		plugin_similar_resetLinked($newsID);

		// Reset news with the same tags [ AFTER actual edit - new tags ]
		plugin_similar_reset($newsID);

		return 1;
	}

	// Add {plugin_similar} variable into news
	function showNews($newsID, $SQLnews, &$tvars, $mode = array()) {
		global $mysql, $tpl, $PFILTERS;

		$tpath = locatePluginTemplates(array('similar', 'similar_entry'), 'similar', pluginGetVariable('similar', 'localsource'));

		// Show similar news only in full mode
		if ($mode['style'] == 'full') {
			// Check if we have similar news
			$similars = $SQLnews['similar_status'];
			if (!$similars) {
				$scount = pluginGetVariable('similar', 'count');
				$scount = (($scount < 1)||($scount > 20))?5:$scount;
				$similars = plugin_similar_recover($newsID, $scount);
			}

			// Locate similar news

			// Accroding to pcall parameter we should decide if full data export from news should be done
			$callingParams = array();

			// Filter for dimensions
			$filter = '';
			if (!($similar_enabled = intval(pluginGetVariable('similar', 'similar_enabled'))) || !($samecat_enabled=intval(pluginGetVariable('similar', 'samecat_enabled')))) {
				if ($similar_enabled) {
					$filter = 'and (dimension = 0)';
				} else if ($samecat_enabled) {
					$filter = 'and (dimension = 1)';
				} else
					$filter = 'and 0';
			}

			$query = "select n.id, n.catid, n.alt_name, n.postdate, si.id as si_id, si.dimension as si_dimension, si.newsID as si_newsID, si.refNewsID as si_refNewsID, si.refNewsQuantaty as si_refNewsQuantaty, si.refNewsTitle as si_refNewsTitle, si.refNewsDate as si_refNewsDate from ".prefix."_similar_index si left join ".prefix."_news n on n.id = si.refNewsID where si.newsID = ". db_squote($newsID).' '.($filter!=''?$filter.' ':'')."order by si.refNewsQuantaty desc";
			if (pluginGetVariable('similar', 'pcall')) {
				$query = "select n.*, si.id as si_id, si.dimension as si_dimension, si.newsID as si_newsID, si.refNewsID as si_refNewsID, si.refNewsQuantaty as si_refNewsQuantaty, si.refNewsTitle as si_refNewsTitle, si.refNewsDate as si_refNewsDate from ".prefix."_similar_index si left join ".prefix."_news n on n.id = si.refNewsID where si.newsID = ". db_squote($newsID).' '.($filter!=''?$filter.' ':'')."order by si.refNewsQuantaty desc";
				$callingParams['plugin'] = 'lastnews';
				switch (intval(pluginGetVariable('similar', 'pcall_mode'))) {
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

			if (($similars == 2) && count($similarRows = $mysql->select($query))) {

				// Array for dimensions of data [ similar / same category ]
				$result = array('', '');
				foreach ($similarRows as $similar) {
					$txvars = array ();

					// Execute filters [ if requested ]
					if (pluginGetVariable('similar', 'pcall') && is_array($PFILTERS['news']))
						foreach ($PFILTERS['news'] as $k => $v) { $v->showNewsPre($similar['id'], $similar, $callingParams); }

					// Set formatted date
					$dformat = pluginGetVariable('similar','dateformat')?pluginGetVariable('similar','dateformat'):'{day0}.{month0}.{year}';
					$txvars['vars']['date'] = str_replace(array('{day}', '{day0}', '{month}', '{month0}', '{year}', '{year2}', '{month_s}', '{month_l}'),
							array(date('j',$similar['si_refNewsDate']), date('d',$similar['si_refNewsDate']), date('n',$similar['si_refNewsDate']), date('m',$similar['si_refNewsDate']), date('y',$similar['si_refNewsDate']), date('Y',$similar['si_refNewsDate']), $langShortMonths[date('n',$similar['si_refNewsDate'])-1], $langMonths[date('n',$similar['si_refNewsDate'])-1]), $dformat);
					$txvars['vars']['title'] = $similar['si_refNewsTitle'];
					$txvars['vars']['url'] = newsGenerateLink($similar);

					// Execute filters [ if requested ]
					if (pluginGetVariable('similar', 'pcall') && is_array($PFILTERS['news']))
						foreach ($PFILTERS['news'] as $k => $v) { $v->showNews($similar['id'], $similar, $txvars, $callingParams); }

					$tpl -> template('similar_entry', $tpath['similar_entry']);
					$tpl -> vars('similar_entry', $txvars);
					$result[$similar['si_dimension']] .= $tpl -> show('similar_entry');
				}

				$tpl -> template('similar', $tpath['similar']);
				$tpl -> vars('similar', array ( 'vars' => array ('entries' => $result[0])));
				$tvars['vars']['plugin_similar_tags'] = $tpl -> show('similar');
			} else {
				$tvars['vars']['plugin_similar_tags'] = '';
				$tvars['vars']['plugin_similar_categ'] = '';
			}
		}

		return 1;
	}

	// Mass news modify
	function massModifyNewsNotify($idList, $setValue, $currentData) {

		// We are interested only in 'approve' field modification
		if (!isset($setValue['approve']))
			return 1;

		// Turn on - call RESET()
		if (!$setValue['approve']) {
			plugin_similar_reset($idList);
		} else {
			// Turn off - call renew for all linked news
			plugin_similar_resetLinked($idList);
		}
		return 1;
	}

	function deleteNews($newsID, $SQLnews) {
		global $mysql;

		plugin_similar_resetLinked($newsID);

		// Delete similarity info
		$mysql->query("delete from ".prefix."_similar_index where newsID = ".intval($newsID));
	}
}

// Activate plugin ONLY if plugin tags already activated
if (getPluginStatusActive('tags')) {
	register_filter('news','similar', new SimilarNewsFilter);
}
