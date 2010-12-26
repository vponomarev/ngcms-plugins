<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

add_act('index', 'plugin_voting');
register_plugin_page('voting','','plugin_voting_screen',0);

function plugin_voting() {
	global $mysql, $tpl, $template, $REQUEST_URI;

	$voteid = intval(pluginGetVariable('voting','active'));
	$rand = pluginGetVariable('voting','rotate');
	$voted = isset($_COOKIE['ngcms_voting'])?explode(",",$_COOKIE['ngcms_voting'].''):array();
	$skin = pluginGetVariable('voting','skin');
	if ((!is_dir(extras_dir.'/voting/tpl/skins/'.$skin))||(!$skin)) { $skin = 'basic'; }

	$template['vars']['voting'] = plugin_showvote($skin, 4, $voteid, $rand, $voted);
}

//
// Show selected vote with chosen skin/prefix
// Params:
//  1. Skin
//  2. Mode:
//		0 - [list] show list [auto],
//		1 - [list] show one [auto]
//		2 - [list] force show/edit one
//		3 - [list] force show/show one
// 		4 - [one] show one [auto],
//		5 - [one] force show/edit one,
//		6 - [one] force show/show one
//	3. voteid - vote id (in show one mode)
//  4. rand - rand flag (in show one mode)
//	5. votedList - list of voted (in show list mode)
function plugin_showvote($tpl_skin, $mode, $voteid = 0, $rand = 0, $votedList = array()) {
	global $tpl, $mysql, $username, $userROW, $ip, $REQUEST_URI, $TemplateCache, $SYSTEM_FLAGS;

	$result = '';
	$post_url = generateLink('core', 'plugin', array('plugin' => 'voting'), array());

	$tpath = locatePluginTemplates(array('shls_vote', 'edls_vote', 'shls_vline', 'edls_vline', 'lshdr', 'sh_vote', 'ed_vote', 'sh_vline', 'ed_vline'), 'voting', pluginGetVariable('voting', 'localsource'), $tpl_skin);
	// Preload templates
	if ($mode<4) {
	 	$tpl->template('shls_vote',$tpath['shls_vote']);
	 	$tpl->template('edls_vote',$tpath['edls_vote']);
	 	$tpl->template('shls_vline',$tpath['shls_vline']);
	 	$tpl->template('edls_vline',$tpath['edls_vline']);
	 	$tpl->template('lshdr',$tpath['lshdr']);
	 	$tpl->vars('lshdr',array('vars' => array('home' => home, 'post_url' => $post_url)));
	 	$result = $tpl->show('lshdr');
	} else {
	 	$tpl->template('sh_vote',$tpath['sh_vote']);
	 	$tpl->template('ed_vote',$tpath['ed_vote']);
	 	$tpl->template('sh_vline',$tpath['sh_vline']);
	 	$tpl->template('ed_vline',$tpath['ed_vline']);
	}

	// Page number
	$page = isset($_GET['page'])?intval($_GET['page']):1;
	if ($page < 1) { $page = 1; }

	$vpp  = intval(pluginGetVariable('voting', 'vpp'));
	$pageCount	= 0;
	if ($vpp>0 && !$mode) {
		// Calculate real number of pages
		$pc = $mysql->record("select count(*) as cnt from ".prefix."_vote where active = 1");
		$pageCount = ceil($pc['cnt'] / $vpp);
	}
	if ($page > $pageCount) { $page = $pageCount; }

	if (!$mode) {
		$where = 'where active = 1 order by id desc'.(($vpp>0)?(' limit '.(($page-1)*$vpp).', '.$vpp):'');
	} else {
		if ($rand) {
			$where = 'where active = 1 order by rand() limit 1';
		} else if ($voteid) {
			$where = 'where id = '.db_squote($voteid);
		} else {
			$where = 'where active = 1 limit 1';
		}
	}

	// If we have voteid - show only this vote, else - show all active
	//print "QUERY: 'select * from ".prefix."_vote $where'<br>\n";
	$vCount = 0;
	foreach ($mysql->select("select * from ".prefix."_vote $where") as $row) {
		$vCount++;
		$votelines = '';

		$dup = 0;
		if ($secure = pluginGetVariable('voting','secure')) {
			$condition = (is_array($userROW))?"userid = ".$userROW['id']:"ip='$ip'";
			if ($mysql->record("select * from ".prefix."_votestat where voteid = ".$row['id']." and $condition limit 1")) {
				$dup = 1;
			}
		} else { $dup = array_key_exists($row['id'], $votedList); }

		$cnt = 0;
		//print ">Query: 'select * from ".prefix."_voteline where voteid = ".$row['id']." and active=1'<br>\n";
		$lrows = $mysql->select("select * from ".prefix."_voteline where voteid = ".$row['id']." and active=1");
		foreach ($lrows as $lrow) { $cnt += $lrow['cnt']; }
		if (!$cnt) { $cnt = 1; }

		// Choose template name for this operation
		switch ($mode) {
			case '0':
			case '1': $tpl_prefix = ($dup||$row['closed']||($row['regonly'] && !$username))?'shls':'edls'; break;
			case '2': $tpl_prefix = 'edls'; break;
			case '3': $tpl_prefix = 'shls'; break;
			case '4': $tpl_prefix = ($dup||$row['closed']||($row['regonly'] && !$username))?'sh':'ed'; break;
			case '5': $tpl_prefix = 'ed'; break;
			case '6': $tpl_prefix = 'sh'; break;
		}

		$num = 1;
		$tcount = 0;
		foreach ($lrows as $lrow) {
		        $tvars['vars'] = array (
		        	'id' => $lrow['id'],
		        	'name' => $lrow['name'],
		        	'num' => $num,
		        	'count' => $lrow['cnt'],
		        	'perc' => intval($lrow['cnt']*100/$cnt),
		        	'post_url' => $post_url,
		        	'tpl_dir' => admin_url.'/plugins/voting/tpl/skins/'.$tpl_skin);
			$tpl->vars($tpl_prefix.'_vline', $tvars);
			$votelines .= $tpl->show($tpl_prefix.'_vline');
			$num++;
			$tcount += $lrow['cnt'];
		}

		$tvars['vars'] = array (
			'votename' => $row['name'],
			'voteid' => $row['id'],
			'votelines' => $votelines,
			'votedescr' => $row['descr'],
			'REFERER' => isset($REQUEST_URI)?$REQUEST_URI:'',
			'home' => home,
			'vcount' => $tcount,
			'post_url' => $post_url,
			'tpl_dir' => admin_url.'/plugins/voting/tpl/skins/'.$tpl_skin);
		$tvars['regx']['#\[votedescr](.*?)\[/votedescr]#is'] = (strlen($row['descr']) > 0) ? '$1' : '';
		$tpl->vars($tpl_prefix.'_vote', $tvars);
		$result .= $tpl->show($tpl_prefix.'_vote');
	}

	// Add page navigation
	if ($pageCount > 0) {
		$paginationParams = array(
			'pluginName' => 'core',
			'pluginHandler' => 'plugin',
			'params' => array('plugin' => 'voting'),
			'xparams' => array(),
			'paginator' => array('page', 1, false)
		);

		templateLoadVariables(true);
		$navigations = $TemplateCache['site']['#variables']['navigation'];
		$result .= '<br/>'.generatePagination($page, 1, $pageCount, 10, $paginationParams, $navigations);
	}


	if (!$vCount) {
		$result = 'No votings found';
	}
	return $result;
}

