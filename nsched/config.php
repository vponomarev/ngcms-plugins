<?php

// Protect against hack attempts
if (! defined('NGCMS')) {
    die('HAL');
}

//
// Configuration file for plugin
//

// Preload config file
pluginsLoadConfig();
LoadPluginLang($plugin, 'config', '', '', ':');

// Fill configuration parameters
$cfg = [];

array_push($cfg, [
    'descr' => $lang[$plugin.':description'],
]);

array_push($cfg, [
    'name' => 'period',
    'title' => $lang[$plugin.':period'],
    'descr' => $lang[$plugin.':period_descr'],
    'type' => 'select',
    'values' => [
        '0' => $lang[$plugin.':period_value_0'],
        '5m' => $lang[$plugin.':period_value_5m'],
        '10m' => $lang[$plugin.':period_value_10m'],
        '15m' => $lang[$plugin.':period_value_15m'],
        '30m' => $lang[$plugin.':period_value_30m'],
        '1h' => $lang[$plugin.':period_value_1h'],
        '2h' => $lang[$plugin.':period_value_2h'],
        '3h' => $lang[$plugin.':period_value_3h'],
        '4h' => $lang[$plugin.':period_value_4h'],
        '6h' => $lang[$plugin.':period_value_6h'],
        '8h' => $lang[$plugin.':period_value_8h'],
        '12h' => $lang[$plugin.':period_value_12h'],
    ],
    'value' => pluginGetVariable($plugin, 'period'),
]);

// RUN
if ($_REQUEST['action'] == 'commit') {
    // If submit requested, do config save
    //commit_plugin_config_changes($plugin, $cfg);
    $regRun = [];

    switch ($_REQUEST['period']) {
        case '5m':
            $regRun = ['0,5,10,15,20,25,30,35,40,45,50,55', '*'];
            break;
        case '10m':
            $regRun = ['0,10,20,30,40,50', '*'];
            break;
        case '15m':
            $regRun = ['0,15,30,45', '*'];
            break;
        case '30m':
            $regRun = ['0,30', '*'];
            break;
        case '1h':
            $regRun = ['0', '*'];
            break;
        case '2h':
            $regRun = ['0', '0,2,4,6,8,10,12,14,16,18,20,22'];
            break;
        case '3h':
            $regRun = ['0', '0,3,6,9,12,15,18,21'];
            break;
        case '4h':
            $regRun = ['0', '0,4,8,12,16,20'];
            break;
        case '6h':
            $regRun = ['0', '0,6,12,18'];
            break;
        case '8h':
            $regRun = ['0', '0,8,16'];
            break;
        case '12h':
            $regRun = ['0', '0,12'];
            break;
        default:
            $regRun = ['0', '0'];
            break;
    }

    /** @var cronManager $cron */
    $cron->unregisterTask($plugin);
    $cron->registerTask($plugin, 'run', $regRun[0], $regRun[1], '*', '*', '*');

    commit_plugin_config_changes($plugin, $cfg);
    print_commit_complete($plugin);
} else {
    generate_config_page($plugin, $cfg);
}
