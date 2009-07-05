<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

include_once root."/includes/news.php";

register_plugin_page('rss_export','','plugin_rss_export',0);

function plugin_rss_export(){
	function catURL($ids) {
		global $catz, $linkz;
		$idlist = explode(",",$ids);
		$nlist = array();
		foreach ($idlist as $idc) {
			if (is_array($t = GetCategoryById($idc))) {
				$nlist[] = $t['alt'];
			}
		}
		return GetLink('category', array('alt' => implode("-",$nlist)));
	}

   	global $lang, $PFILTERS;
	global $template, $config, $SUPRESS_TEMPLATE_SHOW, $SUPRESS_MAINBLOCK_SHOW, $mysql, $catz;

	// Generate header
	if ($_REQUEST['category']) { $xcat = $catz[$_REQUEST['category']]; } else { $xcat = ''; }


	// Generate cache file name [ we should take into account SWITCHER plugin ]
	// Take into account: FLAG: use_hide, check if user is logged in
	$cacheFileName = md5('rss_export'.$config['theme'].$config['home_url'].$config['default_lang'].$xcat.extra_get_param('rss_export','use_hide').is_array($userROW)).'.txt';

	if (extra_get_param('rss_export','cache')) {
		$cacheData = cacheRetrieveFile($cacheFileName, extra_get_param('rss_export','cacheExpire'), 'rss_export');
		if ($cacheData != false) {
			// We got data from cache. Return it and stop
			$SUPRESS_TEMPLATE_SHOW = 1;
			$SUPRESS_MAINBLOCK_SHOW = 1;
			print $cacheData;
			return;
		}
	}

	// Generate output
	$output = plugin_rss_export_mk_header($xcat);

	$limit = extra_get_param('rss_export','news_count');
	if ((!is_numeric($limit)) || ($limit<0) || ($limit>500)) { $limit = 50; }
	$old_locale = setlocale(LC_TIME,0);
	setlocale(LC_TIME,'en_EN');
	if (is_array($xcat)) {
		$query = "select * from ".prefix."_news where catid regexp '[[:<:]](".$xcat['id'].")[[:>:]]' and approve=1 order by ".$xcat['orderby'];
	} else {
		$query = "select * from ".prefix."_news where approve=1 order by id desc";
	}


	// Prepare hide template
	if ($config['blocks_for_reg'] && extra_get_param('rss_export','use_hide')) {
		LoadPluginLang('rss_export', 'main','','rexport');
		$hide_template = @file_get_contents(root.'plugins/rss_export/templates/hide.tpl');
		$hide_template = str_replace('{text}',$lang['rexport_hide'],$hide_template);
	}


	foreach ($mysql->select($query." limit $limit") as $row) {
	        // Make standart system call in 'export' mode
	        $export_mode = 'export_body';

		switch (extra_get_param('rss_export','content_show')) {
			case '1': $export_mode = 'export_short'; break;
			case '2': $export_mode = 'export_full'; break;
		}

        $content = news_showone($row['id'], '', array( 'emulate' => $row, 'style' => $export_mode, 'plugin' => 'rss_export' ));

		$enclosure = '';

		// Check if Enclosure `xfields` integration is activated
		if (extra_get_param('rss_export', 'xfEnclosureEnabled') && (true || getPluginStatusActive('xfields'))) {
			// Load (if needed XFIELDS plugin
			include_once(root."/plugins/xfields/xfields.php");

			if (is_array($xfd = xf_decode($row['xfields'])) && isset($xfd[extra_get_param('rss_export','xfEnclosure')])) {
				$enclosure = $xfd[extra_get_param('rss_export','xfEnclosure')];
			}
		}

		$output .= "  <item>\n";
		$output .= "   <title><![CDATA[".((extra_get_param('rss_export','news_title') == 1)&&GetCategories($row['catid'],true)?GetCategories($row['catid'], true).' :: ':'').secure_html($row['title'])."]]></title>\n";
		$output .= "   <link><![CDATA[".GetLink('full', $row)."]]></link>\n";
		$output .= "   <description><![CDATA[".$content."]]></description>\n";

		// Output enclosure URL (if configured & set
		if ($enclosure != '')
			$output .= '   <enclosure url="'.$enclosure.'" />'."\n";

		$output .= "   <category>".GetCategories($row['catid'], true)."</category>\n";
		$output .= "   <guid isPermaLink=\"false\">".home."?id=".$row['id']."</guid>\n";
		$output .= "   <pubDate>".strftime('%a, %d %b %Y %H:%M:%S GMT',$row['postdate'])."</pubDate>\n";
		$output .= "  </item>\n";
	}
	setlocale(LC_TIME,$old_locale);
	$output .= " </channel>\n</rss>\n";

	$SUPRESS_TEMPLATE_SHOW = 1;
	$SUPRESS_MAINBLOCK_SHOW = 1;

	print $output;

	if (extra_get_param('rss_export','cache')) {
		cacheStoreFile($cacheFileName, $output, 'rss_export');
	}
}


function plugin_rss_export_mk_header($xcat) {
	global $config;
 $line = '<?xml version="1.0" encoding="windows-1251"?>'."\n";
 $line.= ' <rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/">'."\n";
 $line.= " <channel>\n";
 if (extra_get_param('rss_export','feed_title_format') == 'handy') {
	$line.= "  <title><![CDATA[".extra_get_param('rss_export', 'feed_title_value')."]]></title>\n";
 } else if (extra_get_param('rss_export', 'feed_title_format') == 'site_title') {
	$line.= "  <title><![CDATA[".$config['home_title'].(is_array($xcat)?' :: '.$xcat['name']:'')."]]></title>\n";
 } else {
	$line.= "  <title><![CDATA[".$config['home_title']."]]></title>\n";
 }
 $line.= "  <link><![CDATA[".$config['home_url']."]]></link>\n";
 $line.= "  <language>ru</language>\n";
 $line.= "  <description><![CDATA[".$config['description']."]]></description>\n";
 $line.= "  <generator><![CDATA[Plugin RSS_EXPORT (0.07) // Next Generation CMS (".engineVersion.")]]></generator>\n";
 return $line;
}

