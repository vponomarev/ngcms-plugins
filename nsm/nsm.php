<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
LoadPluginLang('nsm', 'main', '', '', '#');
register_plugin_page('nsm', '', 'plugin_nsm');
register_plugin_page('nsm', 'add', 'plugin_nsm_add_proxy');
register_plugin_page('nsm', 'edit', 'plugin_nsm_edit_proxy');
register_plugin_page('nsm', 'del', 'plugin_nsm_del');
LoadPluginLibrary('xfields', 'common');
function plugin_nsm_add_proxy() {

	$tpl_name = 'news.add';
	plugin_nsm_add($tpl_name);
}

function plugin_nsm_edit_proxy() {

	$tpl_name = 'news.edit';
	plugin_nsm_edit($tpl_name);
}

// Show list of user's news
function plugin_nsm() {

	global $userROW, $mysql, $twig, $lang, $template;
	// Load permissions
	$perm = checkPermission(array('plugin' => '#admin', 'item' => 'news'), null, array(
		'personal.view',
		'personal.modify',
		'personal.modify.published',
		'personal.delete',
		'personal.delete.published',
		'add',
	));
	$permPlugin = checkPermission(array('plugin' => 'nsm', 'item' => ''), null, array(
		'view',
		'view.draft',
		'view.unpublished',
		'view.published',
		'modify.draft',
		'modify.unpublished',
		'modify.published',
		'delete.draft',
		'delete.unpublished',
		'delete.draft',
		'list',
		'add',
	));
	if (!is_array($userROW) || !$permPlugin['view']) {
		msg(array("type" => "error", "text" => $lang['perm.denied']));

		return;
	}
	$tVars = array(
		'token'  => genUToken('nsm.edit'),
		'addURL' => generatePluginLink('nsm', 'add', array(), array()),
	);
	$tEntries = array();
	$query = "select * from " . prefix . "_news where author_id = " . intval($userROW['id']) . " order by id desc";
	foreach ($mysql->select($query, 1) as $row) {
		// Check if we can show this entry
		if (((($row['approve'] == -1) && (!$permPlugin['view.draft'])) ||
				(($row['approve'] == 0) && (!$permPlugin['view.unpublished'])) ||
				(($row['approve'] == 1) && (!$permPlugin['view.published']))) && (!$permPlugin['list'])
		) {
			continue;
		}
		$cats = (strlen($row['catid']) > 0) ? explode(",", $row['catid']) : array();
		$canView = ((($row['approve'] == -1) && ($permPlugin['view.draft'])) ||
				(($row['approve'] == 0) && ($permPlugin['view.unpublished'])) ||
				(($row['approve'] == 1) && ($permPlugin['view.published']))) && $perm['personal.view'];
		$canEdit = (($row['approve'] == -1) && ($permPlugin['modify.draft'])) ||
			(($row['approve'] == 0) && ($permPlugin['modify.unpublished']) && ($perm['personal.modify'])) ||
			(($row['approve'] == 1) && ($permPlugin['modify.published']) && ($perm['personal.modify.published']));
		$canDelete = (($row['approve'] == -1) && ($permPlugin['delete.draft'])) ||
			(($row['approve'] == 0) && ($permPlugin['delete.unpublished']) && ($perm['personal.delete'])) ||
			(($row['approve'] == 1) && ($permPlugin['delete.published']) && ($perm['personal.delete.published']));
		$tEntries [] = array(
			'php_self'     => $PHP_SELF,
			'home'         => home,
			'newsid'       => $row['id'],
			'userid'       => $row['author_id'],
			'username'     => $row['author'],
			'comments'     => isset($row['com']) ? $row['com'] : '',
			'attach_count' => $row['num_files'],
			'images_count' => $row['num_images'],
			'itemdate'     => date("d.m.Y", $row['postdate']),
			'cats'         => $cats,
			'allcats'      => resolveCatNames($cats) . ' &nbsp;',
			'title'        => secure_html((strlen($row['title']) > 70) ? substr($row['title'], 0, 70) . " ..." : $row['title']),
			'link'         => newsGenerateLink($row, false, 0, true),
			'state'        => $row['approve'],
			'editlink'     => generatePluginLink('nsm', 'edit', array('id' => $row['id']), array('id' => $row['id'])),
			'editlink1'    => generatePluginLink('nsm', 'edit1', array('id' => $row['id']), array('id' => $row['id'])),
			'editlink2'    => generatePluginLink('nsm', 'edit2', array('id' => $row['id']), array('id' => $row['id'])),
			'deletelink'   => generatePluginLink('nsm', 'del', array(), array('id' => $row['id'], 'token' => genUToken('admin.news.edit'))),
			'flags'        => array(
				'canEdit'   => $canEdit ? 1 : 0,
				'canView'   => $canView ? 1 : 0,
				'canDelete' => $canDelete ? 1 : 0,
				'comments'  => getPluginStatusInstalled('comments') ? true : false,
				'status'    => ($row['approve'] == 1) ? true : false,
				'mainpage'  => $row['mainpage'] ? true : false,
			)
		);
	}
	$tVars['entries'] = $tEntries;
	// Link for adding news
	$tVars['flags']['canAdd'] = (($permPlugin['add']) && ($perm['add'])) ? 1 : 0;
	// Determine paths for all template files
	$tpath = locatePluginTemplates(array('news.list'), 'nsm', pluginGetVariable('nsm', 'localsource'));
	$xt = $twig->loadTemplate($tpath['news.list'] . 'news.list.tpl');
	$template['vars']['mainblock'] .= $xt->render($tVars);
}

