<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
LoadPluginLibrary('xfields', 'common');
LoadPluginLibrary('feedback', 'common');
register_htmlvar('js', admin_url . '/plugins/basket/js/basket.js');
//
// РћС‚РѕР±СЂР°Р¶РµРЅРёРµ РѕР±С‰РµР№ РёРЅС„РѕСЂРјР°С†РёРё/РѕСЃС‚Р°С‚РєРѕРІ РІ РєРѕСЂР·РёРЅРµ
function plugin_basket_total() {

	global $mysql, $twig, $userROW, $template;
	// РћРїСЂРµРґРµР»СЏРµРј СѓСЃР»РѕРІРёСЏ РІС‹Р±РѕСЂРєРё
	$filter = array();
	if (is_array($userROW)) {
		$filter [] = '(user_id = ' . db_squote($userROW['id']) . ')';
	}
	if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
		$filter [] = '(cookie = ' . db_squote($_COOKIE['ngTrackID']) . ')';
	}
	// РЎС‡РёС‚Р°РµРј РёС‚РѕРіРё
	$tCount = 0;
	$tPrice = 0;
	if (count($filter) && is_array($res = $mysql->record("select count(*) as count, sum(price*count) as price from " . prefix . "_basket where " . join(" or ", $filter), 1))) {
		$tCount = $res['count'];
		$tPrice = $res['price'];
	}
	// Р“РѕС‚РѕРІРёРј РїРµСЂРµРјРµРЅРЅС‹Рµ
	$tVars = array(
		'count' => $tCount,
		'price' => $tPrice,
	);
	// Р’С‹РІРѕРґРёРј С€Р°Р±Р»РѕРЅ
	$tpath = locatePluginTemplates(array('total'), 'basket', pluginGetVariable('basket', 'localsource'));
	$xt = $twig->loadTemplate($tpath['total'] . '/total.tpl');
	$template['vars']['plugin_basket'] = $xt->render($tVars);
}

//
// РџРѕРєР°Р·Р°С‚СЊ СЃРѕРґРµСЂР¶РёРјРѕРµ РєРѕСЂР·РёРЅС‹
function plugin_basket_list() {

	global $mysql, $twig, $userROW, $template;
	// РћРїСЂРµРґРµР»СЏРµРј СѓСЃР»РѕРІРёСЏ РІС‹Р±РѕСЂРєРё
	$filter = array();
	if (is_array($userROW)) {
		$filter [] = '(user_id = ' . db_squote($userROW['id']) . ')';
	}
	if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
		$filter [] = '(cookie = ' . db_squote($_COOKIE['ngTrackID']) . ')';
	}
	// Р’С‹РїРѕР»РЅСЏРµРј РІС‹Р±РѕСЂРєСѓ
	$recs = array();
	$total = 0;
	if (count($filter)) {
		foreach ($mysql->select("select * from " . prefix . "_basket where " . join(" or ", $filter), 1) as $rec) {
			$total += round($rec['price'] * $rec['count'], 2);
			$rec['sum'] = sprintf('%9.2f', round($rec['price'] * $rec['count'], 2));
			$rec['xfields'] = unserialize($rec['linked_fld']);
			unset($rec['linked_fld']);
			$recs [] = $rec;
		}
	}
	$tVars = array(
		'recs'     => count($recs),
		'entries'  => $recs,
		'total'    => sprintf('%9.2f', $total),
		'form_url' => generatePluginLink('feedback', null, array(), array('id' => intval(pluginGetVariable('basket', 'feedback_form')))),
	);
	// Р’С‹РІРѕРґРёРј С€Р°Р±Р»РѕРЅ
	$xt = $twig->loadTemplate('plugins/basket/list.tpl');
	$template['vars']['mainblock'] = $xt->render($tVars);
}

// Update basket content/counters
function plugin_basket_update() {

	global $mysql, $twig, $userROW, $template, $SUPRESS_TEMPLATE_SHOW;
	// РћРїСЂРµРґРµР»СЏРµРј СѓСЃР»РѕРІРёСЏ РІС‹Р±РѕСЂРєРё
	$filter = array();
	if (is_array($userROW)) {
		$filter [] = '(user_id = ' . db_squote($userROW['id']) . ')';
	}
	if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
		$filter [] = '(cookie = ' . db_squote($_COOKIE['ngTrackID']) . ')';
	}
	// Scan POST params
	if (count($filter)) {
		foreach ($_POST as $k => $v) {
			if (preg_match('#^count_(\d+)$#', $k, $m)) {
				if (intval($v) < 1) {
					$mysql->query("delete from " . prefix . "_basket where (id = " . db_squote($m[1]) . ") and (" . join(" or ", $filter) . ")");
				} else {
					$mysql->query("update " . prefix . "_basket set count = " . db_squote(intval($v)) . "where (id = " . db_squote($m[1]) . ") and (" . join(" or ", $filter) . ")");
				}
			}
		}
	}
	// Redirect to basket page
	$SUPRESS_TEMPLATE_SHOW = true;
	@header("Location: " . generatePluginLink('basket', null, array(), array(), false, true));
}

