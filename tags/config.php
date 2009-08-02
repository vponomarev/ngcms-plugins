<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Configuration file for plugin
//

plugins_load_config();
LoadPluginLang('tags', 'main', '', '', ':');

// Fill configuration parameters
$skList = array();
if ($skDir = opendir(extras_dir.'/tags/tpl/skins')) {
	while ($skFile = readdir($skDir)) {
		if (!preg_match('/^\./', $skFile)) {
			$skList[$skFile] = $skFile;
		}
	}
	closedir($skDir);
}

$cfg = array();
//array_push($cfg, array('descr' => $lang['tags:descr']));
array_push($cfg, array('name' => 'rebuild', 'title' => $lang['tags:rebuild'], 'descr' => $lang['tags:rebuild_desc'], 'type' => 'select', 'value' => 0, 'values' => array ( 0 => $lang['noa'], 1 => $lang['yesa']), 'nosave' => 1));

$cfgX = array();
//array_push($cfg, array('name' => 'timestamp', 'title' => $lang['tags:timestamp'], 'descr' => $lang['tags:timestamp_desc'], 'type' => 'input', 'html_flags' => 'size="40"', 'value' => extra_get_param($plugin, 'timestamp')));
array_push($cfgX, array('name' => 'limit', 'title' => $lang['tags:limit'], 'descr' => $lang['tags:limit_desc'], 'type' => 'input', 'html_flags' => 'size="4"', 'value' => extra_get_param($plugin, 'limit')));
array_push($cfgX, array('name' => 'orderby', 'title' => $lang['tags:orderby'], 'descr' => $lang['tags:orderby_desc'], 'type' => 'select', 'values' => array ( '0' => $lang['tags:order_rand'], '1' => $lang['tags:order_tag_asc'], '2' => $lang['tags:order_tag_desc'], '3' => $lang['tags:order_pop_asc'], '4' => $lang['tags:order_pop_desc']), 'value' => extra_get_param($plugin, 'orderby')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>'.$lang['tags:cfg_sidepanel'].'</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'ppage_limit', 'title' => $lang['tags:limit'], 'descr' => $lang['tags:limit_desc'], 'type' => 'input', 'html_flags' => 'size="4"', 'value' => extra_get_param($plugin, 'ppage_limit')));
array_push($cfgX, array('name' => 'ppage_orderby', 'title' => $lang['tags:orderby'], 'descr' => $lang['tags:orderby_desc'], 'type' => 'select', 'values' => array ( '0' => $lang['tags:order_rand'], '1' => $lang['tags:order_tag_asc'], '2' => $lang['tags:order_tag_desc'], '3' => $lang['tags:order_pop_asc'], '4' => $lang['tags:order_pop_desc']), 'value' => extra_get_param($plugin, 'ppage_orderby')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>'.$lang['tags:cfg_ppage'].'</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'manualstyle', 'title' => $lang['tags:manualstyle'], 'descr' => $lang['tags:manualstyle_desc'], 'type' => 'select', 'values' => array ( '1' => $lang['yesa'], '0' => $lang['noa']), 'value' => (extra_get_param($plugin, 'manualstyle'))?extra_get_param($plugin, 'manualstyle'):0));
array_push($cfgX, array('name' => 'styles', 'title' => $lang['tags:styles'], 'descr' => $lang['tags:styles_desc'], 'type' => 'input', 'html_flags' => 'size=70', 'value' => extra_get_param('tags','styles')));
array_push($cfgX, array('name' => 'styles_weight', 'title' => $lang['tags:styles_weight'], 'descr' => $lang['tags:styles_weight_desc'], 'type' => 'text', 'html_flags' => 'cols=65 rows=4', 'value' => extra_get_param('tags','styles_weight')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>'.$lang['tags:cfg_stylecontrol'].'</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'localsource', 'title' => $lang['tags:localsource'], 'descr' => $lang['tags:localsource'], 'type' => 'select', 'values' => array ( '0' => $lang['tags:lsrc_site'], '1' => $lang['tags:lsrc_plugin']), 'value' => intval(extra_get_param($plugin,'localsource'))));
array_push($cfgX, array('name' => 'skin', 'title'   => $lang['tags:skin'], 'descr' => $lang['tags:skin_desc'], 'type' => 'select', 'values' => $skList, 'value' => extra_get_param('tags','skin')));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>'.$lang['tags:cfg_display'].'</b>', 'entries' => $cfgX));

$cfgX = array();
array_push($cfgX, array('name' => 'cache', 'title' => $lang['tags:use_cache'], 'descr' => $lang['tags:use_cache_desc'], 'type' => 'select', 'values' => array ( '1' => $lang['yesa'], '0' => $lang['noa']), 'value' => intval(extra_get_param($plugin,'cache'))));
array_push($cfgX, array('name' => 'cacheExpire', 'title' => $lang['tags:cache_expire'], 'descr' => $lang['tags:cache_expire_desc'], 'type' => 'input', 'value' => intval(extra_get_param($plugin,'cacheExpire'))?extra_get_param($plugin,'cacheExpire'):'60'));
array_push($cfg,  array('mode' => 'group', 'title' => '<b>'.$lang['tags:cfg_cache'].'</b>', 'entries' => $cfgX));



if (!$_REQUEST['action']) {
	generate_config_page($plugin, $cfg);
}
elseif ($_REQUEST['action'] == 'commit') {
	if ($_REQUEST['rebuild']) {
		// Rebuild index table
		// * Truncate index
		$mysql->query("truncate table ".prefix."_tags_index");
		// * LOCK
		$mysql->query("lock tables ".prefix."_tags write, ".prefix."_tags_index write, ".prefix."_tags_index i write, ".prefix."_news read, ".prefix."_news n read");
		// * Zero counters
		$mysql->query("update ".prefix."_tags set posts = 0");
		// * Scan news [ FILL TAGS ARRAY IN MEMORY ] [ FILL NEWS with tags ARRAY IN MEMORY ]
		$tags = array();
		$tagIndexSQL = $mysql->select("select id, tags from ".prefix."_news where (tags is not NULL) and (tags <> '') and (approve = 1)");
		foreach ($tagIndexSQL as $row) {
			$ntags = preg_split("/, */", trim($row['tags']));
			foreach ($ntags as $ntag) {
				$ntag = trim($ntag);
				if (!strlen($ntag))
					continue;
				$tags[$ntag] = $tags[$ntag] + 1;
			}
		}

		// * Process counters
		foreach ($tags as $tag => $cnt) {
			$mysql->query("insert into ".prefix."_tags (tag, posts) values (".db_squote($tag).",".intval($cnt).") on duplicate key update posts = posts + ".intval($cnt));
		}

		// * Regenerate counters
		foreach ($tagIndexSQL as $row) {
			$ntags = preg_split("/, */", trim($row['tags']));
			$ntagsQ = array();
			foreach ($ntags as $tag) {
				$tag = trim($tag);
				if (!strlen($tag))
					continue;
				$ntagsQ[] = db_squote($tag);
			}
			if (sizeof($ntagsQ))
				$mysql->query("insert into ".prefix."_tags_index (newsID, tagID) select ".db_squote($row['id']).", id from ".prefix."_tags where tag in (".join(",",$ntagsQ).")");
		}

		// * DELETE unused tags
		$mysql->query("delete from ".prefix."_tags where posts = 0");

		$mysql->query("unlock tables");
		print $lang['tags:rebuild_done']."<br/>";
	}
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
}