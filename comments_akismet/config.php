<?php

plugins_load_config();
$cfg = [];
$cfgX = [];
array_push($cfg, ['descr' => 'Плагин позволяет использовать сервис Akismet для фильтрации спама в комментариях']);
array_push($cfgX, ['name' => 'akismet_server', 'title' => 'API-сервер', 'type' => 'input', 'value' => extra_get_param($plugin, 'akismet_server') ? extra_get_param($plugin, 'akismet_server') : 'rest.akismet.com']);
array_push($cfgX, ['name' => 'akismet_apikey', 'title' => 'API-ключ', 'type' => 'input', 'value' => extra_get_param($plugin, 'akismet_apikey')]);
array_push($cfg, ['mode' => 'group', 'title' => '<b>Настройки</b>', 'entries' => $cfgX]);
if ($_REQUEST['action'] == 'commit') {
    // If submit requested, do config save
    commit_plugin_config_changes($plugin, $cfg);
    print_commit_complete($plugin);
} else {
    generate_config_page($plugin, $cfg);
}
