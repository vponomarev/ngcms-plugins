<?

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

//
// Show current chat state
//
function jchat_show($start){
	global $userROW, $mysql, $tpl;

	// Check permissions [ guests do not see chat ]
	if (!extra_get_param('jchat', access) && !is_array($userROW))
		return false;

	// Get limit
	$limit = intval(extra_get_param('jchat', 'history'));
	if (($limit < 1)||($limit > 500))
		$limit = 30;

	$result	= '';
	$maxID	= 0;
	$data	= array();

	foreach (array_reverse($mysql->select("select id, postdate, author, author_id, text from ".prefix."_jchat ".(intval($start)?"where id >".intval($start):'')." order by id desc", 1)) as $row) {
		$maxID = max($maxID, $row['id']);
		$row['author'] = iconv('Windows-1251', 'UTF-8', $row['author']);
		$row['text'] = iconv('Windows-1251', 'UTF-8', $row['text']);

		// Make some conversions to INT type
		$row['id'] = intval($row['id']);

		$data []= $row;
	}

	// Prepare data bundle
	$bundle = array(array(), $data);
	// 1. Check if we need to reconfigure refresh rate

	$conf_refresh = intval(extra_get_param('jchat', 'refresh'));
	if (isset($_REQUEST['timer']) && ($conf_refresh >= 5) && (intval($_REQUEST['timer']) != $conf_refresh))
		$bundle[0] []= array('reload', $conf_refresh);

	$conf_maxidle = intval(extra_get_param('jchat', 'maxidle'));
	if (isset($_REQUEST['idle']) && ($conf_maxidle > 0) && (intval($_REQUEST['idle']) > $conf_maxidle))
		$bundle[0] []= array('stop', $conf_refresh);


	return $bundle;


}

function plugin_jchat_show(){
	global $template, $SUPRESS_TEMPLATE_SHOW;

	$SUPRESS_TEMPLATE_SHOW = 1;
	$template['vars']['mainblock'] = json_encode(jchat_show(intval($_REQUEST['start'])));
}


function plugin_jchat_index() {
	global $template, $tpl, $SUPRESS_TEMPLATE_SHOW, $userROW;

	// Check permissions [ guests do not see chat ]
	if (!extra_get_param('jchat', access) && !is_array($userROW)) {
		$template['vars']['plugin_jchat'] = '';
		return;
	}

	// Determine paths for all template files
	$tpath = locatePluginTemplates(array('jchat'), 'jchat', extra_get_param('jchat', 'localsource'));
	$tvars = array();
	$tvars['vars']['data'] = json_encode(jchat_show(intval($_REQUEST['start'])));

	$history = intval(extra_get_param('jchat', 'history'));
	if (($history < 1)||($history > 500)) $history = 30;

	$refresh = intval(extra_get_param('jchat', 'refresh'));
	if (($refresh < 5)||($refresh > 1800)) $refresh = 120;

	$maxlen = intval(extra_get_param('jchat', 'maxlen'));
	if (($maxlen < 1)||($refresh > 5000)) $maxlen = 500;

	$tvars['vars']['history'] = $history;
	$tvars['vars']['refresh'] = $refresh;

	$tvars['vars']['maxlen'] = $maxlen;

	$tvars['vars']['link_add'] = generateLink('core', 'plugin', array('plugin' => 'jchat', 'handler' => 'add'), array());
	$tvars['vars']['link_show'] = generateLink('core', 'plugin', array('plugin' => 'jchat', 'handler' => 'show'), array());
	$tvars['regx']['#\[not-logged\](.*?)\[\/not-logged\]#is'] = is_array($userROW)?'':'$1';
	$tvars['regx']['#\[post-enabled\](.*?)\[\/post-enabled\]#is'] = (!is_array($userROW) && (extra_get_param('jchat', 'access') < 2))?'':'$1';


	$tpl -> template('jchat', $tpath['jchat']);
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
			return;
		}
		$SQL['author'] = secure_html(substr(trim(convert($_REQUEST['name'])),0,30));
		$SQL['author_id'] = 0;
	}

	if (!trim($_REQUEST['text'])) {
			print json_encode(array('status' => 0, 'error' => 'No text specified'));
			return;
	}

	// If we're guest - check if we can make posts
	if (!is_array($userROW) && (extra_get_param('jchat', 'access') < 2)) {
			print json_encode(array('status' => 0, 'error' => 'Guests are not allowed to post'));
			return;
	}

	// Check for rate limit
	$rate_limit = intval(extra_get_param('jchat', 'rate_limit'));
	if ($rate_limit < 0) $rate_limit = 0;

	if (is_array($mysql->record("select id from ".prefix."_jchat where (ip = ".db_squote($ip).") and (postdate + ".$rate_limit.') > '.time()))) {
			print json_encode(array('status' => 0, 'error' => 'Rate limit. Only 1 message per '.$rate_limit.' sec is allowed'));
			return;
	}

	$maxlen = intval(extra_get_param('jchat', 'maxlen'));
	if (($maxlen < 1)||($maxlen > 5000)) $maxlen = 500;

	$maxwlen = intval(extra_get_param('jchat', 'maxwlen'));
	if (($maxwlen < 1)||($maxlen > 5000)) $maxwlen = 500;

	//
	$postText = secure_html(convert(trim($_REQUEST['text'])));
	$ptb = array();

	foreach (preg_split('#(\s|^)(http\:\/\/[A-Za-z\-\.0-9]+\/\S+)(\s|$)#', $postText, -1, PREG_SPLIT_DELIM_CAPTURE) as $cx) {
		if (preg_match('#http\:\/\/[A-Za-z\-\.0-9]+\/\S+#', $cx, $m)) {
			// LINK
			$cx = '<a href="'.htmlspecialchars($cx).'">'.((strlen($cx)>$maxwlen)?(substr($cx, 0, $maxwlen-2).'..'):$cx).'</a>';
		} else {
			$cx = preg_replace('/(\S{'.$maxwlen.'})(?!\s)/', '$1 ', $cx);
		}
		$ptb[] = $cx;
	}
	$SQL['text'] = join('', $ptb);

	//if (strlen($SQL['text']) > $maxlen)
	//	$SQL['text'] = substr($SQL['text'], 0, $maxlen).'...';

	//$SQL['text'] = preg_replace('/(\S{'.$maxwlen.'})(?!\s)/', '$1 ', $SQL['text']);


	$SQL['chatid'] = 1;
	$SQL['ip']     = $ip;
	$SQL['postdate'] = time();

	// Create comment
	$vnames = array(); $vparams = array();
	foreach ($SQL as $k => $v) { $vnames[]  = $k; $vparams[] = db_squote($v); }

	$mysql->query("insert into ".prefix."_jchat (".implode(",",$vnames).") values (".implode(",",$vparams).")");
	print json_encode(array('status' => 1, 'bundle' => jchat_show(intval($_REQUEST['start']))));
}


register_plugin_page('jchat','add','plugin_jchat_add',0);
register_plugin_page('jchat','show','plugin_jchat_show',0);
add_act('index', 'plugin_jchat_index');