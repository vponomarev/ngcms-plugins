<?php

plugins_load_config();
LoadPluginLang('messaging', 'messaging', '', 'mes');

$cfg = array();
array_push($cfg, array('descr' => $lang['mes_descr']));
array_push($cfg, array('name' => 'subject', 'title' => $lang['mes_subject'], 'type' => 'input', 'html_flags' => 'size=70', 'value' => ''));
array_push($cfg, array('name' => 'content', 'title' => $lang['mes_content'], 'type' => 'text', 'html_flags' => 'rows=10 cols=110 name=content id=content', 'value' => ''));

if ($_REQUEST['action'] == 'commit') {
    messaging($_REQUEST['subject'], $_REQUEST['content']);
} else {
    generate_config_page('messaging', $cfg);
}


?>
