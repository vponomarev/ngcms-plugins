<?php

if (!defined('NGCMS'))
	die ('HAL');

function forum_ban() {
global $twig, $template, $SUPRESS_TEMPLATE_SHOW,  $ip, $ban, $mainblock;

$ban_ip_list = array(
	'127.0.0.1' => array(
		array('desc_error' => 'Описание нарушения'),
		array('desc_error' => 'Описание нарушения'),
		array('desc_error' => 'Описание нарушения'),
	),
	'127.0.0.2' => array(
		array('desc_error' => 'Описание нарушения_2')
	),
);

$ban_ip_range = array( 
	array(
		'ip_start'   => '213.132.82.0',
		'ip_end'     => '213.132.82.255',
		'desc_error' => $desc_error
	),
	array(
		'ip_start'   => '188.162.176.0',
		'ip_end'     => '188.162.191.255',
		'desc_error' => $desc_error
	),
);

$ban_user = array (
	'0' => array(
		'desc_error' => $desc_error,
		'justification' => $justification
	)
);

//print "<pre>".var_export($CurrentHandler, true)."</pre>";

//if(file_exists(FORUM_CACHE.'/ban.php'))
//	include(FORUM_CACHE.'/ban.php');
//else
//	file_put_contents(FORUM_CACHE.'/ban.php', '<?php'."\n\n".'$ban = '.var_export(array(), true).';'."\n\n");

//$ban = '11111111111';

//$mainblock = '111111111111111111111111111111111111111';
}

function forum_ban_main(){
	global $template, $mainblock;
	
	$template['vars']['mainblock'] = $mainblock;
}

add_act('index', 'forum_ban_main');
add_act('forum:core', 'forum_ban');