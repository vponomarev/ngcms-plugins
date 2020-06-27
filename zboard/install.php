<?php
if (!defined('NGCMS'))
{
    die ('HAL');
}

function plugin_zboard_install($action) {
    global $lang, $mysql;

    if(!file_exists(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/zboard'))
        if(!@mkdir(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/zboard/', 0777))
            msg(array("type" => "error", "text" => "Критическая ошибка <br /> не удалось создать папку ".dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/images/zboard'), 1);

    if(!file_exists(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/zboard/thumb'))
        if(!@mkdir(dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/zboard/thumb', 0777))
            msg(array("type" => "error", "text" => "Критическая ошибка <br /> не удалось создать папку ".dirname(dirname(dirname(dirname(__FILE__)))).'/uploads/images/zboard/thumb'), 1);

    if ($action != 'autoapply')
        loadPluginLang('zboard', 'config', '', '', ':');
    $db_update = array(
    array(
        'table'		=> 'zboard',
        'action'	=> 'cmodify',
        'engine'	=> 'MyISAM',
        'key'		=> 'primary key(id), KEY `cat_id` (`cat_id`), KEY `active` (`active`), KEY `zboard_view` (`views`), FULLTEXT (announce_name), FULLTEXT (announce_description)',
        'fields'	=> array(
            array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'int(10)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'	=> 'cmodify', 'name' => 'date', 'type' => 'INT(10)', 'params' => 'NOT NULL DEFAULT \'0\''),
            array('action'	=> 'cmodify', 'name' => 'editdate', 'type' => 'INT(10)', 'params' => 'NOT NULL DEFAULT \'0\''),
            array('action'	=> 'cmodify', 'name' => 'views', 'type' => 'INT(10)', 'params' => 'NOT NULL DEFAULT \'0\''),
            array('action'	=> 'cmodify', 'name' => 'announce_name', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'author', 'type' => 'varchar(100)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'author_id', 'type' => 'int(10)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'author_email', 'type' => 'varchar(80)	', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'announce_period', 'type' => 'int(10)', 'params' => 'NOT NULL DEFAULT \'0\''),
            array('action'	=> 'cmodify', 'name' => 'announce_description', 'type' => 'text', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'announce_contacts', 'type' => 'text', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'cat_id', 'type' => 'int(10)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'active', 'type' => 'tinyint(1)', 'params' => 'NOT NULL DEFAULT \'0\''),
            array('action'	=> 'cmodify', 'name' => 'expired', 'type' => 'varchar(10)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'vip_added', 'type' => 'INT(10)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'vip_expired', 'type' => 'INT(10)', 'params' => 'NOT NULL default \'0\''),
        )
    ),

    array(
        'table'		=> 'zboard_view',
        'action'	=> 'cmodify',
        'engine'	=> 'MyISAM',
        'key'		=> 'primary key(id)',
        'fields'	=> array(
            array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'int(10)', 'params' => 'NOT NULL'),
            array('action'	=> 'cmodify', 'name' => 'cnt', 'type' => 'int(10)', 'params' => 'NOT NULL default \'0\''),
        )
    ),

    array(
        'table'		=> 'zboard_cat',
        'action'	=> 'cmodify',
        'engine'	=> 'MyISAM',
        'key'		=> 'primary key(id), KEY `parent_id` (`parent_id`)',
        'fields'	=> array(
            array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'int(10)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'	=> 'cmodify', 'name' => 'cat_name', 'type' => 'varchar(100)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'description', 'type' => 'varchar(100)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'keywords', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'parent_id', 'type' => 'int(10)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'position', 'type' => 'int(10)', 'params' => 'NOT NULL default \'0\''),
        )
    ),

    array(
        'table'		=> 'zboard_pay_order',
        'action'	=> 'cmodify',
        'engine'	=> 'MyISAM',
        'key'		=> 'primary key(id), KEY `zid` (`zid`)',
        'fields'	=> array(
            array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'int(10)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'	=> 'cmodify', 'name' => 'dt', 'type' => 'int(10)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'zid', 'type' => 'int(10)', 'params' => 'NOT NULL default \'0\''),
            array('action'	=> 'cmodify', 'name' => 'merchant_id', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'order_id', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'amount', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'currency', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'description', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'paymode', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'trans_id', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'status', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'error_msg', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'test_mode', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
        )
    ),
        
    array(
        'table'		=> 'zboard_pay_price',
        'action'	=> 'cmodify',
        'engine'	=> 'MyISAM',
        'key'		=> 'primary key(id)',
        'fields'	=> array(
            array('action'	=> 'cmodify', 'name' => 'id', 'type' => 'int(10)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'	=> 'cmodify', 'name' => 'price', 'type' => 'varchar(100)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'time', 'type' => 'varchar(100)', 'params' => 'NOT NULL default \'\''),
        )
    ),

    array(
        'table'		=> 'zboard_images',
        'action'	=> 'cmodify',
        'engine'	=> 'MyISAM',
        'key'		=> 'primary key(pid), KEY `zid` (`zid`)',
        'fields'	=> array(
            array('action'	=> 'cmodify', 'name' => 'pid', 'type' => 'int(10)', 'params' => 'NOT NULL AUTO_INCREMENT'),
            array('action'	=> 'cmodify', 'name' => 'filepath', 'type' => 'varchar(255)', 'params' => 'NOT NULL default \'\''),
            array('action'	=> 'cmodify', 'name' => 'zid', 'type' => 'int(10)', 'params' => 'NOT NULL default \'0\''),
        )
    )
);

    switch ($action)
    {
        case 'confirm':
            generate_install_page('zboard', file_get_contents(''));break;
        case 'autoapply':
        case 'apply':
            if (fixdb_plugin_install('zboard', $db_update, 'install', ($action=='autoapply')?true:false)) {

                    if(!$mysql->record('SHOW INDEX FROM '.prefix.'_zboard WHERE Key_name = \'announce_name\''))
                        $mysql->query('alter table '.prefix.'_zboard add FULLTEXT (announce_name)');

                    if(!$mysql->record('SHOW INDEX FROM '.prefix.'_zboard WHERE Key_name = \'announce_description\''))
                        $mysql->query('alter table '.prefix.'_zboard add FULLTEXT (announce_description)');

                plugin_mark_installed('zboard');
            } else {
                return false;
            }

            $params = array(
                'count' => '10',
                'max_image_size' => '5',
                'width' => '2000',
                'height' => '2000',
                'width_thumb' => '350',
                'description' => 'Описание',
                'keywords' => 'Ключевые, слова,',
                'list_period' => '1|2|3|4',
                'info_send' => '<div class="msgo">Спасибо, <strong>%user%</strong>! Вы добавили новое объявление.<br /><strong>Объявление будет доступно после проверки.</strong></div>',
                'info_edit' => '<div class="msgo">Спасибо, <strong>%user%</strong>! Вы отредактировали объявление.<br /><strong>Объявление будет доступно после проверки.</strong></div>',
                'template_mail' => '%announce_name% - %author% - %announce_description% - %announce_period% - %announce_contacts% - %date%',
                'ext_image' => '*.jpg;*.jpeg;*.gif;*.png',
                'admin_count' => '10',
                'date' => 'j.m.Y - H:i',
                'send_guest' => '1',
                'use_recaptcha' => '1',
                'use_expired' => '1',
                'views_count' => '1',
                'notice_mail' => '0',
                'count_list' => '20',
                'count_search' => '20',
            );
            foreach ($params as $k => $v) {
                extra_set_param('zboard', $k, $v);
            }
            extra_commit_changes();
            break;
    }
    return true;
}
