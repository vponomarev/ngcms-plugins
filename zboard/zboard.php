<?php

if (!defined('NGCMS'))
    exit('HAL');

LoadPluginLang('zboard', 'main', '', '', '#');
add_act('index', 'zboard_header_show');
register_plugin_page('zboard','','zboard');
register_plugin_page('zboard','show','show_zboard');
register_plugin_page('zboard','send','send_zboard');
register_plugin_page('zboard','search','search_zboard');
register_plugin_page('zboard','list','list_zboard');
register_plugin_page('zboard','edit','edit_zboard');
register_plugin_page('zboard','vip','vip_zboard');
register_plugin_page('zboard','pay','pay_zboard');
register_plugin_page('zboard','expend','expend_zboard');
register_plugin_page('zboard','del','del_zboard');

include_once(dirname(__FILE__).'/cache.php');

function zboard_header_show()
{
global $CurrentHandler, $SYSTEM_FLAGS, $template, $lang;

    /* print '<pre>';
    print_r ($CurrentHandler);
    print '</pre>'; */

    /* print '<pre>';
    print_r ($SYSTEM_FLAGS);
    print '</pre>';  */

    if(empty($_REQUEST['page']))
    {
        $page = $CurrentHandler['params']['page'];
    } else {
        $page = $_REQUEST['page'];
    }

    $pageNo = isset($page)?str_replace('%count%',intval($page), '/ Страница %count%'):'';

    switch ($CurrentHandler['handlerName'])
    {
        case '':
            $titles = str_replace(
                array ('%name_site%', '%separator%', '%group%', '%others%', '%num%'),
                array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['separator'], $SYSTEM_FLAGS['info']['title']['group'],  $SYSTEM_FLAGS['info']['title']['others'], $pageNo),
                $lang['zboard']['titles']);
            break;
        case 'show':
            $titles = str_replace(
                array ('%name_site%', '%group%', '%others%'),
                array ($SYSTEM_FLAGS['info']['title']['header'],  $SYSTEM_FLAGS['info']['title']['group'], $SYSTEM_FLAGS['info']['title']['others']),
                $lang['zboard']['titles_show']);
            break;
        case 'send':
            $titles = str_replace(
                array ('%name_site%', '%group%', '%others%'),
                array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group'], $SYSTEM_FLAGS['info']['title']['others']),
                $lang['zboard']['titles_send']);
            break;
        case 'search':
            $titles = str_replace(
                array ('%name_site%', '%group%', '%others%'),
                array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group'], $SYSTEM_FLAGS['info']['title']['others']),
                $lang['zboard']['titles_search']);
            break;
        case 'list':
            $titles = str_replace(
                array ('%name_site%', '%group%', '%others%'),
                array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group'], $SYSTEM_FLAGS['info']['title']['others']),
                $lang['zboard']['titles_list']);
            break;
        case 'edit':
            $titles = str_replace(
                array ('%name_site%', '%group%', '%others%'),
                array ($SYSTEM_FLAGS['info']['title']['header'], $SYSTEM_FLAGS['info']['title']['group'], $SYSTEM_FLAGS['info']['title']['others']),
                $lang['zboard']['titles_edit']);
            break;
    }



    $template['vars']['titles'] = trim($titles);
}

