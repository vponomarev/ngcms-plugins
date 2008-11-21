<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

class VarMgrNewsFilter extends NewsFilter {
	function showNews($newsID, $SQLnews, &$tvars) {
        global $lang, $langShortMonths, $langMonths;
		if (extra_get_param('varmgr','extdate')) {

			$tvars['vars']['day']		= date('j',$SQLnews['postdate']);
			$tvars['vars']['day0']		= date('d',$SQLnews['postdate']);
			$tvars['vars']['month']		= date('n',$SQLnews['postdate']);
			$tvars['vars']['month0']	= date('m',$SQLnews['postdate']);
			$tvars['vars']['year']		= date('y',$SQLnews['postdate']);
			$tvars['vars']['year2']		= date('Y',$SQLnews['postdate']);

			$tvars['vars']['month_s'] = $langShortMonths[$tvars['vars']['month']-1];
			$tvars['vars']['month_l'] = $langMonths[$tvars['vars']['month']-1];

			if (extra_get_param('varmgr','newdate')) {
				$t = extra_get_param('varmgr','newdate');
				foreach (array('day', 'day0', 'month', 'month0', 'year', 'year2', 'month_text_short', 'month_text_long') as $k) {
					$t = str_replace('{'.$k.'}',$tvars['vars'][$k],$t);
				}
				$tvars['vars']['date'] = $t;
			}
		}
	}
}

register_filter('news','varmgr', new VarMgrNewsFilter);

