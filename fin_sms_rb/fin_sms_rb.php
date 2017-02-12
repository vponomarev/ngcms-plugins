<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
include_once(root . "/extras/finance/inc/finance.php");

class Finance_Acceptor_SMS_RB extends Finance_Acceptor {

	function Finance_Acceptor_SMS_RB() {

		parent::Finance_Acceptor();
		$this->active = 1;
		$this->id = 'sms_rb';
		$this->type = 'SMS';
		$this->name = 'SMS';
	}

	function paymentAcceptForm($sum = 0) {

		global $tpl, $mysql;
		// Формируем список операторов и цены
		$data = array();
		$query = "select * from " . prefix . "_smsrb_price order by country, operator, num";
		foreach ($mysql->select($query) as $row) {
			$data[$row['country']][$row['operator']][$row['num']] = array($row['cost'], $row['partnercost']);
		}
		$tpl->template('pay_form', extras_dir . '/fin_sms_rb/tpl');
		$tvars['vars']['sum'] = $sum;
		$tvars['vars']['back'] = $_REQUEST['back'];
		$tvars['vars']['areas'] = json_encode($data);
		$tvars['vars']['serviceID'] = extra_get_param('fin_sms_rb', 'service_id');
		$tpl->vars('pay_form', $tvars);

		return $tpl->show('pay_form');
	}

	function paymentAccept() {

		global $tpl, $username;
		$return = '<div class="not_logged"><h3>Навигация</h3>&raquo; <a href="/plugin/finance/"><u>Переход к балансу</u></a><br />' . ($_REQUEST['back'] ? '&raquo; <a href="' . $_REQUEST['back'] . '"><u>Переход к контенту</u></a><br />' : '') . '<br /></div><br />';
		$passCode = $_REQUEST['passCode'];
		// Проверяем код
		$url = 'http://easysms.ru/cgi-bin/activate.pl?checkonly=1&serviceID=115&passCode=' . urlencode($passCode);
		$result = @file_get_contents($url);
		$rData = explode("|", trim($result));
		// Если всё OK
		if ($rData[0] == 'OK') {
			// Код можно активировать. Проверяем цену
			$price = file_get_contents('http://easysms.ru/cgi-bin/price_informer.pl?num=' . $rData[3]);
			$priceData = explode('|', $price);
			if ($priceData[0] && ($priceData[0] == $rData[3])) {
				$acceptSum = $priceData[1 + intval(extra_get_param('fin_sms', 'bonus_mode'))];
			} else {
				return $return . 'Не могу получить информацию по стоимости SMS на номер "' . $rData[3] . '"';
			}
			// Посылаем запрос на снятие денег
			$url = 'http://easysms.ru/cgi-bin/activate.pl?checkonly=1&serviceID=115&passCode=' . urlencode($passCode);
			$result = file_get_contents($url);
			$rData = explode("|", trim($result));
			if ($rData[0] == 'OK') {
				// Кладём деньги на счёт
				finance_add_money($username, '1', $acceptSum);

				return $return . 'Ваш счёт пополнен на ' . $acceptSum;
			} else {
				return $return . 'Ошибка обращения к API';
			}
		} else if ($rData[0] == 'DUP') {
			return $return . 'Код уже активирован!';
		} else if ($rData[0] == 'FAIL') {
			return $return . 'Такой код не зарегистрирован в БД!';
		}

		return $return . 'Ошибка доступа к сервису активации.';
	}
}

$sms_acceptor = new Finance_Acceptor_SMS_RB;
finance_register_acceptor($sms_acceptor);

