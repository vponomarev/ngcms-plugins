<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
include_once(root . "/plugins/finance/inc/finance.php");

class Finance_Acceptor_SMSCOIN extends Finance_Acceptor {

	// Fetch price
	function priceFetch() {

		// Generate cache file name
		$cacheFileName = 'pricelist_bank' . pluginGetVariable('fin_smscoin', 'bank_id') . '.txt';
		// Try to fetch cache data
		$cacheData = cacheRetrieveFile($cacheFileName, 60 * 60, 'fin_smscoin');
		if ($cacheData != false) {
			// We got data from cache. Try to read it
			$data = unserialize($cacheData);
			if (is_array($data) && count($data)) {
				$this->pricelist = $data['pricelist'];
				$this->priceindex = $data['priceindex'];

				return true;
			}
		}
		// No data found in cache. Let's fetch it
		@include_once root . 'includes/inc/httpget.inc.php';
		$URL = 'http://service.smscoin.com/json/bank/' . pluginGetVariable('fin_smscoin', 'bank_id') . '/';
		$req = new http_get();
		$vms = $req->get($URL, 20, 1);
		if ($vms === false) {
			return false;
		}
		// We fetched pricelist. Let's scan it
		$data = json_decode(substr($vms, 15), true);
		if ($data === null) {
			return false;
		}
		if (!is_array($data)) {
			return false;
		}
		// Fetch price ratio conversion
		$priceRatio = doubleval(pluginGetVariable('fin_smscoin', 'pay_rate'));
		$priceMode = intval(pluginGetVariable('fin_smscoin', 'clear_mode'));
		// Let's analyse output data & build content tree
		$priceTree = array();
		$priceIndex = array();
		$priceIndexRecNo = 1;
		// 1. Scan countries
		foreach ($data as $record) {
			if (!is_array($record)) return false;
			if (!is_array($priceTree[$record['country']]))
				$priceTree[$record['country']] = array();
			$priceTree[$record['country']]['countryName'] = $record['country_name'];
			if (!is_array($priceTree[$record['country']]['providers']))
				$priceTree[$record['country']]['providers'] = array();
			// Check if we have providers
			if (!is_array($record['providers']) || !count($record['providers'])) {
				if (!is_array($priceTree[$record['country']]['providers']['']))
					$priceTree[$record['country']]['providers'][''] = array('name' => '', 'recs' => array());
				$profit = round($record['usd'] * $priceRatio * ($priceMode ? ($record['profit'] / 100) : 1), 2);
				array_push($priceTree[$record['country']]['providers']['']['recs'], array(
					'price'    => $record['price'],
					'usd'      => $record['usd'],
					'profit'   => $profit,
					'vat'      => $record['vat'],
					'special'  => $record['special'],
					'currency' => $record['currency'],
					'index'    => $priceIndexRecNo
				));
				$priceIndex[$priceIndexRecNo] = array('price' => $record['usd'], 'profit' => $profit);
				$priceIndexRecNo++;
			} else {
				foreach ($record['providers'] as $provider) {
					if (!is_array($priceTree[$record['country']]['providers'][$provider['code']]))
						$priceTree[$record['country']]['providers'][$provider['code']] = array('name' => $provider['name'], 'recs' => array());
					$profit = round($provider['usd'] * $priceRatio * ($priceMode ? ($provider['profit'] / 100) : 1), 2);
					array_push($priceTree[$record['country']]['providers'][$provider['code']]['recs'], array(
						'price'    => $provider['price'],
						'usd'      => $provider['usd'],
						'profit'   => $profit,
						'vat'      => $provider['vat'],
						'special'  => $provider['special'],
						'currency' => $provider['currency'],
						'index'    => $priceIndexRecNo
					));
					$priceIndex[$priceIndexRecNo] = array('price' => $provider['usd'], 'profit' => $profit);
					$priceIndexRecNo++;
				}
			}
		}
		// Check if we have at least 1 record in priceTree
		if (count($priceTree)) {
			$this->pricelist = $priceTree;
			$this->priceindex = $priceIndex;
			cacheStoreFile($cacheFileName, serialize(array('pricelist' => $priceTree, 'priceindex' => $priceIndex)), 'fin_smscoin');

			return true;
		}

		return false;
	}