function plugin_nsm_add($tpl_name) {

	global $lang, $SUPRESS_TEMPLATE_SHOW;
	LoadLang('addnews', 'admin', 'addnews');
	// Load permissions
	$permPlugin = checkPermission(array('plugin' => 'nsm', 'item' => ''), null, array(
		'add',
	));
	$perm = checkPermission(array('plugin' => '#admin', 'item' => 'news'), null, array('add'));
	// Check permissions
	if ((!$permPlugin['add']) || (!$perm['add'])) {
		msg(array("type" => "error", "text" => $lang['perm.denied']));

		return 0;
	}
	if ($_SERVER['REQUEST_METHOD'] != "POST") {
		// Set "Allow comments = default"
		$_REQUEST['allow_com'] = 2;
		plugin_nsm_addForm($tpl_name, null);
	} else {
		// Load library
		require_once(root . '/includes/inc/lib_admin.php');
		require_once root . 'includes/classes/upload.class.php';
		LoadLang('addnews', 'admin', 'addnews');
		if (isset($_REQUEST['mod']) && ($_REQUEST['mod'] == 'preview')) {
			include_once root . 'includes/news.php';
			$lang = LoadLang('preview', 'admin');
			showPreview();
			$SUPRESS_TEMPLATE_SHOW = 1;

			return;
		}
		$o = addNews(array('no.meta' => true, 'no.files' => true, 'no.editurl' => true));
		if (!$o) {
			plugin_nsm_addForm($tpl_name, json_encode(arrayCharsetConvert(0, $_POST)));
		} else {
			// Show list of current news
			plugin_nsm();
		}
	}
}

function plugin_nsm_edit($tpl_name) {

	global $lang, $mysql, $userROW, $SUPRESS_TEMPLATE_SHOW;
	LoadLang('editnews', 'admin', 'editnews');
	// Load permissions
	$perm = checkPermission(array('plugin' => 'nsm', 'item' => ''), null, array(
		'view',
		'view.draft',
		'view.unpublished',
		'view.published',
		'modify.draft',
		'modify.unpublished',
		'modify.published',
		'delete.draft',
		'delete.unpublished',
		'delete.published',
	));
	// We can manage only OWN news
	if (!is_array($userROW)) {
		msg(array("type" => "error", "text" => $lang['perm.denied']));
	}
	// Try to find news that we're trying to edit
	if (!is_array($row = $mysql->record("select * from " . prefix . "_news where (id = " . db_squote($_REQUEST['id']) . ") and (author_id = " . db_squote($userROW['id']) . ")", 1))) {
		msg(array("type" => "error", "text" => $lang['nsm']['err.news_not_found']));

		return;
	}
	// We can manage only OWN news
	if ((!is_array($userROW)) || ($row['author_id'] != $userROW['id'])) {
		msg(array("type" => "error", "text" => $lang['perm.denied']));

		return 0;
	}
	// Check permissions for view
	if ((!$perm['view']) ||
		((($row['approve'] == -1) && (!$perm['view.draft'])) ||
			(($row['approve'] == 0) && (!$perm['view.unpublished'])) ||
			(($row['approve'] == 1) && (!$perm['view.published'])))
	) {
		msg(array("type" => "error", "text" => $lang['perm.denied']));

		return 0;
	}
	LoadLang('editnews', 'admin');
	if ($_SERVER['REQUEST_METHOD'] != "POST") {
		// Save old value of "Allow comments"
		$_REQUEST['allow_com'] = $row['allow_com'];
		plugin_nsm_editForm($tpl_name, null);
	} else {
		// Trying to edit, check if we have permissions for this
		if ((!$perm['view']) ||
			((($row['approve'] == -1) && (!$perm['modify.draft'])) ||
				(($row['approve'] == 0) && (!$perm['modify.unpublished'])) ||
				(($row['approve'] == 1) && (!$perm['modify.published'])))
		) {
			msg(array("type" => "error", "text" => $lang['perm.denied']));

			return 0;
		}
		// Load library
		require_once(root . '/includes/inc/lib_admin.php');
		require_once root . 'includes/classes/upload.class.php';
		if (isset($_REQUEST['mod']) && ($_REQUEST['mod'] == 'preview')) {
			include_once root . 'includes/news.php';
			$lang = LoadLang('preview', 'admin');
			showPreview();
			$SUPRESS_TEMPLATE_SHOW = 1;
		} else {
			$o = editNews(array('no.meta' => true, 'no.files' => true));
			if (!$o) {
				plugin_nsm_editForm($tpl_name, json_encode(arrayCharsetConvert(0, $_POST)));
			}
		}
	}
}

