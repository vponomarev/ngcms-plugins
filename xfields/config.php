<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

// Load lang files
LoadPluginLang('xfields', 'config');

include_once root.'plugins/xfields/xfields.php';

if (!is_array($xf = xf_configLoad()))
	$xf = array();


switch ($_REQUEST['action']) {
	case 'add'		:	showAddEditForm(); break;
    case 'doadd'	:	doAddEdit(); break;
	case 'edit'		:	showAddEditForm(); break;
	case 'doedit'	:	doAddEdit(); break;
	case 'update'	:	doUpdate(); showList(); break;
	default			:	showList();
}



function showList(){
	global $xf, $lang, $tpl;

	$output = '';
	foreach ($xf['news'] as $id => $data) {
		unset($tvars);

		$tvars['vars'] = array( 'name'	=> $id,
								'title'	=> $data['title'],
								'type'	=> $lang['xfields_type_'.$data['type']],
								'required'	=> $data['required']?'<font color="red"><b>Да</b></font>':'Нет',
								'default'	=> $data['default']?$data['default']:'<font color="red">не задано</font>',
								'[link]'	=> '<a href="?mod=extra-config&plugin=xfields&action=edit&field='.$id.'">',
								'[/link]'	=> '</a>',
								'[linkup]'	=> '<a href="?mod=extra-config&plugin=xfields&action=update&subaction=up&field='.$id.'">',
								'[/linkup]'	=> '</a>',
								'[linkdown]'	=> '<a href="?mod=extra-config&plugin=xfields&action=update&subaction=down&field='.$id.'">',
								'[/linkdown]'	=> '</a>',
								'[linkdel]'	=> '<a href="?mod=extra-config&plugin=xfields&action=update&subaction=del&field='.$id.'" onclick="return confirm('."'".$lang['xfields_suretest']."'".');">',
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

	if (!count($xf['news']))
		$output = $lang['xfields_nof'];


	$tvars = array ( 'vars' => array( 'entries' => $output));
	$tpl -> template('config', extras_dir.'/xfields/tpl');
	$tpl -> vars('config', $tvars);
	echo $tpl -> show('config');
}


//
//
function showAddEditForm($xdata = '', $eMode = NULL, $efield = NULL){
	global $xf, $lang, $tpl;

	$field = ($efield == NULL)?$_REQUEST['field']:$efield;

	if ($eMode == NULL) {
		$editMode = (is_array($xf['news'][$field]))?1:0;
	} else {
		$editMode = $eMode;
	}

	$tvars = array ();


	if ($editMode) {
		//$tvars['regx']["'\[add\](.*?)\[/add\]'si"] = $counter?'$1':'';
		$tvars['regx']["'\[add\](.*?)\[/add\]'si"] = '';
		$tvars['regx']["'\[edit\](.*?)\[/edit\]'si"] = '$1';

		$data = is_array($xdata)?$xdata:$xf['news'][$field];
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

		$sopts = array();
		if ($data['type'] == 'select') {
			if (is_array($data['options']))
				foreach ($data['options'] as $k => $v) {
					array_push($sopts, ($data['storekeys']?$k.' => ':'').$v);
				}
		}
		$tvars['vars']['select_options'] = implode("\n", $sopts);

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

	$tpl -> template('config_edit', extras_dir.'/xfields/tpl');
	$tpl -> vars('config_edit', $tvars);
	echo $tpl -> show('config_edit');
}


//
//
function doAddEdit() {
	global $xf, $XF, $lang, $tpl, $mysql;

	$error = 0;

	$field = $_REQUEST['id'];
	$editMode = $_REQUEST['edit']?1:0;

	// Check if field exists or not [depends on mode]
	if ($editMode && (!is_array($xf['news'][$field]))) {
		msg(array("type" => "error", "text" => $lang['xfields_msge_noexists']));
		$error = 1;
	} elseif (!$editMode && (is_array($xf['news'][$field]))) {
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
		showAddEditForm($data, $editMode, $field);
		return;
	}

	$DB = array();
	$DB['new'] = array('storage' => $data['storage'], 'db.type' => $data['db.type'], 'db.len' => $data['db.len']);
	if ($editMode) {
		$DB['old'] = array('storage' => $XF['news'][$field]['storage'], 'db.type' => $XF['news'][$field]['db.type'], 'db.len' => $XF['news'][$field]['db.len']);
	}

	$XF['news'][$field] = $data;
	if (!xf_configSave()) {
		msg(array("type" => "error", "text" => $lang['xfields_msge_errcsave']));
		showAddEditForm($data, $editMode, $field);
		return;
	}

	// Now we should update table `_news` structure and content
	$found = 0;
	foreach ($mysql->select("describe ".prefix."_news", 1) as $row) {
		if ($row['Field'] == 'xfields_'.$field) {
			$found = 1;
			break;
		}
	}

	$dbFlagChanged = 0;

	// 1. If we add XFIELD and field already exists in DB - drop it!
	// 2. If we don't want to store data in separate field - drop it!
	if ($found && (!$editMode || !$DB['new']['storage'])) { print "DROP";
		$mysql->query("alter table ".prefix."_news drop column `xfields_".$field."`");
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
				$mysql->query("alter table ".prefix."_news change column `xfields_".$field.'` `xfields_'.$field.'` '.$ftype);
				$mysql->query("update ".prefix."_news set `xfields_".$field."` = NULL");
			} else {
				$mysql->query("alter table ".prefix."_news add column `xfields_".$field.'` '.$ftype);
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
			foreach ($mysql->select("select id, xfields from ".prefix."_news where (id > ".$maxID.") and (xfields is not NULL) and (xfields <> '') order by id limit 5") as $rec) {
				$recCount++;
				if ($rec['id'] > $maxID) $maxID = $rec['id'];
				$xlist = xf_decode($rec['xfields']);
				if (isset($xlist[$field]) && ($xlist[$field] != '')) {
					$mysql->query("update ".prefix."_news set `xfields_".$field."` = ".db_squote($xlist[$field])." where id = ".db_squote($rec['id']));
				}
			}
		} while ($recCount);



	}


	$tvars = array ( 'vars' => array ( 'id' => $field));
	$tvars['regx']["'\[edit\](.*?)\[/edit\]'si"] = ($editMode)?'$1':'';


	$tpl -> template('config_done', extras_dir.'/xfields/tpl');
	$tpl -> vars('config_done', $tvars);
	echo $tpl -> show('config_done');

}


//
//
function doUpdate() {
	global $xf, $XF, $lang, $tpl;

	$error = 0;
	$field = $_REQUEST['field'];

	// Check if field exists or not [depends on mode]
	if (!is_array($xf['news'][$field])) {
		msg(array("type" => "error", "text" => $lang['xfields_msge_noexists']. '('.$field.')'));
		$error = 1;
	};

	$notif = '';
	switch ($_REQUEST['subaction']) {
		case 'del':		unset($XF['news'][$field]);
						$notif = $lang['xfields_done_del'];
						break;
		case 'up':		array_key_move($XF['news'], $field, -1);
						$notif = $lang['xfields_done_up'];
						break;
		case 'down':	array_key_move($XF['news'], $field, 1);
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