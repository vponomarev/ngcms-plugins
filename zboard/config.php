<?php

if (!defined('NGCMS')) {
    exit('HAL');
}

plugins_load_config();
LoadPluginLang('zboard', 'config', '', '', '#');

include_once(dirname(__FILE__).'/cache.php');

switch ($_REQUEST['action']) {
    case 'list_announce': list_announce();	break;
    case 'edit_announce': edit_announce();		break;
    case 'list_cat': list_cat();								break;
//	case 'caching': caching();									break;
    case 'send_cat': send_cat();								break;
    case 'cat_name_del': cat_name_del(); list_cat();			break;
    case 'cat_edit': cat_edit(); 								break;
    case 'list_price': list_price();								break;
    case 'send_price': send_price();								break;
    case 'price_del': price_del(); list_price();			    break;
    case 'price_edit': price_edit(); 								break;
    case 'list_order': list_order();								break;
    case 'modify': modify(); list_announce();						break;
//	case 'about': about();										break;
    case 'url': url();											break;
    default: main();
}

/*
function caching()
{
global $tpl, $config, $mysql;
    $tpath = locatePluginTemplates(array('config/main', 'config/caching'), 'zboard', 1);

    if (isset($_REQUEST['submit']))
    {
        pluginSetVariable('zboard', 'cache', intval($_REQUEST['cache']));
        pluginSetVariable('zboard', 'cacheExpire', intval($_REQUEST['time']));
        pluginsSaveConfig();

        redirect_zboard('?mod=extra-config&plugin=zboard&action=caching');
    }

    if (isset($_REQUEST['clear_cache']))
    {
        unlink(dirname(__FILE__).'/cache/sql_index.php');

        redirect_zboard('?mod=extra-config&plugin=zboard&action=caching');
    }

    $cache = pluginGetVariable('zboard', 'cache');
    $cache = '<option value="0" '.($cache==0?'selected':'').'>Нет</option><option value="1" '.($cache==1?'selected':'').'>Да</option>';

    $pvars['vars']= array(
        'cache' => $cache,
        'time' => pluginGetVariable('zboard', 'cacheExpire'),
    );

    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_zboard WHERE active = \'0\' ');

    $tpl->template('caching', $tpath['config/caching'].'config');
    $tpl->vars('caching', $pvars);
    $tvars['vars']= array (
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('caching'),
        'global' => 'Настройка кэша'
    );

    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    print $tpl->show('main');
}
*/

function cat_edit()
{
    global $tpl, $mysql, $main_admin;
    $tpath = locatePluginTemplates(array('config/main', 'config/send_cat'), 'zboard', 1);

    $id = intval($_REQUEST['id']);

    $row = $mysql->record('SELECT * FROM '.prefix.'_zboard_cat WHERE id = '.db_squote($id).' LIMIT 1');

    if (isset($_REQUEST['submit'])) {
        $parent_id = intval($_REQUEST['parent']);
        $cat_name = input_filter_com(convert($_REQUEST['cat_name']));
        if (empty($cat_name)) {
            $error_text[] = '?Название категории не задано';
        }
        $description = input_filter_com(convert($_REQUEST['description']));
        if (empty($description)) {
            $error_text[] = 'Описание категории не задано';
        }
        $keywords = input_filter_com(convert($_REQUEST['keywords']));
        if (empty($keywords)) {
            $error_text[] = 'Ключевые слова не заданы';
        }
        //$position = intval($_REQUEST['position']);
        $position = 1;
        if (empty($position)) {
            $error_text[] = 'Не задана позиция';
        }

        if (empty($error_text)) {
            //	position = '.intval($position).'

            $mysql->query('UPDATE '.prefix.'_zboard_cat SET
				cat_name = '.db_squote($cat_name).',
				description = '.db_squote($description).',
				keywords = '.db_squote($keywords).',
				parent_id = '.db_squote($parent_id).',
				position = 1
				WHERE id = '.$id.'
			');

            generate_catz_cache(true);

            redirect_zboard('?mod=extra-config&plugin=zboard&action=list_cat');
        }
    }

    if (!empty($error_text)) {
        foreach ($error_text as $error) {
            $error_input .= msg(array("type" => "error", "text" => $error), 0, 2);
        }
    } else {
        $error_input ='';
    }

    $res = $mysql->query("SELECT * FROM ".prefix."_zboard_cat ORDER BY id");
    $cats = getCats($res);


    $pvars['vars'] = array(
        'cat_name' => $row['cat_name'],
        'keywords' => $row['keywords'],
        'description' => $row['description'],
        'parent_id' => $row['parent_id'],
        'position' => $row['position'],
        'error' => $error_input,
        'catz' => getTree($cats, $row['parent_id'], 0),
    );

    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_zboard WHERE active = \'0\' ');

    $tpl->template('send_cat', $tpath['config/send_cat'].'config');
    $tpl->vars('send_cat', $pvars);
    $tvars['vars']= array(
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('send_cat'),
        'global' => 'Редактировать категорию'
    );

    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    $main_admin = $tpl->show('main');
}

function getCats($res)
{
    $levels = array();
    $tree = array();
    $cur = array();

    while ($rows = mysqli_fetch_assoc($res)) {
        $cur = &$levels[$rows['id']];
        $cur['parent_id'] = $rows['parent_id'];
        $cur['cat_name'] = $rows['cat_name'];

        if ($rows['parent_id'] == 0) {
            $tree[$rows['id']] = &$cur;
        } else {
            $levels[$rows['parent_id']]['children'][$rows['id']] = &$cur;
        }
    }
    return $tree;
}


function getTree($arr, $flg = null, $l = 0)
{
    $flg;
    $out = '';
    $ft = '&#8212; ';
    foreach ($arr as $k=>$v) {
        if ($k==$flg) {
            $out .= '<option value="'.$k.'" selected>'.str_repeat($ft, $l).$v['cat_name'].'</option>';
        } else {
            $out .= '<option value="'.$k.'">'.str_repeat($ft, $l).$v['cat_name'].'</option>';
        }
        if (!empty($v['children'])) {
            //$l = $l + 1;
            $out .= getTree($v['children'], $flg, $l + 1);
            //$l = $l - 1;
        }
    }
    return $out;
}

function send_cat($params = [])
{
    global $tpl, $template, $config, $mysql, $lang, $main_admin;
    $tpath = locatePluginTemplates(array('config/main', 'config/send_cat'), 'zboard', 1);

    if (isset($_REQUEST['submit'])) {
        $cat_name = input_filter_com(convert($_REQUEST['cat_name']));
        $parent_id = intval($_REQUEST['parent']);

        if (empty($cat_name)) {
            $error_text[] = 'Название категории не задано';
        }
        $description = input_filter_com(convert($_REQUEST['description']));
        if (empty($description)) {
            $error_text[] = 'Описание категории не задано';
        }
        $keywords = input_filter_com(convert($_REQUEST['keywords']));
        if (empty($keywords)) {
            $error_text[] = 'Ключевые слова не заданы';
        }
        //$position = intval($_REQUEST['position']);
        $position = 1;
        if (empty($position)) {
            $error_text[] = 'Не задана позиция';
        }

        if (empty($error_text)) {
            $mysql->query('INSERT INTO '.prefix.'_zboard_cat (cat_name, description, keywords, parent_id, position)
				VALUES
				('.db_squote($cat_name).',
					'.db_squote($description).',
					'.db_squote($keywords).',
					'.db_squote($parent_id).',
					'.intval($position).'
				)
			');

            generate_catz_cache(true);

            redirect_zboard('?mod=extra-config&plugin=zboard&action=list_cat');
        }
    }

    if (!empty($error_text)) {
        foreach ($error_text as $error) {
            $error_input .= msg(array("type" => "error", "text" => $error));
        }
    } else {
        $error_input ='';
    }

    $res = $mysql->query("SELECT * FROM ".prefix."_zboard_cat ORDER BY id");
    $cats = getCats($res);

    $pvars['vars'] = array(
        'cat_name' => $cat_name,
        'keywords' => $keywords,
        'description' => $description,
        'position' => $position,
        'parent' => $parent,
        'error' => $error_input,
        'catz' => getTree($cats),
    );

    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_zboard WHERE active = \'0\' ');

    $tpl->template('send_cat', $tpath['config/send_cat'].'config');
    $tpl->vars('send_cat', $pvars);
    $tvars['vars']= array(
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('send_cat'),
        'global' => 'Добавить категорию'
    );

    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    $main_admin = $tpl->show('main');
}