	function Finance_Acceptor_SMSCOIN() {

		parent::Finance_Acceptor();
		$this->active = 1;
		$this->id = 'smscoin';
		$this->type = 'SMSCOIN';
		$this->name = 'SMS (через сервис SMSCOIN.com)';
	}

	function paymentAcceptForm($sum = 0) {

		global $tpl, $username, $userROW;
		if ($_REQUEST['result_ok']) {
			$tpl->template('result_ok', extras_dir . '/fin_smscoin/tpl');
			$tpl->vars('result_ok', array('vars' => array()));

			return $tpl->show('result_ok');
		}
		if ($_REQUEST['result_fail']) {
			$tpl->template('result_fail', extras_dir . '/fin_smscoin/tpl');
			$tpl->vars('result_fail', array('vars' => array()));

			return $tpl->show('result_fail');
		}
		if (!$this->priceFetch()) {
			return 'Не удаётся получить таблицу цен из сервиса SMSCOIN';
		}
		if (!is_array($userROW)) {
			return 'Для пополнения счёта вам необходимо предварительно авторизоваться';
		}
		$tpl->template('pay_form', extras_dir . '/fin_smscoin/tpl');
		$tvars['vars']['syscurrency'] = pluginGetVariable('finance', 'syscurrency');
		$tvars['vars']['pricelist'] = json_encode($this->pricelist);
		$tvars['vars']['form_url'] = generateLink('core', 'plugin', array('plugin' => 'fin_smscoin'), array('mode' => 'wrap_payment'));
		$tvars['vars']['sum'] = $sum;
		$tvars['vars']['wm_number'] = strtoupper(extra_get_param('fin_wm', 'wm_number'));
		$tvars['vars']['userid'] = $userROW['id'];
		$tvars['vars']['login'] = $userROW['name'];
		$tvars['vars']['home'] = home;
		$tvars['vars']['descr'] = 'Пополнение пользовательского счёта (ID:' . $userROW['id'] . '|' . $userROW['name'] . '|' . home . ')';
		$tpl->vars('pay_form', $tvars);

		return $tpl->show('pay_form');
	}

	function paymentAccept() {

		global $tpl, $ip, $mysql, $userROW, $SUPRESS_TEMPLATE_SHOW;
		//
		// Сюда приходит запрос от сервиса SMSCOIN
		//
		$SCOIN = array();
		foreach (array('purse', 'order_id', 'amount', 'clear_amount', 'inv', 'phone', 'sign_v2') as $k) {
			$SCOIN[$k] = $_REQUEST['s_' . $k];
		}
		// Проверяем наличие передаваемых данных
		if (!isset($_REQUEST['s_purse']) || !isset($_REQUEST['s_amount']) || !isset($_REQUEST['s_phone']) || !isset($_REQUEST['s_sign_v2'])) {
			return 'Неверный запрос';
		}
		// Сначала проверяем корректность данных (sign_v2)
		$checkline = pluginGetVariable('fin_smscoin', 'secret_key') . '::' . $SCOIN['purse'] . '::' . $SCOIN['order_id'] . '::' . $SCOIN['amount'] . '::' . $SCOIN['clear_amount'] . '::' . $SCOIN['inv'] . '::' . $SCOIN['phone'];
		$checksym = md5($checkline);
		// Готовим данные для истории транзакций
		$SCOUT = array();
		foreach ($SCOIN as $k => $v) {
			$SCOUT[$k] = db_squote($v);
		}
		$SCOUT['success'] = 0;
		$SCOUT['ip'] = db_squote($ip);
		$SCOUT['dt'] = 'now()';
		// Ошибка MD5 - попытка взлома!
		if (strtolower($SCOIN['sign_v2']) != strtolower($checksym)) {
			$mysql->query('insert into ' . prefix . '_fin_smscoin_history (' . join(", ", array_keys($SCOUT)) . ') values(' . join(', ', array_values($SCOUT)) . ')');

			return 'Ошибка контрольной суммы';
		}
		// Запрос прошел успешно. Определяем получателя, сумму и начисляем деньги
		$userID = 0;
		$userName = '';
		$sum = 0;
		if (is_array($rec = $mysql->record("select * from " . prefix . "_fin_smscoin_transactions where id = " . db_squote($SCOIN['order_id'])))) {
			$userID = $rec['userid'];
			$userName = $rec['username'];
			$sum = $rec['profit'];
		}
		// Логгируем поступление транзакции
		$SCOUT['userid'] = db_squote($userID);
		$SCOUT['sum'] = db_squote($sum);
		$SCOUT['trid'] = db_squote($SCOIN['order_id']);
		// Проверяем на наличие дублирующих записей (RETRY)
		if (is_array($mysql->record("select * from " . prefix . "_fin_smscoin_history where inv = " . db_squote($SCOIN['inv'])))) {
			$SCOUT['success'] = 0;
		} else {
			$SCOUT['success'] = 1;
			// Выполняем пополнение счета пользователя
			finance_add_money($userName, pluginGetVariable('fin_smscoin', 'balance_no'), $sum, 'Пополнение счета через SMSCOIN (' . $sum . ')');
		}
		// Записываем лог транзакции
		$mysql->query('insert into ' . prefix . '_fin_smscoin_history (' . join(", ", array_keys($SCOUT)) . ') values(' . join(', ', array_values($SCOUT)) . ')');
	}
}

