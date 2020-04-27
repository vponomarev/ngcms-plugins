<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('Galaxy in danger');
rpcRegisterFunction('pm_get_username', 'plugin_pm_ajax_get_username');
function plugin_pm_ajax_get_username($params) {

	global $userROW, $mysql;
	if (!is_array($userROW)) {
		// ACCESS DENIED
		return array('status' => 0, 'errorCode' => 3, 'errorText' => 'Access denied');
	}
	$searchName = $params;
	// Return a list of users
	$SQL = 'SELECT name FROM ' . uprefix . '_users WHERE name LIKE ' . db_squote('%' . $searchName . '%') . ' ORDER BY name DESC LIMIT 20';
	// Scan incoming params
	$output = array();
	foreach ($mysql->select($SQL) as $row) {
		$output[] = array($row['name']);
	}

	return array('status' => 1, 'errorCode' => 0, 'data' => array($params, $output));
}