// XFields filter
if (class_exists('XFieldsFilter') && class_exists('FeedbackFilter')) {
	class BasketXFieldsFilter extends XFieldsFilter {

		function showTableEntry($newsID, $SQLnews, $rowData, &$rowVars) {

			global $DSlist;
			// РћРїСЂРµРґРµР»СЏРµРј - СЂР°Р±РѕС‚Р°РµРј Р»Рё РјС‹ РІРЅСѓС‚СЂРё СЃС‚СЂРѕРє С‚Р°Р±Р»РёС†
			if (!pluginGetVariable('basket', 'ntable_flag'))
				return;
			// Р Р°Р±РѕС‚Р°РµРј. РћРїСЂРµРґРµР»СЏРµРј СЂРµР¶РёРј СЂР°Р±РѕС‚С‹ - РїРѕ РІСЃРµРј СЃС‚СЂРѕРєР°Рј РёР»Рё РїРѕ СѓСЃР»РѕРІРёСЋ "РїРѕР»Рµ РёР· xfields РЅРµ СЂР°РІРЅРѕ РЅСѓР»СЋ"
			if (pluginGetVariable('basket', 'ntable_activated')) {
				if (!$rowData['xfields_' . pluginGetVariable('basket', 'ntable_xfield')])
					return;
			}
			$rowVars['flags']['basket_allow'] = true;
			$rowVars['basket_link'] = generatePluginLink('basket', 'add', array('ds' => $DSlist['#xfields:tdata'], 'id' => $rowData['id']), array(), false, true);
			// РЎС‚СЂРѕРєСѓ РјРѕР¶РЅРѕ РґРѕР±Р°РІР»СЏС‚СЊ РІ РєРѕСЂР·РёРЅСѓ
			//print "rowData <pre>(".var_export($rowVars, true).")</pre><br/>\n";
		}
	}

	// Feedback filter
	class BasketFeedbackFilter extends FeedbackFilter {

		// Action executed when form is showed
		function onShow($formID, $formStruct, $formData, &$tvars) {

			global $userROW, $mysql, $twig;
			// РџСЂРѕРІРµСЂСЏРµРј ID С„РѕСЂРјС‹ - РґР°РЅРЅС‹Рµ РєРѕСЂР·РёРЅС‹ РѕС‚РѕР±СЂР°Р¶Р°СЋС‚СЃСЏ С‚РѕР»СЊРєРѕ РІ РєРѕРЅРєСЂРµС‚РЅРѕР№ С„РѕСЂРјРµ
			if (pluginGetVariable('basket', 'feedback_form') != $formID)
				return;
			// РћРїСЂРµРґРµР»СЏРµРј СѓСЃР»РѕРІРёСЏ РІС‹Р±РѕСЂРєРё
			$filter = array();
			if (is_array($userROW)) {
				$filter [] = '(user_id = ' . db_squote($userROW['id']) . ')';
			}
			if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
				$filter [] = '(cookie = ' . db_squote($_COOKIE['ngTrackID']) . ')';
			}
			// Р’С‹РїРѕР»РЅСЏРµРј РІС‹Р±РѕСЂРєСѓ
			$recs = array();
			$total = 0;
			if (count($filter)) {
				foreach ($mysql->select("select * from " . prefix . "_basket where " . join(" or ", $filter)) as $rec) {
					$total += round($rec['price'] * $rec['count'], 2);
					$rec['sum'] = sprintf('%9.2f', round($rec['price'] * $rec['count'], 2));
					$rec['xfields'] = unserialize($rec['linked_fld']);
					unset($rec['linked_fld']);
					$recs [] = $rec;
				}
			}
			$tVars = array(
				'recs'    => count($recs),
				'entries' => $recs,
				'total'   => sprintf('%9.2f', $total),
			);
			// Р’С‹РІРѕРґРёРј С€Р°Р±Р»РѕРЅ
			$xt = $twig->loadTemplate('plugins/basket/lfeedback.tpl');
			$tvars['plugin_basket'] .= $xt->render($tVars);
		}

		function onProcess($formID, $formStruct, $formData, $flagHTML, &$tvars) {

			global $userROW, $mysql, $twig;
			// РџСЂРѕРІРµСЂСЏРµРј ID С„РѕСЂРјС‹ - РґР°РЅРЅС‹Рµ РєРѕСЂР·РёРЅС‹ РѕС‚РѕР±СЂР°Р¶Р°СЋС‚СЃСЏ С‚РѕР»СЊРєРѕ РІ РєРѕРЅРєСЂРµС‚РЅРѕР№ С„РѕСЂРјРµ
			if (pluginGetVariable('basket', 'feedback_form') != $formID)
				return 1;
			// РћРїСЂРµРґРµР»СЏРµРј СѓСЃР»РѕРІРёСЏ РІС‹Р±РѕСЂРєРё
			$filter = array();
			if (is_array($userROW)) {
				$filter [] = '(user_id = ' . db_squote($userROW['id']) . ')';
			}
			if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
				$filter [] = '(cookie = ' . db_squote($_COOKIE['ngTrackID']) . ')';
			}
			// Р’С‹РїРѕР»РЅСЏРµРј РІС‹Р±РѕСЂРєСѓ
			$recs = array();
			$total = 0;
			if (count($filter)) {
				foreach ($mysql->select("select * from " . prefix . "_basket where " . join(" or ", $filter)) as $rec) {
					$total += round($rec['price'] * $rec['count'], 2);
					$rec['sum'] = sprintf('%9.2f', round($rec['price'] * $rec['count'], 2));
					$rec['xfields'] = unserialize($rec['linked_fld']);
					unset($rec['linked_fld']);
					$recs [] = $rec;
				}
			}
			$bVars = array(
				'recs'    => count($recs),
				'entries' => $recs,
				'total'   => sprintf('%9.2f', $total),
			);
			// Р’С‹РІРѕРґРёРј С€Р°Р±Р»РѕРЅ
			$xt = $twig->loadTemplate('plugins/basket/lfeedback.tpl');
			$tvars['plugin_basket'] = $xt->render($bVars);
		}

		// Action executed when post request is completed
		function onProcessNotify($formID) {

			global $mysql, $userROW;
			// РћРїСЂРµРґРµР»СЏРµРј СѓСЃР»РѕРІРёСЏ РІС‹Р±РѕСЂРєРё
			$filter = array();
			if (is_array($userROW)) {
				$filter [] = '(user_id = ' . db_squote($userROW['id']) . ')';
			}
			if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
				$filter [] = '(cookie = ' . db_squote($_COOKIE['ngTrackID']) . ')';
			}
			// Р’С‹РїРѕР»РЅСЏРµРј РІС‹Р±РѕСЂРєСѓ
			if (count($filter)) {
				$mysql->query("delete from " . prefix . "_basket where " . join(" or ", $filter));
			}
		}
	}

	register_plugin_page('basket', '', 'plugin_basket_list', 0);
	register_plugin_page('basket', 'update', 'plugin_basket_update', 0);
	register_filter('xfields', 'basket', new BasketXFieldsFilter);
	register_filter('feedback', 'basket', new BasketFeedbackFilter);
} else {
	//print "Basket error: XFields and Feedback plugins must be activated";
}