function pay_zboard($params = []) {
    global $config, $mysql, $catz, $catmap, $SUPRESS_TEMPLATE_SHOW, $SYSTEM_FLAGS;

	$SUPRESS_TEMPLATE_SHOW = 1;
	$SUPRESS_MAINBLOCK_SHOW = 1;

    $zid = intval($_REQUEST['zid']);
    $price_id = intval($_REQUEST['price_time_id']);
    $current_time = time() + ($config['date_adjust'] * 60);
    $result = intval($_REQUEST['result']);

    if(empty($result) && empty($zid))
    {
        redirect_zboard(link_zboard());
    }


    if(!empty($result))
    {

        switch($result) {
            case '1':
                // fail_url
                redirect_zboard(link_zboard_list());
                break;
            case '2':
                $_REQUEST['sign'] = str_replace(' ', '+', $_REQUEST['sign']);
                $_REQUEST['xml'] = str_replace(' ', '+', $_REQUEST['xml']);

                // result_url
                if (!empty($_REQUEST['xml']) and !empty($_REQUEST['sign'])){
                    // Инициализация переменной для хранения сообщения об ошибке
                    $error = '';
                    // Декодируем входные параметры
                    $xml_encoded = str_replace(' ', '+', $_REQUEST['xml']);
                    $xml = base64_decode($xml_encoded);
                    // преобразуем входной xml в удобный для использования формат
                    $xml_vars = simplexml_load_string($xml);
                    //$file = '/home/s/stdex/air.tw1.ru/public_html/engine/plugins/zboard/eeeeee.txt';
                    //file_put_contents($file, strval($xml_vars), FILE_APPEND | LOCK_EX);

                    if ($xml_vars->order_id)
                    // Если поле order_id не заполнено, продолжать нет смысла.
                    {

                        $hidden_key = pluginGetVariable('zboard', 'pay2pay_hidden_key');
                        $sign = md5($hidden_key.$xml.$hidden_key);
                        $sign_encode = base64_encode($sign);

                        $a_or_id = explode("_", $xml_vars->order_id);
                        $zid = $a_or_id[1];
                        $merchant_id = $xml_vars->merchant_id;
                        $order_id = $xml_vars->order_id;
                        $amount = $xml_vars->amount;
                        $currency = $xml_vars->currency;
                        $description = $xml_vars->description;
                        $description = iconv("utf-8", "windows-1251", $description);
                        $paymode = $xml_vars->paymode;
                        $trans_id = $xml_vars->trans_id;
                        $status = $xml_vars->status;
                        $error_msg = $xml_vars->error_msg;
                        $test_mode = $xml_vars->test_mode;

                        if($sign_encode == $_REQUEST['sign']) {

                           $mysql->query('INSERT INTO '.prefix.'_zboard_pay_order (dt, zid, merchant_id, order_id, amount, currency, description, paymode, trans_id, status, error_msg, test_mode)
                                    VALUES
                                    ('.db_squote($current_time).',
                                        '.db_squote($zid).',
                                        '.db_squote($merchant_id).',
                                        '.db_squote($order_id).',
                                        '.db_squote($amount).',
                                        '.db_squote($currency).',
                                        '.db_squote($description).',
                                        '.db_squote($paymode).',
                                        '.db_squote($trans_id).',
                                        '.db_squote($status).',
                                        '.db_squote($error_msg).',
                                        '.db_squote($test_mode).'
                                    )
                                ');

                            if($status == 'success') {

                                $price_time = $mysql->record('SELECT * FROM '.prefix.'_zboard_pay_price WHERE price = '.db_squote($amount).' LIMIT 1');

                                $expired_time = $current_time + $price_time['time'] * 24 * 60 * 60;

                                $mysql->query('UPDATE '.prefix.'_zboard SET
                                    vip_added = '.db_squote($current_time).',
                                    vip_expired =  '.db_squote($expired_time).',
                                    editdate = '.db_squote($current_time).',
                                    active = 1
                                    WHERE id = '.$zid.'
                                ');
                            }

                        }
                        else {
                            $error = 'Incorrect sign';
                        }


                    }
                    else {
                        $error = 'Unknown order_id';
                    }

                    // Отвечаем серверу Pay2Pay
                    if ($error == '') {
                        $ret = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                        <result>
                        <status>yes</status>
                        <err_msg></err_msg>
                        </result>";
                    }
                    else {
                        $ret = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
                        <result>
                        <status>no</status>
                        <err_msg>$error</err_msg>
                        </result>";
                    }

                    die($ret);
                }
                break;
            case '3':
                // success_url
                redirect_zboard(link_zboard_list());
                break;
            default:
                break;
        }
    }
    elseif(!empty($zid)) {
        if($row = $mysql->record('SELECT * FROM '.prefix.'_zboard_pay_price WHERE id = '.db_squote($price_id).'LIMIT 1'))
        {
            $merchant_id = pluginGetVariable('zboard', 'pay2pay_merchant_id'); // Идентификатор магазина в Pay2Pay
            $secret_key = pluginGetVariable('zboard', 'pay2pay_secret_key'); // Секретный ключ
            $order_id =  $current_time."_".$zid; // Номер заказа
            $amount = $row['price']; // Сумма заказа
            $currency = 'RUB'; // Валюта заказа
            $desc = 'Оплата за VIP объявление, ID: '.$zid; // Описание заказа
            $desc = iconv("windows-1251", "utf-8", $desc);
            $test_mode = pluginGetVariable('zboard', 'pay2pay_test_mode'); // Тестовый режим
            // Формируем xml
            $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
             <request>
             <version>1.2</version>
             <merchant_id>$merchant_id</merchant_id>
             <language>ru</language>
             <order_id>$order_id</order_id>
             <amount>$amount</amount>
             <currency>$currency</currency>
             <description>$desc</description>
             <test_mode>$test_mode</test_mode>
             <other><![CDATA[$id]]></other>
             </request>";
            // Вычисляем подпись
            $sign = md5($secret_key.$xml.$secret_key);
            // Кодируем данные в BASE64
            $xml_encode = base64_encode($xml);
            $sign_encode = base64_encode($sign);
            echo'
            <!DOCTYPE html><html><body>
                <form id="b-site" action="https://merchant.pay2pay.com/?page=init" method="post">
                    <input type="hidden" name="xml" value="'.$xml_encode.'">
                    <input type="hidden" name="sign" value="'.$sign_encode.'">
                </form>
                <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
                <script>$("document").ready(function() {$("#b-site").submit();});</script>
            </body></html>';
            exit;
        }
        else {
            redirect_zboard(link_zboard_list());
        }
    }
    else
    {
        //var_dump($_REQUEST);
        redirect_zboard(link_zboard());
    }
    /**/

}

function vip_zboard($params)
{global $tpl, $template, $twig, $mysql, $SYSTEM_FLAGS, $config, $userROW, $lang, $CurrentHandler;
    $tpath = locatePluginTemplates(array('vip_zboard', 'no_access'), 'zboard', pluginGetVariable('zboard', 'localsource'), pluginGetVariable('zboard','localskin'));
    $xt = $twig->loadTemplate($tpath['vip_zboard'].'vip_zboard.tpl');

    $SYSTEM_FLAGS['info']['title']['group'] = $lang['zboard']['name_plugin'];
    $SYSTEM_FLAGS['info']['title']['others'] = 'Платные объявления';
    $id = isset($params['id'])?abs(intval($params['id'])):abs(intval($_REQUEST['id']));

    if(empty($id))
    {
        redirect_zboard(link_zboard());
    }

    if(isset($userROW) && !empty($userROW))
    {
        if($row = $mysql->record('SELECT * FROM '.prefix.'_zboard WHERE id = '.db_squote($id).' and author_id = \''.intval($userROW['id']).'\' LIMIT 1'))
        {

        foreach ($mysql->select('select * from '.prefix.'_zboard_pay_price') as $row2)
        {

            $entriesPrices[] = array (
                'id' => $row2['id'],
                'price' => $row2['price'],
                'time' => $row2['time'],
            );

        }

        $pay_url = link_zboard_pay();

        $tVars = array(
            'entriesPrices' => isset($entriesPrices)?$entriesPrices:'',
            'tpl_url' => home.'/templates/'.$config['theme'],
            'tpl_home' => admin_url,
            'zid' => intval($id),
            'pay_url' => $pay_url,
            'error' => $error_input,
        );

        $template['vars']['mainblock'] .= $xt->render($tVars);
        $template['vars']['pages'] = '';

        } else {
            header('HTTP/1.1 403 Forbidden');
            $SYSTEM_FLAGS['info']['title']['others'] = 'Вы не являетесь автором этого объявления';
            $xt = $twig->loadTemplate($tpath['no_access'].'no_access.tpl');

            $tVars['vars']['home'] = home;
            $template['vars']['mainblock'] .= $xt->render($tVars);
        }

    } else {
            header('HTTP/1.1 403 Forbidden');
            $SYSTEM_FLAGS['info']['title']['others'] = 'Доступ разрешен только авторизированным';
            $xt = $twig->loadTemplate($tpath['no_access'].'no_access.tpl');

            $tVars['vars']['home'] = home;
            $template['vars']['mainblock'] .= $xt->render($tVars);
    }

}



function del_zboard($params)
{global $userROW, $mysql;
    $id = isset($params['id'])?abs(intval($params['id'])):abs(intval($_REQUEST['id']));
    if(empty($id))
    {
        redirect_zboard(link_zboard_list());
    }
    if(isset($userROW) && !empty($userROW)){

        if($row = $mysql->record('SELECT * FROM '.prefix.'_zboard WHERE id = '.db_squote($id).' and author_id = \''.intval($userROW['id']).'\' LIMIT 1'))
            {
                $mysql->query('UPDATE '.prefix.'_zboard SET
                            active = \'0\'
                            WHERE id = \''.$id.'\' and author_id = \''.$userROW['id'].'\'
                ');

                foreach ($mysql->select('select * from '.prefix.'_zboard_images where zid='.db_squote($id).'') as $row2)
                {
                unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/' . $row2['filepath']);
                unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/thumb/' . $row2['filepath']);
                }
                $mysql->query("delete from ".prefix."_zboard_images where zid = ".db_squote($id)."");

                $mysql->query('delete from '.prefix.'_zboard where id = '.db_squote($id));

                $_SESSION['zboard']['info'] = 'Объявление удалено.';

                generate_entries_cnt_cache(true);
                generate_catz_cache(true);

                redirect_zboard(link_zboard_list());
            }
            else
            {
            $_SESSION['zboard']['info'] = 'Вы пытаетесь удалить не свое объявление.';
            redirect_zboard(link_zboard_list());
            }
    } else {
        $_SESSION['zboard']['info'] = 'У вас нет прав для удаления объявлений.';
        redirect_zboard(link_zboard());
    }
}

function edit_zboard($params = [])
{global $tpl, $template, $twig, $mysql, $SYSTEM_FLAGS, $config, $userROW, $lang, $CurrentHandler;
    $tpath = locatePluginTemplates(array('edit_zboard', 'no_access'), 'zboard', pluginGetVariable('zboard', 'localsource'), pluginGetVariable('zboard','localskin'));
    $xt = $twig->loadTemplate($tpath['edit_zboard'].'edit_zboard.tpl');

    $SYSTEM_FLAGS['info']['title']['group'] = $lang['zboard']['name_plugin'];
    $SYSTEM_FLAGS['info']['title']['others'] = 'Редактирование';
    $id = isset($params['id'])?abs(intval($params['id'])):abs(intval($_REQUEST['id']));

    if(empty($id))
    {
        redirect_zboard(link_zboard());
    }

    if(isset($userROW) && !empty($userROW))
    {
        if($row = $mysql->record('SELECT * FROM '.prefix.'_zboard WHERE id = '.db_squote($id).' and author_id = \''.intval($userROW['id']).'\' LIMIT 1'))
        {

        foreach (explode("|",pluginGetVariable('zboard', 'list_period')) as $line) {
            $list_period .= str_replace( array('{line}', '{activ}'), array($line, ($line==$row['announce_period']?'selected':'')), $lang['zboard']['list_period_edit']);
        }
        /*
        $options = '<option disabled>---------</option>';
        foreach ($mysql->select('SELECT id, cat_name FROM '.prefix.'_zboard_cat') as $cat)
        {
            $options .= '<option value="' . $cat['id'] . '"'.(($row['cat_id']==$cat['id'])?'selected':'').'>' . $cat['cat_name'] . '</option>';
        }
        */
            $res = mysqli_query("SELECT * FROM ".prefix."_zboard_cat ORDER BY id");
            $cats = getCats($res);
            $options = getTree($cats, $row['cat_id'], 0);


        if (isset($_REQUEST['submit']))
        {
            $SQL['editdate'] = time() + ($config['date_adjust'] * 60);

            $SQL['announce_name'] = input_filter_com(convert($_REQUEST['announce_name']));
            if(empty($SQL['announce_name']))
                $error_text[] = 'Название объявления пустое';


            $SQL['author'] = input_filter_com(convert($_REQUEST['author']));
            if(empty($SQL['author']))
                $error_text[] = 'Поле автор не заполнено';

            $SQL['announce_period'] = input_filter_com(convert($_REQUEST['announce_period']));
            if(!empty($SQL['announce_period']))
            {
                if(!in_array($SQL['announce_period'], explode("|",pluginGetVariable('zboard', 'list_period'))))
                {
                    $error_text[] = 'Поле период задано неверно '.$SQL['announce_period'];
                }

            } else {
                $error_text[] = 'Поле период не заполнено';
            }

            $SQL['cat_id'] = intval($_REQUEST['cat_id']);
            if(!empty($SQL['cat_id']))
            {
                $cat = $mysql->result('SELECT 1 FROM '.prefix.'_zboard_cat WHERE id = \'' . $SQL['cat_id'] . '\' LIMIT 1');

                if(empty($cat))
                {
                    $error_text[] = 'Такой категории не существует';
                }
            } else {
                $error_text[] = 'Вы не выбрали категорию';
            }


            $SQL['announce_description'] = str_replace(array("\r\n", "\r"), "\n",input_filter_com(convert($_REQUEST['announce_description'])));
            if(empty($SQL['announce_description']))
            {
                $error_text[] = 'Нет описания к объявлению';
            }

            $SQL['announce_contacts'] = str_replace(array("\r\n", "\r"), "\n",input_filter_com(convert($_REQUEST['announce_contacts'])));
            if(empty($SQL['announce_contacts']))
            {
                $error_text[] = 'Нет контактов к объявлению';
            }

            //$SQL['active'] = $_REQUEST['announce_activeme'];
            $SQL['active'] = 0;

            if(is_array($SQLi)){
                $vnamess = array();
                foreach ($SQLi as $k => $v) { $vnamess[] = $k.' = '.db_squote($v); }
                $mysql->query('update '.prefix.'_zboard set '.implode(', ',$vnamess).' where  id = \''.intval($id).'\'');
            }

            if(empty($error_text))
            {
                $vnames = array();
                foreach ($SQL as $k => $v) { $vnames[] = $k.' = '.db_squote($v); }
                $mysql->query('update '.prefix.'_zboard set '.implode(', ',$vnames).' where  id = \''.intval($id).'\'');

                $_SESSION['zboard']['info'] = str_replace( array('%user%'), array(input_filter_com(convert($_REQUEST['author']))), pluginGetVariable('zboard', 'info_edit'));

                generate_entries_cnt_cache(true);
                generate_catz_cache(true);

                redirect_zboard(link_zboard_list());
            }

        }

        if(!empty($error_text))
        {
            foreach($error_text as $error)
            {
                $error_input .= msg(array("type" => "error", "text" => $error));
            }
        } else {
            $error_input ='';
        }

        if($row['active'] == 1) { $checked = 'checked'; } else  { $checked = ''; }

        foreach ($mysql->select('select * from '.prefix.'_zboard_images where zid='.$id.'') as $row2)
        {

            $entriesImg[] = array (
                'home' => home,
                'del' => home.'/plugin/zboard/edit/?id='.$id.'&delimg='.$row2['pid'].'&filepath='.$row2['filepath'].'',
                'pid' => $row2['pid'],
                'filepath' => $row2['filepath'],
                'zid' => $row2['zid'],
            );

        }

        $tVars = array(
            'entriesImg' => isset($entriesImg)?$entriesImg:'',
            'options' => $options,
            'announce_activeme' => $checked,
            'announce_name' => $row['announce_name'],
            'list_period' => $list_period,
            'announce_contacts' => $row['announce_contacts'],
            'author' => $row['author'],
            'announce_description' => $row['announce_description'],
            'vip_added'				=>	$row['vip_added'],
            'vip_expired'			=>	$row['vip_expired'],
            'tpl_url' => home.'/templates/'.$config['theme'],
            'bb_tags' => zboard_bbcode(),
            'tpl_home' => admin_url,
            'id' => intval($id),
            'error' => $error_input,
        );


    if (isset($_REQUEST['delimg']) && isset($_REQUEST['filepath']))
        {
        $imgID = intval($_REQUEST['delimg']);
        $imgPath = $_REQUEST['filepath'];
        $mysql->query("delete from ".prefix."_zboard_images where pid = ".db_squote($imgID)."");
        //echo root . '/uploads/zboard/' . $imgPath;
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/' . $imgPath);
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/thumb/' . $imgPath);
        //redirect_zboard($url)
        redirect_zboard(home.'/plugin/zboard/edit/?id='.$id.'');
        }

    if (isset($_REQUEST['delme']))
        {

        foreach ($mysql->select('select * from '.prefix.'_zboard_images where zid='.db_squote($id).'') as $row2)
        {
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/' . $row2['filepath']);
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/thumb/' . $row2['filepath']);
        }
        $mysql->query("delete from ".prefix."_zboard_images where zid = ".db_squote($id)."");

        $mysql->query('delete from '.prefix.'_zboard where id = '.db_squote($id));

        redirect_zboard(link_zboard_list());
        }


        $template['vars']['mainblock'] .= $xt->render($tVars);
        $template['vars']['pages'] = '';

        } else {
            header('HTTP/1.1 403 Forbidden');
            $SYSTEM_FLAGS['info']['title']['others'] = 'Вы не являетесь автором этого объявления';
            $xt = $twig->loadTemplate($tpath['no_access'].'no_access.tpl');

            $tVars['vars']['home'] = home;
            $template['vars']['mainblock'] .= $xt->render($tVars);
        }

    } else {
            header('HTTP/1.1 403 Forbidden');
            $SYSTEM_FLAGS['info']['title']['others'] = 'Доступ разрешен только авторизированным';
            $xt = $twig->loadTemplate($tpath['no_access'].'no_access.tpl');

            $tVars['vars']['home'] = home;
            $template['vars']['mainblock'] .= $xt->render($tVars);
    }

}


function expend_zboard($params)
{global $tpl, $template, $twig, $mysql, $SYSTEM_FLAGS, $config, $userROW, $lang, $CurrentHandler;
    $tpath = locatePluginTemplates(array('edit_zboard', 'no_access'), 'zboard', pluginGetVariable('zboard', 'localsource'), pluginGetVariable('zboard','localskin'));

    $xt = $twig->loadTemplate($tpath['edit_zboard'].'edit_zboard.tpl');

    $SYSTEM_FLAGS['info']['title']['group'] = $lang['zboard']['name_plugin'];
    $SYSTEM_FLAGS['info']['title']['others'] = 'Продление';
    $id = isset($params['id'])?abs(intval($params['id'])):abs(intval($_REQUEST['id']));
    $hashcode = isset($params['hashcode'])?$params['hashcode']:$_REQUEST['hashcode'];

    if( empty($id) || empty($hashcode) || !(isset($hashcode)) )
    {
        redirect_zboard(link_zboard());
    }

        if($row = $mysql->record('SELECT * FROM '.prefix.'_zboard WHERE id = '.db_squote($id).' and expired = '.db_squote($hashcode).' LIMIT 1'))
        {

        foreach (explode("|",pluginGetVariable('zboard', 'list_period')) as $line) {
            $list_period .= str_replace( array('{line}', '{activ}'), array($line, ($line==$row['announce_period']?'selected':'')), $lang['zboard']['list_period_edit']);
        }

        $options = '<option disabled>---------</option>';
        foreach ($mysql->select('SELECT id, cat_name FROM '.prefix.'_zboard_cat') as $cat)
        {
            $options .= '<option value="' . $cat['id'] . '"'.(($row['cat_id']==$cat['id'])?'selected':'').'>' . $cat['cat_name'] . '</option>';
        }
        if (isset($_REQUEST['submit']))
        {
            $SQL['editdate'] = time() + ($config['date_adjust'] * 60);

            $SQL['announce_name'] = input_filter_com(convert($_REQUEST['announce_name']));
            if(empty($SQL['announce_name']))
                $error_text[] = 'Название объявления пустое';


            $SQL['author'] = input_filter_com(convert($_REQUEST['author']));
            if(empty($SQL['author']))
                $error_text[] = 'Поле автор не заполнено';

            $SQL['announce_period'] = input_filter_com(convert($_REQUEST['announce_period']));
            if(!empty($SQL['announce_period']))
            {
                if(!in_array($SQL['announce_period'], explode("|",pluginGetVariable('zboard', 'list_period'))))
                {
                    $error_text[] = 'Поле период задано неверно '.$SQL['announce_period'];
                }

            } else {
                $error_text[] = 'Поле период не заполнено';
            }

            $SQL['cat_id'] = intval($_REQUEST['cat_id']);
            if(!empty($SQL['cat_id']))
            {
                $cat = $mysql->result('SELECT 1 FROM '.prefix.'_zboard_cat WHERE id = \'' . $SQL['cat_id'] . '\' LIMIT 1');

                if(empty($cat))
                {
                    $error_text[] = 'Такой категории не существует';
                }
            } else {
                $error_text[] = 'Вы не выбрали категорию';
            }


            $SQL['announce_description'] = str_replace(array("\r\n", "\r"), "\n",input_filter_com(convert($_REQUEST['announce_description'])));
            if(empty($SQL['announce_description']))
            {
                $error_text[] = 'Нет описания к объявлению';
            }

            $SQL['announce_contacts'] = str_replace(array("\r\n", "\r"), "\n",input_filter_com(convert($_REQUEST['announce_contacts'])));
            if(empty($SQL['announce_contacts']))
            {
                $error_text[] = 'Нет контактов к объявлению';
            }

            //$SQL['active'] = $_REQUEST['announce_activeme'];
            $SQL['active'] = 0;
            $SQL['expired'] = '';


            if(is_array($SQLi)){
                $vnamess = array();
                foreach ($SQLi as $k => $v) { $vnamess[] = $k.' = '.db_squote($v); }
                $mysql->query('update '.prefix.'_zboard set '.implode(', ',$vnamess).' where  id = \''.intval($id).'\'');
            }

            if(empty($error_text))
            {
                $vnames = array();
                foreach ($SQL as $k => $v) { $vnames[] = $k.' = '.db_squote($v); }
                $mysql->query('update '.prefix.'_zboard set '.implode(', ',$vnames).' where  id = \''.intval($id).'\'');

                $_SESSION['zboard']['info'] = str_replace( array('%user%'), array(input_filter_com(convert($_REQUEST['author']))), pluginGetVariable('zboard', 'info_edit'));
                redirect_zboard(link_zboard());
            }

        }

        if(!empty($error_text))
        {
            foreach($error_text as $error)
            {
                $error_input .= msg(array("type" => "error", "text" => $error));
            }
        } else {
            $error_input ='';
        }

        if($row['active'] == 1) { $checked = 'checked'; } else  { $checked = ''; }

        foreach ($mysql->select('select * from '.prefix.'_zboard_images where zid='.$id.'') as $row2)
        {

            $entriesImg[] = array (
                'home' => home,
                'del' => home.'/plugin/zboard/expend/?id='.$id.'&hashcode='.$hashcode.'&delimg='.$row2['pid'].'&filepath='.$row2['filepath'].'',
                'pid' => $row2['pid'],
                'filepath' => $row2['filepath'],
                'zid' => $row2['zid'],
            );

        }


        $tVars = array(
            'entriesImg' => isset($entriesImg)?$entriesImg:'',
            'options' => $options,
            'announce_activeme' => $checked,
            'announce_name' => $row['announce_name'],
            'list_period' => $list_period,
            'announce_contacts' => $row['announce_contacts'],
            'author' => $row['author'],
            'announce_description' => $row['announce_description'],
            'tpl_url' => home.'/templates/'.$config['theme'],
            'bb_tags' => zboard_bbcode(),
            'tpl_home' => admin_url,
            'id' => intval($id),
            'error' => $error_input,
        );


    if (isset($_REQUEST['delimg']) && isset($_REQUEST['filepath']))
        {
        $imgID = intval($_REQUEST['delimg']);
        $imgPath = $_REQUEST['filepath'];
        $mysql->query("delete from ".prefix."_zboard_images where pid = ".db_squote($imgID)."");
        //echo root . '/uploads/zboard/' . $imgPath;
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/' . $imgPath);
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/thumb/' . $imgPath);
        //redirect_zboard($url)
        redirect_zboard(home.'/plugin/zboard/expend/?id='.$id.'&hashcode='.$hashcode.'');
        }

    if (isset($_REQUEST['delme']))
        {

        foreach ($mysql->select('select * from '.prefix.'_zboard_images where zid='.db_squote($id).'') as $row2)
        {
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/' . $row2['filepath']);
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/thumb/' . $row2['filepath']);
        }
        $mysql->query("delete from ".prefix."_zboard_images where zid = ".db_squote($id)."");

        $mysql->query('delete from '.prefix.'_zboard where id = '.db_squote($id));

        redirect_zboard(link_zboard());
        }


            $template['vars']['mainblock'] .= $xt->render($tVars);

        } else {
            header('HTTP/1.1 403 Forbidden');
            $xt = $twig->loadTemplate($tpath['no_access'].'no_access.tpl');
            $SYSTEM_FLAGS['info']['title']['others'] = 'Вы не являетесь автором этого объявления';
            $tVars['vars']['home'] = home;
            $template['vars']['mainblock'] .= $xt->render($tVars);
        }

}


function zboard_upload_files($files_del){
    $max_file_size = pluginGetVariable('zboard', 'max_file_size') * 1024 * 1024;
    $extensions = array_map('trim', explode(',',pluginGetVariable('zboard', 'ext_file')));

    if (isset($_FILES['plugin_files']['name']) && !empty($_FILES['plugin_files']['name'])){
        if (is_uploaded_file($_FILES['plugin_files']['tmp_name'])){
            $ext = pathinfo($_FILES['plugin_files']['name'], PATHINFO_EXTENSION);
            if(in_array($ext, $extensions)){
                if ($_FILES['plugin_files']['size'] < $max_file_size){
                    if(is_writable(files_dir . 'zboard/')){
                        $name_file = basename($_FILES['plugin_files']['name'], $ext);
                        $name_file = preg_replace("/[^\w\x7F-\xFF]/", "", $name_file);
                        $Ffile = $name_file . '.' . $ext;

                        if($files_del == $Ffile){
                            unlink(files_dir . 'zboard/'. $files_del);
                        }

                        if(file_exists(files_dir . 'zboard/' . $Ffile))
                            $error_text = 'Такой файл уже существует';
                        else
                            unlink(files_dir . 'zboard/'. $files_del);

                        if(empty($error_text)){
                            if(move_uploaded_file($_FILES['plugin_files']['tmp_name'], files_dir . 'zboard/' . $Ffile)){
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

function zboard_upload_images($images_del, $w, $h, $quality = 100){
    $max_image_size = pluginGetVariable('zboard', 'max_image_size') * 1024 * 1024;
    $extensions = array_map('trim', explode(',',pluginGetVariable('zboard', 'ext_image')));

    if (isset($_FILES['plugin_images']['name']) && !empty($_FILES['plugin_images']['name'])){
        if (is_uploaded_file($_FILES['plugin_images']['tmp_name'])){
            $ext = pathinfo($_FILES['plugin_images']['name'], PATHINFO_EXTENSION);
            if(in_array($ext, $extensions)){
                $new = date("Ymd")."_".rand(1000,9999).'.'.$ext;
                if ($_FILES['plugin_images']['size'] < $max_image_size){
                    if($size_img = getimagesize($_FILES['plugin_images']['tmp_name'])){
                        if(($size_img[0] <= pluginGetVariable('zboard', 'width')) && ($size_img[1] <= pluginGetVariable('zboard', 'height'))){
                            $dir_image = images_dir .'zboard/'.$new;
                            if(move_uploaded_file($_FILES['plugin_images']['tmp_name'], $dir_image)){
                                if(isset($images_del)){
                                    unlink(images_dir . 'zboard/thumb/'.$images_del);
                                    unlink(images_dir . 'zboard/'.$images_del);
                                }

                                switch ($size_img[2])
                                {
                                    case 1: $image_ext = 'gif';		break;
                                    case 2: $image_ext = 'jpeg';	break;
                                    case 3: $image_ext = 'png';		break;
                                    case 6: $image_ext = 'bmp';		break;
                                }

                                $dest_img = imagecreatetruecolor($w, $h);

                                switch ($size_img[2]){
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
                                        $nTColor = imagecolorallocatealpha($dest_img, 0,0,0, 127);
                                        imagefill($dest_img, 0, 0, $nTColor);
                                        imagesavealpha($dest_img, true);
                                    }
                                }

                                imagecopyresampled($dest_img, $src_img, 0, 0, 0, 0, $w, $h, $w, $h);

                                switch ($size_img[2]){
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
                            $error_text = 'Размер изображения больше чем '.pluginGetVariable('template', 'width').' на '.pluginGetVariable('template', 'height');
                        }
                    } else {
                        $error_text = 'Загруженый файл не является изображением';
                    }
                } else {
                    $error_text = 'Размер файла больше допустимого';
                }
            } else {
                $error_text = 'Недопустимое разщирение';
            }
        } else {
            $error_text = 'Изображение не загружено';
        }
    }
    return array($new, $error_text);
}

function list_zboard($params)
{
global $tpl, $template, $twig, $mysql, $SYSTEM_FLAGS, $config, $userROW, $lang, $CurrentHandler;
    $tpath = locatePluginTemplates(array('list_zboard', 'no_access'), 'zboard', pluginGetVariable('zboard', 'localsource'));
    $xt = $twig->loadTemplate($tpath['list_zboard'].'list_zboard.tpl');

    $SYSTEM_FLAGS['info']['title']['group'] = $lang['zboard']['name_plugin'];
    $SYSTEM_FLAGS['info']['title']['others'] = 'Список ваших объявлений';
    $SYSTEM_FLAGS['template.main.name'] = pluginGetVariable('zboard', 'main_template')?pluginGetVariable('zboard', 'main_template'):'main';


    $url = pluginGetVariable('zboard', 'url');

    switch($CurrentHandler['handlerParams']['value']['pluginName'])
    {
        case 'core':
            if(isset($url) && !empty($url))
            {
                return redirect_zboard(generateLink('zboard', 'list'));
            }
            break;
        case 'zboard':
            if(empty($url))
            {
                return redirect_zboard(generateLink('core', 'plugin', array('plugin' => 'zboard')));
            }
            break;
    }

    if(isset($userROW) && !empty($userROW))
    {
        if(isset($_SESSION['zboard']['info']) && !empty($_SESSION['zboard']['info']))
        {
            $info = $_SESSION['zboard']['info'];
            unset($_SESSION['zboard']['info']);
        } else {
            $info = '';
        }
        $limitCount = intval(pluginGetVariable('zboard', 'count_list'));

        $pageNo		= intval($params['page'])?intval($params['page']):intval($_REQUEST['page']);
        if ($pageNo < 1) $pageNo = 1;
        if (!isset($limitStart)) $limitStart = ($pageNo - 1)* $limitCount;

        $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_zboard WHERE active = \'1\' and author_id = \''.intval($userROW['id']).'\'');

        $countPages = ceil($count / $limitCount);

        if($countPages < $pageNo)
        return msg(array("type" => "error", "text" => "Подстраницы не существует"));

        if ($countPages > 1 && $countPages >= $pageNo){
            $paginationParams = checkLinkAvailable('zboard', '')?
                array('pluginName' => 'zboard', 'pluginHandler' => 'list', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)):
                array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'zboard', 'handler' => 'list'), 'xparams' => array(), 'paginator' => array('page', 1, false));

            $navigations = LoadVariables();
            $pages = generatePagination($pageNo, 1, $countPages, 10, $paginationParams, $navigations);
        }

        foreach ($mysql->select('SELECT *, c.id as cid, n.id as nid FROM '.prefix.'_zboard n LEFT JOIN '.prefix.'_zboard_cat c ON n.cat_id = c.id LEFT JOIN '.prefix.'_zboard_images i ON n.id = i.zid WHERE n.active = \'1\' GROUP BY n.id ORDER BY editdate DESC LIMIT '.intval($limitStart).', '.intval($limitCount)) as $row)
        {
            $fulllink = checkLinkAvailable('zboard', 'show')?
                generateLink('zboard', 'show', array('id' => $row['nid'])):
                generateLink('core', 'plugin', array('plugin' => 'zboard', 'handler' => 'show'), array('id' => $row['nid']));
            $catlink = checkLinkAvailable('zboard', '')?
                generateLink('zboard', '', array('cat' => $row['cid'])):
                generateLink('core', 'plugin', array('plugin' => 'zboard'), array('cat' => $row['cid']));

            $vip = checkLinkAvailable('zboard', 'vip')?
                generateLink('zboard', 'edit', array('id' => $row['nid'])):
                generateLink('core', 'plugin', array('plugin' => 'zboard', 'handler' => 'vip'), array('id' => $row['nid']));

            $edit = checkLinkAvailable('zboard', 'edit')?
                generateLink('zboard', 'edit', array('id' => $row['nid'])):
                generateLink('core', 'plugin', array('plugin' => 'zboard', 'handler' => 'edit'), array('id' => $row['nid']));

            $del = checkLinkAvailable('zboard', 'del')?
                generateLink('zboard', 'del', array('id' => $row['nid'])):
                generateLink('core', 'plugin', array('plugin' => 'zboard', 'handler' => 'del'), array('id' => $row['nid']));

            $tEntry[] = array (
                'nid'					=>	$row['nid'],
                'cid'					=>	$row['cid'],
                'date'					=>	$row['date'],
                'editdate'				=>	$row['editdate'],
                'views'					=>	$row['views'],
                'announce_name'			=>	$row['announce_name'],
                'author'				=>	$row['author'],
                'author_id'				=>	$row['author_id'],
                'author_email'			=>	$row['author_email'],
                'announce_period'		=>	$row['announce_period'],
                'announce_description'	=>	zboard_bbcode_p($row['announce_description']),
                'announce_contacts'		=>	$row['announce_contacts'],
                'fulllink'				=>	$fulllink,
                'catlink'				=>	$catlink,
                'cat_name'				=>	$row['cat_name'],
                'pid'					=>	$row['pid'],
                'filepath'				=>	$row['filepath'],
                'vip_added'				=>	$row['vip_added'],
                'vip_expired'			=>	$row['vip_expired'],
                'vip' => $vip,
                'edit' => $edit,
                'del' => $del,
            );

        }


        if ($limitStart)
        {
            $prev = floor($limitStart / $limitCount);
            $PageLink = checkLinkAvailable('zboard', '')?
                generatePageLink(array('pluginName' => 'zboard', 'pluginHandler' => 'list', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev):
                generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'zboard', 'handler' => 'list'), 'xparams' => array(), 'paginator' => array('page', 1, false)), $prev);

            $gvars['regx']["'\[prev-link\](.*?)\[/prev-link\]'si"] = str_replace('%page%',"$1",str_replace('%link%',$PageLink, $navigations['prevlink']));
        } else {
            $gvars['regx']["'\[prev-link\](.*?)\[/prev-link\]'si"] = "";
            $prev = 0;
        }

        if (($prev + 2 <= $countPages))
        {
            $PageLink = checkLinkAvailable('zboard', '')?
                generatePageLink(array('pluginName' => 'zboard', 'pluginHandler' => 'list', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev+2):
                generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'zboard', 'handler' => 'list'), 'xparams' => array(), 'paginator' => array('page', 1, false)), $prev+2);
            $gvars['regx']["'\[next-link\](.*?)\[/next-link\]'si"] = str_replace('%page%',"$1",str_replace('%link%',$PageLink, $navigations['nextlink']));
        } else {
            $gvars['regx']["'\[next-link\](.*?)\[/next-link\]'si"] = "";
        }


        $tVars = array(
        'entries' => isset($tEntry)?$tEntry:'',
        'info' =>	isset($info)?$info:'',
        'pages' => array(
            'true' => (isset($pages) && $pages)?1:0,
            'print' => isset($pages)?$pages:''
        ),
        'prevlink' => array(
                    'true' => !empty($limitStart)?1:0,
                    'link' => str_replace('%page%',
                                            "$1",
                                            str_replace('%link%',
                                                checkLinkAvailable('zboard', '')?
                generatePageLink(array('pluginName' => 'zboard', 'pluginHandler' => 'list', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev = floor($limitStart / $limitCount)):
                generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'zboard', 'handler' => 'list'), 'xparams' => array(), 'paginator' => array('page', 1, false)),$prev = floor($limitStart / $limitCount)),
                                                isset($navigations['prevlink'])?$navigations['prevlink']:''
                                            )
                    ),
        ),
        'nextlink' => array(
                    'true' => ($prev + 2 <= $countPages)?1:0,
                    'link' => str_replace('%page%',
                                            "$1",
                                            str_replace('%link%',
                                                checkLinkAvailable('zboard', '')?
                generatePageLink(array('pluginName' => 'zboard', 'pluginHandler' => 'list', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev+2):
                generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'zboard', 'handler' => 'list'), 'xparams' => array(), 'paginator' => array('page', 1, false)), $prev+2),
                                                isset($navigations['nextlink'])?$navigations['nextlink']:''
                                            )
                    ),
        ),

        'tpl_url' => home.'/templates/'.$config['theme'],
        );

        $template['vars']['mainblock'] .= $xt->render($tVars);
    } else {
        header('HTTP/1.1 403 Forbidden');
        $SYSTEM_FLAGS['info']['title']['others'] = 'Доступ разрешен только авторизированным';
        $xt = $twig->loadTemplate($tpath['no_access'].'no_access.tpl');

        $tVars['vars']['home'] = home;
        $template['vars']['mainblock'] .= $xt->render($tVars);
    }
}
/*
function getcat($id="") {
    global $mysql;

        $content = ($id == "no") ? "" : "<option value=\"\">"._HOMECAT."</option>";
        $result = $mysql->query('SELECT id, cat_name, parent_id FROM '.prefix.'_zboard_cat');
        if (isset($result)) {
            while (list($cid, $title, $parentid) = @mysql_fetch_array($result)) $massiv[$cid] = array($title, $parentid);
            foreach ($massiv as $key => $val) {
                $cont[$key] = $val[0];
                $flag = $val[1];
                while ($flag != "0") {
                    //$cont[$key] = $massiv[$flag][0]." / ".$cont[$key];
                    $cont[$key] = "- ".$cont[$key];
                    $flag = intval($massiv[$flag][1]);
                }
            }
            asort($cont);
            foreach ($cont as $key => $val) {
                $sel = ($id == $key) ? "selected" : "";
                $content .= "<option value=\"$key\" $sel>$val</option>";
            }
        }
        return $content;
}
*/
function zboard($params)
{
global $tpl, $template, $twig, $mysql, $SYSTEM_FLAGS, $config, $userROW, $lang, $CurrentHandler;

    //plugin_zboard_cron();
    //var_dump(getcat());
    $sort = array();
    $cat = isset($params['cat'])?$params['cat']:$_REQUEST['cat'];

    if(isset($cat) && !empty($cat))
    {
        $cat = input_filter_com($cat);
        $cat_id = ' and cat_id = '.db_squote($cat);
        $sort['cat'] = $cat;
    } else {
        //$cat = pluginGetVariable('zboard', 'cat_id');
        $cat_id = '';
    }


    $sorting = $cat_id;

    $url = pluginGetVariable('zboard', 'url');
    //var_dump($sort);
    switch($CurrentHandler['handlerParams']['value']['pluginName'])
    {
    case 'core':
            if(isset($url) && !empty($url) && empty($params['page']) && empty($_REQUEST['page']) && empty($sort))
            {
                return redirect_zboard(generateLink('zboard', ''));
            }else if(isset($url) && !empty($url) or (!empty($params['page']) or !empty($_REQUEST['page']) or !empty($sort)))
            {
                //return redirect_zboard(generatePageLink(array('pluginName' => 'zboard', 'pluginHandler' => '', 'params' => array('cat' => $sort['cat']), 'xparams' => array(), 'paginator' => array('page', 0, false)), intval($_REQUEST['page'])));
            }
            break;
    }

    if(isset($_SESSION['zboard']['info']) && !empty($_SESSION['zboard']['info']))
    {
        $info = $_SESSION['zboard']['info'];
        unset($_SESSION['zboard']['info']);
    } else {
        $info = '';
    }

    $SYSTEM_FLAGS['info']['title']['group'] = $lang['zboard']['name_plugin'];
    $SYSTEM_FLAGS['template.main.name'] = pluginGetVariable('zboard', 'main_template')?pluginGetVariable('zboard', 'main_template'):'main';
    $tpath = locatePluginTemplates(array('zboard'), 'zboard', pluginGetVariable('zboard', 'localsource'), pluginGetVariable('zboard','localskin'));
    $xt = $twig->loadTemplate($tpath['zboard'].'zboard.tpl');

    /*
    $catt = array();
    foreach ($mysql->select('SELECT cat_id, COUNT(id) as num FROM '.prefix.'_zboard WHERE active = \'1\' GROUP BY cat_id ') as $rows)
    {
        $catt[$rows['cat_id']] .= $rows['num'];
    }
    */


    foreach ($mysql->select('SELECT * FROM '.prefix.'_zboard_cat ORDER BY position ASC') as $cat_row)
    {
            if($_REQUEST['cat'] == $cat_row['id'] or $params['cat'] == $cat_row['id'])
            {
                $SYSTEM_FLAGS['meta']['description']	= ($cat_row['description'])?$cat_row['description']:pluginGetVariable('zboard', 'description');
                $SYSTEM_FLAGS['meta']['keywords']		= ($cat_row['keywords'])?$cat_row['keywords']:pluginGetVariable('zboard', 'keywords');
                $SYSTEM_FLAGS['info']['title']['others'] = str_replace( array( '{name}' ), array($cat_row['cat_name']), $lang['zboard']['sorting']);
                $SYSTEM_FLAGS['info']['title']['separator'] =  $lang['zboard']['separator'];
            } else if($cat_row['id'] == $cat)
            {
                $SYSTEM_FLAGS['info']['title']['separator'] =  $lang['zboard']['separator'];
                $SYSTEM_FLAGS['meta']['description']	= ($cat_row['description'])?$cat_row['description']:pluginGetVariable('zboard', 'description');
                $SYSTEM_FLAGS['meta']['keywords']		= ($cat_row['keywords'])?$cat_row['keywords']:pluginGetVariable('zboard', 'keywords');
                $SYSTEM_FLAGS['info']['title']['others'] = '';
            }
            else {
                $SYSTEM_FLAGS['info']['title']['separator'] =  $lang['zboard']['separator'];
                $SYSTEM_FLAGS['meta']['description']	= pluginGetVariable('zboard', 'description');
                $SYSTEM_FLAGS['meta']['keywords']		= pluginGetVariable('zboard', 'keywords');
            }

        /*
        $catlink = checkLinkAvailable('zboard', '')?
                generateLink('zboard', '', array('cat' => $cat_row['id'])):
                generateLink('core', 'plugin', array('plugin' => 'zboard'), array('cat' => $cat_row['id']));

        if($cat_row['id']==$cat)
        {
            $count = $catt[$cat_row['id']];
            //print_r ($cat_row);
        }

        $entriesCatz[] = array (
                'selected' => ($cat_row['id']==$cat)?'selected':'',
                'url' => $catlink,
                'id' => $cat_row['id'],
                'cat_name' => $cat_row['cat_name'],
                'num' => $catt[$cat_row['id']]?$catt[$cat_row['id']]:'0',
            );

        $cats_ID[$cat_row['id']][] = $cat_row;
        $cats[$cat_row['parent_id']][$cat_row['id']] =  $cat_row;
        $cats[$cat_row['parent_id']][$cat_row['id']]['url'] =  $catlink;
        $cats[$cat_row['parent_id']][$cat_row['id']]['num'] =  $catt[$cat_row['id']]?$catt[$cat_row['id']]:'0';
        */
    }
//	var_dump($cats);
//	var_dump(build_tree($cats,0));

    $limitCount = pluginGetVariable('zboard', 'count');

    $pageNo		= intval($params['page'])?intval($params['page']):intval($_REQUEST['page']);
    if ($pageNo < 1)	$pageNo = 1;
    if (!$limitStart)	$limitStart = ($pageNo - 1)* $limitCount;

    $count = $mysql->result('SELECT COUNT(id) FROM '.prefix.'_zboard WHERE active = \'1\' '.$sorting);

    if($count == 0)
        return msg(array("type" => "error", "text" => "В данной категории пока что нету объявлений"));


    $countPages = ceil($count / $limitCount);

    if($countPages < $pageNo)
        return msg(array("type" => "error", "text" => "Подстраницы не существует"));


    if ($countPages > 1 && $countPages >= $pageNo)
    {
        $paginationParams = checkLinkAvailable('zboard', '')?
            array('pluginName' => 'zboard', 'pluginHandler' => '', 'params' => $sort, 'xparams' => array(), 'paginator' => array('page', 0, false)):
            array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'zboard'), 'xparams' => $sort, 'paginator' => array('page', 1, false));

        $navigations = LoadVariables();
        $pages = generatePagination($pageNo, 1, $countPages, 10, $paginationParams, $navigations);
    }

    foreach ($mysql->select('SELECT *, c.id as cid, n.id as nid FROM '.prefix.'_zboard n LEFT JOIN '.prefix.'_zboard_cat c ON n.cat_id = c.id LEFT JOIN '.prefix.'_zboard_images i ON n.id = i.zid WHERE n.active = \'1\' '.$sorting.' ORDER BY n.vip_added DESC, n.editdate DESC LIMIT '.intval($limitStart).', '.intval($limitCount)) as $row)
    {
        $fulllink = checkLinkAvailable('zboard', 'show')?
            generateLink('zboard', 'show', array('id' => $row['nid'])):
            generateLink('core', 'plugin', array('plugin' => 'zboard', 'handler' => 'show'), array('id' => $row['nid']));
        $catlink = checkLinkAvailable('zboard', '')?
            generateLink('zboard', '', array('cat' => $row['cid'])):
            generateLink('core', 'plugin', array('plugin' => 'zboard'), array('cat' => $row['cid']));

        // $irow = $mysql->record('select * from '.prefix.'_zboard_images where zid='.$row['nid'].' LIMIT 1');

        /*
        $irow = $mysql->record('select * from '.prefix.'_zboard_images where zid='.$row['nid'].' LIMIT 1');

        foreach ($mysql->select('select * from '.prefix.'_zboard_images where zid='.$row['nid'].' LIMIT 1') as $row2)
        {
        $gvars['vars'] = array (
            'home' => home,
            'pid' => $row2['pid'],
            'filepath' => $row2['filepath'],
            'zid' => $row2['zid'],
        );

        $tpl->template('list_images', $tpath['config/list_images'].'config');
        $tpl->vars('list_images', $gvars);
        $entriesImg .= $tpl -> show('list_images');
        }
        $pvars['vars']['entriesImg'] = $entriesImg;
        */


        //var_dump($row);
        $entries[] = array (
            'announce_name' => $row['announce_name'],
            'announce_author' => $row['author'],
            'announce_author_id' => $row['author_id'],
            'announce_author_email'			=>	$row['author_email'],
            'announce_author_link' => checkLinkAvailable('uprofile', 'show')?
                                    generateLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id'])):
                                    generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('name' => $row['author'], 'id' => $row['author_id'])),
            'announce_description' => zboard_bbcode_p($row['announce_description']),
            'announce_contacts' => $row['announce_contacts'],
            'vip_added'				=>	$row['vip_added'],
            'vip_expired'			=>	$row['vip_expired'],
            'fulllink' => $fulllink,
            'date' => (empty($row['date']))?'':$row['date'],
            'editdate' => (empty($row['date']))?'':$row['date'],
            'views'		=>	$row['views'],
            'cat_name' => $row['cat_name'],
            'cid' => $row['cid'],
            'catlink' => $catlink,
            'home' => home,
            'full' => str_replace( array( '{url}', '{name}'), array($fulllink, $row['announce_name']), $lang['zboard']['fulllink']),
            'pid'					=>	$row['pid'],
            'filepath'				=>	$row['filepath'],
            'tpl_url' => home.'/templates/'.$config['theme'],
        );

    }

        if ($limitStart)
        {
            $prev = floor($limitStart / $limitCount);
            $PageLink = checkLinkAvailable('zboard', '')?
                generatePageLink(array('pluginName' => 'zboard', 'pluginHandler' => '', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev):
                generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'zboard'), 'xparams' => array(), 'paginator' => array('page', 1, false)), $prev);

            $gvars['regx']["'\[prev-link\](.*?)\[/prev-link\]'si"] = str_replace('%page%',"$1",str_replace('%link%',$PageLink, $navigations['prevlink']));
        } else {
            $gvars['regx']["'\[prev-link\](.*?)\[/prev-link\]'si"] = "";
            $prev = 0;
        }

        if (($prev + 2 <= $countPages))
        {
            $PageLink = checkLinkAvailable('zboard', '')?
                generatePageLink(array('pluginName' => 'zboard', 'pluginHandler' => '', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev+2):
                generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'zboard'), 'xparams' => array(), 'paginator' => array('page', 1, false)), $prev+2);
            $gvars['regx']["'\[next-link\](.*?)\[/next-link\]'si"] = str_replace('%page%',"$1",str_replace('%link%',$PageLink, $navigations['nextlink']));
        } else {
            $gvars['regx']["'\[next-link\](.*?)\[/next-link\]'si"] = "";
        }


        $tVars = array(
            'info' =>	isset($info)?$info:'',
    //		'entriesCatz' => isset($entriesCatz)?$entriesCatz:'',
            'entries' => isset($entries)?$entries:'',
    //		'entries_cat_tree' => build_tree($cats,0),
            'pages' => array(
            'true' => (isset($pages) && $pages)?1:0,
            'print' => isset($pages)?$pages:''
                            ),
            'prevlink' => array(
                    'true' => !empty($limitStart)?1:0,
                    'link' => str_replace('%page%',
                                            "$1",
                                            str_replace('%link%',
                                                checkLinkAvailable('zboard', '')?
                generatePageLink(array('pluginName' => 'zboard', 'pluginHandler' => '', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev = floor($limitStart / $limitCount)):
                generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'zboard'), 'xparams' => array(), 'paginator' => array('page', 1, false)), $prev = floor($limitStart / $limitCount)),
                                                isset($navigations['prevlink'])?$navigations['prevlink']:''
                                            )
                    ),
                                ),
            'nextlink' => array(
                    'true' => ($prev + 2 <= $countPages)?1:0,
                    'link' => str_replace('%page%',
                                            "$1",
                                            str_replace('%link%',
                                                checkLinkAvailable('zboard', '')?
                generatePageLink(array('pluginName' => 'zboard', 'pluginHandler' => '', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false)), $prev+2):
                generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'zboard'), 'xparams' => array(), 'paginator' => array('page', 1, false)), $prev+2),
                                                isset($navigations['nextlink'])?$navigations['nextlink']:''
                                            )
                    ),
                                ),
            'tpl_url' => home.'/templates/'.$config['theme'],
            'tpl_home' => admin_url,
        );

            $template['vars']['mainblock'] .= $xt->render($tVars);
}

