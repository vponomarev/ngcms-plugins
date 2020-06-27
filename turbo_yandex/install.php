<?php

// Защита от попыток взлома.
if (!defined('NGCMS')) {
    die('HAL');
}

// Подгрузка библиотек-файлов плагина.
plugins_load_config();
LoadPluginLang($plugin, 'install', '', '', ':');
include_once extras_dir . '/turbo_yandex/lib/helpers.php';

// Используем функции из пространства `Plugins`.
use function Plugins\catz;
use function Plugins\config;
use function Plugins\setting;
use function Plugins\trans;
use function Plugins\dd;

// Install script for plugin.
function plugin_turbo_yandex_install($action)
{
    // Дублирование глобальных переменных.
    $plugin = 'turbo_yandex';
    $locale = config('default_lang', 'russian');

    $ULIB = new urlLibrary();
    $ULIB->loadConfig();

    $ULIB->registerCommand($plugin, '', [
        'vars'  => [
            'page' => [
                'matchRegex' => '\d+',
                'descr' => [$locale => trans($plugin.':ULIB_page')]
            ],
        ],

        'descr' => [$locale => trans($plugin.':ULIB_main')],
    ]);

    $ULIB->registerCommand($plugin, 'category', [
        'vars' => [
            'category' => [
                'matchRegex' => '.+?',
                'descr' => [$locale => trans($plugin.':ULIB_category_altname')]
            ],

            'page' => [
                'matchRegex' => '\d+',
                'descr' => [$locale => trans($plugin.':ULIB_page')]
            ],
        ],

        'descr' => [$locale => trans($plugin.':ULIB_category')],
    ]);

    $UHANDLER = new urlHandler();
    $UHANDLER->loadConfig();
    $UHANDLER->registerHandler(0, [
        'pluginName' => $plugin,
        'handlerName' => '',
        'flagPrimary' => true,
        'flagFailContinue' => false,
        'flagDisabled' => false,
        'rstyle' => [
            'rcmd' => '/turbo-yandex/page-{page}.xml',
            'regex' => '#^/turbo-yandex/page-(\\d+).xml$#',
            'regexMap' => [
                1 => 'page',
            ],
            'reqCheck' => [],
            'setVars'  => [],
            'genrMAP'  => [
                [0, '/turbo-yandex/page-'],
                [2, 'page'],
                [0, '.xml']
            ],
        ],
    ]);

    $UHANDLER->registerHandler(0, [
        'pluginName' => $plugin,
        'handlerName' => 'category',
        'flagPrimary' => true,
        'flagFailContinue' => false,
        'flagDisabled' => false,
        'rstyle' => [
            'rcmd' => '/turbo-yandex/{category}/page-{page}.xml',
            'regex' => '#^/turbo-yandex/(.+?)/page-(\\d+).xml$#',
            'regexMap' => [
                1 => 'category',
                2 => 'page',
            ],
            'reqCheck' => [],
            'setVars' => [],
            'genrMAP' => [
                [0, '/turbo-yandex/'],
                [1, 'category'],
                [0, '/page-'],
                [2, 'page'],
                [0, '.xml']
            ],
        ],
    ]);

    // Apply requested action
    // $action: possible action modes
    //  confirm - screen for installation confirmation
    //  apply - apply installation, with handy confirmation
    //  autoapply - apply installation in automatic mode [INSTALL script]
    switch ($action) {
        case 'autoapply':
        case 'apply':
            setting($plugin, [
                // Максимальное количество элементов в каждой ленте.
                'countItems' => 200,

                // Извлекать URL-адреса изображений из текста новости.
                'extractImages' => false,

                // Директория, содержащая шаблоны плагина.
                'localsource' => 1,

                // Использовать кеширование данных.
                'cache' => 0,

                // Период обновления кеша.
                'cacheExpire' => 60,

            ]);

            pluginsSaveConfig();

            $ULIB->saveConfig();
            $UHANDLER->saveConfig();

            plugin_mark_installed($plugin);

            if ('apply' === $action) {
                header('HTTP/1.1 302 Found');
    			header('Location: '.admin_url.'/admin.php?mod=extras');
            }

            break;

        default:
            generate_install_page($plugin, trans($plugin.':description'));
            break;
    }

    return true;
}
