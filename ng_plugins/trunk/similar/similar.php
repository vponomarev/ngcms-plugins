<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

// Preload plugin tags
load_extras('core', 'tags');

include_once("inc/similar.php");

class SimilarNewsfilter extends NewsFilter {
	function addNewsNotify(&$tvars, $SQL, $newsid) {
		global $mysql;

		$scount = extra_get_param('similar', 'count');
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
	function showNews($newsID, $SQLnews, &$tvars, $mode) {
		global $mysql, $tpl;

		$tpath = locatePluginTemplates(array('similar', 'similar_entry'), 'similar', extra_get_param('similar', 'localsource'));

		// Show similar news only in full mode
		if ($mode['style'] == 'full') {
			// Check if we have similar news
			$similars = $SQLnews['similar_status'];
			if (!$similars) {
				$scount = extra_get_param('similar', 'count');
				$scount = (($scount < 1)||($scount > 20))?5:$scount;
				$similars = plugin_similar_recover($newsID, $scount);
			}	

			// Locate similar news
			// Accroding to pcall parameter we should decide if full data export from news should be done
			$query = "select n.id, n.catid, n.alt_name, n.postdate, si.id as si_id, si.newsID as si_newsID, si.refNewsID as si_refNewsID, si.refNewsQuantaty as si_refNewsQuantaty, si.refNewsTitle as si_refNewsTitle, si.refNewsDate as si_refNewsDate from ".prefix."_similar_index si left join ".prefix."_news n on n.id = si.refNewsID where si.newsID = ". db_squote($newsID)." order by si.refNewsQuantaty desc";
			if (extra_get_param('similar', 'pcall')) {
				$query = "select n.*, si.id as si_id, si.newsID as si_newsID, si.refNewsID as si_refNewsID, si.refNewsQuantaty as si_refNewsQuantaty, si.refNewsTitle as si_refNewsTitle, si.refNewsDate as si_refNewsDate from ".prefix."_similar_index si left join ".prefix."_news n on n.id = si.refNewsID where si.newsID = ". db_squote($newsID)." order by si.refNewsQuantaty desc";
			}

			if (($similars == 2) && count($similarRows = $mysql->select($query))) {

				$result = '';
				foreach ($similarRows as $similar) {
					$txvars = array ();

					// Set formatted date
					$dformat = extra_get_param('similar','dateformat')?extra_get_param('similar','dateformat'):'{day0}.{month0}.{year}';
					$txvars['vars']['date'] = str_replace(array('{day}', '{day0}', '{month}', '{month0}', '{year}', '{year2}', '{month_s}', '{month_l}'),
							array(date('j',$similar['si_refNewsDate']), date('d',$similar['si_refNewsDate']), date('n',$similar['si_refNewsDate']), date('m',$similar['si_refNewsDate']), date('y',$similar['si_refNewsDate']), date('Y',$similar['si_refNewsDate']), $langShortMonths[date('n',$similar['si_refNewsDate'])-1], $langMonths[date('n',$similar['si_refNewsDate'])-1]), $dformat);
					$txvars['vars']['title'] = $similar['si_refNewsTitle'];
					$txvars['vars']['url'] = getLink('full', $similar);

					$tpl -> template('similar_entry', $tpath['similar_entry']);
					$tpl -> vars('similar_entry', $txvars);
					$result .= $tpl -> show('similar_entry');
				}

				$tpl -> template('similar', $tpath['similar']);
				$tpl -> vars('similar', array ( 'vars' => array ('entries' => $result)));
				$tvars['vars']['plugin_similar'] = $tpl -> show('similar');
			} else {
				$tvars['vars']['plugin_similar'] = '';
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
register_filter('news','similar', new SimilarNewsFilter);

