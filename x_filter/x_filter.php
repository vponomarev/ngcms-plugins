<?php

// Protect against hack attempts.
if (! defined('NGCMS')) {
    die('HAL');
}

add_act('index', 'x_filterForm');
loadPluginLibrary('x_filter', false);
register_plugin_page('x_filter', '', 'x_filterPage', 0);
LoadPluginLang('x_filter', 'frontend', '', 'x_filter', ':');

function x_filterForm()
{
    global $template;

    $x_filter = Plugins\XFilter::getInstance();

    $x_filter->htmlVars();
    $template['vars']['x_filter_form'] = $x_filter->renderForm();
}

function x_filterPage()
{
    global $template;

    $x_filter = Plugins\XFilter::getInstance();

    $x_filter->htmlVars(true);
    $x_filter->makePageInfo();
    $template['vars']['mainblock'] = $x_filter->renderPage();
}
