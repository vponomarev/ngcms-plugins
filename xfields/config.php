<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
// Load lang files
LoadPluginLang('xfields', 'config');
LoadPluginLang('xfields', 'config', '', 'xfconfig', '#');
include_once root . 'plugins/xfields/xfields.php';
if (!is_array($xf = xf_configLoad()))
	$xf = array();
//
// Управление необходимыми действиями
$sectionID = $_REQUEST['section'];
if (!in_array($sectionID, array('news', 'grp.news', 'users', 'grp.users', 'tdata'))) {
	$sectionID = 'news';
}
switch ($_REQUEST['action']) {
	case 'add'        :
		showAddEditForm();
		break;
	case 'doadd'    :
		doAddEdit();
		break;
	case 'edit'        :
		showAddEditForm();
		break;
	case 'doedit'    :
		doAddEdit();
		break;
	case 'update'    :
		doUpdate();
		showList();
		break;
	default            :
		showList();
}
//
// Показать список полей
function showList() {

	global $sectionID;
	if (in_array($sectionID, array('grp.news', 'grp.users'))) {
		showSectionList();
	} else {
		showFieldList();
	}
}

//
//
function showSectionList() {

	global $xf, $lang, $tpl, $twig, $sectionID;
	$output = '';
	//$output .= "<pre>".var_export($xf[$sectionID], true)."</pre>";
	$tVars = array(
		'section_name' => $lang['xfconfig']['section.' . $sectionID],
	);
	// Prepare data
	$grpNews = array();
	foreach ($xf['grp.news'] as $k => $v) {
		$grpNews[$k] = array(
			'title'   => iconv('Windows-1251', 'UTF-8', $v['title']),
			'entries' => $v['entries'],
		);
	}
	foreach (array('news', 'grp.news', 'users', 'grp.users', 'tdata') as $cID)
		$tVars['bclass'][$cID] = ($cID == $sectionID) ? 'btnActive' : 'btnInactive';
	$tVars['json']['groups.config'] = json_encode($grpNews);
	$tVars['json']['fields.config'] = json_encode(arrayCharsetConvert(0, $xf['news']));
	$xt = $twig->loadTemplate('plugins/xfields/tpl/groups.tpl');
	echo $xt->render($tVars);
}

//
// Показать список доп. полей
function showFieldList() {

	global $xf, $lang, $twig, $sectionID;
	$xEntries = array();
	$output = '';
	foreach ($xf[$sectionID] as $id => $data) {
		$storage = '';
		if ($data['storage']) {
			$storage = '<br/><font color="red"><b>' . $data['db.type'] . ($data['db.len'] ? (' (' . $data['db.len'] . ')') : '') . '</b> </font>';
		}
		$xEntry = array(
			'name'     => $id,
			'title'    => $data['title'],
			'type'     => $lang['xfconfig']['type_' . $data['type']] . $storage,
			'default'  => (($data['type'] == "checkbox") ? ($data['default'] ? $lang['yesa'] : $lang['noa']) : ($data['default'])),
			'link'     => '?mod=extra-config&plugin=xfields&action=edit&section=' . $sectionID . '&field=' . $id,
			'linkup'   => '?mod=extra-config&plugin=xfields&action=update&subaction=up&section=' . $sectionID . '&field=' . $id,
			'linkdown' => '?mod=extra-config&plugin=xfields&action=update&subaction=down&section=' . $sectionID . '&field=' . $id,
			'linkdel'  => '?mod=extra-config&plugin=xfields&action=update&subaction=del&section=' . $sectionID . '&field=' . $id,
			'area'     => (intval($data['area']) > 0) ? intval($data['area']) : '',
			'flags'    => array(
				'required' => $data['required'] ? true : false,
				'default'  => (($data['default'] != '') || ($data['type'] == "checkbox")) ? true : false,
				'disabled' => $data['disabled'] ? true : false,
				'regpage'  => $data['regpage'] ? true : false,
			),
		);
		$options = '';
		if (is_array($data['options']) && count($data['options'])) {
			foreach ($data['options'] as $k => $v)
				$options .= (($data['storekeys']) ? ('<b>' . $k . '</b>: ' . $v) : ('<b>' . $v . '</b>')) . "<br>\n";
		}
		$xEntry['options'] = $options;
		$xEntries [] = $xEntry;
	}
	if (!count($xf[$sectionID]))
		$output = $lang['xfconfig']['nof'];
	$tVars = array(
		'entries'      => $xEntries,
		'section_name' => $lang['xfconfig']['section.' . $sectionID],
		'sectionID'    => $sectionID,
	);
	foreach (array('news', 'grp.news', 'users', 'grp.users', 'tdata') as $cID)
		$tVars['bclass'][$cID] = ($cID == $sectionID) ? 'btnActive' : 'btnInactive';
	$xt = $twig->loadTemplate('plugins/xfields/tpl/config.tpl');
	echo $xt->render($tVars);
}

