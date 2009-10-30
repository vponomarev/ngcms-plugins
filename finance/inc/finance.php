<?

// ================================================================
// Глобальные переменные
// ================================================================
global $FINANCE_MONEY_ACCEPTORS;

$FINANCE_MONEY_ACCEPTORS = array();

$FINANCE_CACHE = array();

// ================================================================
// Шаблон класса пополнения баланса
// ================================================================
class Finance_Acceptor {
	// Конструктор
	function Finance_Acceptor() {
		$this->active = 0;
		$this->type = '';
		$this->id   = '';
		$this->name = '';
		$this->description = '';
	}

	// Форма принятия платежа
	function paymentAcceptForm($sum = 0) { return 1; }

	// Приём платежа
	function paymentAccept() { return 1; }


	// Подтверждение платежа :: генератор формы
	function payConfirmForm($sum = 0) { return 1; }

	// Подтверждение платежа :: действия
	function payConfirm() { return 1; }

	// Проведение платежа :: генератор формы
	function payForm() { return 1; }

	// Проведение платежа :: форма
	function pay() { return 1; }
}



// =============================================== //
// Scope of new financial functions                //
// =============================================== //

// Инициализация кеша. В нём сохраняем наиболее часть используемые данные - файл
// balance_manager, описывающие типы балансов [ expire = 3600 ]
function financeInitCache() {
	global $FINANCE_CACHE, $mysql;

	$data = cacheRetrieveFile('master.txt', 3600, 'finance');
	if ($data != false) {
		// We got data from cache, fine
		$BM = unserialize($data);
	} else {
		// We need to preload data into cache
		$BM = $mysql->select("select * from ".prefix."_balance_manager");
		cacheStoreFile('master.txt', serialize($BM), 'finance');
	}
	$FINANCE_CACHE['BM'] = $BM;
}


//
// Зарегистрировать новый аксептор денег (средство ввода денег на сайт)
//
function finance_register_acceptor(&$ref) {
 global $FINANCE_MONEY_ACCEPTORS;

 array_push($FINANCE_MONEY_ACCEPTORS, $ref);
}

//
// Проверка баланса пользователя (в единицах валюты)
// параметры:
// $userlogin	- логин пользователя чей баланс проверяем
// $balance_no	- номер баланса для проверки (0 - основной баланс, -1 - считать общую сумму со всех монетарных балансов)
// возвращаемое значение:
// * сумма баланса указанного пользователя (если пользователь не найден, то 0)

function finance_check_money($userlogin, $balance_no = -1) {
	global $mysql, $userROW, $FINANCE_CACHE;

	if (is_array($userROW) && ($userlogin == $userROW['name'])) {
		$res = $userROW;
	} else {
		$res = $mysql->record("select * from ".uprefix."_users where name=".db_squote($userlogin));
	}
	if ($res) {
		if ($balance_no>=0) { return $res['balance'.$balance_no]; }

		$sum = $res['balance'];
		// Проверяем нет ли данных уже в кеше
		if (isset($FINANCE_CACHE['BM']) && is_array($FINANCE_CACHE['BM'])) {
			foreach ($FINANCE_CACHE['BM'] as $row)
				if ($row['monetary'] == 1)
					 $sum+= $res['balance'.$row['id']];
		} else {
			foreach ($mysql->select("select * from ".prefix."_balance_manager where monetary = 1") as $row) {
				 $sum+= $res['balance'.$row['id']];
			}
		}
		return $sum;
	}
	return 0;
}

