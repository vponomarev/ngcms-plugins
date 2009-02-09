<?php

//
// Recover SIMILAR data for selected news
// [ RECOVER is called directly before news is showed ]
function plugin_similar_recover($newsID, $count) {
	global $mysql;

//	print "call REGENERATE SIMILARS for NEWS: ".$newsID." (".$count.")<br/>\n";
	if (!$count)
		return 0;

	// Delete old data
	$mysql->query("delete from ".prefix."_similar_index where newsID = ".db_squote($newsID));

	// Load a list of similar looking news via TAG index
	$list = $mysql->select("select i.newsID, count(i.tagID) as cnt from ".prefix."_tags_index i where (i.newsID <> ".db_squote($newsID).") and (i.tagID in ( select tagID from ".prefix."_tags_index where newsID = ".db_squote($newsID).") ) group by i.newsID order by cnt desc limit ".intval($count));

	// Check if we have something similar-looking. Break if nothing similar
	if (!sizeof($list)) {
		$mysql->query("update ".prefix."_news set similar_status = 1 where id = ".db_squote($newsID));
		// Return: OK, news do not have any similars
		return 1;
	}

	// Fine. Now we have a list of similar news.
	// Let's load data from this news
	$nlist = array();
	foreach ($list as $sr)
		$nlist [$sr['newsID']] = $sr['cnt'];

	$nl = $mysql->select("select id, title, editdate, postdate from ".prefix."_news where id in (".join(", ", array_keys($nlist)).")"); 

	// Now we have everything we need. Let's update similar list
	foreach ($nl as $nrow) {
		$mysql->query("insert into ".prefix."_similar_index (newsID, refNewsID, refNewsQuantaty, refNewsTitle, refNewsDate) values (".
			db_squote($newsID).", ".db_squote($nrow['id']).", ".db_squote($nlist[$nrow['id']]).", ".db_squote($nrow['title']).", ".db_squote($nrow['editdate']?$nrow['editdate']:$nrow['postdate']).")");
	}

	// And at the end - update news status
	$mysql->query("update ".prefix."_news set similar_status = 2 where id = ".db_squote($newsID));

	// Return: OK, news have similars
	return 2;
}

function plugin_similar_repopulate($newsid, $count){
	global $mysql;

	$affectedList = array();

	$newsIDlist = is_array($newsid)?$newsid:array($newsid);

	if (!count($newsIDlist))
		return array();

	// Populate list for delete
	$ld = array();
	foreach ($newsIDlist as $newsID)
		$ld [] = db_squote($newsID);

	// Delete old rec's
	$mysql->query("delete from ".prefix."_similar_index where newsID in (". join(", ", $ld).") or refNewsID in (". join(", ", $ld).")");

	foreach ($newsIDlist as $newsID) {
		$list = $mysql->select("select i.newsID, count(i.tagID) as cnt, n.title, n.editdate, n.postdate from ".prefix."_tags_index i left join ".prefix."_news n on n.id = i.newsID where (i.newsID <> ".db_squote($newsID).") and (i.tagID in ( select tagID from ".prefix."_tags_index where newsID = ".db_squote($newsID).") ) group by newsID order by cnt desc");

		// Populate data for our news
		for ($i = 0; $i < min(count($list), $count); $i++) {
			$row = $list[$i];
			$mysql->query("insert into ".prefix."_similar_index (newsID, refNewsID, refNewsQuantaty, refNewsTitle, refNewsDate) values (".db_squote($newsID).", ".db_squote($row['newsID']).", ".db_squote($row['cnt']).", ".db_squote($row['title']).", ".db_squote($row['editdate']?$row['editdate']:$row['postdate']).")");
		}

		foreach ($list as $row)
			$affectedList [ $row['newsID'] ] = $row['newsID'];
	}

	// And we return a list of affected news ( array of newsID's )
	return $affectedList;
}
