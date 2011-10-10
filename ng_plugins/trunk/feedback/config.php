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
	case 'update'	: if (doUpdate()) {
					  	showForm();
					}
					  break;
	case 'delform'	: delForm();
					  showList();
					  break;
	default			: showList();
}


// Simply create new form
function addForm(){
	global $mysql, $lang;
	$mysql->query("insert into ".prefix."_feedback (name, title) values ('newform', 'New form')");
}

// Save form params
function saveForm() {
	global $mysql, $lang;

	$id = intval($_REQUEST['id']);

	// First - try to fetch form
	if (!is_array($recF = $mysql->record("select * from ".prefix."_feedback where id = ".$id))) {
		msg(array('type' => 'error', 'text' => '��������� ���� ����� �� ����������'));
		showForm(1);
		return;
	}

	// ������� ������ email ������� �������������
	$emails = '';
	if (is_array($_POST['elist'])) {
		$elist	= $_POST['elist'];
		$eok	= array();

		$num = 1;
		foreach ($elist as $erec) {
			// ��������� ������� email'�� � ������
			$mlist = preg_split('# *(\,) *#', trim($erec[2], -1));
			if (count($mlist) && strlen($mlist[0])) {
				$eok[$num] = array($num, trim($erec[1]), $mlist);
				$num++;
			}
		}
		$emails = serialize($eok);
	}
	$name = trim($_REQUEST['name']);

	// ��������� ���� ������������
	if ($name=='') {
		msg(array('type' => 'error', 'text' => '���������� ��������� ID �����'));
		showForm(1);
		return;
	}

	// ��������� ������
	if (is_array($mysql->record("select * from ".prefix."_feedback where id <> ".$id." and name =".db_squote($name)))) {
		msg(array('type' => 'error', 'text' => '����� � ����� ID ��� ����������. ������ ������������ ���������� ID ��� ������ ����'));
		showForm(1);
		return;
	}

	// ��������� ���������
	$flags =	($_REQUEST['jcheck']?'1':'0').
				($_REQUEST['captcha']?'1':'0').
				($_REQUEST['html']?'1':'0').
				(((intval($_REQUEST['link_news']) >= 0) && (intval($_REQUEST['link_news']) <= 2))?intval($_REQUEST['link_news']):0);


	$mysql->select("update ".prefix."_feedback set name=".db_squote($name).", title=".db_squote($_REQUEST['title']).", template=".db_squote($_REQUEST['template']).", emails=".db_squote($emails).", description=".db_squote($_REQUEST['description']).", active=".intval($_REQUEST['active']).", flags=".db_squote($flags)." where id = ".$id);
	showForm(1);
}

function showList(){
	global $mysql, $lang, $twig;

	$tVars = array();
	$tForms = array();

	foreach ($mysql->select("select * from ".prefix."_feedback order by name") as $frow) {
		$tForm = array(
			'id'	=> $frow['id'],
			'name'	=> $frow['name'],
			'title'	=> $frow['title'],
			'link_news'	=> intval(substr($frow['flags'], 3, 1)),
			'flags'	=> array(
				'active'	=> $frow['active'],
			),
			'linkEdit'	=> '?mod=extra-config&plugin=feedback&action=form&id='.$frow['id'],
			'linkDel'	=> '?mod=extra-config&plugin=feedback&action=delform&id='.$frow['id'],

		);
		$tForms[]= $tForm;
	}

	$tVars['entries'] = $tForms;

	$templateName = 'plugins/feedback/tpl/conf.forms.tpl';
	$xt = $twig->loadTemplate($templateName);
	echo $xt->render($tVars);
}

