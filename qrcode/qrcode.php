<?php

if (!defined('NGCMS')) die ('HAL');

class QRcodeNewsFilter extends NewsFilter {
	function showNews($newsID, $SQLnews, &$tvars) {
		global $config, $CurrentHandler, $tpl;

		$cacheFileName = md5('qrcode'.$newsID.$config['home_url'].$config['theme'].$config['default_lang']).'.txt';

		$cacheData = cacheRetrieveFile($cacheFileName, pluginGetVariable('qrcode', 'cacheExpire'), 'qrcode');
		if ($cacheData != false) {
			$tvars['vars']['plugin_qrcode'] = $cacheData;
			return 1;				
		}

		//$chl  = urlencode($chl);		
		$chl = newsGenerateLink($SQLnews, false, 0, true);
		
		$chs	= (intval(pluginGetVariable('qrcode','chs'))>546)?546:intval(pluginGetVariable('qrcode','chs'));
		$chs	= $chs .'x'. $chs;
		$chld	= pluginGetVariable('qrcode','chld');
		$margin	= intval(pluginGetVariable('qrcode','margin'));
		
	    $url  = 'http://chart.apis.google.com/chart?chs='.$chs.'&cht=qr&chl='.$chl.'&chld='.$chld.'|'.$margin;
		if	(pluginGetVariable('qrcode','upload'))		
			$url = uploadQRcode($newsID, $url);

		// Determine paths for all template files
		$tpath = locatePluginTemplates(array('qrcode'), 'qrcode', pluginGetVariable('qrcode', 'localsource'));
			$tpl -> template('qrcode', $tpath['qrcode']);
		$tpl -> vars('qrcode', array ('vars' => array ('qrcode' => $url, 'title' => $SQLnews['title'])));
		$tvars['vars']['plugin_qrcode'] = $tpl -> show('qrcode');	 
		cacheStoreFile($cacheFileName, $tvars['vars']['plugin_qrcode'], 'qrcode');

		return 1;
	}
}

register_filter('news', 'qrcode', new QRcodeNewsFilter);

function uploadQRcode($newsID, $url) {
	global $mysql, $fmanager;

	@include_once root.'includes/classes/upload.class.php';
	@include_once root.'includes/inc/file_managment.php';

	$fmanager = new file_managment();
	$imanager = new image_managment();
	
	$fmanager->get_limits('image');
	$dir = $fmanager->dname;

	if (!is_dir($dir.'/qrcode'))
		$fmanager->category_create('image', 'qrcode');

	$fparam = array('type' => 'image', 'category' => 'qrcode', 'manual' => 1, 'url' => $url.'.jpg', 'replace' => 1);
	$up		= $fmanager->file_upload($fparam);

	// Now write info about image into DB
	if (is_array($sz = $imanager->get_size($dir.'/qrcode/'.$up[1])))
		$mysql->query("update ".prefix."_".$fmanager->tname." set width=".db_squote($sz[1]).", height=".db_squote($sz[2]).",description=".$newsID." where id = ".db_squote($up[0]));

	$url = $fmanager->uname;
	$url .= '/qrcode/'.$up[1];

	return $url;
}