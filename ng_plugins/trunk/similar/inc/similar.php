<?php


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
