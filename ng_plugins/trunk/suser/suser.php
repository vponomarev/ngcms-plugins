<?php

if (!defined('NGCMS')) die ('HAL');

register_plugin_page('suser','','suser_show');

LoadPluginLang('suser', 'main', '', '', '#');
add_act('index_post', 'suser_header_show');

function suser_header_show($params)
{
global $CurrentHandler, $SYSTEM_FLAGS, $template, $lang;
	
if(checkLinkAvailable('suser', '')){
  if($CurrentHandler['handlerParams']['value']['pluginName'] == 'core')
   return error404();
}
	
	/* print '<pre>';
	print_r ($CurrentHandler);
	print '</pre>'; */
	
	/* print '<pre>';
	print_r ($SYSTEM_FLAGS);
	print '</pre>';  */
	
	$title_plg = pluginGetVariable('suser', 'title_plg');
	$SYSTEM_FLAGS['info']['title']['group'] = isset($title_plg)?$title_plg:$SYSTEM_FLAGS['info']['title']['group'];
	
	$description = pluginGetVariable('suser', 'description');
	$SYSTEM_FLAGS['meta']['description'] = isset($description)?$description:$SYSTEM_FLAGS['meta']['description'];
	
	$keywords = pluginGetVariable('suser', 'keywords');
	$SYSTEM_FLAGS['meta']['keywords'] = isset($keywords)?$keywords:$SYSTEM_FLAGS['meta']['keywords'];
	
	if(empty($_REQUEST['page']))
	{
		$page = $CurrentHandler['params']['page'];
	} else {
		$page = $_REQUEST['page'];
	}
	
	$pageNo = isset($page)?str_replace('%count%',intval($page), '/ �������� %count%'):'';
	//var_dump($CurrentHandler);
	switch ($CurrentHandler['handlerName'])
	{
	
		case '':
			$titles = str_replace(
				array ('%name_site%', '%group%', '%num%'),
				array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group'], $pageNo),
				$lang['suser']['titles']);
			break;
	}

	
	$template['vars']['titles'] = trim($titles);
}

function suser_show($params)
{
global $catz, $twig, $catmap, $mysql, $config, $userROW, $tpl, $parse, $template, $lang, $PFILTERS, $SYSTEM_FLAGS, $CurrentHandler;

	$tpath = locatePluginTemplates(array('suser','userlist'), 'suser', pluginGetVariable('suser', 'localsource'));
	$xt = $twig->loadTemplate($tpath['userlist'].'userlist.tpl');
	
	$pageNo = isset($_REQUEST['page'])?intval($_REQUEST['page']):0;
	
	$username = isset($_REQUEST['username'])?$_REQUEST['username']:'';
	$show_group = isset($_REQUEST['show_group'])?$_REQUEST['show_group']:'';
	$sort_by = isset($_REQUEST['sort_by'])?$_REQUEST['sort_by']:'';
	$sort_dir = isset($_REQUEST['sort_dir'])?$_REQUEST['sort_dir']:'';

		switch($show_group){
			case 1: $where[] = 'status = \'1\''; break;
			case 2: $where[] = 'status = \'2\''; break;
			case 3: $where[] = 'status = \'3\''; break;
			case 4: $where[] = 'status = \'4\''; break;
		}
		
		if(isset($username) && $username)
			$where[] = 'name LIKE '.securemysql_suser('%'.$username.'%').'';
		
		if(is_array($where) && $where)
			$where = 'WHERE '.implode(' AND ', $where);

	
	if(isset($_REQUEST['reset'])){
	$username = '';
	$show_group = '';
	$sort_by = '';
	$sort_dir = '';
	
	redirect_link_suser(generateLink('suser'));
	
	}
	
	switch($sort_dir){
		case 'ASC': $sort_d = 'ASC'; break;
		case 'DESC': $sort_d = 'DESC';  break;
		default: $sort_d = 'DESC';
	}
	
	switch($sort_by){
		case 'username': $sort_b = 'name'; break;
		case 'registered': $sort_b = 'reg';  break;
		case 'num_posts': $sort_b = 'news';  break;
		case 'num_comments': $sort_b = 'com';  break;
		default: $sort_b = 'id';
	}
	
	$limitCount = intval(pluginGetVariable('suser', 'user_per_page'));
	
	if (($limitCount < 2)||($limitCount > 2000)) $limitCount = 1;
	
	$count = $mysql->result('SELECT COUNT(*) FROM `'.prefix.'_users` '.$where .'');
	
	$countPages = ceil($count / $limitCount);
	if($countPages < $pageNo)
		return $output = information_suser('����������� �� ����������', $title = '����������');
	
	if ($pageNo < 1) $pageNo = 1;
	if (!isset($limitStart)) $limitStart = ($pageNo - 1)* $limitCount;
	
	if ($countPages > 1 && $countPages >= $pageNo){
		$paginationParams = array('pluginName' => 'suser', 'params' => array(), 'xparams' => array('username' => $username, 'show_group' => $show_group, 'sort_by' => $sort_by, 'sort_dir' => $sort_dir), 'paginator' => array('page', 1, false));

		$navigations = LoadVariables_suser();
		$pages = generatePagination($pageNo, 1, $countPages, 10, $paginationParams, $navigations);
	}
	
	$status = array('4' => '�����������',
						'3' => '���������',
						'2' => '��������',
						'1' => '�������������');
	
	foreach ($mysql->select('SELECT *
				FROM '.prefix.'_users 
				'.$where.'
				ORDER BY '.$sort_b.' '.$sort_d.'
				LIMIT '.$limitStart.', '.$limitCount) as $row)
	{
		$tEntry[] = array (
			'profile_link' => checkLinkAvailable('uprofile', 'show')?
									generateLink('uprofile', 'show', array('name' => $row['name'], 'id' => $row['author_id'])):
									generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('name' => $row['name'], 'id' => $row['author_id'])),
			'ublog_link' => generatePluginLink('ublog', null, array('uid' => $row['author_id'], 'uname' => $row['name'])),
			'profile' => $row['name'],
			'status' => $status[$row['status']],
			'last' => $row['last'],
			'reg' => $row['reg'],
			'news' => $row['news'],
			'com' => isset($row['com'])?$row['com']:'0',
		);
	}

	$tVars = array(

		'username' => isset($_REQUEST['username'])?secureinput_suser($_REQUEST['username']):'',
		'show_group_'.$show_group => 1,
		'sort_by_'.$sort_by => 1,
		'sort_dir_'.$sort_dir => 1,
		
		'entries' => isset($tEntry)?$tEntry:'',
		'pages' => array(
			'true' => (isset($pages) && $pages)?1:0,
			'print' => isset($pages)?$pages:''
		),
		
		'prevlink' => array(
					'true' => !empty($limitStart)?1:0,
					'link' => str_replace('%page%',
											"$1",
											str_replace('%link%', 
												checkLinkAvailable('suser')?
												generatePageLink(array('pluginName' => 'suser', 'params' => array(), 'xparams' => array('username' => $username, 'show_group' => $show_group, 'sort_by' => $sort_by, 'sort_dir' => $sort_dir), 'paginator' => array('page', 1, false)), $prev = floor($limitStart / $limitCount)):
												generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'suser'), 'xparams' => array('username' => $username, 'show_group' => $show_group, 'sort_by' => $sort_by, 'sort_dir' => $sort_dir), 'paginator' => array('page', 1, false)), $prev = floor($limitStart / $limitCount)), 
												isset($navigations['prevlink'])?$navigations['prevlink']:''
											)
					),
		),
		'nextlink' => array(
					'true' => ($prev + 2 <= $countPages)?1:0,
					'link' => str_replace('%page%',
											"$1",
											str_replace('%link%', 
												checkLinkAvailable('suser')?
												generatePageLink(array('pluginName' => 'suser', 'params' => array(), 'xparams' => array('username' => $username, 'show_group' => $show_group, 'sort_by' => $sort_by, 'sort_dir' => $sort_dir), 'paginator' => array('page', 1, false)), $prev+2):
												generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'suser'), 'xparams' => array('username' => $username, 'show_group' => $show_group, 'sort_by' => $sort_by, 'sort_dir' => $sort_dir), 'paginator' => array('page', 1, false)), $prev+2), 
												isset($navigations['nextlink'])?$navigations['nextlink']:''
											)
					),
		),
	);
	
	$template['vars']['mainblock'] .= $xt->render($tVars);

}