//
// Проверка доступности средств у пользователя для определённого типа действия
// $userlogin   - логин пользователя
// $type	- тип действия
// $tprice	- цена в "единицах" при наличии баланса по данному типу
// $price	- цена в валюте
function finance_check_enough_money($userlogin, $type, $tprice, $price) {
	global $mysql, $userROW, $FINANCE_CACHE;

	if (is_array($userROW) && ($userlogin == $userROW['name'])) {
		$res = $userROW;
	} else {
		$res = $mysql->record("select * from ".uprefix."_users where name=".db_squote($userlogin));
	}

	//print "call finance_check_enough_money('$userlogin','$type','$tprice','$price')<br />\n";
	if ($res) {
		// Пользователь найден

		// Проверяем наличие средств на основном (монетарном) балансе
		if ($res['balance'] > $price) { return 1; }

		// Проверяем наличие поинтов на выделенных балансах
		if ($type) {
		        $tbalance = 0;
			foreach ($mysql->select("select * from ".prefix."_balance_manager where type=".db_squote($type)) as $row) {
				$tbalance+= $res['balance'.$row['id']];
			}
			if ($tbalance >= $tprice) { return 1; }
		}

		// Проверяем наличие средств на всех монетарных балансах
		$balance = $res['balance'];

		// Обращаемся к кешу если он есть
		if (isset($FINANCE_CACHE['BM']) && is_array($FINANCE_CACHE['BM'])) {
			foreach ($FINANCE_CACHE['BM'] as $row)
				if ($row['monetary'] == 1)
					 $balance+= $res['balance'.$row['id']];
		} else {
			foreach ($mysql->select("select * from ".prefix."_balance_manager where monetary = 1") as $row) {
			 	$balance+=$res['balance'.$row['id']];
			}
		}
		if ($balance >= $price) { return 1; }
	}
	return 0;
}

//
// Retrieve balance type
// $balance_no	- balance number
// Returns: empty string if monetary, filled string if type is present
//
function finance_get_balance_type($balance_no) {
	global $mysql;
	$res = $mysql->record("select * from ".prefix."_balance_manager where id=".intval($balance_no));
	if ($res) { return $res['monetary']?'':$res['type']; }
	return 0;
}


//
// Снятие средств с баланса пользователя
// Параметры:
// $identity - массив идентифицирующий пользователя. Заполняется один из параметров
//	# 'id'	- ID пользователя
//	# 'login'	- Login пользователя
// $payment - массив с параметрами платежа
//  # 'type' - тип проводимого платежа:
//     'money'  -	_только_ валютный
//     'points' -	_только_ в поинтах
//     'auto'   -	[default] сначала пытаемся списать в поинтах, если их недостаточно -
//					 списываем средства в валюте
//	# 'ptype' - тип поинтов для оплаты (в случае авто оплаты или оплаты по поинтам)
//	# 'value' - массив с параметрами платежа
//		'money' = сумма платежа в валюте
//		'points' = массив (тип поинтов,кол-во поинтов) при списании в поинтах
//	# 'description' - описание платежа [ для помещения в таблицу finance_history ]
//

