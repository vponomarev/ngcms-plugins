<?

// ================================================================
// ���������� ����������
// ================================================================
global $FINANCE_MONEY_ACCEPTORS;

$FINANCE_MONEY_ACCEPTORS = array();

$FINANCE_CACHE = array();

// ================================================================
// ������ ������ ���������� �������
// ================================================================
class Finance_Acceptor {
	// �����������
	function Finance_Acceptor() {
		$this->active = 0;
		$this->type = '';
		$this->id   = '';
		$this->name = '';
		$this->description = '';
	}

	// ����� �������� �������
	function paymentAcceptForm($sum = 0) { return 1; }

	// ���� �������
	function paymentAccept() { return 1; }


	// ������������� ������� :: ��������� �����
	function payConfirmForm($sum = 0) { return 1; }

	// ������������� ������� :: ��������
	function payConfirm() { return 1; }

	// ���������� ������� :: ��������� �����
	function payForm() { return 1; }

	// ���������� ������� :: �����
	function pay() { return 1; }
}



// =============================================== //
// Scope of new financial functions                //
// =============================================== //

// ������������� ����. � �� ��������� �������� ����� ������������ ������ - ����
// balance_manager, ����������� ���� �������� [ expire = 3600 ]
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
// ���������������� ����� �������� ����� (�������� ����� ����� �� ����)
//
function finance_register_acceptor(&$ref) {
 global $FINANCE_MONEY_ACCEPTORS;

 array_push($FINANCE_MONEY_ACCEPTORS, $ref);
}

