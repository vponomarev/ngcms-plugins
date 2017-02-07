<?php
if (!defined('NGCMS')) {
	exit('HAL');
}
plugins_load_config();
LoadPluginLang('xsyslog', 'config', '', '', '#');
switch ($_REQUEST['action']) {
	case 'list_xsyslog':
		show_xsyslog();
		break;
	default:
		show_xsyslog();
}
function show_xsyslog() {

	global $tpl, $mysql, $lang, $twig, $confArray;
	$tpath = locatePluginTemplates(array('main', 'list_xsyslog', 'list_entries'), 'xsyslog', 1);
	$tVars = array();
	//include_once 'file';
	// Load admin page based cookies
	$admCookie = admcookie_get();
	// Author filter (by name)
	$fAuthorName = $_REQUEST['an'];
	// Status filter (by 1/0)
	$fStatus = $_REQUEST['status'];
	//print $fStatus;
	// Selected plugin
	$fPlugin = $_REQUEST['fplugins'];
	// Selected item
	$fItem = $_REQUEST['fitems'];
	// Selected date
	$fDateStart = $_REQUEST['dr1'];
	$fDateEnd = $_REQUEST['dr2'];
	if ($fDateStart == 'DD.MM.YYYY') $fDateStart = '';
	if ($fDateEnd == 'DD.MM.YYYY') $fDateEnd = '';
	// Records Per Page
	// - Load
	$news_per_page = isset($_REQUEST['rpp']) ? intval($_REQUEST['rpp']) : intval($admCookie['syslog']['pp']);
	// - Set default value for `Records Per Page` parameter
	if (($news_per_page < 2) || ($news_per_page > 2000))
		$news_per_page = 10;
	// - Save into cookies current value
	$admCookie['syslog']['pp'] = $news_per_page;
	admcookie_set($admCookie);
	$conditions = array();
	if ($fAuthorName) {
		array_push($conditions, "username = " . db_squote($fAuthorName));
	}
	switch ($fStatus) {
		case 'null':
			break;
		case 0:
			array_push($conditions, "status = " . db_squote($fStatus));
			break;
		case 1:
			array_push($conditions, "status = " . db_squote($fStatus));
			break;
	}
	if ($fPlugin) {
		array_push($conditions, "plugin = " . db_squote($fPlugin));
	}
	if ($fItem) {
		array_push($conditions, "item = " . db_squote($fItem));
	}
	if ($fDateStart && $fDateEnd) {
		array_push($conditions, "dt BETWEEN STR_TO_DATE(" . db_squote($fDateStart) . ",'%d.%m.%Y') AND STR_TO_DATE(" . db_squote($fDateEnd) . ",'%d.%m.%Y')");
	} elseif ($fDateStart) {
		array_push($conditions, "dt BETWEEN STR_TO_DATE(" . db_squote($fDateStart) . ",'%d.%m.%Y') AND NOW()");
	} elseif ($fDateEnd) {
		array_push($conditions, "dt BETWEEN STR_TO_DATE('01.01.1970','%d.%m.%Y') AND STR_TO_DATE(" . db_squote($fDateEnd) . ",'%d.%m.%Y')");
	}
	//var_export($conditions);
	$fSort = "ORDER BY id DESC";
	$sqlQPart = "from " . prefix . "_syslog " . (count($conditions) ? "where " . implode(" AND ", $conditions) : '') . ' ' . $fSort;
	$sqlQCount = "select count(id) " . $sqlQPart;
	$sqlQ = "select * " . $sqlQPart;
	$pageNo = $_REQUEST['page'] ? intval($_REQUEST['page']) : 0;
	if ($pageNo < 1) $pageNo = 1;
	if (!$start_from) $start_from = ($pageNo - 1) * $news_per_page;
	$count = $mysql->result($sqlQCount);
	$countPages = ceil($count / $news_per_page);
	foreach ($mysql->select($sqlQ . ' LIMIT ' . $start_from . ', ' . $news_per_page) as $row) {
		$tEntry[] = array(
			'id'       => $row['id'],
			'date'     => $row['dt'],
			'ip'       => $row['ip'],
			'plugin'   => $row['plugin'],
			'item'     => $row['item'],
			'ds'       => $row['ds'],
			'action'   => $row['action'],
			//		'alist' => unserialize($row['alist']),
			'userid'   => $row['userid'],
			'username' => $row['username'],
			'status'   => $row['status'],
			'stext'    => $row['stext'],
		);
		//var_export(unserialize($row['alist']));
	}
	$xt = $twig->loadTemplate($tpath['list_xsyslog'] . 'list_xsyslog.tpl');
	$tVars = array(
		'pagesss' => generateAdminPagelist(array('current' => $pageNo, 'count' => $countPages, 'url' => admin_url . '/admin.php?mod=extra-config&plugin=xsyslog' . ($news_per_page ? '&rpp=' . $news_per_page : '') . ($fAuthorName ? '&an=' . $fAuthorName : '') . ($fStatus ? '&status=' . $fStatus : '') . ($fPlugin ? '&fplugins=' . $fPlugin : '') . ($fItem ? '&fitems=' . $fItem : '') . ($fDateStart ? '&dr1=' . $fDateStart : '') . ($fDateEnd ? '&dr2=' . $fDateEnd : '') . '&page=%page%')),
		'entries' => isset($tEntry) ? $tEntry : ''
	);
	$xg = $twig->loadTemplate($tpath['main'] . 'main.tpl');
	$tVars = array(
		'entries'    => $xt->render($tVars),
		'php_self'   => $confArray['predefined']['PHP_SELF'],
		'skins_url'  => skins_url,
		'home'       => home,
		'rpp'        => $news_per_page,
		'an'         => secure_html($fAuthorName),
		'fstatus'    => $fStatus,
		'catPlugins' => makeVARList(array('obj' => 'plugin', 'name' => 'fplugins', 'selected' => $fPlugin, 'class' => 'bfsortlist', 'doempty' => 1)),
		'catItems'   => makeVARList(array('obj' => 'item', 'name' => 'fitems', 'selected' => $fItem, 'class' => 'bfsortlist', 'doempty' => 1)),
		'fDateStart' => $fDateStart ? $fDateStart : '',
		'fDateEnd'   => $fDateEnd ? $fDateEnd : '',
	);
	//var_export($tVars['fstatus']);
	print $xg->render($tVars);
}