function search_zboard($params)
{
global $tpl, $template, $twig, $mysql, $SYSTEM_FLAGS, $config, $userROW, $CurrentHandler, $lang;

    $url = pluginGetVariable('zboard', 'url');
    switch($CurrentHandler['handlerParams']['value']['pluginName'])
    {
        case 'core':
            if(isset($url) && !empty($url))
            {
                return redirect_zboard(generateLink('zboard', 'search'));
            }
            break;
        case 'zboard':
            if(empty($url))
            {
                return redirect_zboard(generateLink('core', 'plugin', array('plugin' => 'zboard')));
            }
            break;
    }

    $SYSTEM_FLAGS['info']['title']['group'] = $lang['zboard']['name_plugin'];
    $SYSTEM_FLAGS['info']['title']['others'] = $lang['zboard']['name_search'];
    $SYSTEM_FLAGS['template.main.name'] = pluginGetVariable('zboard', 'main_template')?pluginGetVariable('zboard', 'main_template'):'main';
    $SYSTEM_FLAGS['meta']['description']	= (pluginGetVariable('zboard', 'description'))?pluginGetVariable('zboard', 'description'):$SYSTEM_FLAGS['meta']['description'];
    $SYSTEM_FLAGS['meta']['keywords']		= (pluginGetVariable('zboard', 'keywords'))?pluginGetVariable('zboard', 'keywords'):$SYSTEM_FLAGS['meta']['keywords'];

    $tpath = locatePluginTemplates(array('search_zboard'), 'zboard', pluginGetVariable('zboard', 'localsource'), pluginGetVariable('zboard','localskin'));
    $xt = $twig->loadTemplate($tpath['search_zboard'].'search_zboard.tpl');

    if(isset($_REQUEST['submit']) && $_REQUEST['submit']){
        $keywords = secure_search_zboard($_REQUEST['keywords']);
        $cat_id = intval($_REQUEST['cat_id']);
        if(empty($cat_id))
            $cat_id = 0;

        $search_in = secure_search_zboard($_REQUEST['search_in']);
        if(empty($search_in))
            $search_in = 'all';

        $search = mb_substr($keywords, 0, 64);
         if( strlen($search) < 3 )
            $output = msg(array("type" => "error", "text" => "Слишком короткое слово"), 1, 2);

        $keywords = array();

        $get_url = $search;

        $search = str_replace(" +", " ", $search);
        $stemmer = new Lingua_Stem_Ru();

        $tmp = explode( " ", $search );

        foreach ( $tmp as $wrd )
            $keywords[] = $stemmer->stem_word($wrd);

        $string = implode( "* ", $keywords );
        $string = $string.'*';

        $text = implode('|', $keywords);

        if(isset($params['page']))
            $pageNo = isset($_REQUEST['page'])?intval($_REQUEST['page']):0;
        else
            $pageNo = isset($_REQUEST['page'])?intval($_REQUEST['page']):0;

        $limitCount = intval(pluginGetVariable('zboard', 'count_search'));

        if (($limitCount < 2)||($limitCount > 2000)) $limitCount = 2;

        if($cat_id)
            $cats_id = " AND a.`cat_id` = '{$cat_id}'";
        else
            $cats_id = NULL;

        switch($search_in){
            case 'all':$sql_count = "SELECT COUNT(*) FROM ".prefix."_zboard AS a
                                    WHERE MATCH (a.announce_name, a.announce_description) AGAINST ('{$string}' IN BOOLEAN MODE){$cats_id} and a.active = 1 ";
                                    break;
            case 'text':$sql_count = "SELECT COUNT(*) FROM ".prefix."_zboard AS a
                                    WHERE MATCH (a.announce_description) AGAINST ('{$string}' IN BOOLEAN MODE){$cats_id} and a.active = 1 ";
                                    break;
            case 'title':$sql_count = "SELECT COUNT(*) FROM ".prefix."_zboard AS a
                                    WHERE MATCH (a.announce_name) AGAINST ('{$string}' IN BOOLEAN MODE){$cats_id} and a.active = 1 ";
                                    break;
        }

        $count = $mysql->result($sql_count);

        $countPages = ceil($count / $limitCount);
        if($countPages < $pageNo)
            $output = msg(array("type" => "error", "text" => "Подстраницы не существует"), 1, 2);

        if ($pageNo < 1) $pageNo = 1;
        if (!isset($limitStart)) $limitStart = ($pageNo - 1)* $limitCount;

        if ($countPages > 1 && $countPages >= $pageNo){

            $paginationParams = checkLinkAvailable('zboard', 'search')?
                array('pluginName' => 'zboard', 'pluginHandler' => 'search', 'params' => array('keywords' => $get_url, 'cat_id' => $cat_id, 'search_in' => $search_in, 'submit'=> 'Отправить'), 'xparams' => array('keywords' => $get_url, 'cat_id' => $cat_id, 'search_in' => $search_in, 'submit'=> 'Отправить'), 'paginator' => array('page', 1, false)):
                array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'zboard', 'handler' => 'search'), 'xparams' => array('keywords' => $get_url, 'cat_id' => $cat_id, 'search_in' => $search_in, 'submit'=> 'Отправить'), 'paginator' => array('page', 1, false));

            $navigations = LoadVariables();
            $pages = generatePagination($pageNo, 1, $countPages, 10, $paginationParams, $navigations);
        }

        switch($search_in){
            case 'all': $sql_two = 'SELECT *, a.id as aid, b.id as bid FROM '.prefix.'_zboard a LEFT JOIN '.prefix.'_zboard_cat b ON a.cat_id = b.id LEFT JOIN '.prefix.'_zboard_images c ON a.id = c.zid WHERE MATCH (a.announce_name, a.announce_description) AGAINST (\''.$string.'\' IN BOOLEAN MODE)'.$cats_id.' and a.active = \'1\' GROUP BY a.id ORDER BY MATCH (a.announce_name, a.announce_description) AGAINST (\''.$string.'\' IN BOOLEAN MODE) DESC LIMIT '.$limitStart.', '.$limitCount; break;
            case 'text':$sql_two = 'SELECT *, a.id as aid, b.id as bid FROM '.prefix.'_zboard a LEFT JOIN '.prefix.'_zboard_cat b ON a.cat_id = b.id LEFT JOIN '.prefix.'_zboard_images c ON a.id = c.zid WHERE MATCH (a.announce_description) AGAINST (\''.$string.'\' IN BOOLEAN MODE)'.$cats_id.' and a.active = \'1\' GROUP BY a.id ORDER BY MATCH (a.announce_description) AGAINST (\''.$string.'\' IN BOOLEAN MODE) DESC LIMIT '.$limitStart.', '.$limitCount; break;
            case 'title':$sql_two = 'SELECT *, a.id as aid, b.id as bid FROM '.prefix.'_zboard a LEFT JOIN '.prefix.'_zboard_cat b ON a.cat_id = b.id LEFT JOIN '.prefix.'_zboard_images c ON a.id = c.zid WHERE MATCH (a.announce_name) AGAINST (\''.$string.'\' IN BOOLEAN MODE)'.$cats_id.' and a.active = \'1\' GROUP BY a.id ORDER BY MATCH (a.announce_name) AGAINST (\''.$string.'\' IN BOOLEAN MODE) DESC LIMIT '.$limitStart.', '.$limitCount; break;
        }

        foreach ($mysql->select($sql_two) as $row_two){
            /* print '<pre>';
            print_r ($row_two);
            print '</pre>'; */

            $fulllink = checkLinkAvailable('zboard', 'show')?
                generateLink('zboard', 'show', array('id' => $row_two['aid'])):
                generateLink('core', 'plugin', array('plugin' => 'zboard', 'handler' => 'show'), array('id' => $row_two['aid']));
            $catlink = checkLinkAvailable('zboard', '')?
                generateLink('zboard', '', array('cat' => $row_two['bid'])):
                generateLink('core', 'plugin', array('plugin' => 'zboard'), array('cat' => $row_two['bid']));


            $tEntry[] = array (
                'aid'					=>	$row_two['aid'],
                'bid'					=>	$row_two['bid'],
                'date'					=>	$row_two['date'],
                'editdate'				=>	$row_two['editdate'],
                'views'					=>	$row_two['views'],
                'announce_name'			=>	$row_two['announce_name'],
                'author'				=>	$row_two['author'],
                'author_id'				=>	$row_two['author_id'],
                'author_email'			=>	$row_two['author_email'],
                'announce_period'		=>	$row_two['announce_period'],
                'announce_description'	=>	preg_replace("/\b(".$text.")(.*?)\b/i", "<span style='color:red; font-weight:bold'>\\0</span>", zboard_bbcode_p($row_two['announce_description'])),
                'announce_contacts'		=>	$row_two['announce_contacts'],
                'vip_added'				=>	$row_two['vip_added'],
                'vip_expired'			=>	$row_two['vip_expired'],
                'fulllink'				=>	$fulllink,
                'catlink'				=>	$catlink,
                'cat_name'				=>	$row_two['cat_name'],
                'pid'					=>	$row_two['pid'],
                'filepath'				=>	$row_two['filepath'],
            );

        }

        if( empty($row_two) )
            $output = msg(array("type" => "error", "text" => "По вашему запросу <b>".$get_url."</b> ничего не найдено"), 1, 2);
    }else{
            $res = mysqli_query("SELECT * FROM ".prefix."_zboard_cat ORDER BY id");
            $cats = getCats($res);
            $options = getTree($cats, $row['cat_id'], 0);

        //	$tVars['options'] = $options;

        /*foreach ($mysql->select('SELECT `id`, `title` FROM `'.prefix.'_forum_forums` ORDER BY `position`') as $row){
            $tEntry[] = array (
                'forum_id' => $row['id'],
                'forum_name' => $row['title'],
            );
        }*/

    }

    $tVars = array(
        'entries' => isset($tEntry)?$tEntry:'',
        'options' => isset($options)?$options:'',
        'output'	  =>  $output,
        'get_url'	  =>  $get_url,
        'submit' => (isset($_REQUEST['submit']) && $_REQUEST['submit'])?0:1,
        'pages' => array(
            'true' => (isset($pages) && $pages)?1:0,
            'print' => isset($pages)?$pages:''
        ),
        'prevlink' => array(
                    'true' => !empty($limitStart)?1:0,
                    'link' => str_replace('%page%',
                                            "$1",
                                            str_replace('%link%',
                                                checkLinkAvailable('zboard', 'search')?
                                                generatePageLink(array('pluginName' => 'zboard', 'pluginHandler' => 'search', 'params' => array('keywords' => $get_url?$get_url:'', 'cat_id' => $cat_id, 'search_in' => $search_in, 'submit'=> 'Отправить'), 'xparams' => array('keywords' => $get_url?$get_url:'', 'cat_id' => $cat_id, 'search_in' => $search_in, 'submit'=> 'Отправить'), 'paginator' => array('page', 1, false)), $prev = floor($limitStart / $limitCount)):
                                                generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'zboard', 'handler' => 'search'), 'xparams' => array('keywords' => isset($get_url)?$get_url:'', 'cat_id' => isset($cat_id)?$cat_id:'', 'search_in' => isset($search_in)?$search_in:'', 'submit'=> 'Отправить'), 'paginator' => array('page', 1, false)),
                                                    $prev = floor((isset($limitStart) && $limitStart)?$limitStart:10 / (isset($limitCount) && $limitCount)?$limitCount:'5')),
                                                isset($navigations['prevlink'])?$navigations['prevlink']:''
                                            )
                    ),
        ),
        'nextlink' => array(
                    'true' => ($prev + 2 <= $countPages)?1:0,
                    'link' => str_replace('%page%',
                                            "$1",
                                            str_replace('%link%',
                                                checkLinkAvailable('zboard', 'search')?
                                                generatePageLink(array('pluginName' => 'zboard', 'pluginHandler' => 'search', 'params' => array('keywords' => $get_url, 'cat_id' => $cat_id, 'search_in' => $search_in, 'submit'=> 'Отправить'), 'xparams' => array('keywords' => $get_url?$get_url:'', 'cat_id' => $cat_id, 'search_in' => $search_in, 'submit'=> 'Отправить'), 'paginator' => array('page', 1, false)), $prev+2):
                                                generatePageLink(array('pluginName' => 'core', 'pluginHandler' => 'plugin', 'params' => array('plugin' => 'zboard', 'handler' => 'search'), 'xparams' => array('keywords' => $get_url, 'cat_id' => $cat_id, 'search_in' => $search_in, 'submit'=> 'Отправить'), 'paginator' => array('page', 1, false)), $prev+2),
                                                isset($navigations['nextlink'])?$navigations['nextlink']:''
                                            )
                    ),
        ),
    );

    //$output = $xt->render($tVars);
    $template['vars']['mainblock'] .= $xt->render($tVars);

}