// Form for adding news
function plugin_nsm_addForm($tpl_name = 'news.add', $retry = '') {

	global $userROW, $twig, $lang, $template, $config, $PHP_SELF, $catz;
	// Load permissions
	$perm = checkPermission(array('plugin' => '#admin', 'item' => 'news'), null, array(
		'add',
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
		'personal.nocat',
		'personal.customdate',
	));
	$permPlugin = checkPermission(array('plugin' => 'nsm', 'item' => ''), null, array(
		'add',
	));
	LoadLang('addnews', 'admin', 'addnews');
	// Check if current user have permission to add news
	if ((!$perm['add']) || (!$permPlugin['add'])) {
		msg(array("type" => "error", "text" => $lang['perm.denied']));

		return;
	}
	if (class_exists('XFieldsFilter')) {
		// Load config
		$xf = xf_configLoad();
		if (!is_array($xf))
			return false;
		$output = '';
		$xfEntries = array();
		$xfList = array();
		if (is_array($xf['news']))
			foreach ($xf['news'] as $id => $data) {
				if ($data['disabled'])
					continue;
				$xfEntry = array(
					'title'        => $data['title'],
					'id'           => $id,
					'value'        => $xdata[$id],
					'secure_value' => secure_html($xdata[$id]),
					'data'         => $data,
					'required'     => $lang['xfields_fld_' . ($data['required'] ? 'required' : 'optional')],
					'flags'        => array(
						'required' => $data['required'] ? true : false,
					),
				);
				switch ($data['type']) {
					case 'checkbox'  :
						$val = '<input type="checkbox" id="form_xfields_' . $id . '" name="xfields[' . $id . ']" title="' . $data['title'] . '" value="1" ' . ($data['default'] ? 'checked="checked"' : '') . '"/>';
						$xfEntry['input'] = $val;
						break;
					case 'text'  :
						$val = '<input type="text" id="form_xfields_' . $id . '" name="xfields[' . $id . ']" title="' . $data['title'] . '" value="' . secure_html($data['default']) . '"/>';
						$xfEntry['input'] = $val;
						break;
					case 'select':
						$val = '<select name="xfields[' . $id . ']" id="form_xfields_' . $id . '" >';
						if (!$data['required']) $val .= '<option value=""></option>';
						if (is_array($data['options']))
							foreach ($data['options'] as $k => $v)
								$val .= '<option value="' . secure_html(($data['storekeys']) ? $k : $v) . '"' . ((($data['storekeys'] && $data['default'] == $k) || (!$data['storekeys'] && $data['default'] == $v)) ? ' selected' : '') . '>' . $v . '</option>';
						$val .= '</select>';
						$xfEntry['input'] = $val;
						break;
					case 'textarea'  :
						$val = '<textarea cols="30" rows="5" name="xfields[' . $id . ']" id="form_xfields_' . $id . '" >' . $data['default'] . '</textarea>';
						$xfEntry['input'] = $val;
						break;
					case 'images'   :
						$iCount = 0;
						$input = '';
						$tfVars = array('images' => array());
						// Show entries for allowed number of attaches
						for ($i = $iCount + 1; $i <= intval($data['maxCount']); $i++) {
							$tImage = array(
								'number' => $i,
								'id'     => $id,
								'flags'  => array(
									'exist' => false,
								),
							);
							$tfVars['images'][] = $tImage;
						}
						// Make template
						$xf = $twig->loadTemplate('plugins/xfields/tpl/ed_entry.image.tpl');
						$val = $xf->render($tfVars);
						$xfEntry['input'] = $val;
						break;
					default:
						continue;
				}
				$xfEntries[intval($data['area'])][] = $xfEntry;
				$xfList[$id] = $xfEntry;
			}
		$xfCategories = array();
		foreach ($catz as $cId => $cData) {
			$xfCategories[$cData['id']] = $cData['xf_group'];
		}
		/*
		// Prepare table data [if needed]
		$flagTData = false;
		if (isset($xf['tdata']) && is_array($xf['tdata'])) {
			// Data are not provisioned
			$tlist = array();

			// Prepare config
			$tclist = array();
			$thlist = array();
			foreach ($xf['tdata'] as $fId => $fData) {
				if ($fData['disabled'])
					continue;

				$flagTData = true;

				$tclist[$fId] = array(
					'title'     => $fData['title'],
					'required'  => $fData['required'],
					'type'      => $fData['type'],
					'default'   => $fData['default'],
				);
				$thlist [] = array(
					'id'    => $fId,
					'title' => $fData['title'],
				);
				if ($fData['type'] == 'select') {
					$tclist[$fId]['storekeys']  = $fData['storekeys'];
					$tclist[$fId]['options']    = $fData['options'];
				}

			}
		}


		$tfVars = array(
		//  'entries'   =>  $xfEntries,
			'xfGC'      =>  json_encode(arrayCharsetConvert(0, $xf['grp.news'])),
			'xfCat'     =>  json_encode(arrayCharsetConvert(0, $xfCategories)),
			'xfList'    =>  json_encode(arrayCharsetConvert(0, array_keys($xf['news']))),
			'xtableConf'    =>  json_encode(arrayCharsetConvert(0, $tclist)),
			'xtableVal'     =>  isset($_POST['xftable'])?$_POST['xftable']:json_encode(arrayCharsetConvert(0, $tlist)),
			'xtableHdr'     =>  $thlist,
			'xtablecnt'     =>  count($thlist),
			'flags'         => array(
				'tdata'         => $flagTData,
			),
		);
		*/
		if (!isset($xfEntries[0])) {
			$xfEntries[0] = array();
		}
		foreach ($xfEntries as $k => $v) {
			// Check if we have template for specific area, elsewhere - use basic [0] template
			$templateName = 'plugins/xfields/tpl/news.add.' . (file_exists(root . 'plugins/xfields/tpl/news.add.' . $k . '.tpl') ? $k : '0') . '.tpl';
			$xf = $twig->loadTemplate($templateName);
			$tfVars['entries'] = $v;
			$tfVars['entryCount'] = count($v);
			$tfVars['area'] = $k;
			// Table data is available only for area 0
			$tfVars['flags']['tdata'] = (!$k) ? $flagTData : 0;
			// Render block
			$xf_render[$k] .= $xf->render($tfVars);
		}
		unset($tfVars['entries']);
		unset($tfVars['area']);
		$xt = $twig->loadTemplate('plugins/xfields/tpl/news.general.tpl');
		$xfields['general'] = $xt->render($tfVars);
		$xfields['fields'] = $xfList;
	} else {
		$xfields = array();
	}
	$tVars = array(
		'xfields'    => $xfields,
		'php_self'   => $PHP_SELF,
		'changedate' => ChangeDate(),
		'mastercat'  => makeCategoryList(array('doempty' => 1, 'greyempty' => !$perm['personal.nocat'], 'nameval' => 0)),
		'extcat'     => makeCategoryList(array('nameval' => 0, 'checkarea' => 1)),
		'token'      => genUToken('admin.news.add'),
		'listURL'    => generateLink('core', 'plugin', array('plugin' => 'nsm'), array()),
		'JEV'        => $retry ? $retry : '{}',
		'smilies'    => ($config['use_smilies']) ? InsertSmilies('', 20, 'currentInputAreaID') : '',
		'quicktags'  => ($config['use_bbcodes']) ? QuickTags('currentInputAreaID', 'news') : '',
		'flags'      => array(
			'mainpage'            => $perm['add.mainpage'] && $perm['personal.mainpage'],
			'favorite'            => $perm['add.favorite'] && $perm['personal.favorite'],
			'pinned'              => $perm['add.pinned'] && $perm['personal.pinned'],
			'catpinned'           => $perm['add.catpinned'] && $perm['personal.catpinned'],
			'html'                => $perm['add.html'] && $perm['personal.html'],
			'mainpage.disabled'   => !$perm['personal.mainpage'],
			'favorite.disabled'   => !$perm['personal.favorite'],
			'pinned.disabled'     => !$perm['personal.pinned'],
			'catpinned.disabled'  => !$perm['personal.catpinned'],
			'edit_split'          => $config['news.edit.split'] ? true : false,
			'meta'                => $config['meta'] ? true : false,
			'html.disabled'       => !$perm['personal.html'],
			'customdate.disabled' => !$perm['personal.customdate'],
			'multicat.show'       => $perm['personal.multicat'],
			'extended_more'       => ($config['extended_more'] || ($tvars['vars']['content.delimiter'] != '')) ? true : false,
			'can_publish'         => $perm['personal.publish'],
			'mondatory_cat'       => (!$perm['personal.nocat']) ? true : false,
		),
	);
	// Determine paths for all template files
	$tpath = locatePluginTemplates(array($tpl_name), 'nsm', pluginGetVariable('nsm', 'localsource'));
	$xt = $twig->loadTemplate($tpath[$tpl_name] . $tpl_name . '.tpl');
	$template['vars']['mainblock'] .= $xt->render($tVars);
}