function showForm($edMode){
	global $mysql, $lang, $twig;

	$tVars = array();

	// Load form
	$id = intval($_REQUEST['id']);

	$tvars = array();
	if (!is_array($frow = $mysql->record("select * from ".prefix."_feedback where id = ".$id))) {
		$tVars['content'] = "��������� ����� [".$id."] �� ����������!";

		$xt = $twig->loadTemplate('plugins/feedback/tpl/conf.notify.tpl');
		echo $xt->render($tVars);
		return false;
	}

	$tVars['formID']			= $frow['id'];
	$tVars['formName']			= $frow['name'];

	// Unpack form data
	$fData = unserialize($frow['struct']);
	if (!is_array($fData)) $fData = array();

	$tEntries = array();
	foreach ($fData as $fName => $fInfo) {
		$tEntry = array(
			'name'		=> $fInfo['name'],
			'title'		=> $fInfo['title'],
			'type'		=> $fInfo['type'],
			'auto'		=> intval($fInfo['auto']),
			'block'		=> intval($fInfo['block']),
		);
		$tEntries[]= $tEntry;
	}

	// ������� ������ email'��
	$tEGroups = array();

	if (($elist = unserialize($frow['emails'])) === false) {
		$elist[1]= array(1, '', preg_split("# *(\r\n|\n) *#", $frow['emails']));
	}

	$num = 1;
	foreach ($elist as $erec) {
		$tEGroup = array(
			'num'		=> $erec[0],
			'name'		=> $erec[1],
			'value'		=> secure_html(join(', ', $erec[2])),
		);
		$tEGroups[]= $tEGroup;
		$num++;
	}
	$tEGroups[]= array($num,'','');

	$tVars['id']				= $frow['id'];
	$tVars['name']				= $edMode?$_REQUEST['name']:$frow['name'];
	$tVars['title']				= $edMode?$_REQUEST['title']:$frow['title'];
	$tVars['description']		= $edMode?$_REQUEST['description']:$frow['description'];
	$tVars['url']				= generateLink('core', 'plugin', array('plugin' => 'feedback'), array('id' => $frow['id']), true, true);
	$tVars['egroups']			= $tEGroups;
	$tVars['link_news']			= array(
		'options'	=> array(0,1,2),
		'value'		=> intval(substr($frow['flags'], 3, 1)),
	);
	$tVars['flags']				= array(
			'active'			=> intval($edMode?$_REQUEST['active']:$frow['active']),
			'jcheck'			=> intval($edMode?$_REQUEST['jcheck']:intval(substr($frow['flags'],0,1))),
			'captcha'			=> intval($edMode?$_REQUEST['captcha']:intval(substr($frow['flags'],1,1))),
			'html'				=> intval($edMode?$_REQUEST['html']:intval(substr($frow['flags'],2,1))),
			'haveForm'			=> 1,

	);

	// Generate list of templates
	$lf = array('') + ListFiles(extras_dir.'/feedback/tpl/templates', 'tpl');
	$lout = '';
	foreach ($lf as $file)
		$lout .= '<option value="'.$file.'"'.($frow['template'] == $file?' selected="selected"':'').'>'.($file == ''?'<�������������>':$file).'</option>';

	$tVars['template_options'] = $lout;
	$tVars['entries'] = $tEntries;

	$xt = $twig->loadTemplate('plugins/feedback/tpl/conf.form.tpl');
	echo $xt->render($tVars);

}

function showFormRow() {
	global $mysql, $lang, $twig;

	$tVars = array();

	// Load form
	$id		= intval($_REQUEST['form_id']);
	$fRowId	= $_REQUEST['row'];

	$recordFound = 0;
	do {
		// Check if form exists
		if (!is_array($frow = $mysql->record("select * from ".prefix."_feedback where id = ".$id))) {
			$tVars['content'] = "��������� ����� [".$id."] �� ����������!";
			break;
		}

		$tVars['flags']['haveForm']	= 1;
		$tVars['formID']			= $frow['id'];
		$tVars['formName']			= $frow['name'];

		// Unpack form data
		$fData = unserialize($frow['struct']);
		if (!is_array($fData)) $fData = array();

		// Check if form's row exists
		if ($fRowId && !isset($fData[$fRowId])) {
			$tVars['content'] = "��������� ���� [".$id."][".$fRowId."] �� ����������!";
			break;
		}

		$editMode = ($fRowId)?1:0;

		if ($editMode) {
			$xRow = $fData[$fRowId];

			$tVars['flags']['haveField']			= 1;
			$tVars['fieldName']						= $xRow['name'];

			$tVars['field']['name']					= $xRow['name'];
			$tVars['field']['title']				= secure_html($xRow['title']);
			$tVars['field']['default']				= secure_html($xRow['default']);
			$tVars['field']['type']['value']		= $xRow['type'];
			$tVars['field']['required']['value']	= $xRow['required'];
			$tVars['field']['auto']['value']		= $xRow['auto'];
			$tVars['field']['block']['value']		= $xRow['block'];

		} else {
			$tVars['flags']['addField']				= 1;

			$tVars['field']['title']				= '';

			$tVars['field']['type']['value']		= 'text';
			$tVars['field']['required']['value']	= 0;
			$tVars['field']['auto']['value']		= 0;
			$tVars['field']['block']['value']	= 0;
		}

		$xsel = '';
		foreach (array('text', 'textarea', 'select', 'date') as $ts) {
			$tVars['field'][$ts.'_default'] = ($xRow['type'] == $ts)?secure_html($xRow['default']):'';
			$xsel .= '<option value="'.$ts.'"'.(($xRow['type'] == $ts)?' selected':'').'>'.$lang['feedback:type.'.$ts];
		}
		$tVars['field']['type']['options'] = $xsel;
		$tVars['field']['select_options'] = join("\n", $xRow['options']);
		$tVars['field']['required']['options']	= array(0, 1);
		$tVars['field']['auto']['options'] = array (0, 1, 2);
		$tVars['field']['block']['options'] = array (0, 1, 2);

		$recordFound = 1;
	} while (0);

	$templateName = 'plugins/feedback/tpl/'.($recordFound?'conf.form.editrow':'conf.notify').'.tpl';

	$xt = $twig->loadTemplate($templateName);
	echo $xt->render($tVars);
}


