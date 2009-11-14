<?

// #==========================================================#
// # Plugin name: xfields [ Additional fields managment ]     #
// # Author: Vitaly A Ponomarev, vp7@mail.ru                  #
// # Allowed to use only with: Next Generation CMS            #
// #==========================================================#

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

// Load lang files
LoadPluginLang('xfields', 'config');

// Perform replacements while showing news
class XFieldsNewsFilter extends NewsFilter {

	function addNewsForm(&$tvars) {
		global $lang, $tpl;

		// Load config
		$xf = xf_configLoad();
		if (!is_array($xf))
			return false;

		$output = '';

		if (is_array($xf['news']))
			foreach ($xf['news'] as $id => $data) {
				switch ($data['type']) {
					case 'text'  : 	$input = '<input type="text" name="xfields['.$id.']" title="'.$data['title'].'" value="'.secure_html($data['default']).'"/>'; break;
					case 'select': 	$input = '<select name="xfields['.$id.']">';
									if (!$data['required']) $input .= '<option value=""></option>';
									if (is_array($data['options']))
										foreach ($data['options'] as $k => $v)
											$input .= '<option value="'.secure_html(($data['storekeys'])?$k:$v).'"'.((($data['storekeys'] && $data['default'] == $k)||(!$data['storekeys'] && $data['default'] == $v))?' selected':'').'>'.$v.'</option>';
									$input .= '</select>';
									break;
					case 'textarea'  :	$input = '<textarea cols="30" rows="5" name="xfields['.$id.']">'.$data['default'].'</textarea>'; break;
				}
				$tv = array ( 'vars' => array( 'title' => $data['title'], 'input' => $input, 'require' => $lang['xfields_fld_'.($data['required']?'required':'optional')]));

				$tpl -> template('add_entry', extras_dir.'/xfields/tpl');
				$tpl -> vars('add_entry', $tv);
				$output .= $tpl -> show('add_entry');
			}

		$tv = array ( 'vars' => array( 'entries' => $output));
		$tpl -> template('add_news', extras_dir.'/xfields/tpl');
		$tpl -> vars('add_news', $tv);
		$tvars['vars']['plugin_xfields'] = $tpl -> show('add_news');
		return 1;
	}
	function addNews(&$tvars, &$SQL) {
		global $lang;
		// Load config
		$xf = xf_configLoad();
		if (!is_array($xf))
			return 1;

		$rcall = $_REQUEST['xfields'];
		if (!is_array($rcall)) $rcall = array();

		$xdata = array();
		foreach ($xf['news'] as $id => $data) {
			// Fill xfields. Check that all required fields are filled
			if ($rcall[$id] != '') {
				$xdata[$id] = $rcall[$id];
			} else if ($data['required']) {
				msg(array("type" => "error", "text" => str_replace('{field}', $id, $lang['xfields_msge_emptyrequired'])));
				return 0;
			}
			// Check if we should save data into separate SQL field
			if ($data['storage'] && ($rcall[$id] != ''))
				$SQL['xfields_'.$id] = $rcall[$id];
		}

	    $SQL['xfields']   = xf_encode($xdata);
		return 1;
	}
	function editNewsForm($newsID, $SQLold, &$tvars) {
		global $lang, $tpl;

		// Load config
		$xf = xf_configLoad();
		if (!is_array($xf))
			return false;

		// Fetch xfields data
		$xdata = xf_decode($SQLold['xfields']);
		if (!is_array($xdata))
			return false;

		$output = '';

		foreach ($xf['news'] as $id => $data) {
			switch ($data['type']) {
				case 'text'  : 	$input = '<input type="text" name="xfields['.$id.']" title="'.$data['title'].'" value="'.secure_html($xdata[$id]).'" />'; break;
				case 'select': 	$input = '<select name="xfields['.$id.']">';
								if (!$data['required']) $input .= '<option value="">&nbsp;</option>';
								if (is_array($data['options']))
									foreach ($data['options'] as $k => $v) {
										$input .= '<option value="'.secure_html(($data['storekeys'])?$k:$v).'"'.((($data['storekeys'] && ($xdata[$id] == $k))||(!$data['storekeys'] && ($xdata[$id] == $v)))?' selected':'').'>'.$v.'</option>';
									}
								$input .= '</select>';
								break;
				case 'textarea'  :	$input = '<textarea cols="30" rows="4" name="xfields['.$id.']">'.$xdata[$id].'</textarea>'; break;
			}
			$tv = array ( 'vars' => array( 'title' => $data['title'], 'input' => $input, 'require' => $lang['xfields_fld_'.($data['required']?'required':'optional')]));

			$tpl -> template('ed_entry', extras_dir.'/xfields/tpl');
			$tpl -> vars('ed_entry', $tv);
			$output .= $tpl -> show('ed_entry');
		}

		$tv = array ( 'vars' => array( 'entries' => $output));
		$tpl -> template('ed_news', extras_dir.'/xfields/tpl');
		$tpl -> vars('ed_news', $tv);
		$tvars['vars']['plugin_xfields'] .= $tpl -> show('ed_news');
		return 1;
	}
	function editNews($newsID, $SQLold, &$SQLnew, &$tvars) {
		global $lang;
		// Load config
		$xf = xf_configLoad();
		if (!is_array($xf))
			return 1;

		$rcall = $_REQUEST['xfields'];
		if (!is_array($rcall)) $rcall = array();

		$xdata = array();
		foreach ($xf['news'] as $id => $data) {
			if ($rcall[$id] != '') {
				$xdata[$id] = $rcall[$id];
			} else if ($data['required']) {
				msg(array("type" => "error", "text" => str_replace('{field}', $id, $lang['xfields_msge_emptyrequired'])));
				return 0;
			}
			// Check if we should save data into separate SQL field
			if ($data['storage'])
				$SQLnew['xfields_'.$id] = $rcall[$id];
		}

	    $SQLnew['xfields']   = xf_encode($xdata);
		return 1;
	}

