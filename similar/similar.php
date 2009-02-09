<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

// Preload plugin tags
load_extras('core', 'tags');
register_filter('news','similar', new SimilarNewsFilter);

include_once("inc/similar.php");

class SimilarNewsfilter extends NewsFilter {
	function addNewsNotify(&$tvars, $SQL, $newsid) {
		global $mysql;

		$scount = extra_get_param('similar', 'count');
		$scount = (($scount < 1)||($scount > 20))?5:$scount;

		// Modify DB data
		plugin_similar_repopulate(plugin_similar_repopulate($newsid, $scount), $scount, array($newsid));

		return 1;
	}

	// Make changes in DB after EditNews was successfully executed
	function editNewsNotify($newsID, $SQLnews, &$SQLnew, &$tvars) {
		global $mysql;

		$scount = extra_get_param('similar', 'count');
		$scount = (($scount < 1)||($scount > 20))?5:$scount;

		// Modify DB data
		plugin_similar_repopulate(plugin_similar_repopulate($newsID, $scount), $scount, array($newsID));

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
			if (!$similars)
				$similars = plugin_similar_recover($newsID, extra_get_param('similar', 'count'));
	
			// Locate similar news
			if (($similars == 2) && count($similarRows = $mysql->select("select si.*, n.id as n_id, n.catid as n_catid, n.alt_name as n_alt_name, n.postdate as n_postdate from ".prefix."_similar_index si left join ".prefix."_news n on n.id = si.refNewsID where si.newsID = ". db_squote($newsID)." order by si.refNewsQuantaty desc"))) {

				$result = '';
				foreach ($similarRows as $similar) {
					$txvars = array ();

					// Set formatted date
					$dformat = extra_get_param('similar','dateformat')?extra_get_param('similar','dateformat'):'{day0}.{month0}.{year}';
					$txvars['vars']['date'] = str_replace(array('{day}', '{day0}', '{month}', '{month0}', '{year}', '{year2}', '{month_s}', '{month_l}'),
							array(date('j',$similar['refNewsDate']), date('d',$similar['refNewsDate']), date('n',$similar['refNewsDate']), date('m',$similar['refNewsDate']), date('y',$similar['refNewsDate']), date('Y',$similar['refNewsDate']), $langShortMonths[date('n',$similar['refNewsDate'])-1], $langMonths[date('n',$similar['refNewsDate'])-1]), $dformat);
					$txvars['vars']['title'] = $similar['refNewsTitle'];
					$txvars['vars']['url'] = getLink('full', array('id' => $similar['n_id'], 'catid' => $similar['n_catid'], 'alt_name' => $similar['n_alt_name'], 'postdate' => $similar['n_postdate']));

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

		// Catch a list of changed news
		$modList = array();
		foreach ($currentData as $newsID => $newsData)
			if ($newsData['approve'] != $setValue['approve'])
				$modList [] = $newsID;

		// If no news was changed - exit
		if (!count($modList))
			return 1;

		$scount = extra_get_param('similar', 'count');
		$scount = (($scount < 1)||($scount > 20))?5:$scount;

		// Modify DB data
		plugin_similar_repopulate(plugin_similar_repopulate($modList, $scount), $scount, $idList);
		return 1;
	}

	function deleteNewsNotify($newsID, $SQLnews) {
		global $mysql;

		$scount = extra_get_param('similar', 'count');
		$scount = (($scount < 1)||($scount > 20))?5:$scount;

		// Fetch list of news that should be modified
		$nList = array();

		foreach ($mysql->select("select distinct(newsID) from ".prefix."_similar_index where refNewsID = ".db_squote($newsID)) as $row) {
			$nList [] = $row['newsID'];
		}

		// Modify DB data
		plugin_similar_repopulate($nList, $scount);
	}
}
