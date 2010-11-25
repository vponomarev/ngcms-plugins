<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

// Load langs
loadPluginLang('feedback', 'config', '', '', ':');

// Switch action

switch ($_REQUEST['action']) {
	case 'addform'  : addForm();
					  showList();
					  break;
	case 'saveform':  saveForm();
					  break;
	case 'form'		: showForm(0);
					  break;
	case 'row'		: showFormRow();
					  break;
	case 'editrow'	: editFormRow();
					  break;
	case 'update'	: doUpdate();
					  showForm();
					  break;
	case 'delform'	: delForm();
					  showList();
					  break;
	default			: showList();
}


// Simply create new form
function addForm(){
	global $mysql, $tpl, $template, $lang;
	$mysql->query("insert into ".prefix."_feedback (name, title) values ('newform', 'New form')");
}

// Save form params
function saveForm() {
	global $mysql, $tpl, $template, $lang;

	$id = intval($_REQUEST['id']);

	// First - try to fetch form
	if (!is_array($recF = $mysql->record("select * from ".prefix."_feedback where id = ".$id))) {
		msg(array('type' => 'error', 'text' => 'Указанная вами форма не существует'));
		showForm(1);
		return;
	}

	// Готовим список email адресов пользователей
	$emails = '';
	if (is_array($_POST['elist'])) {
		$elist	= $_POST['elist'];
		$eok	= array();

		$num = 1;
		foreach ($elist as $erec) {
			// Проверяем наличие email'ов в списке
			$mlist = preg_split('# *(\,) *#', trim($erec[2], -1));
			if (count($mlist) && strlen($mlist[0])) {
				$eok[$num] = array($num, trim($erec[1]), $mlist);
				$num++;
			}
		}
		$emails = serialize($eok);
	}
	$name = trim($_REQUEST['name']);

	// Проверяем ввод наименования
	if ($name=='') {
		msg(array('type' => 'error', 'text' => 'Необходимо заполнить ID формы'));
		showForm(1);
		return;
	}

	// Проверяем дубляж
	if (is_array($mysql->record("select * from ".prefix."_feedback where id <> ".$id." and name =".db_squote($name)))) {
		msg(array('type' => 'error', 'text' => 'Форма с таким ID уже существует. Нельзя использовать одинаковый ID для разных форм'));
		showForm(1);
		return;
	}

	// Сохраняем изменения
	$flags = ($_REQUEST['jcheck']?'1':'0').($_REQUEST['captcha']?'1':'0').($_REQUEST['html']?'1':'0');

	$mysql->select("update ".prefix."_feedback set name=".db_squote($name).", title=".db_squote($_REQUEST['title']).", template=".db_squote($_REQUEST['template']).", emails=".db_squote($emails).", description=".db_squote($_REQUEST['description']).", active=".intval($_REQUEST['active']).", flags=".db_squote($flags)." where id = ".$id);
	showForm(1);
}

function showList(){
	global $mysql, $tpl, $template, $lang;

	$tpath = locatePluginTemplates(array('conf.forms.hdr', 'conf.forms.row'), 'feedback', extra_get_param('feedback', 'localsource'));
	$output = '';

	$tpl->template('conf.forms.row', $tpath['conf.forms.row']);
	foreach ($mysql->select("select * from ".prefix."_feedback order by name") as $frow) {
		$tvars = array( 'vars' => array(
			'[link]'	=> '<a href="?mod=extra-config&plugin=feedback&action=form&id='.$frow['id'].'">',
			'[/link]'	=> '</a>',
			'[/linkdel]'	=> '</a>',
			'id'		=> $frow['id'],
			'name'		=> $frow['name'],
			'title'		=> $frow['title'],
			'active'	=> $frow['active']?$lang['yesa']:$lang['noa'],

		));
		if ($frow['active']) {
			$tvars['vars']['[linkdel]'] = '<a onclick="alert('."'".$lang['feedback:active_nodel']."'".');">';
		} else {
			$tvars['vars']['[linkdel]']	= '<a href="?mod=extra-config&plugin=feedback&action=delform&id='.$frow['id'].'" onclick="return confirm('."'".$lang['feedback:suretest']."'".');">';
		}

		$tpl -> vars('conf.forms.row', $tvars);
		$output .= $tpl -> show('conf.forms.row');
	}

	$tpl->template('conf.forms.hdr', $tpath['conf.forms.hdr']);
	$tpl->vars('conf.forms.hdr', array ( 'vars' => array ( 'entries' => $output)));
	print $tpl->show('conf.forms.hdr');

}

