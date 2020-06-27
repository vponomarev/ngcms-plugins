<?php

// Защита от попыток взлома.
if (!defined('NGCMS')) {
    die('HAL');
}

// Дублирование глобальных переменных.
$plugin = 'turbo_yandex';

// Подгрузка библиотек-файлов плагина.
plugins_load_config();
LoadPluginLang($plugin, 'deinstall', '', '', ':');
include_once extras_dir.'/turbo_yandex/lib/helpers.php';

// Используем функции из пространства `Plugins`.
use function Plugins\trans;

// Если была отправлена форма, то сохраняем настройки.
if ('commit' === $_REQUEST['action']) {
    $ULIB = new urlLibrary();
    $ULIB->loadConfig();
    $ULIB->removeCommand($plugin, '');
    $ULIB->removeCommand($plugin, 'category');
    $ULIB->saveConfig();

    $UHANDLER = new urlHandler();
    $UHANDLER->loadConfig();
    $UHANDLER->removePluginHandlers($plugin, '');
    $UHANDLER->removePluginHandlers($plugin, 'category');
    $UHANDLER->saveConfig();

    plugin_mark_deinstalled($plugin);

    header('HTTP/1.1 302 Found');
    header('Location: '.admin_url.'/admin.php?mod=extras');
}

generate_install_page($plugin, trans($plugin.':description'), 'deinstall');