function list_cat()
{
    global $tpl, $mysql, $main_admin;
    $tpath = locatePluginTemplates(array('config/main', 'config/list_cat', 'config/list_cat_entries'), 'zboard', 1);

    foreach ($mysql->select('SELECT cat_id, COUNT(id) as num FROM '.prefix.'_zboard GROUP BY cat_id') as $rows) {
        $cat[$rows['cat_id']] .= $rows['num'];
    }


    foreach ($mysql->select('SELECT * from '.prefix.'_zboard_cat ORDER BY position ASC') as $row) {
        $gvars['vars'] = array(
            'num' => $cat[$row['id']],
            'id' => $row['id'],
            'cat_name' => '<a href="?mod=extra-config&plugin=zboard&action=cat_edit&id='.$row['id'].'"  />'.$row['cat_name'].'</a>',
            'cat_name_del' => '<a href="?mod=extra-config&plugin=zboard&action=cat_name_del&id='.$row['id'].'"  /><img title="???????" alt="???????" src="/engine/skins/default/images/delete.gif"></a>',
        );

        $tpl->template('list_cat_entries', $tpath['config/list_cat_entries'].'config');
        $tpl->vars('list_cat_entries', $gvars);
        $entries .= $tpl -> show('list_cat_entries');
    }

    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_zboard WHERE active = \'0\' ');




    $pvars['vars']['entries'] = isset($entries)?$entries:'';
    $tpl->template('list_cat', $tpath['config/list_cat'].'config');
    $tpl->vars('list_cat', $pvars);
    $tvars['vars']= array(
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('list_cat'),
        'global' => 'Список категорий'
    );

    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    $main_admin = $tpl->show('main');
}

function list_order()
{
    global $tpl, $mysql, $main_admin;
    $tpath = locatePluginTemplates(array('config/main', 'config/list_order', 'config/list_order_entries'), 'zboard', 1);

    foreach ($mysql->select('SELECT *, po.id as id, zb.id as zid from '.prefix.'_zboard_pay_order po LEFT JOIN '.prefix.'_zboard zb  ON po.zid = zb.id ORDER BY po.id ASC') as $row) {
        $gvars['vars'] = array(
            'id' => $row['id'],
            'dt' => (empty($row['dt']))?'Дата не указана':date(pluginGetVariable('zboard', 'date'), $row['dt']),
            'price' => $row['amount']." ".$row['currency'],
            'discr' => $row['description'],
            'status' => $row['status'],
            'announce' => '<a href="?mod=extra-config&plugin=zboard&action=edit_announce&id='.$row['zid'].'"  />'.$row['announce_name'].'</a>',
        );

        $tpl->template('list_order_entries', $tpath['config/list_order_entries'].'config');
        $tpl->vars('list_order_entries', $gvars);
        $entries .= $tpl -> show('list_order_entries');
    }

    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_zboard WHERE active = \'0\' ');

    $pvars['vars']['entries'] = isset($entries)?$entries:'';
    $tpl->template('list_order', $tpath['config/list_order'].'config');
    $tpl->vars('list_order', $pvars);
    $tvars['vars']= array(
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('list_order'),
        'global' => 'Прайс'
    );

    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    $main_admin = $tpl->show('main');
}

function list_price()
{
    global $tpl, $mysql, $main_admin;
    $tpath = locatePluginTemplates(array('config/main', 'config/list_price', 'config/list_price_entries'), 'zboard', 1);

    foreach ($mysql->select('SELECT * from '.prefix.'_zboard_pay_price ORDER BY id ASC') as $row) {
        $gvars['vars'] = array(
            'id' => $row['id'],
            'time' => $row['time'],
            'price' => '<a href="?mod=extra-config&plugin=zboard&action=price_edit&id='.$row['id'].'"  />'.$row['price'].'</a>',
            'price_del' => '<a href="?mod=extra-config&plugin=zboard&action=price_del&id='.$row['id'].'"  /><img src="/engine/skins/default/images/delete.gif"></a>',
        );

        $tpl->template('list_price_entries', $tpath['config/list_price_entries'].'config');
        $tpl->vars('list_price_entries', $gvars);
        $entries .= $tpl -> show('list_price_entries');
    }

    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_zboard WHERE active = \'0\' ');

    $pvars['vars']['entries'] = isset($entries)?$entries:'';
    $tpl->template('list_price', $tpath['config/list_price'].'config');
    $tpl->vars('list_price', $pvars);
    $tvars['vars']= array(
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('list_price'),
        'global' => 'Прайс'
    );

    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    $main_admin = $tpl->show('main');
}

function send_price($params)
{
    global $tpl, $template, $config, $mysql, $lang, $main_admin;
    $tpath = locatePluginTemplates(array('config/main', 'config/send_price'), 'zboard', 1);

    if (isset($_REQUEST['submit'])) {
        $price = input_filter_com(convert($_REQUEST['price']));
        $time = intval($_REQUEST['time']);

        if (empty($price)) {
            $error_text[] = 'Прайс не задан';
        }

        if (empty($time)) {
            $error_text[] = 'Время не задано';
        }

        if (empty($error_text)) {
            $mysql->query('INSERT INTO '.prefix.'_zboard_pay_price (time, price)
				VALUES
				('.db_squote($time).',
                '.db_squote($price).'
				)
			');

            redirect_zboard('?mod=extra-config&plugin=zboard&action=list_price');
        }
    }

    if (!empty($error_text)) {
        foreach ($error_text as $error) {
            $error_input .= msg(array("type" => "error", "text" => $error));
        }
    } else {
        $error_input ='';
    }

    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_zboard WHERE active = \'0\' ');

    $pvars['vars'] = array(
        'time' => $time,
        'price' => $price,
        'error' => $error_input,
    );


    $tpl->template('send_price', $tpath['config/send_price'].'config');
    $tpl->vars('send_price', $pvars);
    $tvars['vars']= array(
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('send_price'),
        'global' => 'Добавить категорию'
    );

    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    $main_admin = $tpl->show('main');
}

