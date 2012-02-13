<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');


LoadPluginLang('nsm', 'main', '', '', '#');
register_plugin_page('nsm','','plugin_nsm');
register_plugin_page('nsm','add','plugin_nsm_add');
register_plugin_page('nsm','edit','plugin_nsm_edit');

// Show list of user's news
function plugin_nsm(){
	global $userROW, $mysql, $twig, $lang, $template;

	// Load permissions
	$perm = checkPermission(array('plugin' => 'nsm', 'item' => ''), null, array(
		'view',
		'view.draft',
		'view.unpublished',
		'view.published'
	));

	if (!is_array($userROW) || !$perm['view']) {
		print "PERM DENIED";
		return;
	}

	$tVars = array();
	$tEntries = array();
	$query = "select * from ".prefix."_news where author_id = ".intval($userROW['id'])." order by id desc";

	foreach ($mysql->select($query, 1) as $row) {
		$tEntries []= array(
			'php_self'		=> $PHP_SELF,
			'home'			=> home,
			'newsid'		=> $row['id'],
			'userid'		=> $row['author_id'],
			'username'		=> $row['author'],
			'comments'		=> isset($row['com'])?$row['com']:'',
			'attach_count'	=> $row['num_files'],
			'images_count'	=> $row['num_images'],
			'itemdate'		=> date("d.m.Y",$row['postdate']),
			'allcats'		=> resolveCatNames($cats).' &nbsp;',
			'title'			=> secure_html((strlen($row['title']) > 70)?substr($row['title'],0,70)." ...":$row['title']),
			'link'			=> newsGenerateLink($row, false, 0, true),
			'state'			=> $row['approve'],
			'editlink'		=> generatePluginLink('nsm', 'edit',array('id' => $row['id']), array('id' => $row['id'])),
			'flags'			=> array(
				'editable'		=> ($row['approve'] < 1 )?1:0,
				'comments'		=> getPluginStatusInstalled('comments')?true:false,
				'status'		=> ($row['approve'] == 1)?true:false,
				'mainpage'		=> $row['mainpage']?true:false,
			)
		);

	}
	$tVars['entries'] = $tEntries;

	// Determine paths for all template files
	$tpath = locatePluginTemplates(array('news.list'), 'nsm', pluginGetVariable('nsm', 'localsource'));

	$xt = $twig->loadTemplate($tpath['news.list'].'news.list.tpl');
	$template['vars']['mainblock'] .= $xt->render($tVars);
}

function plugin_nsm_add(){
	global $lang;

	// Load permissions
	$perm = checkPermission(array('plugin' => 'nsm', 'item' => ''), null, array(
		'add',
	));

	// Check permissions
	if (!$perm['add']) {
		msg(array("type" => "error", "text" => $lang['perm.denied']));
		return 0;
	}


	if ($_SERVER['REQUEST_METHOD'] != "POST") {
		plugin_nsm_addForm(null);
	} else {
		// Load library
		require_once(root.'/includes/inc/lib_admin.php');

		LoadLang('addnews', 'admin', 'addnews');

		$o = addNews(array('no.meta' => true, 'no.files' => true));
		if (!$o) {
			plugin_nsm_addForm(json_encode(arrayCharsetConvert(0, $_POST)));
		}
	}
}

function plugin_nsm_edit(){
	global $lang;

	// Load permissions
	$perm = checkPermission(array('plugin' => 'nsm', 'item' => ''), null, array(
		'view',
		'view.draft',
		'view.unpublished',
		'view.published',
		'edit.draft',
		'edit.unpublished',
	));

	// Check permissions
	if (!$perm['view']) {
		msg(array("type" => "error", "text" => $lang['perm.denied']));
		return 0;
	}

	LoadLang('editnews', 'admin');

	if ($_SERVER['REQUEST_METHOD'] != "POST") {
		plugin_nsm_editForm(null);
	} else {
		// Load library
		require_once(root.'/includes/inc/lib_admin.php');
		require_once root.'includes/classes/upload.class.php';

		$o = editNews(array('no.meta' => true, 'no.files' => true));
	//	if (!$o) {
	//		plugin_nsm_addForm(json_encode(arrayCharsetConvert(0, $_POST)));
	//	}
	}
}


