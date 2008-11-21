<?php

//
// Configuration file for plugin
//

// Preload config file
plugins_load_config();

@include_once("XML/RSS.php");
$HAVE_PEAR_XML_RSS = 0;
if (class_exists('XML_RSS')) {
	$HAVE_PEAR_XML_RSS = 1;
}	


// Fill configuration parameters
$cfg = array();
array_push($cfg, array('descr' => '<font color=red><b>��������!</b>������ ��� <b>�� ����������</b>!!!<br>������ ������������ � �������������� ����� ��� ������� ������������� ��� ������.</font><br><br><a href="'.home.'/plugin/rss_import/">������� �� �������� �������</a> (�������������� ������ ��������� <b>�������</b>)'));

// RUN 
if ($_REQUEST['action'] == 'commit') {
	// If submit requested, do config save
	commit_plugin_config_changes($plugin, $cfg);
	print_commit_complete($plugin);
} else {
	generate_config_page($plugin, $cfg);
}


?>