function price_edit()
{
    global $tpl, $mysql, $main_admin;
    $tpath = locatePluginTemplates(array('config/main', 'config/send_price'), 'zboard', 1);

    $id = intval($_REQUEST['id']);

    $row = $mysql->record('SELECT * FROM '.prefix.'_zboard_pay_price WHERE id = '.db_squote($id).' LIMIT 1');

    if (isset($_REQUEST['submit'])) {
        $time = intval($_REQUEST['time']);
        $price = input_filter_com(convert($_REQUEST['price']));

        if (empty($price)) {
            $error_text[] = 'Прайс не задан';
        }

        if (empty($time)) {
            $error_text[] = 'Время не задано';
        }

        if (empty($error_text)) {
            //	position = '.intval($position).'

            $mysql->query('UPDATE '.prefix.'_zboard_pay_price SET
				time = '.db_squote($time).',
				price = '.db_squote($price).'
				WHERE id = '.$id.'
			');

            redirect_zboard('?mod=extra-config&plugin=zboard&action=list_price');
        }
    }

    if (!empty($error_text)) {
        foreach ($error_text as $error) {
            $error_input .= msg(array("type" => "error", "text" => $error), 0, 2);
        }
    } else {
        $error_input ='';
    }

    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_zboard WHERE active = \'0\' ');

    $pvars['vars'] = array(
        'time' => $row['time'],
        'price' => $row['price'],
        'error' => $error_input,
    );

    $tpl->template('send_price', $tpath['config/send_price'].'config');
    $tpl->vars('send_price', $pvars);
    $tvars['vars']= array(
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('send_price'),
        'global' => 'Редактировать прайс'
    );

    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    $main_admin = $tpl->show('main');
}

function price_del()
{
    global $mysql;

    $id = intval($_REQUEST['id']);

    if (empty($id)) {
        return msg(array("type" => "error", "text" => "Ошибка, вы не выбрали что хотите удалить"));
    }

    $mysql->query("delete from ".prefix."_zboard_pay_price where id = {$id}");

    msg(array("type" => "info", "info" => "Прайс удален"));
}