function showForm($edMode){
	global $mysql, $tpl, $template, $lang;

	$tpath = locatePluginTemplates(array('conf.form.hdr', 'conf.form.row', 'conf.form.egroup'), 'feedback', extra_get_param('feedback', 'localsource'));
	$output = '';

	// Load form
	$id = intval($_REQUEST['id']);

	$tvars = array();
	if (!is_array($frow = $mysql->record("select * from ".prefix."_feedback where id = ".$id))) {
		$output = 'Указанной формы не существует!';
		$tvars['regx']['#\[enabled\](.+?)\[\/enabled\]#is'] = '';
	} else {
		// Unpack form data
		$fData = unserialize($frow['struct']);
		if (!is_array($fData)) $fData = array();

		$tpl->template('conf.form.row', $tpath['conf.form.row']);

		foreach ($fData as $fName => $fInfo) {
			$txvars = array();
			$txvars['vars']['name']		= $fInfo['name'];
			$txvars['vars']['title']	= $fInfo['title'];
			$txvars['vars']['type']		= $lang['feedback:type.'.$fInfo['type']];

			$txvars['vars']['[linkup]']	= '<a href="?mod=extra-config&plugin=feedback&action=update&subaction=up&id='.$id.'&name='.$fInfo['name'].'">';
			$txvars['vars']['[/linkup]'] = '</a>';
			$txvars['vars']['[linkdown]']	= '<a href="?mod=extra-config&plugin=feedback&action=update&subaction=down&id='.$id.'&name='.$fInfo['name'].'">';
			$txvars['vars']['[/linkdown]'] = '</a>';
			$txvars['vars']['[linkdel]']	= '<a href="?mod=extra-config&plugin=feedback&action=update&subaction=del&id='.$id.'&name='.$fInfo['name'].'">';
			$txvars['vars']['[/linkdel]'] = '</a>';
			$txvars['vars']['[link]']	= '<a href="?mod=extra-config&plugin=feedback&action=row&form_id='.$id.'&row='.$fInfo['name'].'">';
			$txvars['vars']['[/link]'] = '</a>';

			$tpl->vars('conf.form.row', $txvars);
			$output .= $tpl->show('conf.form.row');

		}
		$tvars['regx']['#\[enabled\](.+?)\[\/enabled\]#is'] = '$1';
		$tvars['vars']['id']				= $frow['id'];
		$tvars['vars']['name']				= $edMode?$_REQUEST['name']:$frow['name'];
		$tvars['vars']['title']				= $edMode?$_REQUEST['title']:$frow['title'];
		$tvars['vars']['active_checked']	= ($edMode?$_REQUEST['active']:$frow['active'])?'checked="checked"':'';
		$tvars['vars']['jcheck_checked']	= ($edMode?$_REQUEST['jcheck']:intval(substr($frow['flags'],0,1)))?'checked="checked"':'';
		$tvars['vars']['captcha_checked']	= ($edMode?$_REQUEST['captcha']:intval(substr($frow['flags'],1,1)))?'checked="checked"':'';
		$tvars['vars']['html_checked']		= ($edMode?$_REQUEST['html']:intval(substr($frow['flags'],2,1)))?'checked="checked"':'';
		$tvars['vars']['description']		= secure_html($frow['description']);
		$tvars['vars']['url']				= generateLink('core', 'plugin', array('plugin' => 'feedback'), array('id' => $frow['id']), true, true);

		// Prepare output for Email groups
		{
			$tpl -> template('conf.form.egroup', $tpath['conf.form.egroup']);
			$elist = array();

			if (($elist = unserialize($frow['emails'])) === false) {
				$elist[1]= array(1, '', preg_split("# *(\r\n|\n) *#", $frow['emails']));
			}

			// Add an empty line to $elist
			$elist[] = array('', '', '');

			$eout = '';
			$num = 1;
			foreach ($elist as $erec) {
				$txvars = array('vars' => array(
					'nname'		=> 'elist['.$num.'][0]',
					'nvalue'	=> $erec[0],
					'gname'		=> 'elist['.$num.'][1]',
					'gvalue'	=> $erec[1],
					'vname'		=> 'elist['.$num.'][2]',
					'vvalue'	=> join(', ', $erec[2]),
				));
				$tpl -> vars('conf.form.egroup', $txvars);
				$eout .= $tpl -> show('conf.form.egroup');
				$num++;
			}
		}
		$tvars['vars']['egroups']			= $eout;

		// Generate list of templates
		$lf = array('') + ListFiles(extras_dir.'/feedback/tpl/templates', 'tpl');
		$lout = '';
		foreach ($lf as $file)
			$lout .= '<option value="'.$file.'"'.($frow['template'] == $file?' selected="selected"':'').'>'.($file == ''?'<автоматически>':$file).'</option>';

		$tvars['vars']['template_options'] = $lout;

	}
	$tvars['vars']['entries'] = $output;

	$tpl->template('conf.form.hdr', $tpath['conf.form.hdr']);
	$tpl->vars('conf.form.hdr', $tvars);
	print $tpl->show('conf.form.hdr');


}

