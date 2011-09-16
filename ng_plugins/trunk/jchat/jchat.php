<?

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Show current chat state
//
function jchat_show($lastEventID, $maxLoadedID, $commands = array()){
	global $userROW, $mysql, $tpl;

	// Check permissions [ guests do not see chat ]
	if (!pluginGetVariable('jchat', 'access') && !is_array($userROW))
		return false;

	// Get limit
	$limit = intval(pluginGetVariable('jchat', 'history'));
	if (($limit < 1)||($limit > 500))
		$limit = 30;

	$result	= '';
	$maxID	= 0;
	$data	= array();

	// Prepare data bundle
	$bundle = array(array(), array());

	// Check if chat work in WINDOW mode
	$winMode = intval($_REQUEST['win']);

	$conf_maxidle = intval(pluginGetVariable('jchat', ($winMode?'win.':'').'maxidle'));
	if (isset($_REQUEST['idle']) && ($conf_maxidle > 0) && (intval($_REQUEST['idle']) > $conf_maxidle)) {
		$bundle[0] []= array('stop');
	}

	// Check if we have new events
	$newEvents = $mysql->record("select max(id) as id, max(type) as type from ".prefix."_jchat_events where id > ".intval($lastEventID));

	// Check if we have to update lastEventID
	if ($newEvents['id'] > $lastEventID) {
		$bundle[0] []= array('setLastEvent', intval($newEvents['id']));
	} else {
		// No new events
		return $bundle;
	}

	// Possible actions
	// * 3 = RELOAD
	if ($newEvents['type'] == 3) {
		$bundle[0] []= array('reload');
		return $bundle;
	}

	// * 2 = There are deleted messages, return the whole list
	if ($newEvents['type'] == 2) {
		$bundle[0] []= array('clear');
		$query = "select id, postdate, author, author_id, text from ".prefix."_jchat order by id desc limit ".$limit;
	}

	// * 1 = There are new messages
	if ($newEvents['type'] == 1) {
		$query = "select id, postdate, author, author_id, text from ".prefix."_jchat where id >".intval($maxLoadedID)." order by id desc limit ".$limit;
	}

	// * NO NEW EVENTS - do not fetch data
	if (intval($newEvents['type']) < 1) {
		return $bundle;
	}

	foreach (array_reverse($mysql->select($query, 1)) as $row) {
		$maxID = max($maxID, $row['id']);
		$row['author'] = iconv('Windows-1251', 'UTF-8', $row['author']);
		$row['text'] = iconv('Windows-1251', 'UTF-8', preg_replace('#^\@(.+?)\:#','<i>$1</i>:',$row['text']));
		$row['time'] = strftime('%H:%M', $row['postdate']);
		$row['datetime'] = strftime('%d.%m.%Y %H:%M', $row['postdate']);
		if (getPluginStatusActive('uprofile')) {
			$row['profile_link'] = generatePluginLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id']));
		}

		// Make some conversions to INT type
		$row['id'] = intval($row['id']);

		$data []= $row;
	}

	// Attach messages to bundle
	$bundle[1] = $data;

	// 1. Check if we need to reconfigure refresh rate
	$conf_refresh = intval(pluginGetVariable('jchat', 'refresh'));
	if (($conf_refresh < 5)||($conf_refresh > 1800))
		$conf_refresh = 120;

	//if (isset($_REQUEST['timer']) && ($conf_refresh >= 5) && (intval($_REQUEST['timer']) != $conf_refresh))
	//	$bundle[0] []= array('settimer', $conf_refresh);

	// Add extra commands (if passed)
	if (is_array($commands) && count($commands)) {
		foreach ($commands as $cmd) {
			$bundle[0] []= $cmd;
		}
	}

	return $bundle;
}

function plugin_jchat_show(){
	global $template, $SUPRESS_TEMPLATE_SHOW, $mysql;

	$SUPRESS_TEMPLATE_SHOW = 1;
	//	$template['vars']['mainblock'] = json_encode(jchat_show(intval($_REQUEST['lastEvent']), intval($_REQUEST['start'])));
	print json_encode(jchat_show(intval($_REQUEST['lastEvent']), intval($_REQUEST['start'])));
	exit;
}