function url()
{
    global $tpl, $mysql, $main_admin;
    $tpath = locatePluginTemplates(array('config/main', 'config/url'), 'zboard', 1);

    if (isset($_REQUEST['submit'])) {
        if (isset($_REQUEST['url']) && !empty($_REQUEST['url'])) {
            $ULIB = new urlLibrary();
            $ULIB->loadConfig();

            $ULIB->registerCommand(
                'zboard',
                '',
                array('vars' =>
                        array( 	'cat' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'Категории')),
                                'page' => array('matchRegex' => '\d{1,4}', 'descr' => array('russian' => 'Постраничная навигация'))
                        ),
                        'descr'	=> array('russian' => 'Главная страница'),
                )
            );

            $ULIB->registerCommand(
                'zboard',
                'show',
                array('vars' =>
                        array(	'id' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'ID объявления')),
                        ),
                        'descr'	=> array('russian' => 'Ссылка на объявление'),
                )
            );

            $ULIB->registerCommand(
                'zboard',
                'send',
                array('vars' =>
                        array(),
                        'descr'	=> array('russian' => 'Добавить объявлдение'),
                )
            );

            $ULIB->registerCommand(
                'zboard',
                'search',
                array('vars' =>
                        array(),
                        'descr'	=> array('russian' => 'Поиск по объявлениям'),
                )
            );

            $ULIB->registerCommand(
                'zboard',
                'list',
                array('vars' =>
                        array( 'page' => array('matchRegex' => '\d{1,4}', 'descr' => array('russian' => 'Постраничная навигация'))
                        ),
                        'descr'	=> array('russian' => 'Список объявлений добавленных пользователем'),
                )
            );

            $ULIB->registerCommand(
                'zboard',
                'edit',
                array('vars' =>
                        array(	'id' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'ID объявления')),
                        ),
                        'descr'	=> array('russian' => 'Ссылка для редактирования'),
                )
            );

            $ULIB->registerCommand(
                'zboard',
                'del',
                array('vars' =>
                        array(	'id' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'ID объявления')),
                        ),
                        'descr'	=> array('russian' => 'Ссылка для удаления'),
                )
            );

            $ULIB->registerCommand(
                'zboard',
                'expend',
                array('vars' =>
                        array(	'id' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'ID объявления')),
                                'hashcode' => array('matchRegex' => '.+?', 'descr' => array('russian' => 'Hashcode объявления')),
                        ),
                        'descr'	=> array('russian' => 'Ссылка для продления'),
                )
            );

            $ULIB->saveConfig();

            $UHANDLER = new urlHandler();
            $UHANDLER->loadConfig();

            $UHANDLER->registerHandler(
                0,
                array(
                'pluginName' => 'zboard',
                'handlerName' => '',
                'flagPrimary' => true,
                'flagFailContinue' => false,
                'flagDisabled' => false,
                'rstyle' =>
                array(
                  'rcmd' => '/zboard/[cat/{cat}/][page/{page}/]',
                  'regex' => '#^/zboard/(?:cat/(\\d+)/){0,1}(?:page/(\\d{1,4})/){0,1}$#',
                  'regexMap' =>
                  array(
                    1 => 'cat',
                    2 => 'page',
                  ),
                  'reqCheck' =>
                  array(
                  ),
                  'setVars' =>
                  array(
                  ),
                  'genrMAP' =>
                  array(
                    0 =>
                    array(
                      0 => 0,
                      1 => '/zboard/',
                      2 => 0,
                    ),
                    1 =>
                    array(
                      0 => 0,
                      1 => 'cat/',
                      2 => 1,
                    ),
                    2 =>
                    array(
                      0 => 1,
                      1 => 'cat',
                      2 => 1,
                    ),
                    3 =>
                    array(
                      0 => 0,
                      1 => '/',
                      2 => 1,
                    ),
                    4 =>
                    array(
                      0 => 0,
                      1 => 'page/',
                      2 => 3,
                    ),
                    5 =>
                    array(
                      0 => 1,
                      1 => 'page',
                      2 => 3,
                    ),
                    6 =>
                    array(
                      0 => 0,
                      1 => '/',
                      2 => 3,
                    ),
                  ),
                ),
              )
            );

            $UHANDLER->registerHandler(
                0,
                array(
                'pluginName' => 'zboard',
                'handlerName' => 'show',
                'flagPrimary' => true,
                'flagFailContinue' => false,
                'flagDisabled' => false,
                'rstyle' =>
                array(
                  'rcmd' => '/zboard/{id}/',
                  'regex' => '#^/zboard/(\\d+)/$#',
                  'regexMap' =>
                  array(
                    1 => 'id',
                  ),
                  'reqCheck' =>
                  array(
                  ),
                  'setVars' =>
                  array(
                  ),
                  'genrMAP' =>
                  array(
                    0 =>
                    array(
                      0 => 0,
                      1 => '/zboard/',
                      2 => 0,
                    ),
                    1 =>
                    array(
                      0 => 1,
                      1 => 'id',
                      2 => 0,
                    ),
                    2 =>
                    array(
                      0 => 0,
                      1 => '/',
                      2 => 0,
                    ),
                  ),
                ),
              )
            );

            $UHANDLER->registerHandler(
                0,
                array(
                'pluginName' => 'zboard',
                'handlerName' => 'send',
                'flagPrimary' => true,
                'flagFailContinue' => false,
                'flagDisabled' => false,
                'rstyle' =>
                array(
                  'rcmd' => '/zboard/send/',
                  'regex' => '#^/zboard/send/$#',
                  'regexMap' =>
                  array(
                  ),
                  'reqCheck' =>
                  array(
                  ),
                  'setVars' =>
                  array(
                  ),
                  'genrMAP' =>
                  array(
                    0 =>
                    array(
                      0 => 0,
                      1 => '/zboard/send/',
                      2 => 0,
                    ),
                  ),
                ),
              )
            );

            $UHANDLER->registerHandler(
                0,
                array(
                'pluginName' => 'zboard',
                'handlerName' => 'search',
                'flagPrimary' => true,
                'flagFailContinue' => false,
                'flagDisabled' => false,
                'rstyle' =>
                array(
                  'rcmd' => '/zboard/search/',
                  'regex' => '#^/zboard/search/$#',
                  'regexMap' =>
                  array(
                  ),
                  'reqCheck' =>
                  array(
                  ),
                  'setVars' =>
                  array(
                  ),
                  'genrMAP' =>
                  array(
                    0 =>
                    array(
                      0 => 0,
                      1 => '/zboard/search/',
                      2 => 0,
                    ),
                  ),
                ),
              )
            );

            $UHANDLER->registerHandler(
                0,
                array(
                'pluginName' => 'zboard',
                'handlerName' => 'list',
                'flagPrimary' => true,
                'flagFailContinue' => false,
                'flagDisabled' => false,
                'rstyle' =>
                array(
                  'rcmd' => '/zboard/list/[page/{page}/]',
                  'regex' => '#^/zboard/list/(?:page/(\\d{1,4})/){0,1}$#',
                  'regexMap' =>
                  array(
                    1 => 'page',
                  ),
                  'reqCheck' =>
                  array(
                  ),
                  'setVars' =>
                  array(
                  ),
                  'genrMAP' =>
                  array(
                    0 =>
                    array(
                      0 => 0,
                      1 => '/zboard/list/',
                      2 => 0,
                    ),
                    1 =>
                    array(
                      0 => 0,
                      1 => 'page/',
                      2 => 1,
                    ),
                    2 =>
                    array(
                      0 => 1,
                      1 => 'page',
                      2 => 1,
                    ),
                    3 =>
                    array(
                      0 => 0,
                      1 => '/',
                      2 => 1,
                    ),
                  ),
                ),
              )
            );

            $UHANDLER->registerHandler(
                0,
                array(
                'pluginName' => 'zboard',
                'handlerName' => 'edit',
                'flagPrimary' => true,
                'flagFailContinue' => false,
                'flagDisabled' => false,
                'rstyle' =>
                array(
                  'rcmd' => '/zboard/edit/{id}/',
                  'regex' => '#^/zboard/edit/(\\d+)/$#',
                  'regexMap' =>
                  array(
                    1 => 'id',
                  ),
                  'reqCheck' =>
                  array(
                  ),
                  'setVars' =>
                  array(
                  ),
                  'genrMAP' =>
                  array(
                    0 =>
                    array(
                      0 => 0,
                      1 => '/zboard/edit/',
                      2 => 0,
                    ),
                    1 =>
                    array(
                      0 => 1,
                      1 => 'id',
                      2 => 0,
                    ),
                    2 =>
                    array(
                      0 => 0,
                      1 => '/',
                      2 => 0,
                    ),
                  ),
                ),
              )
            );

            $UHANDLER->registerHandler(
                0,
                array(
                'pluginName' => 'zboard',
                'handlerName' => 'del',
                'flagPrimary' => true,
                'flagFailContinue' => false,
                'flagDisabled' => false,
                'rstyle' =>
                array(
                  'rcmd' => '/zboard/del/{id}/',
                  'regex' => '#^/zboard/del/(\\d+)/$#',
                  'regexMap' =>
                  array(
                    1 => 'id',
                  ),
                  'reqCheck' =>
                  array(
                  ),
                  'setVars' =>
                  array(
                  ),
                  'genrMAP' =>
                  array(
                    0 =>
                    array(
                      0 => 0,
                      1 => '/zboard/del/',
                      2 => 0,
                    ),
                    1 =>
                    array(
                      0 => 1,
                      1 => 'id',
                      2 => 0,
                    ),
                    2 =>
                    array(
                      0 => 0,
                      1 => '/',
                      2 => 0,
                    ),
                  ),
                ),
              )
            );


            $UHANDLER->registerHandler(
                0,
                array(
                'pluginName' => 'zboard',
                'handlerName' => 'expend',
                'flagPrimary' => true,
                'flagFailContinue' => false,
                'flagDisabled' => false,
                'rstyle' =>
                array(
                  'rcmd' => '/zboard/expend/[id/{id}/][hashcode/{hashcode}/]',
                  'regex' => '#^/zboard/expend/(?:id/(\\d+)/){0,1}(?:hashcode/(.+?)/){0,1}$#',
                  'regexMap' =>
                  array(
                    1 => 'id',
                    2 => 'hashcode',
                  ),
                  'reqCheck' =>
                  array(
                  ),
                  'setVars' =>
                  array(
                  ),
                  'genrMAP' =>
                  array(
                    0 =>
                    array(
                      0 => 0,
                      1 => '/zboard/',
                      2 => 0,
                    ),
                    1 =>
                    array(
                      0 => 0,
                      1 => 'id/',
                      2 => 1,
                    ),
                    2 =>
                    array(
                      0 => 1,
                      1 => 'id',
                      2 => 1,
                    ),
                    3 =>
                    array(
                      0 => 0,
                      1 => '/',
                      2 => 1,
                    ),
                    4 =>
                    array(
                      0 => 0,
                      1 => 'hashcode/',
                      2 => 3,
                    ),
                    5 =>
                    array(
                      0 => 1,
                      1 => 'hashcode',
                      2 => 3,
                    ),
                    6 =>
                    array(
                      0 => 0,
                      1 => '/',
                      2 => 3,
                    ),
                  ),
                ),
              )
            );

            $UHANDLER->saveConfig();
        } else {
            $ULIB = new urlLibrary();
            $ULIB->loadConfig();
            $ULIB->removeCommand('zboard', '');
            $ULIB->removeCommand('zboard', 'show');
            $ULIB->removeCommand('zboard', 'send');
            $ULIB->removeCommand('zboard', 'search');
            $ULIB->removeCommand('zboard', 'list');
            $ULIB->removeCommand('zboard', 'edit');
            $ULIB->removeCommand('zboard', 'del');
            $ULIB->removeCommand('zboard', 'expend');
            $ULIB->saveConfig();
            $UHANDLER = new urlHandler();
            $UHANDLER->loadConfig();
            $UHANDLER->removePluginHandlers('zboard', '');
            $UHANDLER->removePluginHandlers('zboard', 'show');
            $UHANDLER->removePluginHandlers('zboard', 'send');
            $UHANDLER->removePluginHandlers('zboard', 'search');
            $UHANDLER->removePluginHandlers('zboard', 'list');
            $UHANDLER->removePluginHandlers('zboard', 'edit');
            $UHANDLER->removePluginHandlers('zboard', 'del');
            $UHANDLER->removePluginHandlers('zboard', 'expend');
            $UHANDLER->saveConfig();
        }

        pluginSetVariable('zboard', 'url', intval($_REQUEST['url']));
        pluginsSaveConfig();

        redirect_zboard('?mod=extra-config&plugin=zboard&action=url');
    }
    $url = pluginGetVariable('zboard', 'url');
    $url = '<option value="0" '.(empty($url)?'selected':'').'>Нет</option><option value="1" '.(!empty($url)?'selected':'').'>Да</option>';
    $pvars['vars']['info'] = $url;

    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_zboard WHERE active = \'0\' ');

    $tpl->template('url', $tpath['config/url'].'config');
    $tpl->vars('url', $pvars);
    $tvars['vars']= array(
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('url'),
        'global' => 'Настройка ЧПУ'
    );

    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    $main_admin = $tpl->show('main');
}

