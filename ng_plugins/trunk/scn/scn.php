<?php

// #==========================================================#
// # Plugin name: scn [ Show news from the same category ]    #
// # Author: SwiZZeR                                          #
// # Allowed to use only with: CMS 2z                         #
// #==========================================================#

if (!defined('2z')) { die("Don't you figure you're so cool?"); }


class scnNewsFilter extends NewsFilter {
	// Show news call :: processor (call after all processing is finished and before show)
	function showNews($newsID, $SQLnews, &$tvars) { 

		global $config, $mysql, $tpl, $template;
	
		$counter   = intval(extra_get_param('popular','counter'));
		$number    = intval(extra_get_param('popular','number'));
		$maxlength = intval(extra_get_param('popular','maxlength'));



add_act('news_full', 'scn');

function scn(){
	global $catz, $mysql, $tvars;

	$number = (extra_get_param('scn','number')) ? extra_get_param('scn','number') : '5';
	$cat_arr = explode('-', category);

	foreach ($cat_arr as $v) {
		$cat_str .= $catz[$v]['id'].',';
	}

	$cat_str = substr($cat_str, 0, -1);

	if (extra_get_param('scn','orderby') == "4") { $orderby = 'id asc'; }
	elseif (extra_get_param('scn','orderby') == "3") { $orderby = 'id desc'; }
	elseif (extra_get_param('scn','orderby') == "2") { $orderby = 'postdate asc'; }
	elseif (extra_get_param('scn','orderby') == "1") { $orderby = 'postdate desc'; }
	elseif (extra_get_param('scn','orderby') == "0") { $orderby = 'rand()'; }

	$sql = "select * from ".prefix."_news where alt_name != '".altname."' and catid regexp '[".$cat_str."]' order by ".$orderby." limit 0,".$number;

	foreach($mysql->select($sql) as $row) {
		if (extra_get_param('scn','mantemplate') == '0') {
			$tpl .= '<a href="'.GetLink('full', $row).'" title="'.$row['title'].'">'.$row['title'].'</a><br />';
		}
		else {
			$tpl .= extra_get_param('scn','template');
			$tpl = str_replace('{link}', GetLink('full', $row), $tpl);
			$tpl = str_replace('{title}', $row['title'], $tpl);
			$tpl = str_replace('{date}', date('d/m/Y', $row['postdate']), $tpl);
			$tpl = str_replace('{author}', $row['author'], $tpl);
			$tpl = str_replace('{com}', $row['com'], $tpl);
			$tpl = str_replace('{views}', $row['views'], $tpl);
		}
	}

	$tvars['vars']['plugin_scn'] = $tpl;
}