//
//
function showAddEditForm($xdata = '', $eMode = null, $efield = null) {

	global $xf, $lang, $sectionID, $twig;
	$field = ($efield == null) ? $_REQUEST['field'] : $efield;
	if ($eMode == null) {
		$editMode = (is_array($xf[$sectionID][$field])) ? 1 : 0;
	} else {
		$editMode = $eMode;
	}
	$tVars = array();
	if ($editMode) {
		$data = is_array($xdata) ? $xdata : $xf[$sectionID][$field];
		$tVars['flags']['editMode'] = 1;
		$tVars['flags']['disabled'] = $data['disabled'] ? true : false;
		$tVars['flags']['regpage'] = $data['regpage'] ? true : false;
		$tVars = $tVars + array(
				'id'           => $field,
				'title'        => $data['title'],
				'type'         => $data['type'],
				'storage'      => intval($data['storage']),
				'db_type'      => $data['db.type'],
				'db_len'       => (intval($data['db.len']) > 0) ? intval($data['db.len']) : '',
				'area'         => (intval($data['area']) > 0) ? intval($data['area']) : '',
				'bb_support'   => $data['bb_support'] ? 'checked="checked"' : '',
				'html_support' => $data['html_support'] ? 'checked="checked"' : '',
				'noformat'     => $data['noformat'] ? 'checked="checked"' : '',
			);
		$xsel = '';
		foreach (array('text', 'textarea', 'select', 'multiselect', 'checkbox', 'images') as $ts) {
			$tVars['defaults'][$ts] = ($data['type'] == $ts) ? (($ts == "checkbox") ? ($data['default'] ? ' checked="checked"' : '') : $data['default']) : '';
			$xsel .= '<option value="' . $ts . '"' . (($data['type'] == $ts) ? ' selected' : '') . '>' . $lang['xfields_type_' . $ts];
		}
		$sOpts = array();
		$fNum = 1;
		if ($data['type'] == 'select') {
			if (is_array($data['options']))
				foreach ($data['options'] as $k => $v) {
					array_push($sOpts, '<tr><td><input size="12" name="so_data[' . ($fNum) . '][0]" type="text" value="' . ($data['storekeys'] ? htmlspecialchars($k, ENT_COMPAT | ENT_HTML401, 'cp1251') : '') . '"/></td><td><input type="text" size="55" name="so_data[' . ($fNum) . '][1]" value="' . htmlspecialchars($v, ENT_COMPAT | ENT_HTML401, 'cp1251') . '"/></td><td><a href="#" onclick="return false;"><img src="' . skins_url . '/images/delete.gif" alt="DEL" width="12" height="12" /></a></td></tr>');
					$fNum++;
				}
		}
		if (!count($sOpts)) {
			array_push($sOpts, '<tr><td><input size="12" name="so_data[1][0]" type="text" value=""/></td><td><input type="text" size="55" name="so_data[1][1]" value=""/></td><td><a href="#" onclick="return false;"><img src="' . skins_url . '/images/delete.gif" alt="DEL" width="12" height="12" /></a></td></tr>');
		}
		$m_sOpts = array();
		$fNum = 1;
		if ($data['type'] == 'multiselect') {
			if (is_array($data['options']))
				foreach ($data['options'] as $k => $v) {
					array_push($m_sOpts, '<tr><td><input size="12" name="mso_data[' . ($fNum) . '][0]" type="text" value="' . ($data['storekeys'] ? htmlspecialchars($k, ENT_COMPAT | ENT_HTML401, 'cp1251') : '') . '"/></td><td><input type="text" size="55" name="mso_data[' . ($fNum) . '][1]" value="' . htmlspecialchars($v, ENT_COMPAT | ENT_HTML401, 'cp1251') . '"/></td><td><a href="#" onclick="return false;"><img src="' . skins_url . '/images/delete.gif" alt="DEL" width="12" height="12" /></a></td></tr>');
					$fNum++;
				}
		}
		if (!count($m_sOpts)) {
			array_push($m_sOpts, '<tr><td><input size="12" name="mso_data[1][0]" type="text" value=""/></td><td><input type="text" size="55" name="mso_data[1][1]" value=""/></td><td><a href="#" onclick="return false;"><img src="' . skins_url . '/images/delete.gif" alt="DEL" width="12" height="12" /></a></td></tr>');
		}
		$tVars = $tVars + array(
				'sOpts'          => implode("\n", $sOpts),
				'm_sOpts'        => implode("\n", $m_sOpts),
				'type_opts'      => $xsel,
				'storekeys_opts' => '<option value="0">Сохранять значение</option><option value="1"' . (($data['storekeys']) ? ' selected' : '') . '>Сохранять код</option>',
				'required_opts'  => '<option value="0">Нет</option><option value="1"' . (($data['required']) ? ' selected' : '') . '>Да</option>',
				'images'         => array(
					'maxCount'    => intval($data['maxCount']),
					'thumbWidth'  => intval($data['thumbWidth']),
					'thumbHeight' => intval($data['thumbHeight']),
				),
			);
		foreach (array('imgStamp', 'imgShadow', 'imgThumb', 'thumbStamp', 'thumbShadow') as $k) {
			$tVars['images'][$k] = intval($data[$k]) ? 'checked="checked"' : '';
		}
		//print "<pre>".var_export($tVars, true)."</pre>";
	} else {
		$sOpts = array();
		array_push($sOpts, '<tr><td><input size="12" name="so_data[1][0]" type="text" value=""/></td><td><input type="text" size="55" name="so_data[1][1]" value=""/></td><td><a href="#" onclick="return false;"><img src="' . skins_url . '/images/delete.gif" alt="DEL" width="12" height="12" /></a></td></tr>');
		$m_sOpts = array();
		array_push($m_sOpts, '<tr><td><input size="12" name="mso_data[1][0]" type="text" value=""/></td><td><input type="text" size="55" name="mso_data[1][1]" value=""/></td><td><a href="#" onclick="return false;"><img src="' . skins_url . '/images/delete.gif" alt="DEL" width="12" height="12" /></a></td></tr>');
		$tVars['flags']['editmode'] = 0;
		$tVars['flags']['disabled'] = false;
		$tVars = $tVars + array(
				'sOpts'   => implode("\n", $sOpts),
				'm_sOpts' => implode("\n", $m_sOpts),
				'id'      => '',
				'title'   => '',
				'type'    => 'text',
				'storage' => '0',
				'db_type' => '',
				'db_len'  => '',
			);
		$xsel = '';
		foreach (array('text', 'textarea', 'select', 'multiselect', 'checkbox', 'images') as $ts) {
			$tVars['defaults'][$ts] = '';
			$xsel .= '<option value="' . $ts . '"' . (($data['type'] == 'text') ? ' selected' : '') . '>' . $lang['xfields_type_' . $ts];
		}
		$tVars = $tVars + array(
				'type_opts'      => $xsel,
				'storekeys_opts' => '<option value="0">Сохранять значение</option><option value="1">Сохранять код</option>',
				'required_opts'  => '<option value="0">Нет</option><option value="1">Да</option>',
				'select_options' => '',
				'images' => array(
					'maxCount'    => '1',
					'thumbWidth'  => '150',
					'thumbHeight' => '150',
				),
			);
		foreach (array('imgStamp', 'imgShadow', 'imgThumb', 'thumbStamp', 'thumbShadow') as $k) {
			$tVars['images'][$k] = '';
		}
	}
	$tVars['sectionID'] = $sectionID;
	$xt = $twig->loadTemplate('plugins/xfields/tpl/config_edit.tpl');
	echo $xt->render($tVars);
}