function showFormRow() {
	global $mysql, $tpl, $template, $lang;

	$tpath = locatePluginTemplates(array('conf.form.edithdr', 'conf.form.editrow'), 'feedback', extra_get_param('feedback', 'localsource'));
	$output = '';

	// Load form
	$id		= intval($_REQUEST['form_id']);
	$fRowId	= $_REQUEST['row'];

	$enabled = 0;
	do {
		// Check if form exists
		if (!is_array($frow = $mysql->record("select * from ".prefix."_feedback where id = ".$id))) {
			$tvars = array('vars' => array('content' => "Указанная форма [".$id."] не существует!"));
			break;
		}

		// Unpack form data
		$fData = unserialize($frow['struct']);
		if (!is_array($fData)) $fData = array();

		// Check if form's row exists
		if ($fRowId && !isset($fData[$fRowId])) {
			$tvars = array('vars' => array('content' => "Указанное поле [".$id."][".$fRowId."] не существует!"));
			break;
		}

		$editMode = ($fRowId)?1:0;
		$txvars = array();

		if ($editMode) {
			$xRow = $fData[$fRowId];
			$txvars['regx']["'\[add\](.*?)\[/add\]'si"]		= '';
			$txvars['regx']["'\[edit\](.*?)\[/edit\]'si"]	= '$1';

			$txvars['vars']['type']		= $xRow['type'];
			$txvars['vars']['name']		= $xRow['name'];
			$txvars['vars']['title']	= $xRow['title'];
			$txvars['vars']['defult']	= $xRow['default'];

			$txvars['vars']['storekeys_opts'] = '<option value="0"'.(($xRow['storekeys'])?'':' selected="selected"').'>Сохранять значение</option><option value="1"'.(($xRow['storekeys'])?' selected="selected"':'').'>Сохранять код</option>';
			$txvars['vars']['required_opts'] = '<option value="0"'.(($xRow['required'])?'':' selected="selected"').'>Нет</option><option value="1"'.(($xRow['required'])?' selected="selected"':'').'>Да</option>';

		} else {
			$txvars['regx']['#\[add\](.+?)\[\/add\]#is']	= '$1';
			$txvars['regx']['#\[edit\](.+?)\[\/edit\]#is']	= '';

			$txvars['vars']['storekeys_opts'] = '<option value="0">Сохранять значение</option><option value="1">Сохранять код</option>';
			$txvars['vars']['required_opts'] = '<option value="0">Нет</option><option value="1">Да</option>';
			$txvars['vars']['type'] = 'text';
			$txvars['vars']['title'] = '';
			$txvars['vars']['select_options'] = '';

			$txvars['vars']['name'] = '';
		}

		$xsel = '';
		foreach (array('text', 'textarea', 'select', 'date') as $ts) {
			$txvars['vars'][$ts.'_default'] = ($xRow['type'] == $ts)?$xRow['default']:'';
			$xsel .= '<option value="'.$ts.'"'.(($xRow['type'] == $ts)?' selected':'').'>'.$lang['feedback:type.'.$ts];
		}
		$txvars['vars']['type_opts'] = $xsel;
		if ($xRow['storekeys']) {
			$so = array();
			foreach ($xRow['options'] as $k => $v)
				$so[] = $k.' => '.$v;
			$txvars['vars']['select_options'] = join("\n", $so);
		} else {
			$txvars['vars']['select_options'] = join("\n", $xRow['options']);
		}

		$tpl->template('conf.form.editrow', $tpath['conf.form.editrow']);
		$tpl->vars('conf.form.editrow', $txvars);

		$tvars = array('vars' => array('content' => $tpl->show('conf.form.editrow'), 'form_id' => $frow['id'], 'form_name' => $frow['name']));
		$enabled = 1;

	} while (0);

	$tvars['regx']['#\[enabled\](.+?)\[\/enabled\]#is'] = $enabled?'$1':'';

	$tpl->template('conf.form.edithdr', $tpath['conf.form.edithdr']);
	$tpl->vars('conf.form.edithdr', $tvars);
	echo $tpl->show('conf.form.edithdr');
}


