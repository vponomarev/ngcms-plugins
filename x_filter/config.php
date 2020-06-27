<?php

// Protect against hack attempts.
if (! defined('NGCMS')) {
    die('HAL');
}

// Duplicate var.
$plugin = 'x_filter';

// Preload config file.
plugins_load_config();
LoadPluginLang($plugin, 'backend', '', '', ':');
// loadPluginLibrary('x_filter', 'helpers');

// Prepare var.
$orderby = [
	'id_desc' => $lang[$plugin.':orderby_iddesc'],
	'id_asc' => $lang[$plugin.':orderby_idasc'],
	'postdate_desc' => $lang[$plugin.':orderby_postdatedesc'],
	'postdate_asc' => $lang[$plugin.':orderby_postdateasc'],
	'title_desc' => $lang[$plugin.':orderby_titledesc'],
	'title_asc' => $lang[$plugin.':orderby_titleasc']
];

// Fill configuration parameters.
$cfg = [];
array_push($cfg, [
	'descr' => $lang[$plugin.':description'],
]);

$cfgX = [];
array_push($cfgX, [
		'name'  => "skipcat",
		'title' => $lang[$plugin.':skipcat'],
		'type'  => 'input',
		'value' => pluginGetVariable($plugin, "skipcat"),
	]
);
array_push($cfgX, [
		'name'   => "showAllCat",
		'type'   => 'select',
		'title'  => $lang[$plugin.':showAllCat'],
		'values' => [
            1 => $lang['yesa'],
            0 => $lang['noa']
        ],
		'value'  => pluginGetVariable($plugin, "showAllCat"),
	]
);
array_push($cfgX, [
		'name'   => "order",
		'type'   => 'select',
		'title'  => $lang[$plugin.':orderby_title'],
		'values' => $orderby,
		'value'  => pluginGetVariable($plugin, "order"),
	]
);
array_push($cfgX, [
		'name'  => "showNumber",
		'title' => $lang[$plugin.':number_title'],
		'type'  => 'input',
		'value' => intval(pluginGetVariable($plugin, "showNumber")) ? pluginGetVariable($plugin, "showNumber") : 10,
	]
);
array_push($cfg, [
		'mode'    => 'group',
		'title'   => $lang[$plugin.':group'],
		'entries' => $cfgX,
	]
);

$cfgX = [];
array_push($cfgX, [
		'name'   => 'localsource',
		'title'  => $lang[$plugin.':localsource'],
		'type'   => 'select',
		'values' => [
            '0' => $lang[$plugin.':localsource_0'],
            '1' => $lang[$plugin.':localsource_1']
        ],
		'value'  => intval(pluginGetVariable($plugin, 'localsource')),
	]
);
array_push($cfg, [
		'mode'    => 'group',
		'title'   => $lang[$plugin.':group_2'],
		'entries' => $cfgX,
	]
);

$cfgX = [];
array_push($cfgX, [
		'name'   => 'use_css',
		'title'  => $lang[$plugin.':use_css'],
    	'descr' => $lang[$plugin.':use_css#desc'],
		'type'   => 'select',
		'values' => [
            1 => $lang['yesa'],
            0 => $lang['noa']
        ],
		'value'  => intval(pluginGetVariable($plugin, 'use_css')),
	]
);
array_push($cfgX, [
		'name'   => 'use_js',
		'title'  => $lang[$plugin.':use_js'],
    	'descr' => $lang[$plugin.':use_js#desc'],
		'type'   => 'select',
		'values' => [
            1 => $lang['yesa'],
            0 => $lang['noa']
        ],
		'value'  => intval(pluginGetVariable($plugin, 'use_js')),
	]
);
array_push($cfgX, [
		'name'   => 'canonical',
		'title'  => $lang[$plugin.':canonical'],
    	'descr' => $lang[$plugin.':canonical#desc'],
		'type'   => 'select',
		'values' => [
            1 => $lang['yesa'],
            0 => $lang['noa']
        ],
		'value'  => intval(pluginGetVariable($plugin, 'canonical')),
	]
);
array_push($cfgX, [
		'name'   => 'meta_robots',
		'title'  => $lang[$plugin.':meta_robots'],
    	'descr' => $lang[$plugin.':meta_robots#desc'],
		'type'   => 'select',
		'values' => [
            1 => $lang['yesa'],
            0 => $lang['noa']
        ],
		'value'  => intval(pluginGetVariable($plugin, 'meta_robots')),
	]
);
array_push($cfg, [
		'mode'    => 'group',
		'title'   => $lang[$plugin.':group_3'],
		'entries' => $cfgX,
	]
);

$cfgX = [];
array_push($cfgX, [
		'name'   => 'cache',
		'title'  => $lang[$plugin.':cache'],
		'type'   => 'select',
		'values' => [
            '1' => $lang['yesa'],
            '0' => $lang['noa']
        ],
		'value'  => intval(pluginGetVariable($plugin, 'cache')),
	]
);
array_push($cfgX, [
		'name'  => 'cacheExpire',
		'title' => $lang[$plugin.':cacheExpire'],
		'type'  => 'input',
		'value' => intval(pluginGetVariable($plugin, 'cacheExpire')) ? pluginGetVariable($plugin, 'cacheExpire') : 60,
	]
);
array_push($cfg, [
		'mode'    => 'group',
		'title'   => $lang[$plugin.':group_4'],
		'entries' => $cfgX,
	]
);

// If submit requested, do config save.
if ('commit' == $_REQUEST['action']) {
	/*// Check incomming variables.
    if (empty($site_key = trim(secure_html($_POST['site_key']))) or empty($secret_key = trim(secure_html($_POST['secret_key'])))) {
        msg([
			'type' => 'error',
			'text' => $lang[$plugin.':msg.not_complete'],
		]);

        return generate_config_page($plugin, $cfg);
    }*/

	commit_plugin_config_changes($plugin, $cfg);

    return print_commit_complete($plugin);
}

generate_config_page($plugin, $cfg);
