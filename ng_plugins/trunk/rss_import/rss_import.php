<?php

if (!defined('2z')) { die("Don't you figure you're so cool?"); }


register_plugin_page('rss_import','','plugin_rss_import',0);


function plugin_rss_import(){
	global $template, $config, $SUPRESS_TEMPLATE_SHOW, $SUPRESS_MAINBLOCK_SHOW, $mysql, $catz, $tpl;

	$rss_url = $_REQUEST['url'];

	$tvars['vars']['url'] = $rss_url;
	$tvars['vars']['error_msg'] = '';
	$tvars['vars']['title'] = '';
	$tvars['vars']['description'] = '';

	$tpl->template('import_main',extras_dir.'/rss_import/tpl');


	$ok = 0;
	if ($rss_url) {

		@include_once "inc/rss.inc.php";
		$rss = new RSS_PROCESSOR;
		if ($rss->scan($rss_url)) {
			$ok = 1;
			$tvars['vars']['title'] = $rss->result['channel']['title'];
			$tvars['vars']['description'] = $rss->result['channel']['description'];

		} else {
			$tvars['vars']['error_msg'] = "Не могу просканировать URL '$rss_url'";
		}	
	}

	$tpl->vars('import_main', $tvars);
	$template['vars']['mainblock'] = $tpl->show('import_main');

	if (!$ok) { return; }
	$clist = CategoryList();

	$tpl->template('import_news',extras_dir.'/rss_import/tpl');

	for ($i = 0; $i < count($rss->result['items']); $i++) {
		$mynews = $rss->result['items'][$i];
		$tvars['vars'] = array ('num' => $i, 'title' => $mynews['title'], 'description' => $mynews['description'], 'guid' => $mynews['guid'], 'link' => $mynews['link'], 'date' => $mynews['pubdate'], 'catlist' => $clist);

		$tpl->vars('import_news', $tvars);
		$template['vars']['mainblock'] .= $tpl->show('import_news');
	}

}

?>