//
//
function doAddEdit() {

	global $xf, $XF, $lang, $tpl, $twig, $mysql, $sectionID;
	//print "<pre>".var_export($_POST, true)."</pre>";
	$error = 0;
	$field = $_REQUEST['id'];
	$editMode = $_REQUEST['edit'] ? 1 : 0;
	// Check if field exists or not [depends on mode]
	if ($editMode && (!is_array($xf[$sectionID][$field]))) {
		msg(array("type" => "error", "text" => $lang['xfields_msge_noexists']));
		$error = 1;
	} elseif (!$editMode && (is_array($xf[$sectionID][$field]))) {
		msg(array("type" => "error", "text" => $lang['xfields_msge_exists']));
		$error = 1;
	}
	// Check if Field name fits our requirements
	if (!$editMode) {
		if (!preg_match('/^[a-z]{1}[a-z0-9]{2}[a-z0-9]*$/', $field)) {
			msg(array("type" => "error", "text" => $lang['xfields_msge_format']));
			$error = 1;
		}
	}
	// Let's fill parameters
	$data['title'] = $_REQUEST['title'];
	$data['required'] = intval($_REQUEST['required']);
	$data['disabled'] = intval($_REQUEST['disabled']);
	$data['area'] = intval($_REQUEST['area']);
	$data['type'] = $_REQUEST['type'];
	$data['bb_support'] = $_REQUEST['bb_support'] ? 1 : 0;
	$data['default'] = '';
	if (($sectionID == 'users') && ($data['type'] != 'images'))
		$data['regpage'] = intval($_REQUEST['regpage']);
	switch ($data['type']) {
		case 'checkbox':
			$data['default'] = $_REQUEST['checkbox_default'] ? 1 : 0;
			break;
		case 'text':
			if ($_REQUEST['text_default'] != '')
				$data['default'] = $_REQUEST['text_default'];
			$data['bb_support'] = $_REQUEST['text_bb_support'] ? 1 : 0;
			$data['html_support'] = $_REQUEST['text_html_support'] ? 1 : 0;
			break;
		case 'textarea':
			if ($_REQUEST['textarea_default'] != '')
				$data['default'] = $_REQUEST['textarea_default'];
			$data['bb_support'] = $_REQUEST['textarea_bb_support'] ? 1 : 0;
			$data['html_support'] = $_REQUEST['textarea_html_support'] ? 1 : 0;
			$data['noformat'] = $_REQUEST['textarea_noformat'] ? 1 : 0;
			break;
		case 'select':
			// Check options
			$optlist = array();
			$optvals = array();
			if (isset($_REQUEST['so_data']) && is_array($_REQUEST['so_data'])) {
				foreach ($_REQUEST['so_data'] as $k => $v) {
					if (is_array($v) && isset($v[0]) && isset($v[1]) && (($v[0] != '') || ($v[1] != ''))) {
						if ($v[0] != '') {
							$optlist[$v[0]] = $v[1];
						} else {
							$optlist[] = $v[1];
						}
						//print "<pre>SO_LINE: ".$v[0].", ".$v[1]."</pre>";
					}
				}
			}
			$opt_vals = array_values($optlist);
			/*
			$opts = $_REQUEST['select_options'];
			$optlist = array();
			$optvals = array();
			foreach (explode("\n", $opts) as $line) {
				$line = trim($line);
				if (preg_match('/^(.+?) *\=\> *(.+?)$/', $line, $match)) {
					$optlist[$match[1]] = $match[2];
					$optvals[$match[2]] = 1;
				} elseif ($line != '') {
					$optlist[] = $line;
					$optvals[$line] = 1;
				}
			}
			*/
			$data['storekeys'] = intval($_REQUEST['select_storekeys']) ? 1 : 0;
			$data['options'] = $optlist;
			if (trim($_REQUEST['select_default'])) {
				$data['default'] = trim($_REQUEST['select_default']);
				if (
					(($data['storekeys']) && (!array_key_exists($data['default'], $optlist))) ||
					((!$data['storekeys']) && (!in_array($data['default'], $optlist)))
				) {
					msg(array("type" => "error", "text" => $lang['xfields_msge_errdefault']));
					$error = 1;
				}
			}
			break;
		case 'multiselect':
			// Check options
			$optlist = array();
			$optvals = array();
			if (isset($_REQUEST['mso_data']) && is_array($_REQUEST['mso_data'])) {
				foreach ($_REQUEST['mso_data'] as $k => $v) {
					if (is_array($v) && isset($v[0]) && isset($v[1]) && (($v[0] != '') || ($v[1] != ''))) {
						if ($v[0] != '') {
							$optlist[$v[0]] = $v[1];
						} else {
							$optlist[] = $v[1];
						}
						//print "<pre>SO_LINE: ".$v[0].", ".$v[1]."</pre>";
					}
				}
			}
			$opt_vals = array_values($optlist);
			/*
			$opts = $_REQUEST['select_options'];
			$optlist = array();
			$optvals = array();
			foreach (explode("\n", $opts) as $line) {
				$line = trim($line);
				if (preg_match('/^(.+?) *\=\> *(.+?)$/', $line, $match)) {
					$optlist[$match[1]] = $match[2];
					$optvals[$match[2]] = 1;
				} elseif ($line != '') {
					$optlist[] = $line;
					$optvals[$line] = 1;
				}
			}
			*/
			$data['storekeys'] = intval($_REQUEST['select_storekeys_multi']) ? 1 : 0;
			$data['options'] = $optlist;
			if (trim($_REQUEST['select_default_multi'])) {
				$data['default'] = trim($_REQUEST['select_default_multi']);
				if (
					(($data['storekeys']) && (!array_key_exists($data['default'], $optlist))) ||
					((!$data['storekeys']) && (!in_array($data['default'], $optlist)))
				) {
					msg(array("type" => "error", "text" => $lang['xfields_msge_errdefault']));
					$error = 1;
				}
			}
			break;
		case 'images':
			$data['maxCount'] = intval($_REQUEST['images_maxCount']);
			$data['imgShadow'] = intval($_REQUEST['images_imgShadow']) ? 1 : 0;
			$data['imgStamp'] = intval($_REQUEST['images_imgStamp']) ? 1 : 0;
			$data['imgThumb'] = intval($_REQUEST['images_imgThumb']) ? 1 : 0;
			$data['thumbWidth'] = intval($_REQUEST['images_thumbWidth']);
			$data['thumbHeight'] = intval($_REQUEST['images_thumbHeight']);
			$data['thumbStamp'] = intval($_REQUEST['images_thumbStamp']) ? 1 : 0;
			$data['thumbShadow'] = intval($_REQUEST['images_thumbShadow']) ? 1 : 0;
			break;
		default:
			$data['type'] = '';
			break;
	}
	if (!$data['type']) {
		msg(array("type" => "error", "text" => $lang['xfields_msge_errtype']));
		$error = 1;
	}
	if (!$data['title']) {
		msg(array("type" => "error", "text" => $lang['xfields_msge_errtitle']));
		$error = 1;
	}
	// Check for storage params
	$data['storage'] = $_REQUEST['storage'];
	$data['db.type'] = $_REQUEST['db_type'];
	$data['db.len'] = intval($_REQUEST['db_len']);
	if ($data['storage']) {
		// Check for correct DB type
		if (!in_array($data['db.type'], array('int', 'decimal', 'char', 'datetime', 'text'))) {
			msg(array("type" => "error", "text" => $lang['xfields_error.db.type']));
			$error = 1;
		}
		// Check for correct DB len (if applicable)
		if (($data['db.type'] == 'char') && ((intval($data['db.len']) < 1) || (intval($data['db.len']) > 255))) {
			msg(array("type" => "error", "text" => $lang['xfields_error.db.len']));
			$error = 1;
		}
	}
	if ($error) {
		showAddEditForm($data, $editMode, $field, $sectionID);

		return;
	}
	$DB = array();
	$DB['new'] = array('storage' => $data['storage'], 'db.type' => $data['db.type'], 'db.len' => $data['db.len']);
	if ($editMode) {
		$DB['old'] = array('storage' => $XF[$sectionID][$field]['storage'], 'db.type' => $XF[$sectionID][$field]['db.type'], 'db.len' => $XF[$sectionID][$field]['db.len']);
	}
	$XF[$sectionID][$field] = $data;
	if (!xf_configSave()) {
		msg(array("type" => "error", "text" => $lang['xfields_msge_errcsave']));
		showAddEditForm($data, $editMode, $field);

		return;
	}
	// Now we should update table `_news` structure and content
	if (!($tableName = xf_getTableBySectionID($sectionID))) {
		print 'Ошибка: неизвестная секция/блок (' . $sectionID . ')';

		return;
	}
	$found = 0;
	foreach ($mysql->select("describe " . $tableName, 1) as $row) {
		if ($row['Field'] == 'xfields_' . $field) {
			$found = 1;
			break;
		}
	}
	$dbFlagChanged = 0;
	// 1. If we add XFIELD and field already exists in DB - drop it!
	// 2. If we don't want to store data in separate field - drop it!
	if ($found && (!$editMode || !$DB['new']['storage'])) {
		$mysql->query("alter table " . $tableName . " drop column `xfields_" . $field . "`");
	}
	// If we need to have this field - let's make it. But only if smth was changed
	do {
		if (!$data['storage']) break;
		// Anything should be done only if field is changed
		if (($DB['old']['db.type'] == $DB['new']['db.type']) && ($DB['old']['db.len'] == $DB['new']['db.len'])) break;
		$ftype = '';
		switch ($DB['new']['db.type']) {
			case 'int':
				$ftype = 'int';
				break;
			case 'decimal':
				$ftype = 'decimal (12,2)';
				break;
			case 'datetime':
				$ftype = 'datetime';
				break;
			case 'char':
				if (($DB['new']['db.len'] > 0) && ($DB['new']['db.len'] <= 255)) {
					$ftype = 'char(' . intval($DB['new']['db.len']) . ')';
					break;
				}
			case 'text':
				$ftype = 'text';
				break;
		}
		if ($ftype) {
			$dbFlagChanged = 1;
			if ($found) {
				$mysql->query("alter table " . $tableName . " change column `xfields_" . $field . '` `xfields_' . $field . '` ' . $ftype);
				$mysql->query("update " . $tableName . " set `xfields_" . $field . "` = NULL");
			} else {
				$mysql->query("alter table " . $tableName . " add column `xfields_" . $field . '` ' . $ftype);
			}
		}
	} while (0);
	// Second - fill field's content if required
	if ($DB['new']['storage'] && $dbFlagChanged) {
		// Make updates with chunks for 500 RECS
		$recCount = 0;
		$maxID = 0;
		do {
			$recCount = 0;
			foreach ($mysql->select("select id, xfields from " . $tableName . " where (id > " . $maxID . ") and (xfields is not NULL) and (xfields <> '') order by id limit 500") as $rec) {
				$recCount++;
				if ($rec['id'] > $maxID) $maxID = $rec['id'];
				$xlist = xf_decode($rec['xfields']);
				if (isset($xlist[$field]) && ($xlist[$field] != '')) {
					$mysql->query("update " . $tableName . " set `xfields_" . $field . "` = " . db_squote($xlist[$field]) . " where id = " . db_squote($rec['id']));
				}
			}
		} while ($recCount);
	}
	$tVars = array(
		'id'        => $field,
		'sectionID' => $sectionID,
		'flags'     => array(
			'editMode' => $editMode ? true : false,
		),
	);
	$tVars['sectionID'] = $sectionID;
	$xt = $twig->loadTemplate('plugins/xfields/tpl/config_done.tpl');
	echo $xt->render($tVars);
}

