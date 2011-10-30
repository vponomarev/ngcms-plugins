<?php

// Global XF variables
global $XF;
global $XF_loaded;


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
function xf_configSave($xf = null) {
	global $lang, $XF, $XF_loaded;

	if (!$XF_loaded) return false;
	if (!($confdir = get_plugcfg_dir('xfields'))) return false;

	// Open config
	if (!($fn = fopen($confdir.'/config.php', 'w'))) return false;

	// Write config
	fwrite($fn, "<?php\n\$xarray = ".var_export(is_array($xf)?$xf:$XF, true).";\n");
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


function xf_getTableBySectionID($sectionID) {
	switch ($sectionID) {
		case 'news':	return prefix.'_news';
		case 'users':	return prefix.'_users';
		case 'tdata':	return prefix.'_xfields';
	}
	return false;
}


//
// Class for managing xfields data processing
class XFieldsFilter {
	//
	function showTableEntry($newsID, $SQLnews, $rowData, &$rowVars) {

	}
}