function list_announce()
{
    global $tpl, $mysql, $lang, $main_admin;
    $tpath = locatePluginTemplates(array('config/main', 'config/list_announce', 'config/list_entries'), 'zboard', 1);

    $news_per_page = pluginGetVariable('zboard', 'admin_count');

    if (($news_per_page < 2)||($news_per_page > 2000)) {
        $news_per_page = 2;
    }

    $pageNo		= intval($_REQUEST['page'])?$_REQUEST['page']:0;
    if ($pageNo < 1) {
        $pageNo = 1;
    }
    if (!$start_from) {
        $start_from = ($pageNo - 1)* $news_per_page;
    }

    $count = $mysql->result('SELECT count(id) from '.prefix.'_zboard');
    $countPages = ceil($count / $news_per_page);

    foreach ($mysql->select('SELECT * from '.prefix.'_zboard ORDER BY editdate DESC LIMIT '.$start_from.', '.$news_per_page) as $row) {
        switch ($row['active']) {
            case 1: $active = 'Да'; break;
            case 0: $active = 'Нет'; break;
            default: $active = 'Ошибка';
        }

        foreach ($mysql->select('SELECT id, cat_name FROM '.prefix.'_zboard_cat where id='.$row['cat_id'].'') as $cat) {
            $options = $cat['cat_name'];
        }

        $gvars['vars'] = array(
            'id' => $row['id'],
            'announce_name' => '<a href="?mod=extra-config&plugin=zboard&action=edit_announce&id='.$row['id'].'"  />'.$row['announce_name'].'</a>',
            'announce_period' => $row['announce_period'],
            'announce_description' => $row['announce_description'],
            'announce_contacts' => $row['announce_contacts'],
            'vip_added'				=>	$row['vip_added'],
            'vip_expired'			=>	$row['vip_expired'],
            'date' => (empty($row['date']))?'Дата не указана':date(pluginGetVariable('zboard', 'date'), $row['date']),
            'category' => $options,
            'active' => $active,
            'author' => $row['author'],
        );

        $tpl->template('list_entries', $tpath['config/list_entries'].'config');
        $tpl->vars('list_entries', $gvars);
        $entries .= $tpl -> show('list_entries');
    }

    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_zboard WHERE active = \'0\' ');

    $pvars['vars']['pagesss'] = generateAdminPagelist(array('current' => $pageNo, 'count' => $countPages, 'url' => admin_url.'/admin.php?mod=extra-config&plugin=zboard&action=list_announce'.($_REQUEST['news_per_page']?'&news_per_page='.$news_per_page:'').($_REQUEST['author']?'&author='.$_REQUEST['author']:'').($_REQUEST['sort']?'&sort='.$_REQUEST['sort']:'').($postdate?'&postdate='.$postdate:'').($author?'&author='.$author:'').($status?'&status='.$status:'').'&page=%page%'));
    $pvars['vars']['entries'] = $entries;
    $tpl->template('list_announce', $tpath['config/list_announce'].'config');
    $tpl->vars('list_announce', $pvars);
    $tvars['vars']= array(
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('list_announce'),
        'global' => 'Список объявлений'
    );

    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    $main_admin = $tpl->show('main');
}

function edit_announce()
{
    global $tpl, $lang, $mysql, $config, $main_admin;
    $tpath = locatePluginTemplates(array('config/main', 'config/edit_announce', 'config/list_images'), 'zboard', 1);

    $id = intval($_REQUEST['id']);
    if (!empty($id)) {
        $row = $mysql->record('SELECT * FROM '.prefix.'_zboard WHERE id = '.db_squote($id).' LIMIT 1');

        foreach (explode("|", pluginGetVariable('zboard', 'list_period')) as $line) {
            $list_period .= str_replace(array('{line}', '{activ}'), array($line, ($line==$row['announce_period']?'selected':'')), $lang['zboard']['list_period']);
        }
        /*
        $options = '<option disabled>---------</option>';
        foreach ($mysql->select('SELECT id, cat_name FROM '.prefix.'_zboard_cat') as $cat)
        {
            $options .= '<option value="' . $cat['id'] . '"'.(($row['cat_id']==$cat['id'])?'selected':'').'>' . $cat['cat_name'] . '</option>';
        }
        */
        $res = $mysql->query("SELECT * FROM ".prefix."_zboard_cat ORDER BY id");
        $cats = getCats($res);
        $options = getTree($cats, $row['cat_id'], 0);

        if (isset($_REQUEST['submit'])) {
            $SQL['editdate'] = time() + ($config['date_adjust'] * 60);

            $SQL['announce_name'] = input_filter_com(convert($_REQUEST['announce_name']));
            if (empty($SQL['announce_name'])) {
                $error_text[] = 'Название объявления пустое';
            }


            $SQL['author'] = input_filter_com(convert($_REQUEST['author']));
            if (empty($SQL['author'])) {
                $error_text[] = 'Поле автор не заполнено';
            }

            $SQL['announce_period'] = input_filter_com(convert($_REQUEST['announce_period']));
            if (!empty($SQL['announce_period'])) {
                if (!in_array($SQL['announce_period'], explode("|", pluginGetVariable('zboard', 'list_period')))) {
                    $error_text[] = 'Поле период задано неверно '.$SQL['announce_period'];
                }
            } else {
                $error_text[] = 'Поле период не заполнено';
            }

            $SQL['cat_id'] = intval($_REQUEST['cat_id']);
            if (!empty($SQL['cat_id'])) {
                $cat = $mysql->result('SELECT 1 FROM '.prefix.'_zboard_cat WHERE id = \'' . $SQL['cat_id'] . '\' LIMIT 1');

                if (empty($cat)) {
                    $error_text[] = 'Такой категории не существует';
                }
            } else {
                $error_text[] = 'Вы не выбрали категорию';
            }


            $SQL['announce_description'] = str_replace(array("\r\n", "\r"), "\n", input_filter_com(convert($_REQUEST['announce_description'])));
            if (empty($SQL['announce_description'])) {
                $error_text[] = 'Нет описания к объявлению';
            }

            $SQL['announce_contacts'] = str_replace(array("\r\n", "\r"), "\n", input_filter_com(convert($_REQUEST['announce_contacts'])));
            if (empty($SQL['announce_contacts'])) {
                $error_text[] = 'Нет контактов к объявлению';
            }

            $SQL['active'] = $_REQUEST['announce_activeme'];

            if (is_array($SQLi)) {
                $vnamess = array();
                foreach ($SQLi as $k => $v) {
                    $vnamess[] = $k.' = '.db_squote($v);
                }
                $mysql->query('update '.prefix.'_zboard set '.implode(', ', $vnamess).' where  id = \''.intval($id).'\'');
            }

            if (empty($error_text)) {
                $vnames = array();
                foreach ($SQL as $k => $v) {
                    $vnames[] = $k.' = '.db_squote($v);
                }
                $mysql->query('update '.prefix.'_zboard set '.implode(', ', $vnames).' where  id = \''.intval($id).'\'');

                generate_entries_cnt_cache(true);
                generate_catz_cache(true);

                sleep(5);

                redirect_zboard('?mod=extra-config&plugin=zboard&action=list_announce');
            }
        }

        if (!empty($error_text)) {
            foreach ($error_text as $error) {
                $error_input .= msg(array("type" => "error", "text" => $error));
            }
        } else {
            $error_input ='';
        }

        if ($row['active'] == 1) {
            $checked = 'checked';
        } else {
            $checked = '';
        }

        $pvars['vars'] = array(
            'images_url' => str_replace(
                array( '{url_images}', '{url_images_thumb}'),
                array(images_url.'/zboard/'.$row['plugin_images'], images_url.'/zboard/thumb/'.$row['plugin_images']),
                $lang['zboard']['images_url']
            ),
            'options' => $options,
            'announce_activeme' => $checked,
            'announce_name' => $row['announce_name'],
            'list_period' => $list_period,
            'announce_contacts' =>$row['announce_contacts'],
            'author' => $row['author'],
            'announce_description' => $row['announce_description'],
            'vip_added'				=>	$row['vip_added'],
            'vip_expired'			=>	$row['vip_expired'],
            'tpl_url' => home.'/zboard/'.$config['theme'],
            'tpl_home' => admin_url,
            'id' => $id,
            'error' => $error_input,
        );
    } else {
        msg(array("type" => "error", "text" => "Вы выбрали неверное id"));
    }


    foreach ($mysql->select('select * from '.prefix.'_zboard_images where zid='.$id.'') as $row2) {
        $gvars['vars'] = array(
            'home' => home,
            'del' => home.'/engine/admin.php?mod=extra-config&plugin=zboard&action=edit_announce&id='.$id.'&delimg='.$row2['pid'].'&filepath='.$row2['filepath'].'',
            'pid' => $row2['pid'],
            'filepath' => $row2['filepath'],
            'zid' => $row2['zid'],
        );

        $tpl->template('list_images', $tpath['config/list_images'].'config');
        $tpl->vars('list_images', $gvars);
        $entriesImg .= $tpl -> show('list_images');
    }

    $pvars['vars']['entriesImg'] = $entriesImg;

    if (isset($_REQUEST['delimg']) && isset($_REQUEST['filepath'])) {
        $imgID = intval($_REQUEST['delimg']);
        $imgPath = $_REQUEST['filepath'];
        $mysql->query("delete from ".prefix."_zboard_images where pid = ".$imgID."");
        //echo root . '/uploads/zboard/' . $imgPath;
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/' . $imgPath);
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/thumb/' . $imgPath);
        redirect_zboard('?mod=extra-config&plugin=zboard&action=edit_announce&id='.$id.'');
    }

    if (isset($_REQUEST['delme'])) {
        foreach ($mysql->select('select * from '.prefix.'_zboard_images where zid='.db_squote($id).'') as $row2) {
            unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/' . $row2['filepath']);
            unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/thumb/' . $row2['filepath']);
        }
        $mysql->query("delete from ".prefix."_zboard_images where zid = ".db_squote($id)."");

        $mysql->query('delete from '.prefix.'_zboard where id = '.db_squote($id));

        generate_entries_cnt_cache(true);

        redirect_zboard('?mod=extra-config&plugin=zboard&action=list_announce');
    }

    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_zboard WHERE active = \'0\' ');

    $tpl->template('edit_announce', $tpath['config/edit_announce'].'config');
    $tpl->vars('edit_announce', $pvars);
    $tvars['vars']= array(
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('edit_announce'),
        'entriesImg' => $tpl->show('list_images'),
        'global' => 'Редактирование: '.$row['announce_name']
    );

    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    $main_admin = $tpl->show('main');
}

