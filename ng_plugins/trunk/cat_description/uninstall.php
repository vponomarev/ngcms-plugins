<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

if ($_REQUEST['action'] == 'commit') {
	plugin_mark_deinstalled('cat_description');
} else {
	$text = 'C����� ������ ����� ������';
	generate_install_page('cat_description', $text, 'deinstall');
}