// Index screen for side panel
function plugin_jchat_index() {
	global $template, $tpl, $SUPRESS_TEMPLATE_SHOW, $userROW, $CurrentHandler;

	loadPluginLang('jchat', 'main', '', '', ':');

	// We shouldn't show side jchat panel if user currently visited separate jchat window
	if ($CurrentHandler['pluginName'] == 'jchat') {
		$template['vars']['plugin_jchat'] = '';
		return;
	}

	// Check permissions [ guests do not see chat ]
	if (!pluginGetVariable('jchat', 'access') && !is_array($userROW)) {
		$template['vars']['plugin_jchat'] = '';
		return;
	}

	// Determine paths for all template files
	$tpath = locatePluginTemplates(array('jchat'), 'jchat', pluginGetVariable('jchat', 'localsource'));
	$tvars = array();
	$start = isset($_REQUEST['start'])?intval($_REQUEST['start']):0;
	$tvars['vars']['data'] = json_encode(jchat_show(0,0));

	$history = intval(pluginGetVariable('jchat', 'history'));
	if (($history < 1)||($history > 500)) $history = 30;

	$refresh = intval(pluginGetVariable('jchat', 'refresh'));
	if (($refresh < 5)||($refresh > 1800)) $refresh = 120;

	$maxlen = intval(pluginGetVariable('jchat', 'maxlen'));
	if (($maxlen < 1)||($refresh > 5000)) $maxlen = 500;

	$tvars['vars']['history'] = $history;
	$tvars['vars']['refresh'] = $refresh;

	$tvars['vars']['maxlen'] = $maxlen;
	$tvars['vars']['msgOrder'] = intval(pluginGetVariable('jchat', 'order'));

	$tvars['vars']['link_add'] = generateLink('core', 'plugin', array('plugin' => 'jchat', 'handler' => 'add'), array());
	$tvars['vars']['link_del'] = generateLink('core', 'plugin', array('plugin' => 'jchat', 'handler' => 'del'), array());
	$tvars['vars']['link_show'] = generateLink('core', 'plugin', array('plugin' => 'jchat', 'handler' => 'show'), array());
	$tvars['regx']['#\[is\.admin\](.*?)\[\/is\.admin\]#is'] = (is_array($userROW) && ($userROW['status'] == 1))?'$1':'';
	$tvars['regx']['#\[not-logged\](.*?)\[\/not-logged\]#is'] = is_array($userROW)?'':'$1';
	$tvars['regx']['#\[post-enabled\](.*?)\[\/post-enabled\]#is'] = (!is_array($userROW) && (pluginGetVariable('jchat', 'access') < 2))?'':'$1';

	$tvars['regx']['#\[selfwin\](.*?)\[\/selfwin\]#is'] = pluginGetVariable('jchat', 'enable_win') ? '$1' : '';
	$tvars['vars']['link_selfwin'] = generatePluginLink('jchat', null);

	$tpl -> template('jchat', $tpath['jchat'], '', array('includeAllowed' => true));
	$tpl -> vars('jchat', $tvars);
	//print $tpl -> show('jchat');
	$template['vars']['plugin_jchat'] = $tpl -> show('jchat');
}

