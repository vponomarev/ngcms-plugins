<?php

// #====================================================================================#
// # ������������ �������: finance [ Finance manager ]                                  #
// # ��������� � ������������� �: Next Generation CMS                                   #
// # �����: Vitaly A Ponomarev, vp7@mail.ru                                             #
// #====================================================================================#

// #====================================================================================#
// # ���� �������                                                                       #
// #====================================================================================#
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

// �������� ����������
include_once(root."/plugins/finance/inc/finance.php");

// �������������� ���
financeInitCache();

// ���������� �������� ����
loadPluginLang('finance', 'main', '', '', ':');


//
// ������ �������� (��� ���������� �����)
//
class FinanceNewsFilter extends NewsFilter {
	function addNewsForm(&$tvars) {
		$tvars['plugin']['finance']  = '<tr><td width="100%" class="contentHead"><img src="'.admin_url.'/skins/default/images/nav.gif" hspace="8" alt="" />������� ������ � �������</td></tr>';
		$tvars['plugin']['finance'] .= '<tr><td width="100%" class="contentEntry1"><table><tr><td>��������� �������:</td><td><input name="fin_price" /> <small>( � ������� <b>xxx.xx</b>, ���� �� �������, �� ������ � ������� ���������)</small></td></tr></table></td></tr>';
		return 1;
	}
	function addNews(&$tvars, &$SQL) {
	        $SQL['fin_price']   = $_REQUEST['fin_price'];
		return 1;
	}
	function editNewsForm($newsID, $SQLold, &$tvars) {
		$tvars['plugin']['finance']  = '<tr><td width="100%" class="contentHead"><img src="'.admin_url.'/skins/default/images/nav.gif" hspace="8" alt="" />������� ������ � �������</td></tr>';
		$tvars['plugin']['finance'] .= '<tr><td width="100%" class="contentEntry1"><table><tr><td>��������� �������:</td><td><input name="fin_price" value="'.$SQLold['fin_price'].'" /> <small>( � ������� <b>xxx.xx</b>, ���� �� �������, �� ������ � ������� ���������)</small></td></tr></table></td></tr>';
		return 1;
	}
	function editNews($newsID, $SQLold, &$SQLnew, &$tvars) {
	        $SQLnew['fin_price']   = $_REQUEST['fin_price'];
		return 1;
	}
	function showNews($newsID, $SQLnews, &$tvars, $mode = array()) {
		global $tpl, $mysql, $userROW, $FINANCE_MONEY_ACCEPTORS;

		// ���� ���� �� ���������� - ����������
		$tvars['regx']["#\[finance\](.*?)\[/finance\]#s"] = $SQLnews['fin_price']?"$1":'';
		if (!$SQLnews['fin_price']) { return; }

		// ���������� ���� "����"
		$tvars['vars']['fin_price'] = $SQLnews['fin_price'];

		// ����� ������� ������. ��������� ��� �� � ������������ ��� � ��� �������
		if ($userROW['id'] &&($srow = $mysql->record("select * from ".prefix."_subscribe_manager where user_id = ".db_squote($userROW['id'])." and access_element_id = ".db_squote($SQLnews['id'])))) {
			// ������ ����. ��������� �������
			foreach (array('story', 'short-story', 'full-story') as $item) {
				if (isset($tvars['vars'][$item])) {
					$tvars['vars'][$item] = preg_replace('#\[fin_lock\](.+?)\[\/fin_lock\]#','$1',$tvars['vars'][$item]);
				}
			}

			$tvars['regx']["#\[fin_on\](.*?)\[/fin_on\]#s"] = "$1";
			$tvars['regx']["#\[fin_off\](.*?)\[/fin_off\]#s"] = '';
			return;
		}

		$tvars['regx']["#\[fin_on\](.*?)\[/fin_on\]#s"] = '';
		$tvars['regx']["#\[fin_off\](.*?)\[/fin_off\]#s"] = '$1';


		// ���������� ��������� (�������� � � ������/��������)
		$price = $SQLnews['fin_price'];
		if (is_array($userROW)) {
			$ubalance = sprintf('%5.2f',finance_check_money($userROW['name'])/100);
			$enough = finance_check_enough_money($userROW['name'],0,0,$price*100)?1:0;
		} else {
			$ubalance = 0;
			$enough = 0;
		}

		if (is_array($userROW)) {
			// ���� ���������
			load_extras('fin_acceptors');
			if ($enough) {
			 	$tpl->template('enough',extras_dir.'/finance/tpl');
			 	$tpl->vars('enough', array('vars'=>array('price' => $price, 'ubalance' => $ubalance, 'newsid' => $SQLnews['id'], 'backurl' => $_SERVER['REQUEST_URI'])));
			 	$text = $tpl->show('enough');
			} else {
				$needsum = $price - $ubalance;

				$aclist = '';
				foreach ($FINANCE_MONEY_ACCEPTORS as $acceptor) {
					if ($acceptor->active) {
						$aclist .= '<li><a href="'.
							generateLink('core', 'plugin', array('plugin' => 'finance'), array('mode' => 'pay_accept_form', 'acceptor' => $acceptor->id, 'needsum' => $needsum, 'back' => $_SERVER['REQUEST_URI'])).
							//'/plugin/finance/?mode=pay_accept_form&acceptor='.$acceptor->id.'&needsum='.$needsum.'&back='.$_SERVER['REQUEST_URI'].
							'">'.$acceptor->name.'</a></li>';
					}
				}
			 	$tpl->template('notenough',extras_dir.'/finance/tpl');
			 	$tpl->vars('notenough', array('vars'=>array('price' => $price, 'ubalance' => $ubalance, 'acceptors' => $aclist)));
			 	$text = $tpl->show('notenough');
			}
		} else {
			$tpl->template('needauth',extras_dir.'/finance/tpl');
			$tpl->vars('needauth', array('vars'=>array('price' => $price)));
			$text = $tpl->show('needauth');
		}

		foreach (array('story', 'short-story', 'full-story') as $item) {
			if (isset($tvars['vars'][$item])) {
				$tvars['vars'][$item] = preg_replace('#\[fin_lock\].+?\[\/fin_lock\]#',$text,$tvars['vars'][$item]);
			}
		}
	}
}