function show_zboard($params)
{
global $tpl, $template, $twig, $mysql, $SYSTEM_FLAGS, $config, $userROW, $CurrentHandler, $lang;
    $id = isset($params['id'])?abs(intval($params['id'])):abs(intval($_REQUEST['id']));
//	$name = preg_match('/^[a-zA-Z0-9_\xC0-\xD6\xD8-\xF6]+$/', $params['name'])?input_filter_com(convert($params['name'])):'';

    $url = pluginGetVariable('zboard', 'url');
    switch($CurrentHandler['handlerParams']['value']['pluginName'])
    {
        case 'core':
            if(isset($url) && !empty($url))
            {
                return redirect_zboard(generateLink('zboard', 'show', array('id' => $id)));
            }
            break;
        case 'zboard':
            if(empty($url))
            {
                return redirect_zboard(generateLink('core', 'plugin', array('plugin' => 'zboard', 'handler' => 'show'), array('id' => $id)));
            }
            break;
    }

    if( !empty($id) )
        $sql = 'n.id = '.db_squote($id).'';
    else
        redirect_zboard(link_zboard());

    $SYSTEM_FLAGS['template.main.name'] = pluginGetVariable('zboard', 'main_template')?pluginGetVariable('zboard', 'main_template'):'main';

    $tpath = locatePluginTemplates(array('show_zboard'), 'zboard', pluginGetVariable('zboard', 'localsource'), pluginGetVariable('zboard','localskin'));
    $xt = $twig->loadTemplate($tpath['show_zboard'].'show_zboard.tpl');


    $row = $mysql->record('SELECT *, c.id as cid, n.id as nid FROM '.prefix.'_zboard n LEFT OUTER JOIN '.prefix.'_zboard_cat c ON n.cat_id = c.id  WHERE '.$sql.' ORDER BY date DESC LIMIT 1');


        foreach ($mysql->select('select * from '.prefix.'_zboard_images where zid='.$row['nid'].' ') as $row2)
        {

            $entriesImg[] = array (
                'home' => home,
                'pid' => $row2['pid'],
                'filepath' => $row2['filepath'],
                'zid' => $row2['zid'],
            );
        }

    $SYSTEM_FLAGS['info']['title']['others'] = $row['announce_name'];
    $SYSTEM_FLAGS['info']['title']['group'] = $lang['zboard']['name_plugin'];
    $SYSTEM_FLAGS['meta']['description']	= ($row['description'])?$row['description']:pluginGetVariable('zboard', 'description');
    $SYSTEM_FLAGS['meta']['keywords']		= ($row['keywords'])?$row['keywords']:pluginGetVariable('zboard', 'keywords');

    if(isset($row) && !empty($row))
    {

        $cmode = intval(pluginGetVariable('zboard', 'views_count'));
        if ($cmode > 1) {
            // Delayed update of counters
            $mysql->query("insert into ".prefix."_zboard_view (id, cnt) values (".db_squote($row['nid']).", 1) on duplicate key update cnt = cnt + 1");
        } else if ($cmode > 0) {
            $mysql->query("update ".prefix."_zboard set views=views+1 where id = ".db_squote($row['nid']));
        }

        $fulllink = checkLinkAvailable('zboard', 'show')?
            generateLink('zboard', 'show', array('id' => $row['nid'])):
            generateLink('core', 'plugin', array('plugin' => 'zboard', 'handler' => 'show'), array('id' => $row['nid']));

        $catlink = checkLinkAvailable('zboard', '')?
            generateLink('zboard', '', array('cat' => $row['cid'])):
            generateLink('core', 'plugin', array('plugin' => 'zboard'), array('cat' => $row['cid']));

        $tVars = array (
            'entriesImg' => isset($entriesImg)?$entriesImg:'',
            'announce_name' => $row['announce_name'],
            'announce_author' => $row['author'],
            'announce_author_id' => $row['author_id'],
            'announce_author_link' => checkLinkAvailable('uprofile', 'show')?
                                    generateLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id'])):
                                    generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('name' => $row['author'], 'id' => $row['author_id'])),
            'announce_description' => zboard_bbcode_p($row['announce_description']),
            'announce_contacts' => $row['announce_contacts'],
            'vip_added'				=>	$row['vip_added'],
            'vip_expired'			=>	$row['vip_expired'],
            'fulllink' => $fulllink,
            'date' => (empty($row['date']))?'':$row['date'],
            'editdate' => (empty($row['editdate']))?'':$row['editdate'],
            'cat_name' => $row['cat_name'],
            'cid' => $row['cid'],
            'catlink' => $catlink,
            'views' => $row['views']+1,
            'home' => home,
            'full' => str_replace( array( '{url}', '{name}'), array($fulllink, $row['announce_name']), $lang['zboard']['fulllink']),
            'tpl_url' => home.'/templates/'.$config['theme'],
        );

        $template['vars']['mainblock'] .= $xt->render($tVars);
    } else {
        error404();
    }
}