function plugin_jchat_add() {
	global $userROW, $template, $mysql, $SUPRESS_TEMPLATE_SHOW, $ip;

	$SUPRESS_TEMPLATE_SHOW = 1;

	//
	if (is_array($userROW)) {
		$SQL['author'] = $userROW['name'];
		$SQL['author_id'] = $userROW['id'];
	} else {
		if (!trim($_REQUEST['name'])) {
			print json_encode(array('status' => 0, 'error' => 'No name specified'));
			exit;
		}
		$SQL['author'] = secure_html(substr(trim(convert($_REQUEST['name'])),0,30));
		$SQL['author_id'] = 0;
	}

	if (!trim($_REQUEST['text'])) {
			print json_encode(array('status' => 0, 'error' => 'No text specified'));
			exit;
	}

	// If we're guest - check if we can make posts
	if (!is_array($userROW) && (pluginGetVariable('jchat', 'access') < 2)) {
			print json_encode(array('status' => 0, 'error' => 'Guests are not allowed to post'));
			exit;
	}

	// Check for rate limit
	$rate_limit = intval(pluginGetVariable('jchat', 'rate_limit'));
	if ($rate_limit < 0) $rate_limit = 0;

	if (is_array($mysql->record("select id from ".prefix."_jchat where (ip = ".db_squote($ip).") and (postdate + ".$rate_limit.') > '.time()))) {
			print json_encode(array('status' => 0, 'error' => 'Rate limit. Only 1 message per '.$rate_limit.' sec is allowed'));
			exit;
	}

	$maxlen = intval(pluginGetVariable('jchat', 'maxlen'));
	if (($maxlen < 1)||($maxlen > 5000)) $maxlen = 500;

	$maxwlen = intval(pluginGetVariable('jchat', 'maxwlen'));
	if (($maxwlen < 1)||($maxlen > 5000)) $maxwlen = 500;

	// Load text & strip it to maxlen
	$postText = substr(secure_html(convert(trim($_REQUEST['text']))), 0, $maxlen);

	$ptb = array();

	foreach (preg_split('#(\s|^)(http\:\/\/[A-Za-z\-\.0-9]+\/\S*)(\s|$)#', $postText, -1, PREG_SPLIT_DELIM_CAPTURE) as $cx) {
		if (preg_match('#http\:\/\/[A-Za-z\-\.0-9]+\/\S*#', $cx, $m)) {
			// LINK
			$cx = '<a href="'.htmlspecialchars($cx).'">'.((strlen($cx)>$maxwlen)?(substr($cx, 0, $maxwlen-2).'..'):$cx).'</a>';
		} else {
			$cx = preg_replace('/(\S{'.$maxwlen.'})(?!\s)/', '$1 ', $cx);
		}
		$ptb[] = $cx;
	}
	$SQL['text'] = join('', $ptb);

	$SQL['chatid'] = 1;
	$SQL['ip']     = $ip;
	$SQL['postdate'] = time();

	// Create comment
	$vnames = array(); $vparams = array();
	foreach ($SQL as $k => $v) { $vnames[]  = $k; $vparams[] = db_squote($v); }

	// Add new message to chat
	$mysql->query("insert into ".prefix."_jchat (".implode(",",$vnames).") values (".implode(",",$vparams).")");

	// Update LastEventNotification
	$mysql->query("insert into ".prefix."_jchat_events (chatid, postdate, type) values (".$SQL['chatid'].", ".db_squote($SQL['postdate']).", 1)");

	$lid = $mysql->result("select LAST_INSERT_ID()");
	$mysql->query("delete from ".prefix."_jchat_events where type=1 and id <> ".db_squote($lid));

	print json_encode(array('status' => 1, 'bundle' => jchat_show(intval($_REQUEST['lastEvent']), intval($_REQUEST['start']))));
	exit;
}

function plugin_jchat_del() {
	global $userROW, $template, $mysql, $SUPRESS_TEMPLATE_SHOW, $ip;

	$SUPRESS_TEMPLATE_SHOW = 1;

	// Only ADMINS can delete items from chat
	if (!is_array($userROW) || ($userROW['status'] > 1)) {
		print json_encode(array('status' => 0, 'error' => 'Permission denied'));
		exit;
	}

	// Try to load chat message
	$id = intval($_REQUEST['id']);

	if (!($crow = $mysql->record("select * from ".prefix."_jchat where id = ".db_squote($id)))) {
		print json_encode(array('status' => 0, 'error' => 'Item not found (ID: '.$id.')'));
		exit;
	}


	// Delete item
	$mysql->query("delete from ".prefix."_jchat where id = ".$id);

	// Update LastEventNotification
	$mysql->query("insert into ".prefix."_jchat_events (chatid, postdate, type) values (1, unix_timestamp(now()), 2)");

	$lid = $mysql->result("select LAST_INSERT_ID()");
	$mysql->query("delete from ".prefix."_jchat_events where type=2 and id <> ".db_squote($lid));


	// Return updated list of items from chat
	print json_encode(array('status' => 1, 'bundle' => jchat_show(intval($_REQUEST['lastEvent']), intval($_REQUEST['start']))));
	exit;
}


