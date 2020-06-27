<?php

// Защита от попыток взлома.
if (! defined('NGCMS')) {
    die('HAL');
}

// Дублирование глобальных переменных.
$plugin = 'ng-grecaptcha';

// Подгрузка библиотек-файлов плагина.
plugins_load_config();
LoadPluginLang($plugin, 'config', '', '', ':');

// Используем функции из пространства `Plugins`.
use function Plugins\dd;
use function Plugins\setting;
use function Plugins\trans;

// Подготовка переменных.
$range = range(0.1, 0.9, 0.1);
$scores = array_combine($range, $range);

// Заполнить параметры конфигурации.
$cfg = [];

// Описание плагина.
array_push($cfg, [
    'descr' => trans($plugin.':description'),
]);

// Ключи reCAPTCHA.
array_push($cfg, [
    'mode' => 'group',
    'title' => trans($plugin.':group_keys'),
    'entries' => [
        [
            'name' => 'site_key',
            'title' => trans($plugin.':site_key'),
            'descr' => trans($plugin.':site_key#descr'),
            'type' => 'input',
            'html_flags' => 'style="min-width:260px;text-align:right;" autocomplete="off" required',
            'value' => setting($plugin, 'site_key', null),

        ], [
            'name' => 'secret_key',
            'title' => trans($plugin.':secret_key'),
            'descr' => trans($plugin.':secret_key#descr'),
            'type' => 'input',
            'html_flags' => 'style="min-width:260px;text-align:right;" autocomplete="off" required',
            'value' => setting($plugin, 'secret_key', null),

        ],

    ],

]);

// Основные настройки.
array_push($cfg, [
    'mode' => 'group',
    'title' => trans($plugin.':group_main'),
    'entries' => [
        [
            'name' => 'score',
            'title' => trans($plugin.':score'),
            'descr' => trans($plugin.':score#descr'),
            'type' => 'select',
            'values' => $scores,
            'value' => (float) setting($plugin, 'score', 0.5),

        ],  [
            'name' => 'guests_only',
            'title' => trans($plugin.':guests_only'),
            'descr' => trans($plugin.':guests_only#descr'),
            'type' => 'select',
            'values' => [
                trans('noa'),
                trans('yesa'),

            ],
            'value' => (int) setting($plugin, 'guests_only', false),

        ],

    ],

]);

// Формирование переменной `htmlvars`.
array_push($cfg, [
    'mode' => 'group',
    'title' => trans($plugin.':group_htmlvars'),
    'entries' => [
        [
            'name' => 'use_api_js',
            'title' => trans($plugin.':use_api_js'),
            'descr' => trans($plugin.':use_api_js#descr'),
            'type' => 'select',
            'values' => [
                trans('noa'),
                trans('yesa'),

            ],
            'value' => (int) setting($plugin, 'use_api_js', true),

        ], [
            'name' => 'use_attach_js',
            'title' => trans($plugin.':use_attach_js'),
            'descr' => trans($plugin.':use_attach_js#descr'),
            'type' => 'select',
            'values' => [
                trans('noa'),
                trans('yesa'),

            ],
            'value' => (int) setting($plugin, 'use_attach_js', true),

        ],
    ]
]);

// Если была отправлена форма, то сохраняем настройки.
if ('commit' === $_REQUEST['action']) {
    // Валидация входящих обязательных параметров.
    try {
        if (empty($site_key = trim(secure_html($_POST['site_key'])))) {
            throw new \InvalidArgumentException('empty-site-key');
        }

        if (empty($secret_key = trim(secure_html($_POST['secret_key'])))) {
            throw new \InvalidArgumentException('empty-secret-key');
        }

    } catch (\InvalidArgumentException $e) {
        $message = $e->getMessage();

        msg([
            'type' => 'error',
            'text' => trans("$plugin:error.required_parameters"),
            'info' => trans("$plugin:error.$message"),

        ]);

        return generate_config_page($plugin, $cfg);
    }

    commit_plugin_config_changes($plugin, $cfg);

    return print_commit_complete($plugin);
}

generate_config_page($plugin, $cfg);
