<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
add_act('core', 'check_pda');

function check_pda() {

	global $twig;

	require_once 'MobileDetect.php';
	$twig->addExtension(new Twig_Extension_MobileDetect());
}