// Form for adding news
function plugin_nsm_addForm($retry = ''){
	global $userROW, $twig, $lang, $template, $config, $PHP_SELF;

	// Load permissions
	$perm = checkPermission(array('plugin' => '#admin', 'item' => 'news'), null, array(
		'add.approve',
		'add.mainpage',
		'add.pinned',
		'add.catpinned',
		'add.favorite',
		'add.html',
		'personal.publish',
		'personal.html',
		'personal.mainpage',
		'personal.pinned',
		'personal.catpinned',
		'personal.favorite',
		'personal.setviews',
		'personal.multicat',
		'personal.customdate',
	));

	LoadLang('addnews', 'admin', 'addnews');

	// Check if current user have permission to add news
//	if (!$perm['add.onsite']) {
//		msg(array("type" => "error", "text" => $lang['perm.denied']));
//		return;
//	}

	$tVars = array(
		'php_self'			=> $PHP_SELF,
		'changedate'		=> ChangeDate(),
		'mastercat'			=>	makeCategoryList(array('doempty' => 1, 'nameval' => 0)),
		'extcat'			=>  makeCategoryList(array('nameval' => 0, 'checkarea' => 1)),
		'JEV'				=> $retry?$retry:'{}',
		'smilies'			=> ($config['use_smilies'])?InsertSmilies('', 20, 'currentInputAreaID'):'',
		'quicktags'			=> ($config['use_bbcodes'])?QuickTags('currentInputAreaID', 'news'):'',
		'flags'				=> array(
			'mainpage'			=> $perm['add.mainpage'] && $perm['personal.mainpage'],
			'favorite'			=> $perm['add.favorite'] && $perm['personal.favorite'],
			'pinned'			=> $perm['add.pinned'] && $perm['personal.pinned'],
			'catpinned'			=> $perm['add.catpinned'] && $perm['personal.catpinned'],
			'html'				=> $perm['add.html'] && $perm['personal.html'],
			'mainpage.disabled'	=> !$perm['personal.mainpage'],
			'favorite.disabled'	=> !$perm['personal.favorite'],
			'pinned.disabled'	=> !$perm['personal.pinned'],
			'catpinned.disabled'	=> !$perm['personal.catpinned'],
			'edit_split'		=> $config['news.edit.split']?true:false,
			'meta'				=> $config['meta']?true:false,
			'html.disabled'		=> !$perm['personal.html'],
			'customdate.disabled'	=> !$perm['personal.customdate'],
			'multicat.show'		=> $perm['personal.multicat'],
			'extended_more'		=> ($config['extended_more'] || ($tvars['vars']['content.delimiter'] != ''))?true:false,
			'can_publish'		=> $perm['personal.publish'],
		),
	);

	// Determine paths for all template files
	$tpath = locatePluginTemplates(array('news.add'), 'nsm', pluginGetVariable('nsm', 'localsource'));

	$xt = $twig->loadTemplate($tpath['news.add'].'news.add.tpl');
	$template['vars']['mainblock'] .= $xt->render($tVars);
}

