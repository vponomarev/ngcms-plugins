<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

LoadPluginLibrary('xfields', 'common');
LoadPluginLibrary('feedback', 'common');

register_htmlvar('js', admin_url.'/plugins/basket/js/basket.js');

//
// ����������� ����� ����������/�������� � �������
function plugin_basket_total() {
	global $mysql, $twig, $userROW, $template;

	// ���������� ������� �������
	$filter = array();
	if (is_array($userROW)) {
		$filter []= '(user_id = '.db_squote($userROW['id']).')';
	}

	if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
		$filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';
	}

	// ������� �����
	$tCount = 0;
	$tPrice = 0;

	if (count($filter) && is_array($res = $mysql->record("select count(*) as count, sum(price*count) as price from ".prefix."_basket where ".join(" or ", $filter), 1))) {
		$tCount = $res['count'];
		$tPrice = $res['price'];
	}

	// ������� ����������
	$tVars = array(
		'count' => $tCount,
		'price' => $tPrice,
	);

	// ������� ������
	$tpath = locatePluginTemplates(array('total'), 'basket', pluginGetVariable('basket', 'localsource'));

	$xt = $twig->loadTemplate($tpath['total'].'/total.tpl');
	$template['vars']['plugin_basket'] = $xt->render($tVars);
}


//
// �������� ���������� �������
function plugin_basket_list(){
	global $mysql, $twig, $userROW, $template;

	// ���������� ������� �������
	$filter = array();
	if (is_array($userROW)) {
		$filter []= '(user_id = '.db_squote($userROW['id']).')';
	}

	if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
		$filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';
	}

	// ��������� �������
	$recs = array();
	$total = 0;
	if (count($filter)) {
		foreach ($mysql->select("select * from ".prefix."_basket where ".join(" or ", $filter), 1) as $rec) {
			$total += round($rec['price'] * $rec['count'], 2);

			$rec['sum'] = sprintf('%9.2f', round($rec['price'] * $rec['count'], 2));
			$rec['xfields'] = unserialize($rec['linked_fld']);
			unset($rec['linked_fld']);

			$recs []= $rec;
		}
	}


	$tVars = array(
		'recs'		=> count($recs),
		'entries'	=> $recs,
		'total'		=> sprintf('%9.2f', $total),
		'form_url'  => generatePluginLink('feedback', null, array(), array('id' => intval(pluginGetVariable('basket', 'feedback_form')))),
	);

	// ������� ������
	$xt = $twig->loadTemplate('plugins/basket/list.tpl');
	$template['vars']['mainblock'] = $xt->render($tVars);
}


// Update basket content/counters
function plugin_basket_update() {
	global $mysql, $twig, $userROW, $template, $SUPRESS_TEMPLATE_SHOW;

	// ���������� ������� �������
	$filter = array();
	if (is_array($userROW)) {
		$filter []= '(user_id = '.db_squote($userROW['id']).')';
	}

	if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
		$filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';
	}

	// Scan POST params
	if (count($filter)) {
		foreach ($_POST as $k => $v) {
			if (preg_match('#^count_(\d+)$#', $k, $m)) {
				if (intval($v) < 1) {
					$mysql->query("delete from ".prefix."_basket where (id = ".db_squote($m[1]).") and (".join(" or ", $filter).")");
				} else {
					$mysql->query("update ".prefix."_basket set count = ".db_squote(intval($v))."where (id = ".db_squote($m[1]).") and (".join(" or ", $filter).")");
				}
			}
		}
	}

	// Redirect to basket page
	$SUPRESS_TEMPLATE_SHOW = true;
	@header("Location: ".generatePluginLink('basket', null, array(), array(), false, true));
}



