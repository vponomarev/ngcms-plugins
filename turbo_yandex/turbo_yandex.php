<?php

// Защита от попыток взлома.
if (!defined('NGCMS')) {
    die('HAL');
}

// Подключаем файл c основной функцией `news_showlist`,
// обрабатывающей запрос к БД по извлечению записей.
// Обеспечивает отработку хуков сторонних плагинов.
if (! function_exists('news_showlist')) {
    include_once root.'includes/news.php';
}

// Подгрузка библиотек-файлов плагина.
loadPluginLibrary('turbo_yandex', false);

use Plugins\TurboYandex;
use function Plugins\trans;

// Регистрация страниц плагина.
register_plugin_page('turbo_yandex', '', 'plugin_turbo_yandex', 0);
register_plugin_page('turbo_yandex', 'category', 'plugin_turbo_yandex', 0);

function plugin_turbo_yandex(array $params = [])
{
    global $SUPRESS_TEMPLATE_SHOW, $SUPRESS_MAINBLOCK_SHOW;

    // Disable executing of `index` action (widget plugins and so on..)
    actionDisable('index');

    // Suppress templates
    $SUPRESS_TEMPLATE_SHOW = 1;
    $SUPRESS_MAINBLOCK_SHOW = 1;

	$turboYandex = new TurboYandex($params);

    header("Content-Type: text/xml; charset=".trans('encoding'));

    echo $turboYandex->cachedContent();
}
