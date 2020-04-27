<?php
if (!defined('NGCMS'))
	die ('HAL');
add_act('index', 'plugin_echo_test');
function plugin_echo_test() {

	global $template;
	$echo = 'произвольный текст';
	$template['vars']['echo_test_var1'] = $echo;
}