/*
function about()
{
global $tpl, $mysql;
    $tpath = locatePluginTemplates(array('config/main', 'config/about'), 'zboard', 1);

    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_zboard WHERE active = \'0\' ');

    $tpl->template('about', $tpath['config/about'].'config');
    $tpl->vars('about', $pvars);
    $tvars['vars']= array (
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('about'),
        'global' => 'О плагине'
    );

    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    print $tpl->show('main');
}
*/

function cat_name_del()
{
    global $mysql;

    $id = intval($_REQUEST['id']);

    if (empty($id)) {
        return msg(array("type" => "error", "text" => "Ошибка, вы не выбрали что хотите удалить"));
    }

    $mysql->query("delete from ".prefix."_zboard_cat where id = {$id}");

    generate_catz_cache(true);

    msg(array("type" => "info", "info" => "Категория удалена"));
}

function modify()
{
    global $mysql;

    $selected_news = $_REQUEST['selected_files'];
    $subaction	=	$_REQUEST['subaction'];

    if (empty($selected_news)) {
        return msg(array("type" => "error", "text" => "Ошибка, вы не выбрали объявление"));
    }

    switch ($subaction) {
        case 'mass_approve': $active = 'active = 1'; break;
        case 'mass_forbidden': $active = 'active = 0'; break;
        case 'mass_delete': $del = true; break;
    }

    foreach ($selected_news as $id) {
        if (isset($active)) {
            $mysql->query('update '.prefix.'_zboard
					set '.$active.'
					WHERE id = '.db_squote($id).'
					');
            $result = 'Объявления Активированы/Деактивированы';
        }
        if (isset($del)) {
            foreach ($mysql->select('select * from '.prefix.'_zboard_images where zid='.db_squote($id).'') as $row2) {
                unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/' . $row2['filepath']);
                unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/thumb/' . $row2['filepath']);
            }
            $mysql->query("delete from ".prefix."_zboard_images where zid = ".db_squote($id)."");

            $mysql->query('delete from '.prefix.'_zboard where id = '.db_squote($id));
            $result = 'Объявления удалены';
        }
    }
    generate_entries_cnt_cache(true);
    generate_catz_cache(true);
    msg(array("type" => "info", "info" => $result));
}

function main()
{
    global $tpl, $mysql, $cron, $main_admin;

    $tpath = locatePluginTemplates(array('config/main', 'config/general.from'), 'zboard', 1);

    if (isset($_REQUEST['submit'])) {
        pluginSetVariable('zboard', 'list_period', secure_html(trim($_REQUEST['list_period'])));
        pluginSetVariable('zboard', 'count', intval($_REQUEST['count']));
        pluginSetVariable('zboard', 'main_template', trim($_REQUEST['main_template']));
        pluginSetVariable('zboard', 'max_image_size', intval($_REQUEST['max_image_size']));
        pluginSetVariable('zboard', 'width_thumb', intval($_REQUEST['width_thumb']));
        pluginSetVariable('zboard', 'width', intval($_REQUEST['width']));
        pluginSetVariable('zboard', 'height', intval($_REQUEST['height']));
        pluginSetVariable('zboard', 'ext_image', secure_html(trim($_REQUEST['ext_image'])));
        pluginSetVariable('zboard', 'admin_count', intval($_REQUEST['admin_count']));
        pluginSetVariable('zboard', 'date', secure_html($_REQUEST['date']));
        pluginSetVariable('zboard', 'notice_mail', intval($_REQUEST['notice_mail']));
        pluginSetVariable('zboard', 'send_guest', intval($_REQUEST['send_guest']));
        pluginSetVariable('zboard', 'template_mail', secure_html($_REQUEST['template_mail']));
        pluginSetVariable('zboard', 'description', secure_html($_REQUEST['description']));
        pluginSetVariable('zboard', 'keywords', secure_html($_REQUEST['keywords']));
        pluginSetVariable('zboard', 'cat_id', secure_html($_REQUEST['cat_id']));
        pluginSetVariable('zboard', 'count_list', secure_html($_REQUEST['count_list']));
        pluginSetVariable('zboard', 'count_search', secure_html($_REQUEST['count_search']));
        pluginSetVariable('zboard', 'info_send', $_REQUEST['info_send']);
        pluginSetVariable('zboard', 'info_edit', $_REQUEST['info_edit']);
        pluginSetVariable('zboard', 'use_recaptcha', $_REQUEST['use_recaptcha']);
        pluginSetVariable('zboard', 'views_count', $_REQUEST['views_count']);
        pluginSetVariable('zboard', 'use_expired', $_REQUEST['use_expired']);
        pluginSetVariable('zboard', 'public_key', $_REQUEST['public_key']);
        pluginSetVariable('zboard', 'private_key', $_REQUEST['private_key']);
        pluginSetVariable('zboard', 'pay2pay_merchant_id', $_REQUEST['pay2pay_merchant_id']);
        pluginSetVariable('zboard', 'pay2pay_secret_key', $_REQUEST['pay2pay_secret_key']);
        pluginSetVariable('zboard', 'pay2pay_hidden_key', $_REQUEST['pay2pay_hidden_key']);
        pluginSetVariable('zboard', 'pay2pay_test_mode', intval($_REQUEST['pay2pay_test_mode']));
        pluginsSaveConfig();

        redirect_zboard('?mod=extra-config&plugin=zboard');
    }

    $views_cnt = intval(pluginGetVariable('zboard', 'views_count'));
    $expired = intval(pluginGetVariable('zboard', 'use_expired'));

    if ($views_cnt == 2) {
        $cron_row = $cron->getConfig();
        foreach ($cron_row as $key=>$value) {
            if (($value['plugin']=='zboard') && ($value['handler']=='zboard_views')) {
                $cron_min = $value['min'];
                $cron_hour = $value['hour'];
                $cron_day = $value['day'];
                $cron_month = $value['month'];
            }
        }
        if (!isset($cron_min)) {
            $cron_min = '0,15,30,45';
        }
        if (!isset($cron_hour)) {
            $cron_hour = '*';
        }
        if (!isset($cron_day)) {
            $cron_day = '*';
        }
        if (!isset($cron_month)) {
            $cron_month = '*';
        }

        $cron->unregisterTask('zboard', 'zboard_views');
        $cron->registerTask('zboard', 'zboard_views', $cron_min, $cron_hour, $cron_day, $cron_month, '*');
    } else {
        $cron->unregisterTask('zboard', 'zboard_views');
    }


    if ($expired == 1) {
        $cron_row_1 = $cron->getConfig();
        foreach ($cron_row_1 as $key_1=>$value_1) {
            if (($value_1['plugin']=='zboard') && ($value_1['handler']=='zboard_expired')) {
                $cron_min = $value_1['min'];
                $cron_hour = $value_1['hour'];
                $cron_day = $value_1['day'];
                $cron_month = $value_1['month'];
            }
        }
        if (!isset($cron_min)) {
            $cron_min = '0,15,30,45';
        }
        if (!isset($cron_hour)) {
            $cron_hour = '*';
        }
        if (!isset($cron_day)) {
            $cron_day = '*';
        }
        if (!isset($cron_month)) {
            $cron_month = '*';
        }

        $cron->unregisterTask('zboard', 'zboard_expired');
        $cron->registerTask('zboard', 'zboard_expired', $cron_min, $cron_hour, $cron_day, $cron_month, '*');
    } else {
        $cron->unregisterTask('zboard', 'zboard_expired');
    }

    $cat_id = pluginGetVariable('zboard', 'cat_id');
    $options = '<option disabled>---------</option>';
    foreach ($mysql->select('SELECT id, cat_name FROM '.prefix.'_zboard_cat') as $row) {
        $options .= '<option value="' . $row['id'] . '"'.(($cat_id==$row['id'])?'selected':'').'>' . $row['cat_name'] . '</option>';
    }
    $list_period = pluginGetVariable('zboard', 'list_period');
    $count = pluginGetVariable('zboard', 'count');
    $max_image_size = pluginGetVariable('zboard', 'max_image_size');
    $width_thumb = pluginGetVariable('zboard', 'width_thumb');
    $width = pluginGetVariable('zboard', 'width');
    $height = pluginGetVariable('zboard', 'height');
    $ext_image = pluginGetVariable('zboard', 'ext_image');
    $admin_count = pluginGetVariable('zboard', 'admin_count');
    $date = pluginGetVariable('zboard', 'date');
    $notice_mail = pluginGetVariable('zboard', 'notice_mail');
    $notice_mail = '<option value="0" '.($notice_mail==0?'selected':'').'>Нет</option><option value="1" '.($notice_mail==1?'selected':'').'>Да</option>';
    $send_guest = pluginGetVariable('zboard', 'send_guest');
    $send_guest = '<option value="0" '.($send_guest==0?'selected':'').'>Нет</option><option value="1" '.($send_guest==1?'selected':'').'>Да</option>';
    $template_mail = pluginGetVariable('zboard', 'template_mail');
    $description = pluginGetVariable('zboard', 'description');
    $keywords = pluginGetVariable('zboard', 'keywords');
    $count_list = pluginGetVariable('zboard', 'count_list');
    $count_search = pluginGetVariable('zboard', 'count_search');
    $info_send = pluginGetVariable('zboard', 'info_send');
    $info_edit = pluginGetVariable('zboard', 'info_edit');
    $use_recaptcha = pluginGetVariable('zboard', 'use_recaptcha');
    $use_recaptcha = '<option value="0" '.($use_recaptcha==0?'selected':'').'>Нет</option><option value="1" '.($use_recaptcha==1?'selected':'').'>Да</option>';
    $views_count = pluginGetVariable('zboard', 'views_count');
    $views_count = '<option value="0" '.($views_count==0?'selected':'').'>Нет</option><option value="1" '.($views_count==1?'selected':'').'>Да</option><option value="2" '.($views_count==2?'selected':'').'>Отложенное</option>';
    $use_expired = pluginGetVariable('zboard', 'use_expired');
    $use_expired = '<option value="0" '.($use_expired==0?'selected':'').'>Нет</option><option value="1" '.($use_expired==1?'selected':'').'>Да</option>';
    $public_key = pluginGetVariable('zboard', 'public_key');
    $private_key = pluginGetVariable('zboard', 'private_key');

    $pay2pay_merchant_id = pluginGetVariable('zboard', 'pay2pay_merchant_id');
    $pay2pay_secret_key = pluginGetVariable('zboard', 'pay2pay_secret_key');
    $pay2pay_hidden_key = pluginGetVariable('zboard', 'pay2pay_hidden_key');
    $pay2pay_test_mode = pluginGetVariable('zboard', 'pay2pay_test_mode');
    $pay2pay_test_mode = '<option value="0" '.($pay2pay_test_mode==0?'selected':'').'>Нет</option><option value="1" '.($pay2pay_test_mode==1?'selected':'').'>Да</option>';

    /*
        if(empty($max_image_size))
            msg(array("type" => "error", "text" => "Критическая ошибка <br /> Размер для изображений не указан"), 1);
        if(empty($width))
            msg(array("type" => "error", "text" => "Критическая ошибка <br /> Ширина изображения не указана"), 1);
        if(empty($height))
            msg(array("type" => "error", "text" => "Критическая ошибка <br /> Высота изображения не указана"), 1);
        if(empty($ext_image))
            msg(array("type" => "error", "text" => "Критическая ошибка <br /> Расширения для изображений не указано"), 1);
    */

    $pvars['vars'] = array(
        'cat_id' => $options,
        'list_period' => $list_period,
        'count' => $count,
        'main_template' => pluginGetVariable('zboard', 'main_template'),
        'max_image_size' => $max_image_size,
        'width_thumb' => $width_thumb,
        'width' => $width,
        'height' => $height,
        'ext_image' => $ext_image,
        'admin_count' => $admin_count,
        'date' => $date,
        'notice_mail' => $notice_mail,
        'send_guest' => $send_guest,
        'template_mail' => $template_mail,
        'description' => $description,
        'keywords' => $keywords,
        'count_list' => $count_list,
        'count_search' => $count_search,
        'info_send' => $info_send,
        'info_edit' => $info_edit,
        'use_recaptcha' => $use_recaptcha,
        'views_count' => $views_count,
        'use_expired' => $use_expired,
        'public_key' => $public_key,
        'private_key' => $private_key,
        'pay2pay_merchant_id' => $pay2pay_merchant_id,
        'pay2pay_secret_key' => $pay2pay_secret_key,
        'pay2pay_hidden_key' => $pay2pay_hidden_key,
        'pay2pay_test_mode' => $pay2pay_test_mode,
    );

    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_zboard WHERE active = \'0\' ');

    $tpl->template('general.from', $tpath['config/general.from'].'config');
    $tpl->vars('general.from', $pvars);
    $tvars['vars']= array(
        'active' => !empty($count)?'[ '.$count.' ]':'',
        'entries' => $tpl->show('general.from'),
        'global' => 'Общие'
    );

    $tpl->template('main', $tpath['config/main'].'config');
    $tpl->vars('main', $tvars);
    $main_admin = $tpl->show('main');
}

