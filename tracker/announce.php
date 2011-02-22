<?php

//
// BitTorrent tracker plugin for NGCMS
// Author: Vitaly Ponomarev
// Used libraries: PEAR File::Bittorrent2
//

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');


function plugin_tracker_announce() {
	global $SUPRESS_TEMPLATE_SHOW, $SUPRESS_MAINBLOCK_SHOW;

	$SUPRESS_TEMPLATE_SHOW = 1;
	$SUPRESS_MAINBLOCK_SHOW = 1;
	//
	include_once "lib/basic.php";
	$enc = new bt_bencode();
	//
	$v_required = array(	'info_hash' => 'string',
							'peer_id' => 'string',
							'port' => 'integer',
							'uploaded' => 'integer',
							'downloaded' => 'integer',
							'left' => 'integer');
	$v_optional = array(	'ip' => 'string',
							'event' => 'string',
							'compact' => 'integer',
							'no_peer_id' => 'integer',
							'numwant' => 'integer',
							'key' => 'string');

	// Fetch required variables
	$params = array();
	foreach (array_keys($v_required) as $key) {
		if (!isset($_GET[$key])) {
			// Return an error
			print $enc->encode(array("failure reason" => "mondatory parameter [$key] is lost", "interval" => 600));
			return;
		}
		$params[$key] = ($v_required[$key] == 'integer')?((int)intval($_GET[$key])):$_GET[$key];
	}

	foreach (array_keys($v_required) as $key) {
		if (isset($_GET[$key]))
			$params[$key] = ($v_required[$key] == 'integer')?((int)intval($_GET[$key])):$_GET[$key];
	}

	// Save user-agent
	$params['agent'] = $_SERVER['HTTP_USER_AGENT'];

	// Try to fetch data on requested info_hash
	$data = tracker_fetch_tstatus(bin2hex($params['info_hash']), 'report', 130, $params);

	// Return an error report (if needed)
	if ($data == false) {
		print $enc->encode(array("failure reason" => "unknown error"));
		return;
	}

	// Return peer list
	if ((!isset($params['numwant'])) || ($params['numwant'] < 5)) {
		$params['numwant'] = 50;
	}

	// Generate list & stats
	$answer = array();
	$c_complete = 0;
	$c_incomplete = 0;
	$peers = ($params['compact'])?'':array();
	foreach ($data as $rec) {
		// Send current peer data to reques
		if ($params['compact']) {
			//
            $sh_ip = explode('.', isset($rec['ip'])?$rec['ip']:$rec['real_ip']);
            $sh_ip = pack('C*', $sh_ip[0], $sh_ip[1], $sh_ip[2], $sh_ip[3]);
            $peers .= $sh_ip.pack('n*', (int)$rec['port']);
		} else {
			$peers[]= array('peer id' => $rec['peer_id'], 'ip' => isset($rec['ip'])?$rec['ip']:$rec['real_ip'], 'port' => $rec['port']);
		}
		if ($rec['left']) {
			$c_incomplete++;
		} else {
			$c_complete++;
		}
	}
	$answer = array('complete' => $c_complete, 'incomplete' => $c_incomplete, 'interval' => 60, 'min_interval' => 10, 'peers' => $peers);
	//print "<pre>".var_export($answer, true)."</pre>";
	print $enc->encode($answer);

	// Prepare LOGS
	// Try to get cache directory name. Return false if it's not possible
//	if (($dir = get_plugcache_dir('tracker'))&&(($fn = fopen($dir.'LOG', 'a')) !== FALSE)) {
//		fwrite($fn, $enc->encode($answer)."\n\n\n".var_export($params, true)."\n\n\n");
//		fclose($fn);
//	}
}

// Show tracker statistics
function plugin_tracker_statistics() {
	global $SUPRESS_TEMPLATE_SHOW, $SUPRESS_MAINBLOCK_SHOW;

	$SUPRESS_TEMPLATE_SHOW = 1;
	$SUPRESS_MAINBLOCK_SHOW = 1;
	//

}


