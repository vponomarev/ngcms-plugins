<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

// Load lang files
LoadPluginLang('xfields', 'config');

include_once root.'plugins/xfields/xfields.php';

if (!is_array($xf = xf_configLoad()))
	$xf = array();

//
// Управление необходимыми действиями
$sectionID= $_REQUEST['section'];
if (!in_array($sectionID, array('news', 'grp.news', 'users', 'grp.users'))) {
	$sectionID = 'news';
}
switch ($_REQUEST['action']) {
	case 'add'		:	showAddEditForm(); break;
    case 'doadd'	:	doAddEdit(); break;
	case 'edit'		:	showAddEditForm(); break;
	case 'doedit'	:	doAddEdit(); break;
	case 'update'	:	doUpdate(); showList(); break;
	default			:	showList();
}


//
// Показать список полей
function showList(){
	global $sectionID;

	if (in_array($sectionID, array('grp.news', 'grp.users'))) {
		showSectionList();
	} else {
		showFieldList();
	}
}

//
//
function showSectionList(){
	global $xf, $lang, $tpl, $sectionID;

	$output = '';
	$output .= "<pre>".var_export($xf[$sectionID], true)."</pre>";

	$tvars = array();
	$tvars['vars']['entries'] = '';// $output;

	// Prepare data
	$grpNews = array();
	foreach ($xf['grp.news'] as $k => $v) {
		$grpNews[$k] = array(
			'title' => iconv('Windows-1251', 'UTF-8',$v['title']),
			'entries' => $v['entries'],
			);
	}

	foreach (array('news', 'grp.news', 'users', 'grp.users') as $cID)
		$tvars['vars']['bclass.'.$cID] = ($cID == $sectionID)?'btnActive':'btnInactive';

	$tvars['vars']['json.groups.config']	= json_encode($grpNews);
	$tvars['vars']['json.fields.config']	= json_encode(arrayCharsetConvert(0, $xf['news']));
	$tpl -> template('groups', extras_dir.'/xfields/tpl');
	$tpl -> vars('groups', $tvars);
	echo $tpl -> show('groups');

}

//
// Показать список доп. полей
function showFieldList(){
	global $xf, $lang, $tpl, $sectionID;

	$output = '';
	foreach ($xf[$sectionID] as $id => $data) {
		unset($tvars);

		$storage = '';
		if ($data['storage']) {
			$storage = '<br/><font color="red"><b>'.$data['db.type'].($data['db.len']?(' ('.$data['db.len'].')'):'').'</b> </font>';
		}

		$tvars['vars'] = array( 'name'	=> $id,
								'title'	=> $data['title'],
								'type'	=> $lang['xfields_type_'.$data['type']].$storage,
								'required'	=> $data['required']?'<font color="red"><b>Да</b></font>':'Нет',
								'default'	=> $data['default']?$data['default']:'<font color="red">не задано</font>',
								'[link]'	=> '<a href="?mod=extra-config&plugin=xfields&action=edit&section='.$sectionID.'&field='.$id.'">',
								'[/link]'	=> '</a>',
								'[linkup]'	=> '<a href="?mod=extra-config&plugin=xfields&action=update&subaction=up&section='.$sectionID.'&field='.$id.'">',
								'[/linkup]'	=> '</a>',
								'[linkdown]'	=> '<a href="?mod=extra-config&plugin=xfields&action=update&subaction=down&section='.$sectionID.'&field='.$id.'">',
								'[/linkdown]'	=> '</a>',
								'[linkdel]'	=> '<a href="?mod=extra-config&plugin=xfields&action=update&subaction=del&section='.$sectionID.'&field='.$id.'" onclick="return confirm('."'".$lang['xfields_suretest']."'".');">',
								'[/linkdel]'	=> '</a>',
								);
		$options = '';
		if (is_array($data['options']) && count($data['options'])) {
			foreach ($data['options'] as $k => $v)
				$options .= (($data['storekeys'])?('<b>'.$k.'</b>: '.$v):('<b>'.$v.'</b>'))."<br>\n";
		}
		$tvars['vars']['options'] = $options;

		$tpl -> template('config_rows', extras_dir.'/xfields/tpl');
		$tpl -> vars('config_rows', $tvars);
		$output .= $tpl -> show('config_rows');
	}

	if (!count($xf[$sectionID]))
		$output = $lang['xfields_nof'];


	$tvars = array ( 'vars' => array(
		'entries' => $output,
		'section_name' => ($sectionID == 'news')?'Новости':'Пользователи',
		'sectionID' => $sectionID
	));
	foreach (array('news', 'grp.news', 'users', 'grp.users') as $cID)
		$tvars['vars']['bclass.'.$cID] = ($cID == $sectionID)?'btnActive':'btnInactive';

	$tpl -> template('config', extras_dir.'/xfields/tpl');
	$tpl -> vars('config', $tvars);
	echo $tpl -> show('config');
}


