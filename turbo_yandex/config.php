<?php

// Защита от попыток взлома.
if (! defined('NGCMS')) {
    die('HAL');
}

// Дублирование глобальных переменных.
$plugin = 'turbo_yandex';
$pluginLink = generatePluginLink($plugin, null, ['page' => 1], [], true, true);

// Подгрузка библиотек-файлов плагина.
plugins_load_config();
LoadPluginLang($plugin, 'config', '', '', ':');
loadPluginLibrary($plugin, 'helpers');

// Используем функции из пространства `Plugins`.
use function Plugins\catz;
use function Plugins\setting;
use function Plugins\trans;
use function Plugins\dd;

// Подготовка переменных.

// Заполнить параметры конфигурации.
$cfg = [];

// Описание плагина.
array_push($cfg, [
    'descr' => trans($plugin.':description'),
]);

// Ссылка на основную ленту.
array_push($cfg, [
    'descr' => sprintf(
        trans($plugin.':description_all'), $pluginLink, $pluginLink
    ),
]);

// Ссылка на ленту конкретной категории.
foreach (catz() as $demoCategory) {
    if ($demoCategory['posts'] > 0) {
        array_push($cfg, [
            'descr' => sprintf(
                trans($plugin.':description_category'),
                generatePluginLink($plugin, 'category', ['page' => 1, 'category' => $demoCategory['alt']], [], true, true),
                $demoCategory['name']
            ),

        ]);

        break;
    }
}

// Основные настройки.
array_push($cfg, [
    'mode' => 'group',
    'title' => trans($plugin.':group_main'),
    'entries' => [
        [
            'name' => 'countItems',
            'title' => trans($plugin.':countItems'),
            'descr' => trans($plugin.':countItems#descr'),
            'type' => 'input',
            'value' => (int) setting($plugin, 'countItems', 200),

        ],

    ],

]);

// Настройки отображения.
array_push($cfg, [
    'mode' => 'group',
    'title' => trans($plugin.':group_view'),
    'entries' => [
        [
            'name' => 'extractImages',
            'title' => trans($plugin.':extractImages'),
            'descr' => trans($plugin.':extractImages#descr'),
            'type' => 'select',
            'values' => [
                trans('noa'),
                trans('yesa'),

            ],
            'value' => (int) setting($plugin, 'extractImages', false),

        ], [
            'name' => 'localsource',
            'title' => trans($plugin.':localsource'),
            'descr' => trans($plugin.':localsource#descr'),
            'type' => 'select',
            'values' => [
                0 => trans($plugin.':localsource_0'),
                1 => trans($plugin.':localsource_1'),

            ],
            'value' => (int) setting($plugin, 'localsource', 1),

        ],

    ],

]);

// Настройки кеширования.
array_push($cfg, [
    'mode' => 'group',
    'title' => trans($plugin.':group_cache'),
    'entries' => [
        [
            'name' => 'cache',
            'title' => trans($plugin.':cache'),
            'descr' => trans($plugin.':cache#descr'),
            'type' => 'select',
            'values' => [
                trans('noa'),
                trans('yesa'),

            ],
            'value' => (int) setting($plugin, 'cache', 0),

        ], [
            'name' => 'cacheExpire',
            'title' => trans($plugin.':cacheExpire'),
            'descr' => trans($plugin.':cacheExpire#descr'),
            'type' => 'input',
            'value' => (int) setting($plugin, 'cacheExpire', 60),

        ],

    ],

]);

// Если была отправлена форма, то сохраняем настройки.
if ('commit' === $_REQUEST['action']) {
    commit_plugin_config_changes($plugin, $cfg);

    return print_commit_complete($plugin);
}

generate_config_page($plugin, $cfg);