function editFormRow(){
	global $mysql, $lang, $twig;

	// Check params
	$id			= intval($_REQUEST['form_id']);
	$fRowId		= $_REQUEST['name'];
	$editMode	= intval($_REQUEST['edit']);
	$tVars		= array();

	$enabled = 0;
	do {
		// Check if form exists
		if (!is_array($frow = $mysql->record("select * from ".prefix."_feedback where id = ".$id))) {
			$tVars['content'] = "��������� ����� [".$id."] �� ����������!";
			break;
		}

		$tVars['flags']['haveForm']	= 1;
		$tVars['formID']			= $frow['id'];
		$tVars['formName']			= $frow['name'];

		// Unpack form data
		$fData = unserialize($frow['struct']);
		if (!is_array($fData)) $fData = array();

		// Check if form's row exists
		if ($editMode && !isset($fData[$fRowId])) {
			$tVars['content'] = "��������� ���� [".$id."][".$fRowId."] �� ����������!";
			break;
		}

		// For "add" mode - check if field already exists
		if (!$editMode && isset($fData[$fRowId])) {
			$tVars['content'] = "��������� ���� [".$id."][".$fRowId."] ��� ����������!";
			break;
		}

		// �������� ������������ �������� � ����� [ ������ ������� � ����� ]
		if (!$editMode && !preg_match('#^[a-zA-Z0-9\.]+$#', $fRowId)) {
			$tVars['content'] = "��� ���� �������� ����������� �������. ��������� ������������ ������ ������� ���������� �������� � �����!";
			break;

		}

		$tVars['flags']['haveField']	= 1;
		$tVars['fieldName']				= $fRowId;

		//
		$enabled = 1;

		// Fill field's params
		$fld = array('name' => $fRowId, 'title' => $_REQUEST['title'], 'auto' => intval($_REQUEST['auto']), 'block' => intval($_REQUEST['block']));
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

						$fld['options'][] = trim($row);
				}
				break;

			default:
				$tVars['content'] = "���������������� ��� ����";
				break;
		}

		if (!isset($fld['type']))
			break;

		// Everything is correct. Let's update field data
		$fData[$fRowId] = $fld;
		$mysql->query("update ".prefix."_feedback set struct = ".db_squote(serialize($fData))." where id = ".$frow['id']);

		$tVars['content'] = "���� ��������";
	} while (0);

	// Show template
	$xt = $twig->loadTemplate('plugins/feedback/tpl/conf.notify.tpl');
	echo $xt->render($tVars);
}

//
//
function doUpdate() {
	global $mysql, $twig;

	// Check params
	$id		= intval($_REQUEST['id']);
	$fRowId	= $_REQUEST['name'];

	$enabled = 0;
	$tVars = array();
	do {
		// Check if form exists
		if (!is_array($frow = $mysql->record("select * from ".prefix."_feedback where id = ".$id))) {
			$tVars['content'] = "��������� ����� [".$id."] �� ����������!";
			break;
		}

		$tVars['flags']['haveForm']	= 1;
		$tVars['formID']			= $frow['id'];
		$tVars['formName']			= $frow['name'];

		// Unpack form data
		$fData = unserialize($frow['struct']);
		if (!is_array($fData)) $fData = array();

		// Check if form's row exists
		if (!isset($fData[$fRowId])) {
			$tVars['content'] = "��������� ���� [".$id."][".$fRowId."] �� ����������!";
			break;
		}
		$enabled = 1;
	} while(0);

	if (!$enabled) {
		// Show template
		$xt = $twig->loadTemplate('plugins/feedback/tpl/conf.notify.tpl');
		echo $xt->render($tVars);

		return false;
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
	return true;
}


//
function delForm() {
	global $mysql, $lang;

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