function finance_pay($identity, $payment) {
	global $mysql, $userROW;

	// Если нам не передали identity пользователя - выходим
	if (!isset($identity['id']) && !isset($identity['login']))
		return false;

	// Если надо заплатить со счета текущего пользователя - берём данные из памяти
	if	(is_array($userROW) && ((isset($identity['id']) && ($userROW['id'] == $identity['id']))||(isset($identity['login']) && ($userROW['login'] == $identity['login'])))) {
		$res = $userROW;
	} else {
		$res = $mysql->record("select * from ".uprefix."_users where ".(isset($identity['id'])?('id='.db_squote($identity['id'])):'login='.db_squote($identity['name'])));
	}

	// Выход если пользователь не найден
	if (!is_array($res)) return false;

	//print "Pay request for '$type' ($tprice) '$price' points<br />\n";

	// Формируем массив для обновления пользовательской таблицы
	$bupdate = array();
	$enough = 0;

	// Проверяем возможные форматы платежей. Если можно платить в поинтах - пытаемся
	if (!isset($payment['type']) || ($payment['type'] == 'auto') || ($payment['type'] == 'points')) {
		// Проверяем наличие достаточной суммы на балансах
		// $ptype - тип баланса; $pvalue - сумма списания с баланса
		$ptype	= $payment['ptype'];
		$pcount	= $payment['value']['points'];
		foreach ($mysql->select("select * from ".prefix."_balance_manager where type=".db_squote($ptype)." order by id desc") as $row) {
			if ($pcount > $res['balance'.$row['id']]) {
				$pcount-=$res['balance'.$row['id']];
				array_push($bupdate,'balance'.$row['id'].' = 0');
			} else {
				array_push($bupdate,'balance'.$row['id'].' = '.($res['balance'.$row['id']] - $pcount));
				$pcount = 0;
				break;
			}
		}

		// Если средств хватило на доп. балансах - отмечаем
		if ($pcount <= 0) $enough = 1;

		//print "Bdiff ".implode(", ",$bupdate);
		//	$mysql->query("update ".uprefix."_users set ".implode(", ",$bupdate)." where name=".db_squote($userlogin));
		//	return 1;
	}

	// Если оплата была разрешена только в поинтах и их не хватает - выходим
	if (($payment['type'] == 'points')&&(!$enough))
		return false;

	// Пытаемся заплатить в валюте
	if (!$enough) {
		$price = $payment['value']['money'];

		// Проверяем наличие средств на всех монетарных балансах. Начинаем с дополнительных
		foreach ($mysql->select("select * from ".prefix."_balance_manager where monetary = 1 order by id desc") as $row) {
			if ($price > $res['balance'.$row['id']]) {
				$price-=$res['balance'.$row['id']];
				array_push($bupdate,'balance'.$row['id'].' = 0');
			} else {
				array_push($bupdate,'balance'.$row['id'].' = '.($res['balance'.$row['id']] - $price));
				$price = 0;
				break;
			}
		}
		// Если на дополнительных средств не хватило - списываем с основного
		if ($price) {
			// Если осталось оплатить больше чем есть вообще - ошибка
			//print "Balance: ".$res['balance'].", ".$res['balance1'].", ".$res['balance2'].", ".$res['balance3']."<br />";
			if ($price > $res['balance']) {
				return false;
			}
			array_push($bupdate,'balance = '.($res['balance'] - $price));
			$price = 0;
		}

	}

	// Если мы дошли до этой точки, значит готовы списывать средства со счета пользователя
	// * списание
	$mysql->query("update ".uprefix."_users set ".implode(", ", $bupdate)." where ".(isset($identity['id'])?('id='.db_squote($identity['id'])):'login='.db_squote($identity['name'])));

	// * сохранение лога
	$mysql->query("insert into ".prefix."_finance_history (user_id, dt, operation_type, paytype, sum) values (".db_squote($res['id']).", now(), 1, ".($enough?(db_squote($value['points'][0]).", ".db_squote($value['points'][1])):("'', ".db_squote($value['money']))));

	// * Возвращаем информацию об успешном списании
	return 1;
}

//
// Положить деньги на баланс пользователя
// $userlogin - логин пользователя
// $balance - номер баланса на который класть деньги (0 - core balance)
// $sum - сумма (в валюте или "поинтах") которую необходимо положить на счет
//
function finance_add_money($userlogin, $balance, $sum) {
	global $mysql, $userROW;

	if (is_array($userROW) && ($userlogin == $userROW['name'])) {
		$res = $userROW;
	} else {
		$res = $mysql->record("select * from ".uprefix."_users where name=".db_squote($userlogin));
	}
	if ($res) {
		// Пользователь найден
		//print "Add money request for user '$userlogin', balance no '$balance' sum '$sum'<br />\n"."update ".uprefix.'_users set balance_'.$balance.'=balance_'.$balance.'+'.intval($sum*100).' where id = '.$res['id'];
		$mysql->query("update ".uprefix.'_users set balance'.$balance.'=balance'.$balance.'+'.intval($sum*100).' where id = '.$res['id']);
		return 1;
	} else { print "Unknow user: $userlogin"; }
	return 0;
}