function secureinput_suser($text){
	if (!is_array($text)) {
		$text = trim($text);
		$search = array("&", "\"", "'", "\\", '\"', "\'", "<", ">");
		$replace = array("&amp;", "&quot;", "&#39;", "&#92;", "&quot;", "&#39;", "&lt;", "&gt;");
		$text = preg_replace("/(&amp;)+(?=\#([0-9]{2,3});)/i", "&", str_replace($search, $replace, $text));
	} else{
		foreach ($text as $key => $value) $text[$key] = secureinput_suser($value);
	}
	
	return $text;
}

function securemysql_suser($sql){
	$sql = db_squote($sql);
	return $sql;
}

function securenum_suser($value) {
	$value = intval($value);
	return $value;
}

function LoadVariables_suser(){
	$tpath = locatePluginTemplates(array(':'), 'suser', pluginGetVariable('suser', 'localsource'));
	return parse_ini_file($tpath[':'].'/variables.ini', true);
}

function link_profile_suser($id, $act = '', $name)
{
	
	$id = intval($id);
	switch($act){
		case '': $url = checkLinkAvailable('suser', 'profile')?
			generateLink('suser', 'profile', array('name' => $name, 'id' => $id)):
			generateLink('core', 'plugin', array('plugin' => 'suser', 'handler' => 'profile'), array('id' => $id)); break;
		case 'edit': $url = checkLinkAvailable('suser', 'profile')?
			generateLink('suser', 'profile', array('name' => $name, 'id' => $id, 'act' => 'edit')):
			generateLink('core', 'plugin', array('plugin' => 'suser', 'handler' => 'profile'), array('id' => $id, 'act' => 'edit')); break;
	}
	return $url;
}

function redirect_link_suser($url)
{
	if (headers_sent()) {
		echo "<script>document.location.href='{$url}';</script>\n";
		exit;
	} else {
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: {$url}");
		exit;
	}
}

function information_suser($info, $title = '����������', $error_404 = false)
{global $twig, $SYSTEM_FLAGS, $CurrentHandler, $template;
	
	$CurrentHandler['handlerName'] = 'erro404';
	
	if($error_404)
		header($_SERVER['SERVER_PROTOCOL']. ' 404 Not Found');

	$tpath = locatePluginTemplates(array('suser','information'), 'suser', pluginGetVariable('suser', 'localsource'));
	$xt = $twig->loadTemplate($tpath['information'].'information.tpl');
	
	$tVars = array(
		'title' => $title,
		'info' => $info,
	);
	
	$template['vars']['mainblock'] = $xt->render($tVars);
}