//
//
function showAddEditForm($xdata = '', $eMode = NULL, $efield = NULL){
	global $xf, $lang, $tpl, $sectionID;

	$field = ($efield == NULL)?$_REQUEST['field']:$efield;

	if ($eMode == NULL) {
		$editMode = (is_array($xf[$sectionID][$field]))?1:0;
	} else {
		$editMode = $eMode;
	}

	$tvars = array ();


	if ($editMode) {
		//$tvars['regx']["'\[add\](.*?)\[/add\]'si"] = $counter?'$1':'';
		$tvars['regx']["'\[add\](.*?)\[/add\]'si"] = '';
		$tvars['regx']["'\[edit\](.*?)\[/edit\]'si"] = '$1';

		$data = is_array($xdata)?$xdata:$xf[$sectionID][$field];
		$tvars['vars']['id'] = $field;
		$tvars['vars']['title'] = $data['title'];
		$tvars['vars']['type'] = $data['type'];
		$tvars['vars']['storage'] = intval($data['storage']);
		$tvars['vars']['db.type'] = $data['db.type'];
		$tvars['vars']['db.len'] = (intval($data['db.len'])>0)?intval($data['db.len']):'';

		$xsel = '';
		foreach (array('text', 'textarea', 'select') as $ts) {
			$tvars['vars'][$ts.'_default'] = ($data['type'] == $ts)?$data['default']:'';
			$xsel .= '<option value="'.$ts.'"'.(($data['type'] == $ts)?' selected':'').'>'.$lang['xfields_type_'.$ts];
		}

		$sOpts = array();
		$fNum = 1;
		if ($data['type'] == 'select') {
			if (is_array($data['options']))
				foreach ($data['options'] as $k => $v) {
					array_push($sOpts, '<tr><td><input size="12" name="so_data['.($fNum).'][0]" type="text" value="'.($data['storekeys']?htmlspecialchars($k):'').'"/></td><td><input type="text" size="55" name="so_data['.($fNum).'][1]" value="'.htmlspecialchars($v).'"/></td><td><a href="#"><img src="{skins_url}/images/delete.gif" alt="DEL" width="12" height="12" /></a></td></tr>');
					$fNum++;
				}
		}
		if (!count($sOpts)) {
			array_push($sOpts, '<tr><td><input size="12" name="so_data[1][0]" type="text" value=""/></td><td><input type="text" size="55" name="so_data[1][1]" value=""/></td><td><a href="#"><img src="{skins_url}/images/delete.gif" alt="DEL" width="12" height="12" /></a></td></tr>');
		}
		$tvars['vars']['sOpts'] = implode("\n", $sOpts);

		$tvars['vars']['type_opts'] = $xsel;
		$tvars['vars']['storekeys_opts'] = '<option value="0">Сохранять значение</option><option value="1"'.(($data['storekeys'])?' selected':'').'>Сохранять код</option>';
		$tvars['vars']['required_opts'] = '<option value="0">Нет</option><option value="1"'.(($data['required'])?' selected':'').'>Да</option>';
	} else {
		$tvars['regx']["'\[add\](.*?)\[/add\]'si"] = '$1';
		$tvars['regx']["'\[edit\](.*?)\[/edit\]'si"] = '';

		$tvars['vars']['id'] = '';
		$tvars['vars']['title'] = '';
		$tvars['vars']['type'] = 'text';

		$tvars['vars']['storage'] = '0';
		$tvars['vars']['db.type'] = '';
		$tvars['vars']['db.len'] = '';

		$xsel = '';
		foreach (array('text', 'textarea', 'select') as $ts) {
			$tvars['vars'][$ts.'_default'] = '';
			$xsel .= '<option value="'.$ts.'"'.(($data['type'] == 'text')?' selected':'').'>'.$lang['xfields_type_'.$ts];
		}
		$tvars['vars']['type_opts'] = $xsel;
		$tvars['vars']['storekeys_opts'] = '<option value="0">Сохранять значение</option><option value="1">Сохранять код</option>';
		$tvars['vars']['required_opts'] = '<option value="0">Нет</option><option value="1">Да</option>';

		$tvars['vars']['select_options'] = '';
	}
	$tvars['vars']['sectionID'] = $sectionID;

	$tpl -> template('config_edit', extras_dir.'/xfields/tpl');
	$tpl -> vars('config_edit', $tvars);
	echo $tpl -> show('config_edit');
}


