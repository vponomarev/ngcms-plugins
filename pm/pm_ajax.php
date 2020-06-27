<?php

// Protect against hack attempts
if (!defined('NGCMS')) {
    die('Galaxy in danger');
}

rpcRegisterFunction('pm_get_username', 'plugin_pm_ajax_get_username');

function plugin_pm_ajax_get_username($searchName)
{
    global $userROW, $mysql;

    if (!is_array($userROW)) {
        // ACCESS DENIED
        return [
			'status' => 0,
			'errorCode' => 3,
			'errorText' => 'Access denied',

		];
    }

    // Return a list of users
    $SQL = 'SELECT name FROM ' . uprefix . '_users WHERE name LIKE ' . db_squote('%' . $searchName . '%') . ' ORDER BY name DESC LIMIT 20';

    // Scan incoming params
    $output = [];

    foreach ($mysql->select($SQL) as $row) {
        $output[] = [
			$row['name'],

		];
    }

    return [
		'status' => 1,
		'errorCode' => 0,
		'data' => [
			$searchName,
			$output
		],

	];
}