function zboard_upload_files($files_del)
{
    $max_file_size = pluginGetVariable('zboard', 'max_file_size') * 1024 * 1024;
    $extensions = explode(',', pluginGetVariable('zboard', 'ext_file'));

    if (isset($_FILES['plugin_files']['name']) && !empty($_FILES['plugin_files']['name'])) {
        if (is_uploaded_file($_FILES['plugin_files']['tmp_name'])) {
            $ext = pathinfo($_FILES['plugin_files']['name'], PATHINFO_EXTENSION);
            if (in_array($ext, $extensions)) {
                if ($_FILES['plugin_files']['size'] < $max_file_size) {
                    if (is_writable(files_dir . 'zboard/')) {
                        $name_file = basename($_FILES['plugin_files']['name'], $ext);
                        $name_file = preg_replace("/[^\w\x7F-\xFF]/", "", $name_file);
                        $Ffile = $name_file . '.' . $ext;

                        if ($files_del == $Ffile) {
                            unlink(files_dir . 'zboard/'. $files_del);
                        }

                        if (file_exists(files_dir . 'zboard/' . $Ffile)) {
                            $error_text = 'Такой файл уже существует';
                        } else {
                            unlink(files_dir . 'zboard/'. $files_del);
                        }

                        if (empty($error_text)) {
                            if (move_uploaded_file($_FILES['plugin_files']['tmp_name'], files_dir . 'zboard/' . $Ffile)) {
                                chmod(files_dir . 'zboard/' . $Ffile, 0644);
                            } else {
                                $error_text = 'Загрузка не удалась';
                            }
                        }
                    } else {
                        $error_text = 'Нет прав на запись';
                    }
                } else {
                    $error_text = 'Размер файла больше допустимого';
                }
            } else {
                $error_text = 'Запрещеное расширение';
            }
        } else {
            $error_text = 'Файл не загружен';
        }
    }
    return array($Ffile, $error_text);
}