//
//
function doAddEdit() {
	global $xf, $XF, $lang, $tpl, $mysql, $sectionID;
print "<pre>".var_export($_POST, true)."</pre>";
	$error = 0;

	$field = $_REQUEST['id'];
	$editMode = $_REQUEST['edit']?1:0;

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
	$data['title']		= $_REQUEST['title'];
	$data['required']	= intval($_REQUEST['required']);
	$data['type']		= $_REQUEST['type'];
	$data['default']	= '';

	switch ($data['type']) {
		case 'text':
			if ($_REQUEST['text_default'] != '')
				$data['default'] = $_REQUEST['text_default'];
			break;
		case 'textarea':
			if ($_REQUEST['textarea_default'] != '')
				$data['default'] = $_REQUEST['textarea_default'];
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

			$data['storekeys'] = intval($_REQUEST['select_storekeys'])?1:0;

			$data['options'] = $optlist;
			if (trim($_REQUEST['select_default'])) {
				$data['default'] = trim($_REQUEST['select_default']);
				if (
					(( $data['storekeys']) && (!array_key_exists($data['default'], $optlist))) ||
					((!$data['storekeys']) && (!$optvals[$data['default']]))
				   ) {
					msg(array("type" => "error", "text" => $lang['xfields_msge_errdefault']));
					$error = 1;
				}
			}

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
	$data['storage']	= $_REQUEST['storage'];
	$data['db.type']	= $_REQUEST['db_type'];
	$data['db.len']		= intval($_REQUEST['db_len']);

	if ($data['storage']) {
		// Check for correct DB type
		if (!in_array($data['db.type'], array('int', 'char', 'datetime'))) {
			msg(array("type" => "error", "text" => $lang['xfields_error.db.type']));
			$error = 1;
		}

		// Check for correct DB len (if applicable)
		if (($data['db.type'] == 'char')&&((intval($data['db.len'])<1)||(intval($data['db.len'])>255))) {
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
	$tableName = '';
	if ($sectionID == 'news') {
		$tableName = prefix.'_news';
	} else if ($sectionID == 'users') {
		$tableName = uprefix.'_users';
	} else {
		print 'Ошибка: неизвестная секция/блок ('.$sectionID.')';
		return;
	}

	$found = 0;
	foreach ($mysql->select("describe ".$tableName, 1) as $row) {
		if ($row['Field'] == 'xfields_'.$field) {
			$found = 1;
			break;
		}
	}

	$dbFlagChanged = 0;

	// 1. If we add XFIELD and field already exists in DB - drop it!
	// 2. If we don't want to store data in separate field - drop it!
	if ($found && (!$editMode || !$DB['new']['storage'])) {
		$mysql->query("alter table ".$tableName." drop column `xfields_".$field."`");
	}

	// If we need to have this field - let's make it. But only if smth was changed
	do {
		if (!$data['storage']) break;
		// Anything should be done only if field is changed
		if (($DB['old']['db.type'] == $DB['new']['db.type'])&&($DB['old']['db.len'] == $DB['new']['db.len'])) break;

		$ftype = '';
		switch ($DB['new']['db.type']) {
			case 'int':			$ftype = 'int'; break;
			case 'datetime':	$ftype = 'datetime'; break;
			case 'char':		if (($DB['new']['db.len'] > 0)&&($DB['new']['db.len'] <= 255)) { $ftype = 'char('.intval($DB['new']['db.len']).')'; break; }
		}

		if ($ftype) {
			$dbFlagChanged = 1;
			if ($found) {
				$mysql->query("alter table ".$tableName." change column `xfields_".$field.'` `xfields_'.$field.'` '.$ftype);
				$mysql->query("update ".$tableName." set `xfields_".$field."` = NULL");
			} else {
				$mysql->query("alter table ".$tableName." add column `xfields_".$field.'` '.$ftype);
			}
		}
	} while(0);



	// Second - fill field's content if required
	if ($DB['new']['storage'] && $dbFlagChanged) {
		// Make updates with chunks for 500 RECS
		$recCount = 0;
		$maxID = 0;
		do {
			$recCount = 0;
			foreach ($mysql->select("select id, xfields from ".$tableName." where (id > ".$maxID.") and (xfields is not NULL) and (xfields <> '') order by id limit 500") as $rec) {
				$recCount++;
				if ($rec['id'] > $maxID) $maxID = $rec['id'];
				$xlist = xf_decode($rec['xfields']);
				if (isset($xlist[$field]) && ($xlist[$field] != '')) {
					$mysql->query("update ".$tableName." set `xfields_".$field."` = ".db_squote($xlist[$field])." where id = ".db_squote($rec['id']));
				}
			}
		} while ($recCount);



	}


	$tvars = array ( 'vars' => array ( 'id' => $field));
	$tvars['regx']["'\[edit\](.*?)\[/edit\]'si"] = ($editMode)?'$1':'';
	$tvars['vars']['sectionID'] = $sectionID;

	$tpl -> template('config_done', extras_dir.'/xfields/tpl');
	$tpl -> vars('config_done', $tvars);
	echo $tpl -> show('config_done');

}


//
//
function doUpdate() {
	global $xf, $XF, $lang, $tpl, $sectionID;

	$error = 0;
	$field = $_REQUEST['field'];

	// Check if field exists or not [depends on mode]
	if (!is_array($xf[$sectionID][$field])) {
		msg(array("type" => "error", "text" => $lang['xfields_msge_noexists']. '('.$sectionID.': '.$field.')'));
		$error = 1;
	};

	$notif = '';
	switch ($_REQUEST['subaction']) {
		case 'del':		unset($XF[$sectionID][$field]);
						$notif = $lang['xfields_done_del'];
						break;
		case 'up':		array_key_move($XF[$sectionID], $field, -1);
						$notif = $lang['xfields_done_up'];
						break;
		case 'down':	array_key_move($XF[$sectionID], $field, 1);
						$notif = $lang['xfields_done_down'];
						break;
		default:		$notif = $lang['xfields_updateunk'];
	}

	if (!xf_configSave()) {
		msg(array("type" => "error", "text" => $lang['xfields_msge_errcsave']));
		return;
	}

	$xf = $XF;
//
//	$tvars = array ( 'vars' => array ( 'id' => $field, 'text' => $notif));
//	$tpl -> template('config_updated', extras_dir.'/xfields/tpl');
//	$tpl -> vars('config_updated', $tvars);
//	echo $tpl -> show('config_updated');
}


function array_key_move(&$arr, $key, $offset) {
 $keys = array_keys($arr);
 $index = -1;
 foreach ($keys as $k => $v) if ($v == $key) { $index = $k; break; }
 if ($index == -1) return 0;
 $index2 = $index + $offset;
 if ($index2 < 0) $index2 = 0;
 if ($index2 > (count($arr)-1)) $index2 = count($arr)-1;
 if ($index == $index2)	return 1;

 $a = min($index, $index2);
 $b = max($index, $index2);

 $arr = array_slice($arr, 0, $a) +
 	array_slice($arr, $b, 1) +
 	array_slice($arr, $a+1, $b-$a) +
 	array_slice($arr, $a, 1) +
 	array_slice($arr, $b, count($arr) - $b);
}