function build_tree($cats,$parent_id,$only_parent = false){
    if(is_array($cats) and isset($cats[$parent_id])){
        $tree = '<ul>';
        if($only_parent==false){
            foreach($cats[$parent_id] as $cat){
                $tree .= '<li><a href="'.$cat['url'].'">'.$cat['cat_name'].'</a> ('.$cat['num'].')';
                $tree .=  build_tree($cats,$cat['id']);
                $tree .= '</li>';
            }
        }elseif(is_numeric($only_parent)){
            $cat = $cats[$parent_id][$only_parent];
            $tree .= '<li>'.$cat['cat_name'].' #'.$cat['id'];
            $tree .=  build_tree($cats,$cat['id']);
            $tree .= '</li>';
        }
        $tree .= '</ul>';
    }
    else return null;
    return $tree;
}

function getCats($res){

    $levels = array();
    $tree = array();
    $cur = array();

    while($rows = mysqli_fetch_assoc($res)){

        $cur = &$levels[$rows['id']];
        $cur['parent_id'] = $rows['parent_id'];
        $cur['cat_name'] = $rows['cat_name'];

        if($rows['parent_id'] == 0){
            $tree[$rows['id']] = &$cur;
        }

        else{
            $levels[$rows['parent_id']]['children'][$rows['id']] = &$cur;
        }
    }
    return $tree;
}