	// Show news call :: processor (call after all processing is finished and before show)
	function showNews($newsID, $SQLnews, &$tvars, $mode = array()) {
		// Try to load config. Stop processing if config was not loaded
		if (($xf = xf_configLoad()) === false) return;

		$fields = xf_decode($SQLnews['xfields']);
		$content = $SQLnews['content'];

		if (is_array($xf['news']))
			foreach ($xf['news'] as $k => $v) {
				$kp = preg_quote($k, "'");
				$xfk = isset($fields[$k])?$fields[$k]:'';
				$tvars['regx']["'\[xfield_".$kp."\](.*?)\[/xfield_".$kp."\]'is"] = ($xfk == "")?"":"$1";
				$tvars['vars']['[xvalue_'.$k.']'] = ($v['type'] == 'textarea')?'<br/>'.(str_replace("\n","<br/>\n",$xfk).(strlen($xfk)?'<br/>':'')):$xfk;
			}
		$SQLnews['content'] = $content;
	}
}

register_filter('news','xfields', new XFieldsNewsFilter);


// Global XF variables
$XF = array();		// $XF - array with configuration
$XF_loaded = 0;		// $XF_loaded - flag if config is loaded


// Load fields definition
function xf_configLoad() {
	global $lang, $XF, $XF_loaded;

	if ($XF_loaded) return $XF;
	if (!($confdir = get_plugcfg_dir('xfields'))) return false;

	if (!file_exists($confdir.'/config.php')) {
		$XF_loaded = 1;
		return array( 'news' => array());
	}
	include $confdir.'/config.php';
	$XF_loaded = 1;
	$XF = is_array($xarray)?$xarray:array( 'news' => array());
	return $XF;
}

// Save fields definition
function xf_configSave() {
	global $lang, $XF, $XF_loaded;

	if (!$XF_loaded) return false;
	if (!($confdir = get_plugcfg_dir('xfields'))) return false;

	// Open config
	if (!($fn = fopen($confdir.'/config.php', 'w'))) return false;

	// Write config
	fwrite($fn, "<?php\n\$xarray = ".var_export($XF, true).";\n");
	fclose($fn);
	return true;
}

// Decode fields from text
function xf_decode($text){

	if ($text == '') return array();

	// MODERN METHOD
	if (substr($text,0,4) == "SER|") return unserialize(substr($text,4));

	// OLD METHOD. OBSOLETE but supported for reading
	$xfieldsdata = explode("||", $text);

	foreach ($xfieldsdata as $xfielddata) {
		list($xfielddataname, $xfielddatavalue) = explode("|", $xfielddata);
		$xfielddataname = str_replace("&#124;", "|", $xfielddataname);
		$xfielddataname = str_replace("__NEWL__", "\r\n", $xfielddataname);
		$xfielddatavalue = str_replace("&#124;", "|", $xfielddatavalue);
		$xfielddatavalue = str_replace("__NEWL__", "\r\n", $xfielddatavalue);
		$data[$xfielddataname] = $xfielddatavalue;
	}
	return $data;
}

// Encode fields into text
function xf_encode($fields){
	if (!is_array($fields)) return '';
	return 'SER|'.serialize($fields);
}