function plugin_jchat_win() {
	global $template, $tpl, $SUPRESS_TEMPLATE_SHOW, $userROW, $lang;

	loadPluginLang('jchat', 'main', '', '', ':');

	if (pluginGetVariable('jchat', 'win_mode'))
		$SUPRESS_TEMPLATE_SHOW = 1;

	// Check permissions [ guests receive an error ]
	if (!pluginGetVariable('jchat', access) && !is_array($userROW)) {
		if (pluginGetVariable('jchat', 'win_mode')) {
			$template['vars']['mainblock'] = $lang['jchat:win.regonly'];
		} else {
			msg(array("type" => "error", "text" => $lang['jchat:regonly']));
		}
		return;
	}

	// Determine paths for all template files
	$tpath = locatePluginTemplates(array('jchat.main', 'jchat.self'), 'jchat', pluginGetVariable('jchat', 'localsource'));

	$tvars = array();
	$tvars['vars']['data'] = json_encode(jchat_show(0,0, array(array('setWinMode', 1))));

	$history = intval(pluginGetVariable('jchat', 'win_history'));
	if (($history < 1)||($history > 500)) $history = 30;

	$refresh = intval(pluginGetVariable('jchat', 'win_refresh'));
	if (($refresh < 5)||($refresh > 1800)) $refresh = 120;

	$maxlen = intval(pluginGetVariable('jchat', 'maxlen'));
	if (($maxlen < 1)||($refresh > 5000)) $maxlen = 500;

	$tvars['vars']['history'] = $history;
	$tvars['vars']['refresh'] = $refresh;

	$tvars['vars']['maxlen'] = $maxlen;
	$tvars['vars']['msgOrder'] = intval(pluginGetVariable('jchat', 'win_order'));

	$tvars['vars']['link_add'] = generateLink('core', 'plugin', array('plugin' => 'jchat', 'handler' => 'add'), array());
	$tvars['vars']['link_show'] = generateLink('core', 'plugin', array('plugin' => 'jchat', 'handler' => 'show'), array());
	$tvars['vars']['link_del'] = generateLink('core', 'plugin', array('plugin' => 'jchat', 'handler' => 'del'), array());
	$tvars['regx']['#\[is\.admin\](.*?)\[\/is\.admin\]#is'] = (is_array($userROW) && ($userROW['status'] == 1))?'$1':'';
	$tvars['regx']['#\[not-logged\](.*?)\[\/not-logged\]#is'] = is_array($userROW)?'':'$1';
	$tvars['regx']['#\[post-enabled\](.*?)\[\/post-enabled\]#is'] = (!is_array($userROW) && (pluginGetVariable('jchat', 'access') < 2))?'':'$1';


	$templateName = intval(pluginGetVariable('jchat', 'win_mode'))?'jchat.self':'jchat.main';

	$tpl -> template($templateName, $tpath[$templateName], '', array('includeAllowed' => true));
	$tpl -> vars($templateName, $tvars);
	$template['vars']['mainblock'] = $tpl -> show($templateName);
}

// Register handler if self window is enabled
if (pluginGetVariable('jchat', 'enable_win'))
	register_plugin_page('jchat', '', 'plugin_jchat_win', 0);

// Register main page processor if panel windows is enabled
if (pluginGetVariable('jchat', 'enable_panel')) {
	add_act('index', 'plugin_jchat_index');
} else {
	global $template;
	$template['vars']['plugin_jchat'] = '';
}

// Register processing applications if SELF or PANEL modes are enabled
if (pluginGetVariable('jchat', 'enable_win') || pluginGetVariable('jchat', 'enable_panel')) {
	register_plugin_page('jchat','add','plugin_jchat_add',0);
	register_plugin_page('jchat','del','plugin_jchat_del',0);
	register_plugin_page('jchat','show','plugin_jchat_show',0);
}