// Update news TORRENT status
function plugin_tracker_updnews($newsID, $SQLnews) {
	global $config, $mysql;


	// Don't do anything if we have MAGNET link
	if ($SQLnews['tracker_magnetid'])
		return;


	// Now we need to check if there're attached .torrent files
	$fName = null;
	$fId = 0;
	// Yes. Let's scan attached files
	foreach ($mysql->select("select * from ".prefix."_files where (linked_ds = 1) and (linked_id = ".db_squote($newsID).")") as $frec) {
		// Check if this is .torrent file
		if (!preg_match('#\.torrent$#', $frec['name'], $null))
			continue;

		// We've got .torrent file
		$fName = ($frec['storage']?$config['attach_dir']:$config['files_dir']).$frec['folder'].'/'.$frec['name'];
		$fId = $frec['id'];
		break;
	}

	if ($fName == null) {
		$mysql->query("update ".prefix."_news set tracker_fileid = 0, tracker_infohash = '', tracker_seed = 0, tracker_leech= 0, tracker_lastupdate = 0 where id = ".intval($newsID));
		return;
	}

	// Let's decode .torrent file
	include_once "lib/decode.php";

	try {
		$bt = new bt_decode();
		$torrent = $bt->decodeFile($fName);

		$seed  = 0;
		$leech = 0;
		if (is_array($data = tracker_fetch_tstatus($SQLnews['tracker_infohash'], 'info'))) {
			foreach ($data as $rec)
				if ($rec['left']) { $leech++; } else { $seed++; }
		}

		$mysql->query("update ".prefix."_news set tracker_fileid = ".intval($fId).", tracker_infohash = ".db_squote($torrent['info_hash']).", tracker_seed =".intval($seed).", tracker_leech=".db_squote($leech).", tracker_lastupdate=".time()." where id = ".intval($newsID));
	} catch (Exception $e) {
		print "Catched exception: ".$e->getMessage()."<br/>\n";
	}
}

class TrackerNewsFilter extends NewsFilter {
	function addNewsForm(&$tvars) {
		global $tpl;

		if (!pluginGetVariable('tracker', 'smagnet')) {
			$tvars['vars']['plugin_tracker'] = '';
			return 1;
		}

		$tpath = locatePluginTemplates(array('news.add'), 'tracker', extra_get_param('tracker', 'localsource'));
		$tpl -> template('news.add', $tpath['news.add']);
		$tpl -> vars('news.add', array ( 'vars' => array ()));
		$tvars['plugin']['tracker'] = $tpl -> show('news.add');

		return 1;
	}

	function addNews(&$tvars, &$SQL) {
		global $mysql;

		if (pluginGetVariable('tracker', 'smagnet') && $_POST['tracker_magnet']) {
			// Create a record in `tracker_magnets` table
			$mysql->query("insert into ".prefix."_tracker_magnets (magnet, infohash) values (".db_squote($_POST['tracker_magnet']).", '')");
			$SQL['tracker_magnetid'] = $mysql->lastid();

			$md = magnetDecodeLink($_POST['tracker_magnet']);
			if (isset($md['torrent.infohash']))
				$SQLnew['tracker_infohash'] = $md['torrent.infohash'];

		}
		return 1;
	}

	function addNewsNotify(&$tvars, $SQL, $newsid){
		if (pluginGetVariable('tracker', 'storrent'))
			plugin_tracker_updnews($newsid, $SQL);
	}

	function editNewsForm($newsID, $SQLold, &$tvars) {
		global $tpl,$mysql;

		if (!pluginGetVariable('tracker', 'smagnet')) {
			$tvars['vars']['plugin_tracker'] = '';
			return 1;
		}

		$tpath = locatePluginTemplates(array('news.edit'), 'tracker', extra_get_param('tracker', 'localsource'));

		// Check if we have joined magnet link
		$magnetLink = '';
		if ($SQLold['tracker_magnetid']) {
			$magnetLink = $mysql->result("select magnet from ".prefix."_tracker_magnets where id = ".db_squote($SQLold['tracker_magnetid']));
		}

		$tpl -> template('news.edit', $tpath['news.edit']);
		$tpl -> vars('news.edit', array ( 'vars' => array ( 'tracker_magnet' => secure_html($magnetLink))));
		$tvars['plugin']['tracker'] = $tpl -> show('news.edit');

		return 1;
	}

	function editNews($newsID, $SQLold, &$SQLnew, &$tvars) {
		global $mysql;

		// First - clear infohash in case of any changes. It will be inited again in this function
		$SQLnew['tracker_infohash'] = '';

		// Don't do anything else if MAGNET support is turned off (don't change existed data)
		if (!pluginGetVariable('tracker', 'smagnet'))
			return 1;


		$magnet_infohash = '';
		if ($_POST['tracker_magnet'] != '') {
			$md = magnetDecodeLink($_POST['tracker_magnet']);
			if (isset($md['torrent.infohash']))
				$magnet_infohash = $md['torrent.infohash'];
		}

		// Decide what to do with magnet link
		// Delete old link
		if (($_POST['tracker_magnet'] == '')&&($SQLold['tracker_magnetid'])) {
			$SQLnew['tracker_magnetid'] = 0;
			$SQLnew['tracker_infohash'] = $magnet_infohash;
			$mysql->query("delete from ".prefix."_tracker_magnets where id = ".db_squote($SQLold['tracker_magnetid']));
		}

		// Add new link
		if (($_POST['tracker_magnet'] != '')&&(!$SQLold['tracker_magnetid'])) {
			// Add new link
			$mysql->query("insert into ".prefix."_tracker_magnets (magnet, infohash) values (".db_squote($_POST['tracker_magnet']).", ".db_squote($magnet_infohash).")");
			$SQLnew['tracker_magnetid'] = $mysql->lastid();
			$SQLnew['tracker_infohash'] = $magnet_infohash;
		}

		// Update existed link
		if (($_POST['tracker_magnet'] != '')&&($SQLold['tracker_magnetid'])) {
			// Add new link
			$mysql->query("update ".prefix."_tracker_magnets set magnet = ".db_squote($_POST['tracker_magnet']).", infohash = ".db_squote($magnet_infohash)." where id = ".db_squote($SQLold['tracker_magnetid']));
			$SQLnew['tracker_infohash'] = $magnet_infohash;
		}
		return 1;
	}