function getTree($arr, $flg, $l){
    $flg;
    $out = '';
    $ft = '&#8212; ';
    foreach($arr as $k=>$v){

    if($k==$flg) { $out .= '<option value="'.$k.'" selected>'.str_repeat($ft, $l).$v['cat_name'].'</option>'; }
    else { $out .= '<option value="'.$k.'">'.str_repeat($ft, $l).$v['cat_name'].'</option>'; }
        if(!empty($v['children'])){
            //$l = $l + 1;
            $out .= getTree($v['children'], $flg, $l + 1);
            //$l = $l - 1;
        }
    }
    return $out;
}


function send_zboard()
{
global $tpl, $template, $twig, $SYSTEM_FLAGS, $config, $userROW, $mysql, $lang, $userROW, $CurrentHandler, $parse;

    $url = pluginGetVariable('zboard', 'url');

    if (pluginGetVariable('zboard','use_recaptcha'))
    {
        require_once(root."/plugins/zboard/lib/recaptchalib.php");
    }
    $publickey = pluginGetVariable('zboard','public_key');
    $privatekey = pluginGetVariable('zboard','private_key');

    switch($CurrentHandler['handlerParams']['value']['pluginName'])
    {
        case 'core':
            if(isset($url) && !empty($url))
            {
                return redirect_zboard(generateLink('zboard', 'send'));
            }
            break;
        case 'zboard':
            if(empty($url))
            {
                return redirect_zboard(generateLink('core', 'plugin', array('plugin' => 'zboard')));
            }
            break;
    }

    $tpath = locatePluginTemplates(array('send_zboard', 'no_access'), 'zboard', pluginGetVariable('zboard', 'localsource'), pluginGetVariable('zboard','localskin'));
    $xt = $twig->loadTemplate($tpath['send_zboard'].'send_zboard.tpl');

    $SYSTEM_FLAGS['info']['title']['group'] = $lang['zboard']['name_plugin'];
    $SYSTEM_FLAGS['info']['title']['others'] = $lang['zboard']['name_send'];
    $SYSTEM_FLAGS['template.main.name'] = pluginGetVariable('zboard', 'main_template')?pluginGetVariable('zboard', 'main_template'):'main';
    $SYSTEM_FLAGS['meta']['description']	= (pluginGetVariable('zboard', 'description'))?pluginGetVariable('zboard', 'description'):$SYSTEM_FLAGS['meta']['description'];
    $SYSTEM_FLAGS['meta']['keywords']		= (pluginGetVariable('zboard', 'keywords'))?pluginGetVariable('zboard', 'keywords'):$SYSTEM_FLAGS['meta']['keywords'];

    $sid = $mysql->lastid('zboard')+1;
    //var_dump($sid);

    foreach (explode("|",pluginGetVariable('zboard', 'list_period')) as $line) {
        $list_period .= str_replace( array('{line}'), array($line), $lang['zboard']['list_period']);
    }

    if((isset($userROW) && !empty($userROW)) || intval(pluginGetVariable('zboard', 'send_guest')))
    {
        $date = time() + ($config['date_adjust'] * 60);
        /*
        $options = '<option disabled>---------</option>';
        foreach ($mysql->select('SELECT id, cat_name FROM '.prefix.'_zboard_cat') as $row)
        {
            $options .= '<option value="' . $row['id'] . '"'.(($_REQUEST['cat_id']==$row['id'])?'selected':'').'>' . $row['cat_name'] . '</option>';
        }
        */
            $res = mysqli_query("SELECT * FROM ".prefix."_zboard_cat ORDER BY id");
            $cats = getCats($res);
            $options = getTree($cats, $row['cat_id'], 0);


        $error_text = array();

        if (isset($_REQUEST['submit']))
        {
            $announce_name = input_filter_com(convert($_REQUEST['announce_name']));
            if(empty($announce_name)) $error_text[] = 'Заголовок объявления не заполнен';

            $author = input_filter_com(convert($_REQUEST['author']));
            if(empty($author)) $error_text[] = 'Поле автор не заполнено';

            if( isset($userROW) && !empty($userROW) )
            {
                $email = $userROW['mail'];
            }
            elseif (empty($userROW) && !empty($_REQUEST['author_email']) )
            {
                $email = secure_html($_REQUEST['author_email']);
                if ( !(filter_var($email, FILTER_VALIDATE_EMAIL)) ) {
                    $error_text[] = 'В поле Email введен неправильный email';
                }
            }
            else
            {
                $error_text[] = 'Поле Email не заполнено';
            }

            $announce_description = str_replace(array("\r\n", "\r"), "\n",input_filter_com(convert($_REQUEST['announce_description'])));
            if(empty($announce_description))
            {
                $error_text[] = 'Поле с объявлением не заполнено';
            }

            $announce_contacts = str_replace(array("\r\n", "\r"), "\n",input_filter_com(convert($_REQUEST['announce_contacts'])));
            if(empty($announce_contacts))
            {
                $error_text[] = 'Поле с контактами не заполнено';
            }

            $find_url_msg = $announce_name.' '.$author.' '.$announce_description;
            preg_match_all("@([\d\pL]([\d\-\pL]*[\d\pL]){2,62}\.)+\pL{2,4}@", $find_url_msg, $find_url);
            //preg_match_all("@(?:(?:https?|ftp|telnet)://(?:[а-яА-ЯёЁa-zA-Z0-9_-]{1,32}(?::[а-яА-ЯёЁa-zA-Z0-9_-]{1,32})?@)?)?(?:(?:[а-яА-ЯёЁa-zA-Z0-9_-]{1,128}\.)+(?:ru|su|рф|com|net|org|mil|edu|arpa|gov|biz|info|aero|inc|name|[a-z]{2})|(?!0)(?:(?!0[^.]|255)[0-9]{1,3}\.){3}(?!0|255)[0-9]{1,3})(?:/[а-яА-ЯёЁa-zA-Z0-9_.,_@%&?+=\~/-]*)?(?:#[^ '\"&]*)?@", $find_url_msg, $find_url);
            //preg_match_all("@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@", $find_url_msg, $find_url);
            if( $find_url[0] ) { $error_text[] = "В полях сообщения нельзя использовать ссылки!"; }

            $announce_period = input_filter_com(convert($_REQUEST['announce_period']));
            if(!empty($announce_period))
            {
                if(!in_array($announce_period, explode("|",pluginGetVariable('zboard', 'list_period'))))
                {
                    $error_text[] = 'Такого слова нет '.$announce_period;
                }

            } else {
                $error_text[] = 'Не указан период';
            }

            if (pluginGetVariable('zboard','use_recaptcha'))
            {

             $resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

                if (!$resp->is_valid) {
               // What happens when the CAPTCHA was entered incorrectly
               $error_text[] = "Проверочный код введен неправильно.";
                }

            }

            $cat_id = intval($_REQUEST['cat_id']);
            if(!empty($cat_id))
            {
                $cat = $mysql->result('SELECT 1 FROM '.prefix.'_zboard_cat WHERE id = \'' . $cat_id . '\' LIMIT 1');

                if(empty($cat))
                {
                    $error_text[] = 'Такой категории не существует';
                }
            } else {
                $error_text[] = 'Вы не выбрали категорию';
            }

            if(empty($error_text))
            {

                $qid = $mysql->lastid('zboard')+1;

                if($sid == $qid) {
                $mysql->query('INSERT INTO '.prefix.'_zboard (date, editdate, announce_name, author, author_id, author_email, announce_period, announce_description, announce_contacts, cat_id, active)
                    VALUES
                    (	'.intval($date).',
                        '.intval($date).',
                        '.db_squote($announce_name).',
                        '.db_squote($author).',
                        '.db_squote($userROW['id']).',
                        '.db_squote($email).',
                        '.db_squote($announce_period).',
                        '.db_squote($announce_description).',
                        '.db_squote($announce_contacts).',
                        '.db_squote($cat_id).',
                        \'0\'
                    )
                ');

                $notice_mail = pluginGetVariable('zboard', 'notice_mail');

                if(isset($notice_mail) && !empty($notice_mail)){

                    if(empty($userROW)) {
                        $body = str_replace( array('%announce_name%', '%author%', '%announce_period%', '%announce_description%', '%announce_contacts%', '%date%'),
                                             array( $announce_name, $author, $announce_period, $announce_description, $announce_contacts, date('j.m.Y - H:i',$date)),
                                pluginGetVariable('zboard', 'template_mail'));
                        foreach ($mysql->select('select * from '.prefix.'_users WHERE status = 1') as $row)
                        {
                            zzMail($row['mail'], 'Добавлено новое объявление, требующее проверки и активации', $body, '', false, 'text/html');
                        }
                    }

                }
                sleep(5);

                $_SESSION['zboard']['info'] = str_replace( array('%user%'), array($author), pluginGetVariable('zboard', 'info_send'));

                redirect_zboard(link_zboard());
                }
                else
                {
        sleep(5);
        foreach ($mysql->select('select * from '.prefix.'_zboard_images where zid='.$sid.'') as $row2)
        {
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/' . $row2['filepath']);
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/thumb/' . $row2['filepath']);
        }
        $mysql->query("delete from ".prefix."_zboard_images where zid = ".$sid."");

        redirect_zboard(link_zboard_send());
                }

            } else {
        sleep(5);
        foreach ($mysql->select('select * from '.prefix.'_zboard_images where zid='.$sid.'') as $row2)
        {
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/' . $row2['filepath']);
        unlink($_SERVER['DOCUMENT_ROOT'] . '/uploads/zboard/thumb/' . $row2['filepath']);
        }
        $mysql->query("delete from ".prefix."_zboard_images where zid = ".$sid."");

            }
        }

        if(!empty($error_text))
        {

            foreach($error_text as $error)
            {
                $error_input .= msg(array("type" => "error", "text" => $error), 1, 2);
            }
        } else {
            $error_input ='';
        }
        //var_dump($error_input);

        $tVars = array(
            'options' => $options,
            'announce_name' => $announce_name,
            'list_period' => $list_period,
            'announce_contacts' => $announce_contacts,
            'author' => $author,
            'author_email' => $email,
            'announce_description' => $announce_description,
            'tpl_url' => home.'/templates/'.$config['theme'],
            'bb_tags' => zboard_bbcode(),
            'tpl_home' => admin_url,
            'ext_image' => pluginGetVariable('zboard', 'ext_image'),
            'id' => intval($sid),
            'error' => $error_input,
            'use_recaptcha' => pluginGetVariable('zboard','use_recaptcha')
        );

        if (pluginGetVariable('zboard','use_recaptcha'))
        {
            $tVars['captcha'] = recaptcha_get_html($publickey);
        }

/*
        if( !empty($userROW['mail']) )
        {
            $tVars['regx']["'\[author_email\](.*?)\[/author_email\]'si"] = '';
        } else {
            $tVars['regx']["'\[author_email\](.*?)\[/author_email\]'si"] = '\\1';
        }

            $tvars['regx']["'\[captcha\](.*?)\[/captcha\]'si"] = '';
            if (pluginGetVariable('zboard','use_recaptcha'))
            {
            $tvars['regx']["'\[captcha\](.*?)\[/captcha\]'si"] = '$1';
            $tvars['vars']['captcha'] = recaptcha_get_html($publickey);
            }
*/

        $template['vars']['mainblock'] .= $xt->render($tVars);
    } else {
        header('HTTP/1.1 403 Forbidden');
        $SYSTEM_FLAGS['info']['title']['others'] = 'Доступ разрешен только авторизированным';
        $xt = $twig->loadTemplate($tpath['no_access'].'no_access.tpl');

        $tVars['vars']['home'] = home;
        $template['vars']['mainblock'] .= $xt->render($tVars);
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

function link_zboard_pay()
{
    $zboardURL = checkLinkAvailable('zboard', 'pay')?
                    generateLink('zboard', 'pay'):
                    generateLink('core', 'plugin', array('plugin' => 'zboard', 'handler' => 'pay'));

    return $zboardURL;
}

function link_zboard()
{
    $zboardURL = checkLinkAvailable('zboard', '')?
                    generateLink('zboard', ''):
                    generateLink('core', 'plugin', array('plugin' => 'zboard'));

    return $zboardURL;
}

function link_zboard_send()
{
    $zboardURL = checkLinkAvailable('zboard', 'send')?
                    generateLink('zboard', 'send'):
                    generateLink('core', 'plugin', array('plugin' => 'zboard', 'handler' => 'send'));

    return $zboardURL;
}

function link_zboard_list()
{
    $zboardURL = checkLinkAvailable('zboard', 'list')?
                    generateLink('zboard', 'list'):
                    generateLink('core', 'plugin', array('plugin' => 'zboard', 'handler' => 'list'));

    return $zboardURL;
}

function LoadVariables()
{
    $tpath = locatePluginTemplates(array(':'), 'zboard', pluginGetVariable('zboard', 'localsource'));
    return parse_ini_file($tpath[':'].'/variables.ini', true);
    //return parse_ini_file(extras_dir.'/zboard/tpl/variables.ini', true);
}

function redirect_zboard($url)
{
    if (headers_sent()) {
        echo "<script>document.location.href='{$url}';</script>\n";
        exit;
    } else {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: {$url}");
        exit;
    }
}

function zboard_bbcode()
{global $tpl, $twig;
    $tpath = locatePluginTemplates(array('bb_tags'), 'zboard', pluginGetVariable('zboard', 'localsource'), pluginGetVariable('zboard','localskin'));
    $xt = $twig->loadTemplate($tpath['bb_tags'].'bb_tags.tpl');
    $tVars = [];
    return $xt->render($tVars);
}

function zboard_bbcode_p($text, $replace = true)
{
    $bb = array(
                '[b]'				=>	'[/b]',
                '[i]'				=>	'[/i]',
                '[s]'				=>	'[/s]',
                '[u]'				=>	'[/u]',
    );

    $tag = array(
                '<b>'				=>	'</b>',
                '<i>'				=>	'</i>',
                '<s>'				=>	'</s>',
                '<u>'				=>	'</u>',
    );

    $bb_open = array_keys($bb);
    $bb_close = array_values($bb);
    $tag_open = array_keys($tag);
    $tag_close = array_values($tag);

    $text = str_replace(array("\r\n", "\r"), "\n", $text);

    if($replace)
    {
        $open_cnt = array();

        $text = split_text($text, 200);

        $text = preg_replace_callback('#\[url=http(s*)://([^\] ]+?)\](.+?)\[/url\]#si', 'UrlLink1', $text);
        $text = preg_replace_callback('#\[url\]http(s*)://(.+?)\[/url\]#si', 'UrlLink2', $text);
        $text = preg_replace_callback('#\[img\]http://([^\] \?]+?)\[/img\]#si', 'ImgLink', $text);

        $text = str_ireplace($bb_open, $tag_open, $text);
        $text = str_ireplace($bb_close, $tag_close, $text);
        $text = str_ireplace($smile_open, $smile_close, $text);



        $text = str_replace("\t", "    ", $text);
        $text = str_replace('  ', '&nbsp;&nbsp;', $text);
        $text = nl2br($text);
    } else {
        $text = str_replace($bb_open, '', $text);
        $text = str_replace($bb_close, '', $text);
        $text = str_replace($smile_open, '', $text);
    }

    return descript($text);
}

function split_text($text, $width = 90, $break = "\n")
{
    return preg_replace('#([^\s]{'. $width .'})#s', '$1'. $break , $text);
}

function UrlLink1($match)
{
    $match[2] = str_replace("\n", "", $match[2]);
    return '<a href="http'. descript($match[1]) .'://'. descript($match[2])
    . '" target="_blanck" >'. descript($match[3]) .'</a>';
}

function UrlLink2($match)
{
    $match[2] = str_replace("\n", "", $match[2]);
    return '<a href="http'. descript($match[1]) .'://'. descript($match[2])
    . '" target="_blanck" >'. descript($match[2]) .'</a>';
}

function ImgLink($match)
{
    $match[1] = str_replace("\n", "", $match[1]);
    return '<img src="http://'. descript($match[1]) .'" border="0" />';
}

function descript($text, $striptags = true) {
    $search = array("40","41","58","65","66","67","68","69","70",
        "71","72","73","74","75","76","77","78","79","80","81",
        "82","83","84","85","86","87","88","89","90","97","98",
        "99","100","101","102","103","104","105","106","107",
        "108","109","110","111","112","113","114","115","116",
        "117","118","119","120","121","122"
        );
    $replace = array("(",")",":","a","b","c","d","e","f","g","h",
        "i","j","k","l","m","n","o","p","q","r","s","t","u",
        "v","w","x","y","z","a","b","c","d","e","f","g","h",
        "i","j","k","l","m","n","o","p","q","r","s","t","u",
        "v","w","x","y","z"
        );
    $entities = count($search);
    for ($i=0; $i < $entities; $i++) {
        $text = preg_replace("#(&\#)(0*".$search[$i]."+);*#si", $replace[$i], $text);
    }
    $text = preg_replace('#(&\#x)([0-9A-F]+);*#si', "", $text);
    $text = preg_replace('#(<[^>]+[/\"\'\s])(onmouseover|onmousedown|onmouseup|onmouseout|onmousemove|onclick|ondblclick|onfocus|onload|xmlns)[^>]*>#iU', ">", $text);
    $text = preg_replace('#([a-z]*)=([\`\'\"]*)script:#iU', '$1=$2nojscript...', $text);
    $text = preg_replace('#([a-z]*)=([\`\'\"]*)javascript:#iU', '$1=$2nojavascript...', $text);
    $text = preg_replace('#([a-z]*)=([\'\"]*)vbscript:#iU', '$1=$2novbscript...', $text);
    $text = preg_replace('#(<[^>]+)style=([\`\'\"]*).*expression\([^>]*>#iU', "$1>", $text);
    $text = preg_replace('#(<[^>]+)style=([\`\'\"]*).*behaviour\([^>]*>#iU', "$1>", $text);
    if ($striptags) {
        do {
            $thistext = $text;
            $text = preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i', "", $text);
        } while ($thistext != $text);
    }
    return $text;
}

function plugin_zboard_cron($isSysCron, $handler)
{
global $tpl, $cron, $mysql, $config, $lang, $parse, $PFILTERS;

    if ($handler == 'zboard_expired') {
        zboardUpdateExpiredAnnounces();
    }

    if ($handler == 'zboard_views') {
        zboardUpdateDelayedCounters();
    }

}

// Update expired announces
function zboardUpdateExpiredAnnounces() {
global $tpl, $cron, $mysql, $config, $lang, $parse, $PFILTERS;

        foreach ($mysql->select("select * from ".prefix."_zboard where active = 1 AND (datediff(NOW(), FROM_UNIXTIME(vip_expired)) > 0) AND vip_expired != 0") as $irow) {
            $mysql->query("UPDATE ".prefix."_zboard SET vip_expired = '', vip_added = '' WHERE id = '".$irow['id']."' ");
        }

        foreach ($mysql->select("select * from ".prefix."_zboard where active = 1 AND datediff(NOW(),FROM_UNIXTIME(editdate)) > announce_period * 30") as $irow) {

        $hashcode = rand_str();

        $mysql->query("UPDATE ".prefix."_zboard SET active = 0, expired = '".$hashcode."' WHERE id = '".$irow['id']."' ");

            //Email informer
            if($irow['uid'] != 0) { $alink = generatePluginLink('uprofile', 'show', array('name' => $irow['name'], 'id' => $irow['uid']), array(), false, true); }
            else { $alink = ''; }

            $body = str_replace(
                    array(	'{username}',
                            '[userlink]',
                            '[/userlink]',
                            '{description}',
                            '{announcename}',
                            '{expired_expend}',
                            ),
                    array(	$irow['name'],
                            ($irow['uid'])?'<a href="'.$alink.'">':'',
                            ($irow['uid'])?'</a>':'',
                            secure_html($irow['announce_description']),
                            $irow['announce_name'],
                            home.generateLink('core', 'plugin', array('plugin' => 'zboard', 'handler' => 'expend'), array('id' => $irow['id'],'hashcode' => $hashcode)),
                            ), $lang['zboard']['mail_exp']
                );

                zzMail($irow['author_email'], $lang['zboard']['mail_exp_title'], $body, 'html');
        }

        generate_entries_cnt_cache(true);
        generate_catz_cache(true);

}

// Update delayed news counters
function zboardUpdateDelayedCounters() {
    global $mysql;

    // Lock tables
    $mysql->query("lock tables ".prefix."_zboard_view write, ".prefix."_zboard write");

    // Read data and update counters
    foreach ($mysql->select("select * from ".prefix."_zboard_view") as $vrec) {
        $mysql->query("update ".prefix."_zboard set views = views + ".intval($vrec['cnt'])." where id = ".intval($vrec['id']));
    }

    // Truncate view table
    //$mysql->query("truncate table ".prefix."_zboard_view");
    // DUE TO BUG IN MYSQL - USE DELETE + OPTIMIZE
    $mysql->query("delete from ".prefix."_zboard_view");
    $mysql->query("optimize table ".prefix."_zboard_view");

    // Unlock tables
    $mysql->query("unlock tables");

    return true;
}

// Generate a random character string
function rand_str($length = 10, $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890')
 {
     // Length of character list
     $chars_length = (strlen($chars) - 1);

     // Start our string
     $string = $chars{rand(0, $chars_length)};

     // Generate random string
     for ($i = 1; $i < $length; $i = strlen($string))
     {
         // Grab a random character from our list
         $r = $chars{rand(0, $chars_length)};

         // Make sure the same two characters don't appear next to each other
         if ($r != $string{$i - 1}) $string .=  $r;
     }

     // Return the string
     return $string;
 }

function secure_search_zboard($text)
{
    $text = convert(trim($text));
    $text = preg_replace("/[^\w\x7F-\xFF\s]/", "", $text);
    return secure_html($text);
}

class Lingua_Stem_Ru
{
    public $VERSION = "0.02";
    public $Stem_Caching = 0;
    public $Stem_Cache = array();
    public $VOWEL = '/аеиоуыэюя/';
    public $PERFECTIVEGROUND = '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[ая])(в|вши|вшись)))$/';
    public $REFLEXIVE = '/(с[яь])$/';
    public $ADJECTIVE = '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|ему|ому|их|ых|ую|юю|ая|яя|ою|ею)$/';
    public $PARTICIPLE = '/((ивш|ывш|ующ)|((?<=[ая])(ем|нн|вш|ющ|щ)))$/';
    public $VERB = '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ен|ило|ыло|ено|ят|ует|уют|ит|ыт|ены|ить|ыть|ишь|ую|ю)|((?<=[ая])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/';
    public $NOUN = '/(а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|й|иям|ям|ием|ем|ам|ом|о|у|ах|иях|ях|ы|ь|ию|ью|ю|ия|ья|я)$/';
    public $RVRE = '/^(.*?[аеиоуыэюя])(.*)$/';
    public $DERIVATIONAL = '/[^аеиоуыэюя][аеиоуыэюя]+[^аеиоуыэюя]+[аеиоуыэюя].*(?<=о)сть?$/';

    public function s(&$s, $re, $to)
    {
        $orig = $s;
        $s = preg_replace($re, $to, $s);
        return $orig !== $s;
    }

    public function m($s, $re)
    {
        return preg_match($re, $s);
    }

    public function stem_word($word)
    {
        $word = strtolower($word);
        $word = strtr($word, 'ё', 'е');

        if($this->Stem_Caching && isset($this->Stem_Cache[$word]))
        {
            return $this->Stem_Cache[$word];
        }
        $stem = $word;
        do
        {
            if(!preg_match($this->RVRE, $word, $p)) break;
            $start = $p[1];
            $RV = $p[2];
            if(!$RV) break;

            if(!$this->s($RV, $this->PERFECTIVEGROUND, ''))
            {
                $this->s($RV, $this->REFLEXIVE, '');

                if($this->s($RV, $this->ADJECTIVE, ''))
                {
                    $this->s($RV, $this->PARTICIPLE, '');
                } else {
                    if(!$this->s($RV, $this->VERB, ''))
                    {
                        $this->s($RV, $this->NOUN, '');
                    }
                }
            }

            $this->s($RV, '/и$/', '');


            if($this->m($RV, $this->DERIVATIONAL))
            {
                $this->s($RV, '/ость?$/', '');
            }

            if(!$this->s($RV, '/ь$/', ''))
            {
                $this->s($RV, '/ейше?/', '');
                $this->s($RV, '/нн$/', 'н');
            }

            $stem = $start.$RV;
        } while(false);
            if($this->Stem_Caching) $this->Stem_Cache[$word] = $stem;
            return $stem;
    }

    public function stem_caching($parm_ref)
    {
        $caching_level = @$parm_ref['-level'];
        if($caching_level)
        {
            if(!$this->m($caching_level, '/^[012]$/'))
            {
                die(__CLASS__ . "::stem_caching() - Legal values are '0','1' or '2'. '$caching_level' is not a legal value");
            }
            $this->Stem_Caching = $caching_level;
        }
        return $this->Stem_Caching;
    }

    public function clear_stem_cache()
    {
        $this->Stem_Cache = array();
    }
}