function plugin_nsm_editForm($tpl_name = 'news.edit', $retry = '') {

	global $lang, $parse, $mysql, $config, $PFILTERS, $tvars, $userROW, $twig, $template, $catz;
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
		'personal.nocat',
		'personal.customdate',
		'personal.altname',
	));
	$permPlugin = checkPermission(array('plugin' => 'nsm', 'item' => ''), null, array(
		'view',
		'view.draft',
		'view.unpublished',
		'view.published',
		'modify.draft',
		'modify.unpublished',
		'modify.published',
		'delete.draft',
		'delete.unpublished',
		'delete.draft',
		'list'
	));
	LoadLang('editnews', 'admin', 'editnews');
	// Get news id
	$id = $_REQUEST['id'];
	// Try to find news that we're trying to edit
	if (!is_array($row = $mysql->record("select * from " . prefix . "_news where (id = " . db_squote($id) . ") and (author_id = " . db_squote($userROW['id']) . ")", 1))) {
		msg(array("type" => "error", "text" => $lang['msge_not_found']));

		return;
	}
	$isOwn = ($row['author_id'] == $userROW['id']) ? 1 : 0;
	$permGroupMode = $isOwn ? 'personal' : 'other';
	// Check permissions
	if (!$perm[$permGroupMode . '.view']) {
		msg(array("type" => "error", "text" => $lang['perm.denied']));

		return;
	}
	// Load attached files/images
	$row['#files'] = $mysql->select("select *, date_format(from_unixtime(date), '%d.%m.%Y') as date from " . prefix . "_files where (linked_ds = 1) and (linked_id = " . db_squote($row['id']) . ')', 1);
	$row['#images'] = $mysql->select("select *, date_format(from_unixtime(date), '%d.%m.%Y') as date from " . prefix . "_images where (linked_ds = 1) and (linked_id = " . db_squote($row['id']) . ')', 1);
	$cats = (strlen($row['catid']) > 0) ? explode(",", $row['catid']) : array();
	$content = $row['content'];
	if (class_exists('XFieldsFilter')) {
		$xf = xf_configLoad();
		if (!is_array($xf))
			return false;
		// Fetch xfields data
		$xdata = xf_decode($row['xfields']);
		if (!is_array($xdata))
			return false;
		$output = '';
		$xfEntries = array();
		$xfList = array();
		foreach ($xf['news'] as $id => $data) {
			if ($data['disabled'])
				continue;
			$xfEntry = array(
				'title'    => $data['title'],
				'id'       => $id,
				'required' => $lang['xfields_fld_' . ($data['required'] ? 'required' : 'optional')],
				'flags'    => array(
					'required' => $data['required'] ? true : false,
				),
			);
			switch ($data['type']) {
				case 'checkbox'  :
					$val = '<input type="checkbox" id="form_xfields_' . $id . '" name="xfields[' . $id . ']" title="' . $data['title'] . '" value="1" ' . ($xdata[$id] ? 'checked="checked"' : '') . '"/>';
					$xfEntry['input'] = $val;
					$xfEntry['value'] = $xdata[$id];
					$xfEntries[intval($data['area'])][] = $xfEntry;
					break;
				case 'text'  :
					$val = '<input type="text" name="xfields[' . $id . ']"  id="form_xfields_' . $id . '" title="' . $data['title'] . '" value="' . secure_html($xdata[$id]) . '" />';
					$xfEntry['input'] = $val;
					$xfEntry['value'] = $xdata[$id];
					$xfEntries[intval($data['area'])][] = $xfEntry;
					break;
				case 'select':
					$val = '<select name="xfields[' . $id . ']" id="form_xfields_' . $id . '" >';
					if (!$data['required']) $val .= '<option value="">&nbsp;</option>';
					if (is_array($data['options']))
						foreach ($data['options'] as $k => $v) {
							$val .= '<option value="' . secure_html(($data['storekeys']) ? $k : $v) . '"' . ((($data['storekeys'] && ($xdata[$id] == $k)) || (!$data['storekeys'] && ($xdata[$id] == $v))) ? ' selected' : '') . '>' . $v . '</option>';
						}
					$val .= '</select>';
					$xfEntry['input'] = $val;
					$xfEntry['value'] = $xdata[$id];
					$xfEntries[intval($data['area'])][] = $xfEntry;
					break;
				case 'textarea' :
					$val = '<textarea cols="30" rows="4" name="xfields[' . $id . ']" id="form_xfields_' . $id . '">' . $xdata[$id] . '</textarea>';
					$xfEntry['input'] = $val;
					$xfEntry['value'] = $xdata[$id];
					$xfEntries[intval($data['area'])][] = $xfEntry;
					break;
				case 'images'   :
					// First - show already attached images
					$iCount = 0;
					$input = '';
					$tfVars = array('images' => array());
					//$tpl -> template('ed_entry.image', extras_dir.'/xfields/tpl');
					if (is_array($SQLold['#images'])) {
						foreach ($SQLold['#images'] as $irow) {
							// Skip images, that are not related to current field
							if (($irow['plugin'] != 'xfields') || ($irow['pidentity'] != $id)) continue;
							// Show attached image
							$iCount++;
							$tImage = array(
								'number'      => $iCount,
								'id'          => $id,
								'preview'     => array(
									'width'  => $irow['p_width'],
									'height' => $irow['p_height'],
									'url'    => $config['attach_url'] . '/' . $irow['folder'] . '/thumb/' . $irow['name'],
								),
								'image'       => array(
									'id'     => $irow['id'],
									'number' => $iCount,
									'url'    => $config['attach_url'] . '/' . $irow['folder'] . '/' . $irow['name'],
									'width'  => $irow['width'],
									'height' => $irow['height'],
								),
								'flags'       => array(
									'preview' => $irow['preview'] ? true : false,
									'exist'   => true,
								),
								'description' => secure_html($irow['description']),
							);
							$tfVars['images'][] = $tImage;
						}
					}
					// Second - show entries for allowed number of attaches
					for ($i = $iCount + 1; $i <= intval($data['maxCount']); $i++) {
						$tImage = array(
							'number' => $i,
							'id'     => $id,
							'flags'  => array(
								'exist' => false,
							),
						);
						$tfVars['images'][] = $tImage;
					}
					// Make template
					$xt = $twig->loadTemplate('plugins/xfields/tpl/ed_entry.image.tpl');
					$val = $xt->render($tfVars);
					$xfEntry['input'] = $val;
					$xfEntry['value'] = $tfVars;
					$xfEntries[intval($data['area'])][] = $xfEntry;
					break;
			}
		}
		$xfCategories = array();
		foreach ($catz as $cId => $cData) {
			$xfCategories[$cData['id']] = $cData['xf_group'];
		}
		// Prepare table data [if needed]
		$flagTData = false;
		if (isset($xf['tdata']) && is_array($xf['tdata'])) {
			// Load table data for specific news
			$tlist = array();
			foreach ($mysql->select("select * from " . prefix . "_xfields where (linked_ds = 1) and (linked_id = " . db_squote($newsID) . ")") as $trow) {
				$ts = unserialize($trow['xfields']);
				$tEntry = array('#id' => $trow['id']);
				// Scan every field for value
				foreach ($xf['tdata'] as $fId => $fData) {
					$fValue = '';
					if (is_array($ts) && isset($ts[$fId])) {
						$fValue = $ts[$fId];
					} elseif (isset($trow['xfields_' . $fId])) {
						$fValue = $trow['xfields_' . $fId];
					}
					$tEntry[$fId] = $fValue;
				}
				$tlist [] = $tEntry;
			}
			// Prepare config
			$tclist = array();
			$thlist = array();
			foreach ($xf['tdata'] as $fId => $fData) {
				if ($fData['disabled'])
					continue;
				$flagTData = true;
				$tclist[$fId] = array(
					'title'    => $fData['title'],
					'required' => $fData['required'],
					'type'     => $fData['type'],
					'default'  => $fData['default'],
				);
				$thlist [] = array(
					'id'    => $fId,
					'title' => $fData['title'],
				);
				if ($fData['type'] == 'select') {
					$tclist[$fId]['storekeys'] = $fData['storekeys'];
					$tclist[$fId]['options'] = $fData['options'];
				}
			}
		}
		// Prepare personal [group] variables
		$tfVars = array(
			//  'entries'       =>  $xfEntries[0],
			'xfGC'       => json_encode(arrayCharsetConvert(0, $xf['grp.news'])),
			'xfCat'      => json_encode(arrayCharsetConvert(0, $xfCategories)),
			'xfList'     => json_encode(arrayCharsetConvert(0, array_keys($xf['news']))),
			'xtableConf' => json_encode(arrayCharsetConvert(0, $tclist)),
			'xtableVal'  => json_encode(arrayCharsetConvert(0, $tlist)),
			'xtableHdr'  => $thlist,
			'xtablecnt'  => count($thlist),
			'flags'      => array(
				'tdata' => $flagTData,
			),
		);
		if (!isset($xfEntries[0])) {
			$xfEntries[0] = array();
		}
		foreach ($xfEntries as $k => $v) {
			// Check if we have template for specific area, elsewhere - use basic [0] template
			$templateName = 'plugins/xfields/tpl/news.edit.' . (file_exists(root . 'plugins/xfields/tpl/news.edit.' . $k . '.tpl') ? $k : '0') . '.tpl';
			$xt = $twig->loadTemplate($templateName);
			$tfVars['entries'] = $v;
			$tfVars['entryCount'] = count($v);
			$tfVars['area'] = $k;
			// Table data is available only for area 0
			$tfVars['flags']['tdata'] = (!$k) ? $flagTData : 0;
			// Render block
			$xfields['xfields'][$k] .= $xt->render($tfVars);
		}
		unset($tfVars['entries']);
		unset($tfVars['area']);
		// Render general part [with JavaScript]
		$xt = $twig->loadTemplate('plugins/xfields/tpl/news.general.tpl');
		$xfields['general'] = $xt->render($tfVars);
		$xfields['fields'] = $xfEntries;
	} else {
		$xfields = array();
	}
	$tVars = array(
		'xfields'     => $xfields,
		'php_self'    => $PHP_SELF,
		'changedate'  => ChangeDate($row['postdate'], 1),
		'mastercat'   => makeCategoryList(array('doempty' => ($perm['personal.nocat'] || !count($cats)) ? 1 : 0, 'greyempty' => !$perm['personal.nocat'], 'nameval' => 0, 'selected' => count($cats) ? $cats[0] : 0)),
		'extcat'      => makeCategoryList(array('nameval' => 0, 'checkarea' => 1, 'selected' => (count($cats) > 1) ? array_slice($cats, 1) : array(), 'disabledarea' => !$perm[$permGroupMode . '.multicat'])),
		'allcats'     => resolveCatNames($cats),
		'id'          => $row['id'],
		'title'       => secure_html($row['title']),
		'content'     => array(),
		'alt_name'    => $row['alt_name'],
		'avatar'      => $row['avatar'],
		'description' => secure_html($row['description']),
		'keywords'    => secure_html($row['keywords']),
		'views'       => $row['views'],
		'author'      => $row['author'],
		'authorid'    => $row['author_id'],
		'token'       => genUToken('admin.news.edit'),
		'JEV'         => $retry ? $retry : '{}',
		'listURL'     => generateLink('core', 'plugin', array('plugin' => 'nsm'), array()),
		'deleteURL'   => generateLink('core', 'plugin', array('plugin' => 'nsm', 'handler' => 'del'), array('token' => genUToken('admin.news.edit'), 'id' => $row['id'])),
		'createdate'  => strftime('%d.%m.%Y %H:%M', $row['postdate']),
		'editdate'    => ($row['editdate'] > $row['postdate']) ? strftime('%d.%m.%Y %H:%M', $row['editdate']) : '-',
		'author_page' => checkLinkAvailable('uprofile', 'show') ?
			generateLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id'])) :
			generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('name' => $row['author'], 'id' => $row['author_id'])),
		'smilies'     => $config['use_smilies'] ? InsertSmilies('', 20, 'currentInputAreaID') : '',
		'quicktags'   => $config['use_bbcodes'] ? QuickTags('currentInputAreaID', 'news') : '',
		'approve'     => $row['approve'],
		'flags'       => array(
			'edit_split'          => $config['news.edit.split'] ? true : false,
			'meta'                => $config['meta'] ? true : false,
			'mainpage'            => $row['mainpage'] ? true : false,
			'favorite'            => $row['favorite'] ? true : false,
			'pinned'              => $row['pinned'] ? true : false,
			'catpinned'           => $row['catpinned'] ? true : false,
			'can_mainpage'        => $perm[$permGroupMode . '.mainpage'] ? true : false,
			'can_pinned'          => $perm[$permGroupMode . '.pinned'] ? true : false,
			'can_catpinned'       => $perm[$permGroupMode . '.catpinned'] ? true : false,
			'raw'                 => ($row['flags'] & 1),
			'html'                => ($row['flags'] & 2),
			'extended_more'       => ($config['extended_more'] || ($tvars['vars']['content.delimiter'] != '')) ? true : false,
			'editable'            => (($perm[$permGroupMode . '.modify' . (($row['approve'] == 1) ? '.published' : '')]) || ($perm[$permGroupMode . '.unpublish'])) ? true : false,
			'deleteable'          => ($perm[$permGroupMode . '.delete' . (($row['approve'] == 1) ? '.published' : '')]) ? true : false,
			'html.lost'           => (($row['flags'] & 2) && (!$perm[$permGroupMode . '.html'])) ? 1 : 0,
			'mainpage.lost'       => (($row['mainpage']) && (!$perm[$permGroupMode . '.mainpage'])) ? true : false,
			'pinned.lost'         => (($row['pinned']) && (!$perm[$permGroupMode . '.pinned'])) ? true : false,
			'catpinned.lost'      => (($row['catpinned']) && (!$perm[$permGroupMode . '.catpinned'])) ? true : false,
			'publish.lost'        => (($row['approve'] == 1) && (!$perm[$permGroupMode . '.modify.published'])) ? true : false,
			'favorite.lost'       => (($row['favorite']) && (!$perm[$permGroupMode . '.favorite'])) ? true : false,
			'multicat.lost'       => ((count($cats) > 1) && (!$perm[$permGroupMode . '.multicat'])) ? true : false,
			'html.disabled'       => (!$perm[$permGroupMode . '.html']) ? true : false,
			'customdate.disabled' => (!$perm[$permGroupMode . '.customdate']) ? true : false,
			'mainpage.disabled'   => (!$perm[$permGroupMode . '.mainpage']) ? true : false,
			'pinned.disabled'     => (!$perm[$permGroupMode . '.pinned']) ? true : false,
			'catpinned.disabled'  => (!$perm[$permGroupMode . '.catpinned']) ? true : false,
			'favorite.disabled'   => (!$perm[$permGroupMode . '.favorite']) ? true : false,
			'setviews.disabled'   => (!$perm[$permGroupMode . '.setviews']) ? true : false,
			'multicat.disabled'   => (!$perm[$permGroupMode . '.multicat']) ? true : false,
			'altname.disabled'    => (!$perm[$permGroupMode . '.altname']) ? true : false,
			'multicat.show'       => $perm['personal.multicat'],
			'mondatory_cat'       => (!$perm['personal.nocat']) ? true : false,
		)
	);
	$tVars['flags']['can_publish'] = ((($row['approve'] == 1) && ($perm[$permGroupMode . '.modify.published'])) || (($row['approve'] < 1) && $perm[$permGroupMode . '.publish'])) ? 1 : 0;
	$tVars['flags']['can_unpublish'] = (($row['approve'] < 1) || ($perm[$permGroupMode . '.unpublish'])) ? 1 : 0;
	$tVars['flags']['can_draft'] = (($row['approve'] == -1) || ($perm[$permGroupMode . '.unpublish'])) ? 1 : 0;
	$tVars['flags']['params.lost'] = ($tVars['flags']['publish.lost'] || $tVars['flags']['html.lost'] || $tVars['flags']['mainpage.lost'] || $tVars['flags']['pinned.lost'] || $tVars['flags']['catpinned.lost'] || $tVars['flags']['multicat.lost']) ? 1 : 0;
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
		foreach ($PFILTERS['news'] as $k => $v) {
			$v->editNewsForm($id, $row, $tVars);
		}
	// Determine paths for all template files
	$tpath = locatePluginTemplates(array($tpl_name), 'nsm', pluginGetVariable('nsm', 'localsource'));
	$xt = $twig->loadTemplate($tpath[$tpl_name] . $tpl_name . '.tpl');
	$template['vars']['mainblock'] .= $xt->render($tVars);
}