function editFormRow(){
	global $mysql, $tpl, $template, $lang;

	$tpath = locatePluginTemplates(array('conf.form.edithdr'), 'feedback', extra_get_param('feedback', 'localsource'));

	// Check params
	$id		= intval($_REQUEST['form_id']);
	$fRowId	= $_REQUEST['name'];
	$editMode = intval($_REQUEST['edit']);

	$enabled = 0;
	do {
		// Check if form exists
		if (!is_array($frow = $mysql->record("select * from ".prefix."_feedback where id = ".$id))) {
			$tvars = array('vars' => array('content' => "Указанная форма [".$id."] не существует!"));
			break;
		}

		// Unpack form data
		$fData = unserialize($frow['struct']);
		if (!is_array($fData)) $fData = array();

		// Check if form's row exists
		if ($editMode && !isset($fData[$fRowId])) {
			$tvars = array('vars' => array('content' => "Указанное поле [".$id."][".$fRowId."] не существует!"));
			break;
		}

		// For "add" mode - check if field already exists
		if (!$editMode && isset($fData[$fRowId])) {
			$tvars = array('vars' => array('content' => "Указанное поле [".$id."][".$fRowId."] уже существует!"));
			break;
		}

		//
		$enabled = 1;

		// Fill field's params
		$fld = array('name' => $fRowId, 'title' => $_REQUEST['title']);
		if (intval($_REQUEST['required']))
			$fld['required'] = 1;

		switch ($_REQUEST['type']) {
			case 'text':
				$fld['type'] = 'text';
				$fld['default'] = $_REQUEST['text_default'];
				break;

			case 'date':
				$fld['type'] = 'date';
				// Check default date
				if (preg_match('#^ *(\d{1,2})\.(\d{1,2})\.(\d{4}) *$#', $_REQUEST['date_default'], $match) &&
					($match[1] >= 1) && ($match[1] <= 31) && ($match[2] >= 1) && ($match[2] <= 12) && ($match[3] >= 1970) && ($match[3] <= 2099)
				   ) {
					$fld['default'] = $match[1].'.'.$match[2].'.'.$match[3];
					$fld['default:vars']['day']		= $match[1];
					$fld['default:vars']['month']	= $match[2];
					$fld['default:vars']['year']	= $match[3];
				}
				break;

			case 'textarea':
				$fld['type'] = 'textarea';
				$fld['default'] = $_REQUEST['textarea_default'];
				break;

			case 'select':
				$fld['type'] = 'select';
				$fld['options'] = array();
				if ($_REQUEST['select_storekeys']) {
					$fld['storekeys'] = 1;
				}

				foreach (explode("\n", $_REQUEST['select_options']) as $row) {
					if (!strlen(trim($row)))
						continue;

					if ($fld['storekeys']) {
						if (preg_match('#^(.+?) *\=\> * (.+)$', trim($row), $match))
							$fld['options'][$match[1]] = $match[2];
					} else {
						$fld['options'][] = trim($row);
					}
				}
				break;

			default:
				$tvars = array('vars' => array('content' => "Неподдерживаемый тип поля"));
				break;
		}

		if (!isset($fld['type']))
			break;

		// Everything is correct. Let's update field data
		$fData[$fRowId] = $fld;
		$mysql->query("update ".prefix."_feedback set struct = ".db_squote(serialize($fData))." where id = ".$frow['id']);

		$tvars = array('vars' => array('content' => "Поле изменено"));
	} while (0);

	// Show template
	$tvars['vars']['form_name'] = $frow['name'];
	$tvars['vars']['form_id'] = $frow['id'];
	$tvars['regx']['#\[enabled\](.+?)\[\/enabled\]#is'] = $enabled?'$1':'';

	$tpl->template('conf.form.edithdr', $tpath['conf.form.edithdr']);
	$tpl->vars('conf.form.edithdr', $tvars);
	echo $tpl->show('conf.form.edithdr');
}