function plugin_nsm_editForm($retry = ''){
	global $lang, $parse, $mysql, $config, $PFILTERS, $tvars, $userROW, $twig, $template;

	// Load permissions
	$perm = checkPermission(array('plugin' => '#admin', 'item' => 'news'), null, array(
		'personal.view',
		'personal.modify',
		'personal.modify.published',
		'personal.publish',
		'personal.unpublish',
		'personal.delete',
		'personal.delete.published',
		'personal.html',
		'personal.mainpage',
		'personal.pinned',
		'personal.catpinned',
		'personal.favorite',
		'personal.setviews',
		'personal.multicat',
		'personal.customdate',
		'personal.altname',
	));

	LoadLang('editnews', 'admin', 'editnews');

	// Get news id
	$id			= $_REQUEST['id'];

	// Try to find news that we're trying to edit
	if (!is_array($row = $mysql->record("select * from ".prefix."_news where (id = ".db_squote($id).") and (author_id = ".db_squote($userROW['id']).")", 1))) {
		msg(array("type" => "error", "text" => $lang['msge_not_found']));
		return;
	}

	$isOwn = ($row['author_id'] == $userROW['id'])?1:0;
	$permGroupMode = $isOwn?'personal':'other';


	// Check permissions
	if (!$perm[$permGroupMode.'.view']) {
		msg(array("type" => "error", "text" => $lang['perm.denied']));
		return;
	}

	// Load attached files/images
	$row['#files'] = $mysql->select("select *, date_format(from_unixtime(date), '%d.%m.%Y') as date from ".prefix."_files where (linked_ds = 1) and (linked_id = ".db_squote($row['id']).')', 1);
	$row['#images'] = $mysql->select("select *, date_format(from_unixtime(date), '%d.%m.%Y') as date from ".prefix."_images where (linked_ds = 1) and (linked_id = ".db_squote($row['id']).')', 1);


	$cats = explode(",", $row['catid']);
	$content = $row['content'];

	$tVars = array(
		'php_self'			=>	$PHP_SELF,
		'changedate'		=>	ChangeDate($row['postdate'], 1),
		'mastercat'			=>	makeCategoryList(array('doempty' => 1, 'nameval' => 0,   'selected' => count($cats)?$cats[0]:0)),
		'extcat'			=>  makeCategoryList(array('nameval' => 0, 'checkarea' => 1, 'selected' => (count($cats)>1)?array_slice($cats,1):array(), 'disabledarea' => !$perm[$permGroupMode.'.multicat'])),
		'allcats'			=>	resolveCatNames($cats),
		'id'				=>	$row['id'],
		'title'				=>	secure_html($row['title']),
		'content'			=>  array(),
		'alt_name'			=>	$row['alt_name'],
		'avatar'			=>	$row['avatar'],
		'description'		=>	secure_html($row['description']),
		'keywords'			=>	secure_html($row['keywords']),
		'views'				=>	$row['views'],
		'author'			=>  $row['author'],
		'authorid'			=>  $row['author_id'],
		'createdate'		=>  strftime('%d.%m.%Y %H:%M', $row['postdate']),
		'editdate'			=>  ($row['editdate'] > $row['postdate'])?strftime('%d.%m.%Y %H:%M', $row['editdate']):'-',
		'author_page'		=>  checkLinkAvailable('uprofile', 'show')?
									generateLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id'])):
									generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('name' => $row['author'], 'id' => $row['author_id'])),
		'smilies'			=> $config['use_smilies']?InsertSmilies('', 20, 'currentInputAreaID'):'',
		'quicktags'			=> $config['use_bbcodes']?QuickTags('currentInputAreaID', 'news'):'',
		'approve'			=> $row['approve'],
		'flags'				=> array(
			'edit_split'		=> $config['news.edit.split']?true:false,
			'meta'				=> $config['meta']?true:false,
			'mainpage'			=> $row['mainpage']?true:false,
			'favorite'			=> $row['favorite']?true:false,
			'pinned'			=> $row['pinned']?true:false,
			'catpinned'			=> $row['catpinned']?true:false,
			'can_mainpage'		=> $perm[$permGroupMode.'.mainpage']?true:false,
			'can_pinned'		=> $perm[$permGroupMode.'.pinned']?true:false,
			'can_catpinned'		=> $perm[$permGroupMode.'.catpinned']?true:false,
			'raw'				=> ($row['flags'] & 1),
			'html'				=> ($row['flags'] & 2),
			'extended_more'		=> ($config['extended_more'] || ($tvars['vars']['content.delimiter'] != ''))?true:false,
			'editable'			=> (($perm[$permGroupMode.'.modify'.(($row['approve'] == 1)?'.published':'')]) || ($perm[$permGroupMode.'.unpublish']))?true:false,
			'deleteable'		=> ($perm[$permGroupMode.'.delete'.(($row['approve'] == 1)?'.published':'')])?true:false,
			'html.lost'			=> (($row['flags'] & 2) && (!$perm[$permGroupMode.'.html']))?1:0,
			'mainpage.lost'		=> (($row['mainpage']) && (!$perm[$permGroupMode.'.mainpage']))?true:false,
			'pinned.lost'		=> (($row['pinned']) && (!$perm[$permGroupMode.'.pinned']))?true:false,
			'catpinned.lost'	=> (($row['catpinned']) && (!$perm[$permGroupMode.'.catpinned']))?true:false,
			'publish.lost'		=> (($row['approve'] == 1) && (!$perm[$permGroupMode.'.modify.published']))?true:false,
			'favorite.lost'		=> (($row['favorite']) && (!$perm[$permGroupMode.'.favorite']))?true:false,
			'multicat.lost'		=> ((count($cats)>1) && (!$perm[$permGroupMode.'.multicat']))?true:false,
			'html.disabled'		=> (!$perm[$permGroupMode.'.html'])?true:false,
			'customdate.disabled'	=> (!$perm[$permGroupMode.'.customdate'])?true:false,
			'mainpage.disabled'	=> (!$perm[$permGroupMode.'.mainpage'])?true:false,
			'pinned.disabled'	=> (!$perm[$permGroupMode.'.pinned'])?true:false,
			'catpinned.disabled'=> (!$perm[$permGroupMode.'.catpinned'])?true:false,
			'favorite.disabled'	=> (!$perm[$permGroupMode.'.favorite'])?true:false,
			'setviews.disabled'	=> (!$perm[$permGroupMode.'.setviews'])?true:false,
			'multicat.disabled'	=> (!$perm[$permGroupMode.'.multicat'])?true:false,
			'altname.disabled'	=> (!$perm[$permGroupMode.'.altname'])?true:false,
			'multicat.show'		=> $perm['personal.multicat'],
		)
	);

	$tVars['flags']['can_publish']		= ((($row['approve'] == 1) && ($perm[$permGroupMode.'.modify.published']))  || (($row['approve'] < 1) && $perm[$permGroupMode.'.publish']))?1:0;
	$tVars['flags']['can_unpublish']	= (($row['approve'] < 1)   || ($perm[$permGroupMode.'.unpublish']))?1:0;
	$tVars['flags']['can_draft']		= (($row['approve'] == -1) || ($perm[$permGroupMode.'.unpublish']))?1:0;

	$tVars['flags']['params.lost']		= ($tVars['flags']['publish.lost'] || $tVars['flags']['html.lost'] || $tVars['flags']['mainpage.lost'] || $tVars['flags']['pinned.lost'] || $tVars['flags']['catpinned.lost'] || $tVars['flags']['multicat.lost'])?1:0;


	// Generate data for content input fields
	if ($config['news.edit.split']) {
		$tVars['content']['delimiter'] = '';
		if (preg_match('#^(.*?)<!--more-->(.*?)$#si', $row['content'], $match)) {
			$tVars['content']['short'] = secure_html($match[1]);
			$tVars['content']['full'] = secure_html($match[2]);
		} else if (preg_match('#^(.*?)<!--more=\"(.*?)\"-->(.*?)$#si', $row['content'], $match)) {
			$tVars['content']['short'] = secure_html($match[1]);
			$tVars['content']['full'] = secure_html($match[3]);
			$tVars['content']['delimiter'] = secure_html($match[2]);
		} else {
			$tVars['content']['short'] = secure_html($row['content']);
			$tVars['content']['full'] = '';
		}
	} else {
		$tVars['content']['short'] = secure_html($row['content']);
	}

	if (is_array($PFILTERS['news']))
		foreach ($PFILTERS['news'] as $k => $v) { $v->editNewsForm($id, $row, $tVars); }

	// Determine paths for all template files
	$tpath = locatePluginTemplates(array('news.edit'), 'nsm', pluginGetVariable('nsm', 'localsource'));

	$xt = $twig->loadTemplate($tpath['news.edit'].'news.edit.tpl');
	$template['vars']['mainblock'] .= $xt->render($tVars);

}