function zboard_upload_images($images_del, $w, $h, $quality = 100)
{
    $max_image_size = pluginGetVariable('zboard', 'max_image_size') * 1024 * 1024;
    $extensions = explode(',', pluginGetVariable('zboard', 'ext_image'));

    if (isset($_FILES['plugin_images']['name']) && !empty($_FILES['plugin_images']['name'])) {
        if (is_uploaded_file($_FILES['plugin_images']['tmp_name'])) {
            $ext = pathinfo($_FILES['plugin_images']['name'], PATHINFO_EXTENSION);
            if (in_array($ext, $extensions)) {
                $new = date("Ymd")."_".rand(1000, 9999).'.'.$ext;
                if ($_FILES['plugin_images']['size'] < $max_image_size) {
                    if ($size_img = getimagesize($_FILES['plugin_images']['tmp_name'])) {
                        if (($size_img[0] <= pluginGetVariable('zboard', 'width')) && ($size_img[1] <= pluginGetVariable('zboard', 'height'))) {
                            $dir_image = images_dir .'zboard/'.$new;
                            if (move_uploaded_file($_FILES['plugin_images']['tmp_name'], $dir_image)) {
                                if (isset($images_del)) {
                                    unlink(images_dir . 'zboard/thumb/'.$images_del);
                                    unlink(images_dir . 'zboard/'.$images_del);
                                }

                                switch ($size_img[2]) {
                                    case 1: $image_ext = 'gif';		break;
                                    case 2: $image_ext = 'jpeg';	break;
                                    case 3: $image_ext = 'png';		break;
                                    case 6: $image_ext = 'bmp';		break;
                                }

                                $dest_img = imagecreatetruecolor($w, $h);

                                switch ($size_img[2]) {
                                    case 1: $src_img = imagecreatefromgif($dir_image);		break;
                                    case 2: $src_img = imagecreatefromjpeg($dir_image);		break;
                                    case 3: $src_img = imagecreatefrompng($dir_image);		break;
                                    case 6: $src_img = imagecreatefrombmp($dir_image);		break;
                                }

                                $oTColor = imagecolortransparent($src_img);
                                if ($oTColor >= 0 && $oTColor < imagecolorstotal($src_img)) {
                                    $TColor = imagecolorsforindex($src_img, $oTColor);
                                    $nTColor = imagecolorallocate($dest_img, $TColor['red'], $TColor['green'], $TColor['blue']);
                                    imagefill($dest_img, 0, 0, $nTColor);
                                    imagecolortransparent($dest_img, $nTColor);
                                } else {
                                    if ($size_img[2] == 3) {
                                        imagealphablending($dest_img, false);
                                        $nTColor = imagecolorallocatealpha($dest_img, 0, 0, 0, 127);
                                        imagefill($dest_img, 0, 0, $nTColor);
                                        imagesavealpha($dest_img, true);
                                    }
                                }

                                imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $w, $h, $w, $h);

                                switch ($size_img[2]) {
                                    case 1: imagegif($dest_img, images_dir .'zboard/thumb/'.$new);				break;
                                    case 2: imagejpeg($dest_img, images_dir .'zboard/thumb/'.$new, $quality);	break;
                                    case 3: imagepng($dest_img, images_dir .'zboard/thumb/'.$new);				break;
                                    case 6: imagebmp($dest_img, images_dir .'zboard/thumb/'.$new);				break;
                                }

                                chmod($dir_image, 0644);
                                chmod(images_dir .'zboard/thumb/'.$new, 0644);
                            } else {
                                $error_text = 'Ошибка при сохранении';
                            }
                        } else {
                            $error_text = 'Размер изображения больше чем '.pluginGetVariable('zboard', 'width').' ?? '.pluginGetVariable('zboard', 'height');
                        }
                    } else {
                        $error_text = 'Загруженый файл не является изображением';
                    }
                } else {
                    $error_text = 'Размер файла больше допустимого';
                }
            } else {
                $error_text = 'Недопустимое расширение';
            }
        } else {
            $error_text = 'Изображение не загружено';
        }
    }
    return array($new, $error_text);
}

function redirect_zboard($url)
{
    if (headers_sent()) {
        echo "<script>document.location.href='{$url}';</script>\n";
        exit;
    } else {
        header('HTTP/1.1 302 Moved Permanently');
        header("Location: {$url}");
        exit;
    }
}

function input_filter_com($text)
{
    $text = trim($text);
    $search = array("<", ">");
    $replace = array("&lt;", "&gt;");
    $text = preg_replace("/(&amp;)+(?=\#([0-9]{2,3});)/i", "&", str_replace($search, $replace, $text));
    return $text;
}