register_filter('news','finance', new FinanceNewsFilter);
register_plugin_page('finance','','plugin_finance_screen',0);

add_act('index', 'finance_info');
add_act('usermenu', 'finance_info_menu');

function finance_info($sth) {
 global $userROW, $template;

 $template['vars']['plugin_finance_balance'] = is_array($userROW)?(sprintf("%.02f", finance_check_money($userROW['name'])/100).' '.pluginGetVariable('finance', 'syscurrency')):'';
}

function finance_info_menu($sth) {
 global $userROW, $tvars;

 $tvars['vars']['plugin_finance_balance'] = is_array($userROW)?(sprintf("%.02f", finance_check_money($userROW['name'])/100).' '.pluginGetVariable('finance', 'syscurrency')):'';
 //$tvars['vars']['plugin_finance_balance'] = is_array($userROW)?sprintf("%.02f", finance_check_money($userROW['name'])/100):'';
}

//
// Generate PluginPage
//
function plugin_finance_screen() {
 global $template;

 load_extras('fin_acceptors');
 $mode = isset($_REQUEST['mode'])?$_REQUEST['mode']:'';
 switch ($mode) {
 	case 'pay': plugin_finance_pay(); break;
 	case 'pay_accept_form': plugin_finance_pay_accept(1); break;
 	case 'pay_accept': plugin_finance_pay_accept(); break;
 	default: plugin_finance_report(); break;
 }
}