//
// �������� ������� ������������ (� �������� ������)
// ���������:
// $userlogin	- ����� ������������ ��� ������ ���������
// $balance_no	- ����� ������� ��� �������� (0 - �������� ������, -1 - ������� ����� ����� �� ���� ���������� ��������)
// ������������ ��������:
// * ����� ������� ���������� ������������ (���� ������������ �� ������, �� 0)

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
		// ��������� ��� �� ������ ��� � ����
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
// �������� ����������� ������� � ������������ ��� ������������ ���� ��������
// $userlogin   - ����� ������������
// $type	- ��� ��������
// $tprice	- ���� � "��������" ��� ������� ������� �� ������� ����
// $price	- ���� � ������
function finance_check_enough_money($userlogin, $type, $tprice, $price) {
	global $mysql, $userROW, $FINANCE_CACHE;

	if (is_array($userROW) && ($userlogin == $userROW['name'])) {
		$res = $userROW;
	} else {
		$res = $mysql->record("select * from ".uprefix."_users where name=".db_squote($userlogin));
	}

	//print "call finance_check_enough_money('$userlogin','$type','$tprice','$price')<br />\n";
	if ($res) {
		// ������������ ������

		// ��������� ������� ������� �� �������� (����������) �������
		if ($res['balance'] > $price) { return 1; }

		// ��������� ������� ������� �� ���������� ��������
		if ($type) {
		        $tbalance = 0;
			foreach ($mysql->select("select * from ".prefix."_balance_manager where type=".db_squote($type)) as $row) {
				$tbalance+= $res['balance'.$row['id']];
			}
			if ($tbalance >= $tprice) { return 1; }
		}

		// ��������� ������� ������� �� ���� ���������� ��������
		$balance = $res['balance'];

		// ���������� � ���� ���� �� ����
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
// ������ ������� � ������� ������������
// ���������:
// $identity - ������ ���������������� ������������. ����������� ���� �� ����������
//	# 'id'	- ID ������������
//	# 'login'	- Login ������������
// $payment - ������ � ����������� �������
//  # 'type' - ��� ����������� �������:
//     'money'  -	_������_ ��������
//     'points' -	_������_ � �������
//     'auto'   -	[default] ������� �������� ������� � �������, ���� �� ������������ -
//					 ��������� �������� � ������
//	# 'ptype' - ��� ������� ��� ������ (� ������ ���� ������ ��� ������ �� �������)
//	# 'value' - ������ � ����������� �������
//		'money' = ����� ������� � ������
//		'points' = ������ (��� �������,���-�� �������) ��� �������� � �������
//	# 'description' - �������� ������� [ ��� ��������� � ������� finance_history ]
//

function finance_pay($identity, $payment) {
	global $mysql, $userROW;

	// ���� ��� �� �������� identity ������������ - �������
	if (!isset($identity['id']) && !isset($identity['login']))
		return false;

	// ���� ���� ��������� �� ����� �������� ������������ - ���� ������ �� ������
	if	(is_array($userROW) && ((isset($identity['id']) && ($userROW['id'] == $identity['id']))||(isset($identity['login']) && ($userROW['login'] == $identity['login'])))) {
		$res = $userROW;
	} else {
		$res = $mysql->record("select * from ".uprefix."_users where ".(isset($identity['id'])?('id='.db_squote($identity['id'])):'login='.db_squote($identity['name'])));
	}

	// ����� ���� ������������ �� ������
	if (!is_array($res)) return false;

	//print "Pay request for '$type' ($tprice) '$price' points<br />\n";

	// ��������� ������ ��� ���������� ���������������� �������
	$bupdate = array();
	$enough = 0;

	// ��������� ��������� ������� ��������. ���� ����� ������� � ������� - ��������
	if (!isset($payment['type']) || ($payment['type'] == 'auto') || ($payment['type'] == 'points')) {
		// ��������� ������� ����������� ����� �� ��������
		// $ptype - ��� �������; $pvalue - ����� �������� � �������
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

		// ���� ������� ������� �� ���. �������� - ��������
		if ($pcount <= 0) $enough = 1;

		//print "Bdiff ".implode(", ",$bupdate);
		//	$mysql->query("update ".uprefix."_users set ".implode(", ",$bupdate)." where name=".db_squote($userlogin));
		//	return 1;
	}

	// ���� ������ ���� ��������� ������ � ������� � �� �� ������� - �������
	if (($payment['type'] == 'points')&&(!$enough))
		return false;

	// �������� ��������� � ������
	if (!$enough) {
		$price = $payment['value']['money'];

		// ��������� ������� ������� �� ���� ���������� ��������. �������� � ��������������
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
		// ���� �� �������������� ������� �� ������� - ��������� � ���������
		if ($price) {
			// ���� �������� �������� ������ ��� ���� ������ - ������
			//print "Balance: ".$res['balance'].", ".$res['balance1'].", ".$res['balance2'].", ".$res['balance3']."<br />";
			if ($price > $res['balance']) {
				return false;
			}
			array_push($bupdate,'balance = '.($res['balance'] - $price));
			$price = 0;
		}

	}

	// ���� �� ����� �� ���� �����, ������ ������ ��������� �������� �� ����� ������������
	// * ��������
	$mysql->query("update ".uprefix."_users set ".implode(", ", $bupdate)." where ".(isset($identity['id'])?('id='.db_squote($identity['id'])):'login='.db_squote($identity['name'])));

	// * ���������� ����
	$mysql->query("insert into ".prefix."_finance_history (user_id, dt, operation_type, paytype, sum) values (".db_squote($res['id']).", now(), 1, ".($enough?(db_squote($value['points'][0]).", ".db_squote($value['points'][1])):("'', ".db_squote($value['money']))));

	// * ���������� ���������� �� �������� ��������
	return 1;
}

//
// �������� ������ �� ������ ������������
// $userlogin - ����� ������������
// $balance - ����� ������� �� ������� ������ ������ (0 - core balance)
// $sum - ����� (� ������ ��� "�������") ������� ���������� �������� �� ����
//
function finance_add_money($userlogin, $balance, $sum) {
	global $mysql, $userROW;

	if (is_array($userROW) && ($userlogin == $userROW['name'])) {
		$res = $userROW;
	} else {
		$res = $mysql->record("select * from ".uprefix."_users where name=".db_squote($userlogin));
	}
	if ($res) {
		// ������������ ������
		//print "Add money request for user '$userlogin', balance no '$balance' sum '$sum'<br />\n"."update ".uprefix.'_users set balance_'.$balance.'=balance_'.$balance.'+'.intval($sum*100).' where id = '.$res['id'];
		$mysql->query("update ".uprefix.'_users set balance'.$balance.'=balance'.$balance.'+'.intval($sum*100).' where id = '.$res['id']);
		return 1;
	} else { print "Unknow user: $userlogin"; }
	return 0;
}