<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

include_once(root."/plugins/finance/inc/finance.php");

class Finance_Acceptor_WM extends Finance_Acceptor {
        function Finance_Acceptor_WM() {
		parent::Finance_Acceptor();

		$this->active = 1;
		$this->id   = 'wm';
		$this->type = 'WebMoney';
		$this->name = 'WebMoney (WMZ/WMR)';
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
	 	return 'Для пополнения счёта вам необходимо предварительно авторизоваться';
	 }	

	 $tpl->template('pay_form',extras_dir.'/fin_wm/tpl');
	 $tvars['vars']['sum'] = $sum;
	 $tvars['vars']['wm_number'] = strtoupper(extra_get_param('fin_wm', 'wm_number'));
	 $tvars['vars']['userid'] = $userROW['id'];
	 $tvars['vars']['login'] = $userROW['name'];
	 $tvars['vars']['home'] = home;
	 $tvars['vars']['descr'] = 'Пополнение пользовательского счёта (ID:'.$userROW['id'].'|'.$userROW['name'].'|'.home.')';
	 $tpl->vars('pay_form', $tvars);
	 return $tpl->show('pay_form');
	}

	function paymentAccept() {
	 global $tpl, $username, $SUPRESS_TEMPLATE_SHOW;

         
	 //
	 // Сюда приходит запрос от сервиса WebMoney
	 //
	 $WM = array();
	 foreach ( array('mode', 'payment_amount', 'payee_purse', 'payment_no', 'payer_wm', 'payer_purse', 'sys_invs_no', 'sys_trans_no', 'sys_trans_date', 'hash') as $k) {
	 	$WM[$k] = $_REQUEST['LMI_'.strtoupper($k)];
	 }

	 $SUPRESS_TEMPLATE_SHOW = 1;

	 // Проверяем соответствие параметров
	 if ($WM['payee_purse'] != strtoupper(extra_get_param('fin_wm', 'wm_number'))) {
	 	// Неверный номер кошелька для приёма платежей
	 	return 'Ошибка (платёж на неверный кошелёк)';
	 }

	 $checkline = $WM['payee_purse'].$WM['payment_amount'].$WM['payment_no'].$WM['mode'].$WM['sys_invs_no'].$WM['sys_trans_no'].$WM['sys_trans_date'].extra_get_param('fin_wm','secret_key').$WM['payer_purse'].$WM['payer_wm'];
	 $checksym = md5($checkline);

	 // Если не передали хеша - значит тестирование
	 if (!$WM['hash']) {
	 	return 'YES';
	 }	
	 
	 if (strtolower($checksym) != strtolower($WM['hash'])) {
	 	return "Ошибка (некорректная хеш-строка)\n";
	 } else {
	 	// Проверка прошла успешно. Берём сумму и кладём на счёт
		$login = $_REQUEST['login'];
		if ($login) {
			$sum = $WM['payment_amount'];
			finance_add_money($login, extra_get_param('fin_wm', 'balance_no'), $sum);
			return 'OK';
		}
	 }
	}	

}

$wm_acceptor = new Finance_Acceptor_WM;
finance_register_acceptor($wm_acceptor);

?>