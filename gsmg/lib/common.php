<?php

//
// Class for managing gsmg
class gsmgFilter {

    // Action executed when page is showed
    function onShow(&$output) {
    }
    
}

function create_gsmg_urls()
{
    $ULIB = new urlLibrary();
    $ULIB->loadConfig();
    
    $ULIB->registerCommand('gsmg', '',
        array ('vars' => array (),
               'descr' => array ('russian' => 'Лента gsmg'),
        )
    );

    $ULIB->saveConfig();
    
    $UHANDLER = new urlHandler();
    $UHANDLER->loadConfig();
    
    $UHANDLER->registerHandler(0,
        array (
        'pluginName' => 'gsmg',
        'handlerName' => '',
        'flagPrimary' => true,
        'flagFailContinue' => false,
        'flagDisabled' => false,
        'rstyle' => array (
          'rcmd' => '/gsmg.xml',
          'regex' => '#^/gsmg.xml$#',
          'regexMap' => 
          array (
          ),
          'reqCheck' => 
          array (
          ),
          'setVars' => 
          array (
          ),
          'genrMAP' => 
          array (
            0 => 
            array (
              0 => 0,
              1 => '/gsmg.xml',
              2 => 0,
            ),
          ),
        ),
      )
    );
    
    $UHANDLER->saveConfig();
}

function remove_gsmg_urls()
{
    $ULIB = new urlLibrary();
    $ULIB->loadConfig();
    $ULIB->removeCommand('gsmg', '');
    $ULIB->saveConfig();

    $UHANDLER = new urlHandler();
    $UHANDLER->loadConfig();
    $UHANDLER->removePluginHandlers('gsmg', '');
    $UHANDLER->saveConfig();

}