//
//
function plugin_finsmscoin() {

	global $userROW, $tpl, $template, $SUPRESS_TEMPLATE_SHOW, $smscoin_acceptor, $mysql;
	if (!$smscoin_acceptor->priceFetch()) {
		$template['vars']['mainblock'] = 'Не удаётся получить таблицу цен из сервиса SMSCOIN';

		return;
	}
	if (!is_array($userROW)) {
		$template['vars']['mainblock'] = 'Для пополнения счёта вам необходимо предварительно авторизоваться';

		return;
	}
	if ($_REQUEST['mode'] == 'wrap_payment') {
		// Определяем цену, передаваемую сервису SMSCOIN
		if (!is_array($smscoin_acceptor->priceindex[$_REQUEST['s_payment']])) {
			$template['vars']['mainblock'] = 'Неверный индекс идентификатора прайс-листа';

			return;
		}
		$priceRec = $smscoin_acceptor->priceindex[$_REQUEST['s_payment']];
		// Создаём запись в таблице транзакций
		$mysql->query("insert into " . prefix . "_fin_smscoin_transactions (dt, userid, username, amount, profit) values (now(), " . db_squote($userROW['id']) . ', ' . db_squote($userROW['name']) . ', ' . db_squote($priceRec['price']) . ', ' . db_squote($priceRec['profit']) . ')');
		$transactionID = $mysql->record("select LAST_INSERT_ID() as id");
		$params = array(
			's_purse'        => pluginGetVariable('fin_smscoin', 'bank_id'),
			's_order_id'     => $transactionID['id'],
			's_amount'       => $priceRec['price'],
			's_clear_amount' => '0',
			's_description'  => 'Put money into account of user (' . $userROW['name'] . ')',
			's_country'      => $_REQUEST['s_country'],
			's_provider'     => $_REQUEST['s_provider']
		);
		// Calculate MD5 hash
		$params['s_sign'] = md5($params['s_purse'] . '::' . $params['s_order_id'] . '::' . $params['s_amount'] . '::' . $params['s_clear_amount'] . '::' . $params['s_description'] . '::' . pluginGetVariable('fin_smscoin', 'secret_key'));
		$inputs = '';
		foreach ($params as $k => $v) {
			$inputs .= '<input type="hidden" name="' . $k . '" value="' . htmlspecialchars($v, null, 'cp1251') . '"/>' . "\n";
		}
		$tvars = array('vars' => array('form_url' => pluginGetVariable('fin_smscoin', 'post_url'), 'inputs' => $inputs));
		$tpl->template('redirect', extras_dir . '/fin_smscoin/tpl');
		$tpl->vars('redirect', $tvars);
		$template['vars']['mainblock'] = $tpl->show('redirect');
		$SUPRESS_TEMPLATE_SHOW = 1;
	} else {
		$template['vars']['mainblock'] = 'Неверный тип запроса';
	}
}

global $smscoin_acceptor;
$smscoin_acceptor = new Finance_Acceptor_SMSCOIN;
finance_register_acceptor($smscoin_acceptor);
register_plugin_page('fin_smscoin', '', 'plugin_finsmscoin', 0);