// XFields filter
if (class_exists('XFieldsFilter') && class_exists('FeedbackFilter')) {
	class BasketXFieldsFilter extends XFieldsFilter {

		function showTableEntry($newsID, $SQLnews, $rowData, &$rowVars) {
			// ���������� - �������� �� �� ������ ����� ������
			if (!pluginGetVariable('basket', 'ntable_flag'))
				return;

			// ��������. ���������� ����� ������ - �� ���� ������� ��� �� ������� "���� �� xfields �� ����� ����"
			if (pluginGetVariable('basket', 'ntable_activated')) {
				if (!$rowData['xfields_'.pluginGetVariable('basket', 'ntable_xfield')])
					return;
			}

			$rowVars['flags']['basket_allow'] = true;
			$rowVars['basket_link'] = generatePluginLink('basket', 'add', array('ds' => $DSlist['#xfields:tdata'], 'id' => $rowData['id']), array(), false, true);

			// ������ ����� ��������� � �������
			//print "rowData <pre>(".var_export($rowVars, true).")</pre><br/>\n";
		}

	}

	// Feedback filter
	class BasketFeedbackFilter extends FeedbackFilter {

		// Action executed when form is showed
		function onShow($formID, $formStruct, $formData, &$tvars) {
			global $userROW, $mysql, $twig;

			// ��������� ID ����� - ������ ������� ������������ ������ � ���������� �����
			if (pluginGetVariable('basket', 'feedback_form') != $formID)
				return;

			// ���������� ������� �������
			$filter = array();
			if (is_array($userROW)) {
				$filter []= '(user_id = '.db_squote($userROW['id']).')';
			}

			if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
				$filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';
			}

			// ��������� �������
			$recs = array();
			$total = 0;
			if (count($filter)) {
				foreach ($mysql->select("select * from ".prefix."_basket where ".join(" or ", $filter)) as $rec) {
					$total += round($rec['price'] * $rec['count'], 2);

					$rec['sum'] = sprintf('%9.2f', round($rec['price'] * $rec['count'], 2));
					$rec['xfields'] = unserialize($rec['linked_fld']);
					unset($rec['linked_fld']);

					$recs []= $rec;
				}
			}


			$tVars = array(
				'recs'		=> count($recs),
				'entries'	=> $recs,
				'total'		=> sprintf('%9.2f', $total),
			);

			// ������� ������
			$xt = $twig->loadTemplate('plugins/basket/lfeedback.tpl');
			$tvars['plugin_basket'] .= $xt->render($tVars);
		}


		function onProcess($formID, $formStruct, $formData, $flagHTML, &$tVars) {
			global $userROW, $mysql, $twig;

			// ��������� ID ����� - ������ ������� ������������ ������ � ���������� �����
			if (pluginGetVariable('basket', 'feedback_form') != $formID)
				return 1;

			// ���������� ������� �������
			$filter = array();
			if (is_array($userROW)) {
				$filter []= '(user_id = '.db_squote($userROW['id']).')';
			}

			if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
				$filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';
			}

			// ��������� �������
			$recs = array();
			$total = 0;
			if (count($filter)) {
				foreach ($mysql->select("select * from ".prefix."_basket where ".join(" or ", $filter)) as $rec) {
					$total += round($rec['price'] * $rec['count'], 2);

					$rec['sum'] = sprintf('%9.2f', round($rec['price'] * $rec['count'], 2));
					$rec['xfields'] = unserialize($rec['linked_fld']);
					unset($rec['linked_fld']);

					$recs []= $rec;
				}
			}

			$tVars = array(
				'recs'		=> count($recs),
				'entries'	=> $recs,
				'total'		=> sprintf('%9.2f', $total),
			);

			// ������� ������
			$xt = $twig->loadTemplate('plugins/basket/lfeedback.tpl');
			$tVars['plugin_basket'] = $xt->render($tVars);
		}

		// Action executed when post request is completed
		function onProcessNotify($formID){
			global $mysql, $userROW;

			// ���������� ������� �������
			$filter = array();
			if (is_array($userROW)) {
				$filter []= '(user_id = '.db_squote($userROW['id']).')';
			}

			if (isset($_COOKIE['ngTrackID']) && ($_COOKIE['ngTrackID'] != '')) {
				$filter []= '(cookie = '.db_squote($_COOKIE['ngTrackID']).')';
			}

			// ��������� �������
			if (count($filter)) {
				$mysql->query("delete from ".prefix."_basket where ".join(" or ", $filter));
			}
		}
	}

	register_plugin_page('basket','','plugin_basket_list',0);
	register_plugin_page('basket','update','plugin_basket_update',0);
	register_filter('xfields','basket', new BasketXFieldsFilter);
	register_filter('feedback','basket', new BasketFeedbackFilter);
} else {
	//print "Basket error: XFields and Feedback plugins must be activated";
}

// Perform replacements while showing news
class BasketNewsFilter extends NewsFilter {
	// Show news call :: processor (call after all processing is finished and before show)
	function showNews($newsID, $SQLnews, &$tvars, $mode = array()) {
		global $DSlist;

		// ���������� - �������� �� �� ������ ����� ������
		if (!pluginGetVariable('basket', 'news_flag')) {
			$tvars['regx']['#\[basket\](.*?)\[\/basket\]#is'] = '';
			return;
		}

		// ��������. ���������� ����� ������ - �� ���� ������� ��� �� ������� "���� �� xfields �� ����� ����"
		if (pluginGetVariable('basket', 'news_activated')) {
			if (!$SQLnews['xfields_'.pluginGetVariable('basket', 'news_xfield')]) {

				$tvars['regx']['#\[basket\](.*?)\[\/basket\]#is'] = '';
				return;
			}
		}

		$tvars['regx']['#\[basket\](.*?)\[\/basket\]#is'] = '$1';
		$tvars['vars']['basket_link'] = generatePluginLink('basket', 'add', array('ds' => $DSlist['news'], 'id' => $SQLnews['id']), array(), false, true);
	}

}

register_filter('news','basket', new BasketNewsFilter);

//
// ����� �����������
add_act('index', 'plugin_basket_total');
