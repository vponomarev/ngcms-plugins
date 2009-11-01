<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

include_once(root."/plugins/finance/inc/finance.php");

class Finance_Acceptor_WM extends Finance_Acceptor {
	function Finance_Acceptor_WM() {
		parent::Finance_Acceptor();

		// ���������� ������ �������������� �����
		$sCurrency = array();
		foreach (array('wmz', 'wmr', 'wme') as $c) {
			if (pluginGetVariable('fin_wm', 'allow_'.$c))
				array_push($sCurrency, $c);
		}
		$sList = join('/', $sCurrency);
		if (!count($sCurrency)) {
			$sList = '��� �������������� �����';
		}

		$this->active = 1;
		$this->id   = 'wm';
		$this->type = 'WebMoney';
		$this->name = 'WebMoney ('.$sList.')';
	}

	function paymentAcceptForm($sum = 0) {
		global $tpl, $username, $userROW;

		if ($_REQUEST['result_ok']) {
		$tpl->template('result_ok',extras_dir.'/fin_wm/tpl');
			$tpl->vars('result_ok', array( 'vars' => array()));
			return $tpl->show('result_ok');
 	 	}

		if ($_REQUEST['result_fail']) {
			$tpl->template('result_fail',extras_dir.'/fin_wm/tpl');
			$tpl->vars('result_fail', array( 'vars' => array()));
			return $tpl->show('result_fail');
 		}

		if (!$username) {
	 		return '��� ���������� ����� ��� ���������� �������������� ��������������';
		}

		$tpl->template('pay_form',extras_dir.'/fin_wm/tpl');
		$tvars['vars']['syscurrency'] = pluginGetVariable('finance', 'syscurrency');

		// ���������� ������ �������������� �����
		$sCurrency = array();
		foreach (array('wmz', 'wmr', 'wme') as $c) {
			if (pluginGetVariable('fin_wm', 'allow_'.$c))
				array_push($sCurrency, '<option value="'.pluginGetVariable('fin_wm', $c.'_number').'">'.$c.' (1 <b>'.$c.'</b> = '.doubleval(pluginGetVariable('fin_wm', $c.'_rate')).' '.pluginGetVariable('finance', 'syscurrency').')</option>');
		}
		$tvars['vars']['currency_list'] = join('', $sCurrency);
		$tvars['vars']['sum'] = $sum;
		$tvars['vars']['wm_number'] = strtoupper(extra_get_param('fin_wm', 'wm_number'));
		$tvars['vars']['userid'] = $userROW['id'];
		$tvars['vars']['login'] = $userROW['name'];
		$tvars['vars']['home'] = home;
		$tvars['vars']['descr'] = '���������� ����������������� ����� (ID:'.$userROW['id'].'|'.$userROW['name'].'|'.home.')';
		$tpl->vars('pay_form', $tvars);
		return $tpl->show('pay_form');
	}

	function paymentAccept() {
		global $tpl, $username, $SUPRESS_TEMPLATE_SHOW;


		//
		// ���� �������� ������ �� ������� WebMoney
		//
		$WM = array();
		foreach ( array('mode', 'payment_amount', 'payee_purse', 'payment_no', 'payer_wm', 'payer_purse', 'sys_invs_no', 'sys_trans_no', 'sys_trans_date', 'hash') as $k) {
			$WM[$k] = $_REQUEST['LMI_'.strtoupper($k)];
		}

		$SUPRESS_TEMPLATE_SHOW = 1;

		// ���������� �� ����� ������ ��� �����
		$pType   = '';
		$pNumber = '';
		$pRating = 0;

		foreach (array('wmz', 'wmr', 'wme') as $c) {
			if (pluginGetVariable('fin_wm', 'allow_'.$c) && ($WM['payee_purse'] == strtoupper(pluginGetVariable('fin_wm', $c.'_number')))) {
				$pNumber = $WM['payee_purse'];
				$pRating = doubleval(pluginGetVariable('fin_wm', $c.'_rate'));
				$pType = $c;
				break;
			}
		}

		// ��������� ������������ ����������
		if ($pNumber == '') {
			// �������� ����� �������� ��� ����� ��������
			return '������ (����� �� �������� ������)';
		}

		$checkline = $WM['payee_purse'].$WM['payment_amount'].$WM['payment_no'].$WM['mode'].$WM['sys_invs_no'].$WM['sys_trans_no'].$WM['sys_trans_date'].extra_get_param('fin_wm','secret_key').$WM['payer_purse'].$WM['payer_wm'];
		$checksym = md5($checkline);

		// ���� �� �������� ���� - ������ ������������
		if (!$WM['hash']) {
			return 'YES';
		}

		if (strtolower($checksym) != strtolower($WM['hash'])) {
			return "������ (������������ ���-������)\n";
		} else {
			// �������� ������ �������. ���� ����� � ����� �� ����
			$login = $_REQUEST['login'];
			if ($login) {
				// ������������ ����� �������
				$sum = doubleval($WM['payment_amount']) * $pRating;
				finance_add_money($login, extra_get_param('fin_wm', 'balance_no'), $sum, '���������� ����� ����� WebMoney ('.$pType.' �� ����� '.$pRating.')');
				return 'OK';
			}
		}
	}

}

$wm_acceptor = new Finance_Acceptor_WM;
finance_register_acceptor($wm_acceptor);