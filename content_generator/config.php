<?php

if (!defined('NGCMS'))
    exit('HAL');

// Preload config file
pluginsLoadConfig();

switch ($_REQUEST['action']) {

    default:
        automation();
}


function automation()
{
    global $twig, $PHP_SELF;


    $tpath = locatePluginTemplates(array('config/main', 'config/automation'), 'content_generator', 1);
    $xt = $twig->loadTemplate($tpath['config/automation'] . 'config/' . 'automation.tpl');

    $tVars = array();

    $xg = $twig->loadTemplate($tpath['config/main'] . 'config/' . 'main.tpl');

    $tVars = array(
        'entries' => $xt->render($tVars),
        'php_self' => $PHP_SELF,
        'plugin_url' => admin_url . '/admin.php?mod=extra-config&plugin=content_generator',
        'skins_url' => skins_url,
        'admin_url' => admin_url,
        'home' => home,
        'current_title' => 'Automation',
    );

    print $xg->render($tVars);

}