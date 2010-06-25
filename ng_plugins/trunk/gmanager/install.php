<?php
if (!defined('NGCMS'))die ('HAL');

function plugin_gmanager_install($action) {
	global $config, $lang;

	if ($action != 'autoapply')
		loadPluginLang('gmanager', 'config', '', '', ':');

	// Fill DB_UPDATE configuration scheme
	$db_update = array(
	 array(
	  'table'  => 'gmanager',
	  'action' => 'cmodify',
	  'key'	   => 'primary key(id)',
	  'fields' => array(
	    array('action' => 'cmodify', 'name' => 'id', 'type' => 'int(11)', 'params' => 'auto_increment'),
	    array('action' => 'cmodify', 'name' => 'id_icon', 'type' => 'int(11)', 'params' => "default '0'"),
	    array('action' => 'cmodify', 'name' => 'order', 'type' => 'int(11)', 'params' => "default '0'"),
	    array('action' => 'cmodify', 'name' => 'if_active', 'type' => 'int(1)', 'params' => "default '0'"),
	    array('action' => 'cmodify', 'name' => 'name', 'type' => 'varchar(25)', 'params' => "default ''"),
	    array('action' => 'cmodify', 'name' => 'title', 'type' => 'varchar(50)', 'params' => "default ''"),
	    array('action' => 'cmodify', 'name' => 'description', 'type' => 'text', 'params' => "default ''"),
	    array('action' => 'cmodify', 'name' => 'keywords', 'type' => 'text', 'params' => "default ''"),
	  )
	 ),
	);

	$ULIB = new urlLibrary();
	$ULIB->loadConfig();
	$ULIB->registerCommand('gmanager', '',
		array ('vars' => array(
				'' => array(
					'matchRegex' => '.+?', 
					'descr' => array(
						$config['default_lang'] => $lang['gmanager:ULIB_main']
					)
				),
				'page' => array(
					'matchRegex' => '\d{1,4}', 
					'descr' => array(
						$config['default_lang'] => $lang['gmanager:ULIB_page']
					)
				),
			),
			'descr'	=> array ($config['default_lang'] => $lang['gmanager:ULIB_main_d']),
		)
	);
	$ULIB->registerCommand('gmanager', 'gallery',
		array ('vars' => array(
				'name' => array(
					'matchRegex' => '.+?', 
					'descr' => array(
						$config['default_lang'] => $lang['gmanager:ULIB_name']
						)
					),
				'id' => array(
					'matchRegex' => '\d{1,4}', 
					'descr' => array(
						$config['default_lang'] => $lang['gmanager:ULIB_id']
						)
					),
				'page' => array(
					'matchRegex' => '\d{1,4}', 
					'descr' => array(
						$config['default_lang'] => $lang['gmanager:ULIB_page']
					)
				),
			),
			'descr'	=> array ($config['default_lang'] => $lang['gmanager:ULIB_gallery_d']),
		)
	);

	// Apply requested action
	switch ($action) {
		case 'confirm':
			generate_install_page('gmanager', $lang['gmanager:desc_install']);
			break;
		case 'autoapply':
		case 'apply':
			if (fixdb_plugin_install('gmanager', $db_update, 'install', ($action=='autoapply')?true:false)) {
				plugin_mark_installed('gmanager');
			} else {
				return false;
			}
			$params = array(
				'locate_tpl'	=> 1,
				'if_auto_cash'	=> 0,
				'if_description'=> 0,
				'if_keywords'	=> 0,
				'main_row'		=> 5,
				'main_cell'		=> 5,
				'main_page'		=> 1,
				'one_row'		=> 5,
				'one_cell'		=> 5,
				'one_page'		=> 1
			);

			foreach ($params as $k => $v) {
				pluginSetVariable('gmanager', $k, $v);
			}
			pluginsSaveConfig();
			$ULIB->saveConfig();

			break;
	}
	return true;
}