// makePluginsList - make <SELECT> list of Plugins
// Params: set via named array
// * name      		- name field of <SELECT>
// * selected  		- ID of category to be selected or array of IDs to select (in list mode)
// * skip      		- ID of category to skip or array of IDs to skip
// * skipDisabled	- skip disabled areas
// * doempty   		- add empty category to the beginning ("no category"), value = 0
// * greyempty		- show empty category as `grey`
// * doall     		- add category named "ALL" to the beginning, value is empty
// * allMarker		- marker value for `doall`
// * dowithout		- add "Without category" after "ALL", value = 0
// * nameval   		- use DB field "name" instead of ID in HTML option value
// * checkarea	 	- flag, if set - generate a list of checkboxes instead of <SELECT>
// * class     		- HTML class name
// * style     		- HTML style
// * disabledarea	- mark all entries (for checkarea) as disabled [for cases when extra categories are not allowed]
// * noHeader		- Don't write header (<select>..</select>) in output
// * returnOptArray	- FLAG: if we should return OPTIONS (with values) array instead of data
// * obj	- objects to show
function makeVARList($params = array()) {

	global $lang, $mysql;
	$optList = array();
	$obj = $params['obj'];
	if (!isset($params['skip'])) {
		$params['skip'] = array();
	}
	if (!is_array($params['skip'])) {
		$params['skip'] = $params['skip'] ? array($params['skip']) : array();
	}
	$name = array_key_exists('name', $params) ? $params['name'] : 'category';
	$out = '';
	if (!isset($params['checkarea']) || !$params['checkarea']) {
		if (!$params['noHeader']) {
			$out = "<select name=\"$name\" id=\"plugmenu\"" .
				((isset($params['style']) && ($params['style'] != '')) ? ' style="' . $params['style'] . '"' : '') .
				((isset($params['class']) && ($params['class'] != '')) ? ' class="' . $params['class'] . '"' : '') .
				">\n";
		}
		if (isset($params['doempty']) && $params['doempty']) {
			$out .= "<option " . (((isset($params['greyempty']) && $params['greyempty'])) ? 'style="background: #c41e3a;" ' : '') . "value=\"0\">" . $lang['no_cat'] . "</option>\n";
			$optList [] = array('k' => 0, 'v' => $lang['nocat']);
		}
		if (isset($params['doall']) && $params['doall']) {
			$out .= "<option value=\"" . (isset($params['allmarker']) ? $params['allmarker'] : '') . "\">" . $lang['sh_all'] . "</option>\n";
			$optList [] = array('k' => (isset($params['allmarker']) ? $params['allmarker'] : ''), 'v' => $lang['sh_all']);
		}
		if (isset($params['dowithout']) && $params['dowithout']) {
			$out .= "<option value=\"0\"" . (((!is_null($params['selected'])) && ($params['selected'] == 0)) ? ' selected="selected"' : '') . ">" . $lang['sh_empty'] . "</option>\n";
			$optList [] = array('k' => 0, 'v' => $lang['sh_empty']);
		}
	}
	$catz = array();
	foreach ($mysql->select("select DISTINCT * from `" . prefix . "_syslog` order by id asc", 1) as $row) {
		$catz[$row[$obj]] = $row;
		$catmap[$row[$obj]] = $row[$obj];
	}
	foreach ($catz as $k => $v) {
		if (in_array($v[$obj], $params['skip'])) {
			continue;
		}
		if ($params['skipDisabled'] && ($v['alt_url'] != '')) {
			continue;
		}
		if (isset($params['checkarea']) && $params['checkarea']) {
			$out .= str_repeat('&#8212; ', $v['poslevel']) .
				'<label><input type="checkbox" name="' .
				$name .
				'_' .
				$v[$obj] .
				'" value="1"' .
				((isset($params['selected']) && is_array($params['selected']) && in_array($v[$obj], $params['selected'])) ? ' checked="checked"' : '') .
				(((($v['alt_url'] != '') || (isset($params['disabledarea']) && $params['disabledarea']))) ? ' disabled="disabled"' : '') .
				'/> ' .
				$v[$obj] .
				"</label><br/>\n";
		} else {
			$out .= "<option value=\"" . ((isset($params['nameval']) && $params['nameval']) ? $v[$obj] : $v[$obj]) . "\"" . ((isset($params['selected']) && ($v[$obj] == $params['selected'])) ? ' selected="selected"' : '') . ($v['alt_url'] != '' ? ' disabled="disabled" style="background: #c41e3a;"' : '') . ">" . str_repeat('&#8212; ', $v['poslevel']) . $v[$obj] . "</option>\n";
			$optList [] = array('k' => ((isset($params['nameval']) && $params['nameval']) ? $v[$obj] : $v[$obj]), 'v' => str_repeat('&#8212; ', $v['poslevel']) . $v[$obj]);
		}
	}
	if (!isset($params['checkarea']) || !$params['checkarea']) {
		if (!$params['noHeader']) {
			$out .= "</select>";
		}
	}
	if (isset($params['returnOptArray']) && $params['returnOptArray'])
		return $optList;

	return $out;
}

function redirect_xsyslog($url) {

	if (headers_sent()) {
		echo "<script>document.location.href='{$url}';</script>\n";
	} else {
		header('HTTP/1.1 302 Moved Permanently');
		header("Location: {$url}");
	}
}