// Perform replacements while showing news
class BasketNewsFilter extends NewsFilter {

	// Show news call :: processor (call after all processing is finished and before show)
	function showNews($newsID, $SQLnews, &$tvars, $mode = array()) {

		global $DSlist;
		// РћРїСЂРµРґРµР»СЏРµРј - СЂР°Р±РѕС‚Р°РµРј Р»Рё РјС‹ РІРЅСѓС‚СЂРё СЃС‚СЂРѕРє С‚Р°Р±Р»РёС†
		if (!pluginGetVariable('basket', 'news_flag')) {
			$tvars['regx']['#\[basket\](.*?)\[\/basket\]#is'] = '';

			return;
		}
		// Р Р°Р±РѕС‚Р°РµРј. РћРїСЂРµРґРµР»СЏРµРј СЂРµР¶РёРј СЂР°Р±РѕС‚С‹ - РїРѕ РІСЃРµРј СЃС‚СЂРѕРєР°Рј РёР»Рё РїРѕ СѓСЃР»РѕРІРёСЋ "РїРѕР»Рµ РёР· xfields РЅРµ СЂР°РІРЅРѕ РЅСѓР»СЋ"
		if (pluginGetVariable('basket', 'news_activated')) {
			if (!$SQLnews['xfields_' . pluginGetVariable('basket', 'news_xfield')]) {
				$tvars['regx']['#\[basket\](.*?)\[\/basket\]#is'] = '';

				return;
			}
		}
		$tvars['regx']['#\[basket\](.*?)\[\/basket\]#is'] = '$1';
		$tvars['vars']['basket_link'] = generatePluginLink('basket', 'add', array('ds' => $DSlist['news'], 'id' => $SQLnews['id']), array(), false, true);
	}
}

register_filter('news', 'basket', new BasketNewsFilter);
//
// Р’С‹Р·РѕРІ РѕР±СЂР°Р±РѕС‚С‡РёРєР°
add_act('index', 'plugin_basket_total');