function plugin_nsm_del() {

	global $lang, $mysql, $userROW;
	// Load permissions
	$permPlugin = checkPermission(array('plugin' => 'nsm', 'item' => ''), null, array(
		'delete.draft',
		'delete.unpublished',
		'delete.published',
	));
	$perm = checkPermission(array('plugin' => '#admin', 'item' => 'news'), null, array(
		'personal.delete',
		'personal.delete.published',
	));
	$id = intval($_REQUEST['id']);
	// Try to find news that we're trying to edit
	if (!is_array($row = $mysql->record("select * from " . prefix . "_news where (id = " . db_squote($id) . ") and (author_id = " . db_squote($userROW['id']) . ")", 1))) {
		msg(array("type" => "error", "text" => $lang['msge_not_found']));

		return;
	}
	// Check permissions
	if ((($row['approve'] == -1) && (!$permPlugin['delete.draft'])) ||
		(($row['approve'] == 0) && (!$permPlugin['delete.unpublished'])) ||
		(($row['approve'] == 1) && (!$permPlugin['delete.published']))
	) {
		msg(array("type" => "error", "text" => 'xx' . $lang['perm.denied']));

		return;
	}
	// Load library
	require_once(root . 'includes/classes/upload.class.php');
	require_once(root . 'includes/inc/file_managment.php');
	require_once(root . 'includes/inc/lib_admin.php');
	LoadLang('editnews', 'admin', 'editnews');
	massDeleteNews(array($row['id']));
	// Show again list of news
	plugin_nsm();
}

