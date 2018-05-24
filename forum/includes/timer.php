<?php
/*
=====================================================
 NG FORUM v.alfa
-----------------------------------------------------
 Author: Nail' R. Davydov (ROZARD)
-----------------------------------------------------
 Jabber: ROZARD@ya.ru
 E-mail: ROZARD@list.ru
-----------------------------------------------------
 © Настоящий программист никогда не ставит 
 комментариев. То, что писалось с трудом, должно 
 пониматься с трудом. :))
-----------------------------------------------------
 Данный код защищен авторскими правами
=====================================================
*/
if (!defined('NGCMS'))
	die ('HAL');

class timer {

	function start_forum() {

		list($msec, $sec) = explode(' ', microtime());
		$this->script_start = (float)$sec + (float)$msec;
		$this->last_event = $this->script_start;
	}

	function stop_forum() {

		list($msec, $sec) = explode(' ', microtime());
		$script_end = (float)$sec + (float)$msec;
		$elapsed_time = round($script_end - $this->script_start, 4);

		return $elapsed_time;
	}
}