	function editNewsNotify($newsID, $SQLnews, &$SQLnew, &$tvars){
		$xSQL = array_merge($SQLnews, $SQLnew);

		if (pluginGetVariable('tracker', 'storrent'))
			plugin_tracker_updnews($newsID, $xSQL);
	}

	function showNews($newsID, $SQLnews, &$tvars, $mode) {
		global $tpl, $config, $mysql, $lang;

		$tvars['vars']['plugin_tracker'] = '';

		// Check if INFOHASH field is filled for this news & TORRENT tracker is allowed
		if (!$SQLnews['tracker_infohash'])
			return;


		// Determine paths for all template files
		$tpath = locatePluginTemplates(array('news.full'), 'tracker', extra_get_param('tracker', 'localsource'));
		$tdata = array();

		// Check if we have MAGNET link in this news
		$haveMagnet = false;
		if (pluginGetVariable('tracker', 'smagnet') && $SQLnews['tracker_magnetid']) {
			if ($magnetRow = $mysql->record("select * from ".prefix."_tracker_magnets where id = ".db_squote($SQLnews['tracker_magnetid']))) {
				$haveMagnet = true;
				$tdata['regx']['#\[magnet\](.+?)\[\/magnet\]#is'] = '$1';
				$tdata['vars']['magnet'] = $magnetRow['magnet'];
			}
		}

		if (!$haveMagnet)
			$tdata['regx']['#\[magnet\](.+?)\[\/magnet\]#is'] = '';

		// Check if we have TORRENT file attached
		$haveTorrent = false;
		if (pluginGetVariable('tracker', 'storrent') && $SQLnews['tracker_fileid']) {
			if ($torrentRow = $mysql->record("select * from ".prefix."_files where id = ".db_squote($SQLnews['tracker_fileid']))) {
				$haveTorrent = true;
				$tdata['regx']['#\[torrent\](.+?)\[\/torrent\]#is'] = '$1';
				$tdata['vars']['torrent'] = $config[($torrentRow['storage']?'attach':'files').'_url'].'/'.$torrentRow['folder'].'/'.$torrentRow['name'];
			}
		}

		if (!$haveTorrent)
			$tdata['regx']['#\[torrent\](.+?)\[\/torrent\]#is'] = '';


		// Check if we have torrent TRACKER enabled
		$haveTracker = false;
		if (pluginGetVariable('tracker', 'tracker')) {
			$haveTracker = true;
			$tdata['regx']['#\[tracker\](.+?)\[\/tracker\]#is'] = '$1';

			// INFO_HASH variable is set and filled in this news
			$seed  = 0;
			$leech = 0;

			if (is_array($data = tracker_fetch_tstatus($SQLnews['tracker_infohash'], 'info'))) {
				$seed  = 0;
				$leech = 0;
				foreach ($data as $rec)
					if ($rec['left']) { $leech++; } else { $seed++; }
			}

			$tdata['vars']['cnt_seeder'] = $seed;
			$tdata['vars']['cnt_leecher'] = $leech;
		}

		if (!$haveTracker)
			$tdata['regx']['#\[tracker\](.+?)\[\/tracker\]#is'] = '';


		// Generate info block if ANYthing is enabled
		if ($haveTorrent || $haveMagnet || $haveTracker) {
			$tpl -> template('news.full', $tpath['news.full']);
			$tpl -> vars('news.full', $tdata);
			$tvars['vars']['plugin_tracker'] = $tpl->show('news.full');
		}
	}
}

include_once "lib/basic.php";
register_plugin_page('tracker','announce','plugin_tracker_announce');
register_filter('news','tracker', new TrackerNewsFilter);

loadPluginLang('tracker', 'main', '', '', ':');
