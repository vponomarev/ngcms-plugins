<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

if ($_REQUEST['action'] == 'commit') {
	plugin_mark_deinstalled('ads_pro');
} else {
	$text = 'C����� ������ ����� ������';
	generate_install_page('ads_pro', $text, 'deinstall');
}