//
//
function plugin_voting_screen() {
 global $mysql, $tpl, $template, $SUPRESS_TEMPLATE_SHOW, $lang, $userROW, $ip;

 @header('Content-type: text/html; charset="windows-1251"');
 $votedList = explode(",",$_COOKIE['ngcms_voting']);

 $skin = pluginGetVariable('voting','skin');
 if ((!is_dir(extras_dir.'/voting/tpl/skins/'.$skin))||(!$skin)) { $skin = 'basic'; }

 $is_ajax = (($_GET['style'] == 'ajax')||($_POST['style'] == 'ajax'))?1:0;
 if (($_REQUEST['mode'] == 'vote') && ($choice = intval($_REQUEST['choice']))) {
    // VOTE REQUEST
	if (($row = $mysql->record("select * from ".prefix."_voteline where id = $choice"))&&
	    ($vrow = $mysql->record("select * from ".prefix."_vote where id = ".$row['voteid']))) {
		// Line was found
		// Check for dupes
		$dup = 0;
		if ($secure = pluginGetVariable('voting','secure')) {
			$condition = (is_array($userROW))?"userid = ".$userROW['id']:"ip=".db_squote($ip);
			if ($mysql->record("select * from ".prefix."_votestat where voteid = ".$vrow['id']." and $condition limit 1")) {
				$dup = 1;
			}
		} else { $dup = array_key_exists($row['id'], $votedList); }

		if ($dup) {
			// Inform that vote is already accepted
			$template['vars']['mainblock'] = 'Vote was already accepted';
			if ($is_ajax) { $SUPRESS_TEMPLATE_SHOW = 1; }
		} else {
			$mysql->query("update ".prefix."_voteline set cnt=cnt+1 where id = ".$row['id']);
			// DONE. Vote accepted

			if ($secure) {
				$query = "insert into ".prefix."_votestat (userid, voteid, voteline, ip, dt) values (".(is_array($userROW)?$userROW['id']:'0').",".$vrow['id'].",".$row['id'].", '$ip', now() )";
				$mysql->query($query);
			} else {
				if (!array_key_exists($vrow['id'],$votedList)) {
					array_push($votedList,$vrow['id']);
					@setcookie('ngcms_voting', implode(",",$votedList), time() + 3600 * 24 * 365, '/');
				}
			}
			$template['vars']['mainblock'] = plugin_showvote($skin,$_REQUEST['list']?3:6,$vrow['id']);
			if ($is_ajax) { $SUPRESS_TEMPLATE_SHOW = 1; }
		}
	} else {
		// No such vote line
		$template['vars']['mainblock'] = 'No such voteline';
		if ($is_ajax) { $SUPRESS_TEMPLATE_SHOW = 1; }
	}
 } else if (($_REQUEST['mode'] == 'show') && ($voteid = intval($_REQUEST['voteid']))) {
	$template['vars']['mainblock'] = plugin_showvote($skin, 6, $voteid);
	if ($is_ajax) { $SUPRESS_TEMPLATE_SHOW = 1; }
 } else {
 	// SHOW REQUEST
	$template['vars']['mainblock'] = plugin_showvote($skin, 0, 0, 0, $votedList);
 }
}