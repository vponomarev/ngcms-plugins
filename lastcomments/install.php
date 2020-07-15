<?php

// Protect against hack attempts
if (!defined('NGCMS')) {
    die('HAL');
}
function plugin_lastcomments_install($action)
{
    global $config, $lang;
    $ULIB = new urlLibrary();
    $ULIB->loadConfig();
    $ULIB->registerCommand(
        'lastcomments',
        '',
        [
            'vars'  => [],
            'descr' => ['russian' => 'Страница с последними комментариями'],
        ]
    );
    $ULIB->registerCommand(
        'lastcomments',
        'rss',
        [
            'vars'  => [],
            'descr' => ['russian' => 'Rss лента последних комментариев'],
        ]
    );
    // Apply requested action
    switch ($action) {
        case 'confirm':
            generate_install_page('lastcomments', 'GO GO GO');
            break;
        case 'autoapply':
        case 'apply':
            if (fixdb_plugin_install('lastcomments', [], 'install', ($action == 'autoapply') ? true : false)) {
                plugin_mark_installed('lastcomments');
            } else {
                return false;
            }
            $ULIB->saveConfig();
            break;
    }

    return true;
}