//
// �������� ������ ������� [������ � �������]
//
function plugin_finance_pay() {
 global $mysql, $username, $userROW, $template;

 $access_type = intval($_REQUEST['access_type'])+0;
 $access_element_id = intval($_REQUEST['access_element_id']);

 if (!$username) { return false; }
 // �������� ������ �� �������
 if ($row = $mysql->record("select * from ".prefix."_news where id = ".$access_element_id)) {
	// ������� �������. ���������, �� ���������� �� �� ��� � ��� ������?
	if ($prow = $mysql->record("select * from ".prefix."_subscribe_manager where user_id = ".$userROW['id'].' and access_element_id = '.$access_element_id)) {
		// ��� ����������!
		$template['vars']['mainblock'] = '�� ��� �������� ������ ������.';
		return;
	}

	if (!$row['fin_price']) {
		$template['vars']['mainblock'] = '������ � ������ ������� ���������.';
		return;
	}
	// �������� ��������
	if (finance_pay(array('id' => $userROW['id']), array('type' => 'money', 'value' => array( 'money' => $row['fin_price'] * 100), 'description' => 'Payment for access'))) {
		// ����� ������ �������. ������������� ������
		$mysql->query("insert into ".prefix."_subscribe_manager(user_id, special_access_type, access_element_id) values(".db_squote($userROW['id']).", $access_type, $access_element_id)");
		$template['vars']['mainblock'] = '������ ������� �������, ������ ������.'.($_REQUEST['back']?'<br /><a href="'.$_REQUEST['back'].'">��������� �����</a>':'');
	} else {
		// ����� �� ������
		$template['vars']['mainblock'] = '� ��� �� ����� ������������ ����� ��� ���������� �������.';
	}
 } else {
	$template['vars']['mainblock'] = '�������, ������ � ������� �� ������ ��������, �� �������.';
 }
}


//
//
//
function plugin_finance_report() {
global $template, $tpl, $userROW, $username, $FINANCE_MONEY_ACCEPTORS;

	if (!is_array($userROW)) {
		$template['vars']['mainblock'] = '���������� ���������� �������� ������ ������������������ �������������!';
	}

	// Determine paths for all template files
	$tpath = locatePluginTemplates(array('screen', 'screen.entries'), 'finance', extra_get_param('finance', 'localsource'));

	$entries = '';
	$tpl -> template('screen.entries', $tpath['screen.entries']);

	$ubalance = sprintf('%5.2f',finance_check_money($username)/100);
	foreach ($FINANCE_MONEY_ACCEPTORS as $acceptor) {
		if ($acceptor->active) {
			$tvars = array( 'vars' => array( 'link' => generateLink('core', 'plugin', array('plugin' => 'finance'), array('mode' => 'pay_accept_form', 'acceptor' => $acceptor->id)), 'name' => $acceptor->name));
			$tpl -> vars('screen.entries', $tvars);
			$entries .= $tpl -> show('screen.entries');
		}
	}

	$tvars = array( 'vars' => array( 'ubalance' => $ubalance, 'currency' => pluginGetVariable('finance', 'syscurrency'),'entries' => $entries));
	$tpl -> template('screen', $tpath['screen.entries']);
	$tpl -> vars('screen', $tvars);
	$template['vars']['mainblock'] .= $tpl -> show('screen');

}


//
//
//
function plugin_finance_pay_accept($need_form=0) {
        global $FINANCE_MONEY_ACCEPTORS, $template;
	$acceptor_id = $_REQUEST['acceptor'];
	foreach ($FINANCE_MONEY_ACCEPTORS as $acceptor) {
		if ($acceptor->active && ($acceptor->id == $acceptor_id)) {
			// ���������� ��� ���� �������
			if ($need_form) {
				// ������� ����� �� ���������
				$template['vars']['mainblock'] = $acceptor->paymentAcceptForm(isset($_REQUEST['needsum'])?secure_html($_REQUEST['needsum']):'');
			} else {
				// ������������������ ���������� �������
				$template['vars']['mainblock'] = $acceptor->paymentAccept();
			}
			return;
		}
	}
	$template['vars']['mainblock'] = '������: ��������� finance acceptor ('.$acceptor_id	.') �� ������';
}