//
//
function doUpdate() {
	global $mysql, $tpl, $template, $lang;

	$tpath = locatePluginTemplates(array('conf.form.edithdr'), 'feedback', extra_get_param('feedback', 'localsource'));

	// Check params
	$id		= intval($_REQUEST['id']);
	$fRowId	= $_REQUEST['name'];

	$enabled = 0;
	do {
		// Check if form exists
		if (!is_array($frow = $mysql->record("select * from ".prefix."_feedback where id = ".$id))) {
			$tvars = array('vars' => array('content' => "Указанная форма [".$id."] не существует!"));
			break;
		}

		// Unpack form data
		$fData = unserialize($frow['struct']);
		if (!is_array($fData)) $fData = array();

		// Check if form's row exists
		if ($editMode && !isset($fData[$fRowId])) {
			$tvars = array('vars' => array('content' => "Указанное поле [".$id."][".$fRowId."] не существует!"));
			break;
		}
		$enabled = 1;
	} while(0);

	if (!$enabled) {
		// Show template
		$tvars['regx']['#\[enabled\](.+?)\[\/enabled\]#is'] = '';

		$tpl->template('conf.form.edithdr', $tpath['conf.form.edithdr']);
		$tpl->vars('conf.form.edithdr', $tvars);
		echo $tpl->show('conf.form.edithdr');
		return;
	}

	// Now make an action

	switch ($_REQUEST['subaction']) {
		case 'del':		unset($fData[$fRowId]);
						break;
		case 'up':		array_key_move($fData, $fRowId, -1);
						break;
		case 'down':	array_key_move($fData, $fRowId, 1);
						break;
	}

	$mysql->query("update ".prefix."_feedback set struct = ".db_squote(serialize($fData))." where id = ".$frow['id']);
}


//
function delForm() {
	global $mysql, $tpl, $template, $lang;

	$mysql->query("delete from ".prefix."_feedback where id = ".intval($_REQUEST['id']));
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