//
//
function doUpdate() {

	global $xf, $XF, $lang, $tpl, $mysql, $sectionID;
	$error = 0;
	$field = $_REQUEST['field'];
	// Check if field exists or not [depends on mode]
	if (!is_array($xf[$sectionID][$field])) {
		msg(array("type" => "error", "text" => $lang['xfields_msge_noexists'] . '(' . $sectionID . ': ' . $field . ')'));
		$error = 1;
	};
	$notif = '';
	switch ($_REQUEST['subaction']) {
		case 'del':        // Delete field from SQL table if required
			if (($XF[$sectionID][$field]['storage']) && ($tableName = xf_getTableBySectionID($sectionID))) {
				// Check if field really exist
				$found = 0;
				foreach ($mysql->select("describe " . $tableName, 1) as $row) {
					if ($row['Field'] == 'xfields_' . $field) {
						$found = 1;
						break;
					}
				}
				if ($found)
					$mysql->query("alter table " . $tableName . " drop column `xfields_" . $field . "`");
			}
			unset($XF[$sectionID][$field]);
			$notif = $lang['xfields_done_del'];
			break;
		case 'up':
			array_key_move($XF[$sectionID], $field, -1);
			$notif = $lang['xfields_done_up'];
			break;
		case 'down':
			array_key_move($XF[$sectionID], $field, 1);
			$notif = $lang['xfields_done_down'];
			break;
		default:
			$notif = $lang['xfields_updateunk'];
	}
	if (!xf_configSave()) {
		msg(array("type" => "error", "text" => $lang['xfields_msge_errcsave']));

		return;
	}
	$xf = $XF;
}

function array_key_move(&$arr, $key, $offset) {

	$keys = array_keys($arr);
	$index = -1;
	foreach ($keys as $k => $v) if ($v == $key) {
		$index = $k;
		break;
	}
	if ($index == -1) return 0;
	$index2 = $index + $offset;
	if ($index2 < 0) $index2 = 0;
	if ($index2 > (count($arr) - 1)) $index2 = count($arr) - 1;
	if ($index == $index2) return 1;
	$a = min($index, $index2);
	$b = max($index, $index2);
	$arr = array_slice($arr, 0, $a) +
		array_slice($arr, $b, 1) +
		array_slice($arr, $a + 1, $b - $a) +
		array_slice($arr, $a, 1) +
		array_slice($arr, $b, count($arr) - $b);
}