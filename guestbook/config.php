<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

plugins_load_config();
LoadPluginLang('guestbook', 'config', '', 'gbconfig', '#');

switch ($_REQUEST['action']) {
    case 'manage_fields'  :
        manage_fields();
        break;
    case 'add_field'      :
        add_field();
        break;
    case 'edit_field'     :
        edit_field();
        break;
    case 'drop_field'     :
        drop_field();
        manage_fields();
        break;
    case 'insert_field'   :
        $result = insert_field();
        if ($result === TRUE) manage_fields(); else add_field();
        break;
    case 'update_field'   :
        $result = update_field();
        if ($result === TRUE) manage_fields(); else edit_field($result);
        break;
    case 'options'        :
        show_options();
        break;
    case 'show_messages'  :
        show_messages();
        break;
    case 'edit_message'   :
        $result = edit_message();
        if ($result === TRUE) show_messages();
        break;
    case 'delete_message' :
        delete_message();
        show_messages();
        break;
    case 'delete_social'  :
        $result = delete_social();
        edit_message($result);
        break;
    case 'modify'         :
        modify();
        show_messages();
        break;
    case 'social'         :
        social_config();
        break;
    default               :
        show_options();
}

/*
 * Add field page
 */
function add_field()
{
    global $twig;

    $xt = $twig->loadTemplate('plugins/guestbook/tpl/config/manage_fields.add.tpl');

    $xg = $twig->loadTemplate('plugins/guestbook/tpl/config/main.tpl');

    $tVars = array(
        'entries' => $xt->render($tVars),
    );

    print $xg->render($tVars);
}

/*
 * Insert field callback
 */
function insert_field()
{
    global $mysql, $lang;

    $id = $_REQUEST['id'];
    $name = $_REQUEST['name'];
    $placeholder = $_REQUEST['placeholder'];
    $default = $_REQUEST['default_value'];
    $required = (isset($_REQUEST['required'])) ? 1 : 0;

    // Check field ID
    if (empty($id)) {
        msg(array("type" => "error", "text" => $lang['gbconfig']['msge_field_id_empty']));
        return FALSE;
    }

    // Check if ID is unique
    $field = $mysql->result('SELECT COUNT(1) FROM ' . prefix . '_guestbook_fields WHERE id = ' . db_squote($id));
    if (!empty($field)) {
        msg(array("type" => "error", "text" => $lang['gbconfig']['msge_field_duplicate_id']));
        return FALSE;
    }

    // Check for characters
    if (!preg_match('#^[a-z]+$#', $id)) {
        msg(array("type" => "error", "text" => $lang['gbconfig']['msge_field_characters']));
        return FALSE;
    }

    // Check for length
    if (strlen($id) < 3) {
        msg(array("type" => "error", "text" => $lang['gbconfig']['msge_field_id_length']));
        return FALSE;
    }

    // Check field name
    if (empty($name)) {
        msg(array("type" => "error", "text" => $lang['gbconfig']['msge_field_name_empty']));
        return FALSE;
    } elseif (strlen($name) < 3) {
        msg(array("type" => "error", "text" => $lang['gbconfig']['msge_field_name_length']));
        return FALSE;
    }

    // Everything is correct - update DB
    $mysql->query("INSERT INTO " . prefix . "_guestbook_fields VALUES(" .
        db_squote($id) . ", " .
        db_squote($name) . ", " .
        db_squote($placeholder) . ", " .
        db_squote($default) . ", " .
        db_squote($required) . ")"
    );
    $mysql->query("ALTER TABLE " . prefix . "_guestbook ADD " . $id . " VARCHAR(50) NOT NULL DEFAULT ''");

    msg(array("text" => $lang['gbconfig']['msgo_field_add_success']));
    return TRUE;
}

/*
 * Edit field page
 */
function edit_field($id)
{
    global $mysql, $twig, $lang;

    $field = array();

    if (!empty($id) || isset($_REQUEST['id'])) {
        $fid = (isset($_REQUEST['id'])) ? $_REQUEST['id'] : $id;
        if (!empty($fid)) {
            $result = $mysql->record('SELECT * FROM ' . prefix . '_guestbook_fields WHERE id = ' . db_squote($fid));
            if (!empty($result)) {
                $field = $result;
            }
        }
    }

    // Field ID is empty or not correct
    if (!count($field)) {
        msg(array("type" => "error", "text" => $lang['gbconfig']['msge_field_id_not_exist']));
        manage_fields();
        return;
    }

    $tVars['field'] = $field;

    $xt = $twig->loadTemplate('plugins/guestbook/tpl/config/manage_fields.edit.tpl');
    $xg = $twig->loadTemplate('plugins/guestbook/tpl/config/main.tpl');

    $tVars = array(
        'entries' => $xt->render($tVars),
    );

    print $xg->render($tVars);
}

/*
 * Update field callback
 */
function update_field()
{
    global $mysql, $lang;

    $id = $_REQUEST['id'];
    $name = $_REQUEST['name'];
    $placeholder = $_REQUEST['placeholder'];
    $default = $_REQUEST['default_value'];
    $required = (isset($_REQUEST['required'])) ? 1 : 0;

    // Check field name
    if (empty($name)) {
        msg(array("type" => "error", "text" => $lang['gbconfig']['msge_field_name_empty']));
        return $id;
    } elseif (strlen($name) < 3) {
        msg(array("type" => "error", "text" => $lang['gbconfig']['msge_field_name_length']));
        return $id;
    }

    // Everything is correct - update DB
    $mysql->query("UPDATE " . prefix . "_guestbook_fields SET " .
        "name = " . db_squote($name) . ", " .
        "placeholder = " . db_squote($placeholder) . ", " .
        "default_value = " . db_squote($default) . ", " .
        'required =' . db_squote($required) .
        " WHERE id = " . db_squote($id)
    );
    msg(array("text" => $lang['gbconfig']['msgo_field_edit_success']));

    return TRUE;
}

/*
 * Drop field callback
 */
function drop_field()
{
    global $mysql, $lang;

    $id = $_REQUEST['id'];

    $field = $mysql->result('SELECT COUNT(1) FROM ' . prefix . '_guestbook_fields WHERE id = ' . db_squote($id));
    if (empty($field)) {
        msg(array("type" => "error", "text" => $lang['gbconfig']['msge_field_id_not_exist']));
        return;
    }

    $mysql->query("DELETE FROM " . prefix . "_guestbook_fields WHERE id = " . db_squote($id));
    $mysql->query("ALTER TABLE " . prefix . "_guestbook DROP " . $id);

    msg(array("text" => $lang['gbconfig']['msgo_field_drop_success']));
}

/*
 * List fields page
 */
function manage_fields()
{
    global $mysql, $twig, $lang;

    $fields = $mysql->select("select * from " . prefix . "_guestbook_fields");

    $tVars = array();
    $tEntries = array();

    foreach ($fields as $fNum => $fRow) {
        $tEntry = array(
            'id' => $fRow['id'],
            'name' => $fRow['name'],
            'placeholder' => $fRow['placeholder'],
            'default_value' => $fRow['default_value'],
            'required' => intval($fRow['required'])
        );
        $tEntries[] = $tEntry;
    }

    $tVars['entries'] = $tEntries;

    $xt = $twig->loadTemplate('plugins/guestbook/tpl/config/manage_fields.tpl');

    $xg = $twig->loadTemplate('plugins/guestbook/tpl/config/main.tpl');

    $tVars = array(
        'entries' => $xt->render($tVars),
    );

    print $xg->render($tVars);
}

function show_options()
{
    global $tpl, $mysql, $lang, $twig;

    $tpath = locatePluginTemplates(array('config/main', 'config/settings'), 'guestbook', 1);

    if (isset($_REQUEST['submit'])) {
        pluginSetVariable('guestbook', 'usmilies', secure_html($_REQUEST['usmilies']));
        pluginSetVariable('guestbook', 'ubbcodes', secure_html($_REQUEST['ubbcodes']));
        pluginSetVariable('guestbook', 'minlength', intval($_REQUEST['minlength']));
        pluginSetVariable('guestbook', 'maxlength', intval($_REQUEST['maxlength']));
        pluginSetVariable('guestbook', 'guests', secure_html($_REQUEST['guests']));
        pluginSetVariable('guestbook', 'ecaptcha', secure_html($_REQUEST['ecaptcha']));
        pluginSetVariable('guestbook', 'public_key', secure_html($_REQUEST['public_key']));
        pluginSetVariable('guestbook', 'private_key', secure_html($_REQUEST['private_key']));
        pluginSetVariable('guestbook', 'perpage', intval($_REQUEST['perpage']));
        pluginSetVariable('guestbook', 'order', secure_html($_REQUEST['order']));
        pluginSetVariable('guestbook', 'date', secure_html($_REQUEST['date']));
        pluginSetVariable('guestbook', 'send_email', secure_html($_REQUEST['send_email']));
        pluginSetVariable('guestbook', 'approve_msg', secure_html($_REQUEST['approve_msg']));
        pluginSetVariable('guestbook', 'admin_count', intval($_REQUEST['admin_count']));

        if (isset($_REQUEST['url']) && intval($_REQUEST['url']) == 1) {
            $ULIB = new urlLibrary();
            $ULIB->loadConfig();
            $ULIB->registerCommand('guestbook', '',
                array(
                    'vars' => array(
                        'page' =>
                            array(
                                'matchRegex' => '\\d{1,4}',
                                'descr' =>
                                    array(
                                        'russian' => 'Страница',
                                    ),
                            ),
                        'act' =>
                            array(
                                'matchRegex' => '.+?',
                                'descr' =>
                                    array(
                                        'russian' => 'action',
                                    ),
                            ),
                    ),
                    'descr' => array('russian' => 'Гостевая книга'),
                )
            );
            $ULIB->registerCommand('guestbook', 'edit',
                array(
                    'vars' => array(
                        'id' =>
                            array(
                                'matchRegex' => '\\d+',
                                'descr' =>
                                    array(
                                        'russian' => 'ID записи',
                                    ),
                            ),
                    ),
                    'descr' => array('russian' => 'Редактирование'),
                )
            );
            $ULIB->saveConfig();
        } else {
            $ULIB = new urlLibrary();
            $ULIB->loadConfig();
            $ULIB->removeCommand('guestbook', '');
            $ULIB->removeCommand('guestbook', 'edit');
            $ULIB->saveConfig();
        }
        pluginSetVariable('guestbook', 'url', intval($_REQUEST['url']));

        pluginsSaveConfig();
        msg(array("text" => $lang['gbconfig']['msgo_settings_saved']));
    }

    $usmilies = pluginGetVariable('guestbook', 'usmilies');
    $ubbcodes = pluginGetVariable('guestbook', 'ubbcodes');
    $minlength = pluginGetVariable('guestbook', 'minlength');
    $maxlength = pluginGetVariable('guestbook', 'maxlength');
    $guests = pluginGetVariable('guestbook', 'guests');
    $ecaptcha = pluginGetVariable('guestbook', 'ecaptcha');
    $public_key = pluginGetVariable('guestbook', 'public_key');
    $private_key = pluginGetVariable('guestbook', 'private_key');
    $perpage = pluginGetVariable('guestbook', 'perpage');
    $order = pluginGetVariable('guestbook', 'order');
    $date = pluginGetVariable('guestbook', 'date');
    $send_email = pluginGetVariable('guestbook', 'send_email');
    $approve_msg = pluginGetVariable('guestbook', 'approve_msg');
    $admin_count = pluginGetVariable('guestbook', 'admin_count');
    $url = pluginGetVariable('guestbook', 'url');

    $xt = $twig->loadTemplate($tpath['config/settings'] . 'config/settings.tpl');

    $tVars = array(
        'skins_url' => skins_url,
        'home' => home,
        'tpl_home' => admin_url,
        'usmilies' => $usmilies,
        'ubbcodes' => $ubbcodes,
        'minlength' => $minlength,
        'maxlength' => $maxlength,
        'guests' => $guests,
        'ecaptcha' => $ecaptcha,
        'public_key' => $public_key,
        'private_key' => $private_key,
        'perpage' => $perpage,
        'order' => $order,
        'url' => $url,
        'date' => $date,
        'send_email' => $send_email,
        'approve_msg' => $approve_msg,
        'admin_count' => $admin_count
    );

    $xg = $twig->loadTemplate($tpath['config/main'] . 'config/main.tpl');

    $tVars = array(
        'entries' => $xt->render($tVars),
    );

    print $xg->render($tVars);
}

function show_messages()
{
    global $tpl, $mysql, $lang, $twig, $config, $PHP_SELF;

    $tpath = locatePluginTemplates(array('config/main', 'config/messages_list'), 'guestbook', 1);

    $tVars = array();

    $news_per_page = pluginGetVariable('guestbook', 'admin_count');

    $fSort = "ORDER BY id DESC";
    $sqlQPart = "from " . prefix . "_guestbook " . $fSort;
    $sqlQCount = "select count(id) " . $sqlQPart;
    $sqlQ = "select * " . $sqlQPart;

    $pageNo = intval($_REQUEST['page']) ? $_REQUEST['page'] : 0;
    if ($pageNo < 1) $pageNo = 1;
    if (!$start_from) $start_from = ($pageNo - 1) * $news_per_page;

    $count = $mysql->result($sqlQCount);
    $countPages = ceil($count / $news_per_page);

    foreach ($mysql->select($sqlQ . ' LIMIT ' . $start_from . ', ' . $news_per_page) as $row) {
        $tEntry[] = array(
            'id' => $row['id'],
            'postdate' => $row['postdate'],
            'message' => $row['message'],
            'answer' => $row['answer'],
            'author' => $row['author'],
            'ip' => $row['ip'],
            'status' => $row['status'],
        );
    }

    $xt = $twig->loadTemplate($tpath['config/messages_list'] . 'config/messages_list.tpl');

    $tVars = array(
        'pagesss' => generateAdminPagelist(array('current' => $pageNo, 'count' => $countPages, 'url' => admin_url . '/admin.php?mod=extra-config&plugin=guestbook&action=show_messages&page=%page%')),
        'entries' => isset($tEntry) ? $tEntry : '',
        'php_self' => $PHP_SELF,
        'skins_url' => skins_url,
        'home' => home,
    );

    $xg = $twig->loadTemplate($tpath['config/main'] . 'config/main.tpl');

    $tVars = array(
        'entries' => $xt->render($tVars),
    );

    print $xg->render($tVars);
}

function delete_social()
{
    global $mysql, $lang;

    $id = intval($_REQUEST['id']);
    $soc = secure_html($_REQUEST['soc']);

    if (!in_array($soc, array('Vkontakte', 'Facebook', 'Google', 'Instagram'))) {
        msg(array("type" => "error", "text" => $lang['gbconfig']['msge_wrong_action']));
        return $id;
    }

    if (!is_array($mysql->record("SELECT id FROM " . prefix . "_guestbook WHERE id=" . db_squote($id)))) {
        msg(array("type" => "error", "text" => $lang['gbconfig']['msge_wrong_action']));
        return $id;
    }

    $entry = $mysql->record("SELECT social FROM " . prefix . "_guestbook WHERE id = " . db_squote($id));

    $social = unserialize($entry['social']);

    $new_social = array();
    foreach ($social as $k => $v) {
        if ($k != $soc) {
            $new_social[$k] = $v;
        }
    }

    $mysql->query("UPDATE " . prefix . "_guestbook set social = " . db_squote(serialize($new_social)) . " WHERE id = " . db_squote($id));

    msg(array("text" => $lang['gbconfig']['msgo_social_deleted']));
    return $id;
}

function delete_message()
{
    global $mysql, $lang;

    $id = intval($_REQUEST['id']);

    if (!is_array($mysql->record("SELECT id FROM " . prefix . "_guestbook WHERE id=" . db_squote($id)))) {
        return msg(array("type" => "error", "text" => $lang['gbconfig']['msge_wrong_action']));
    }

    $mysql->query("DELETE FROM " . prefix . "_guestbook WHERE id = " . $id);

    return msg(array("text" => $lang['gbconfig']['msgo_deleted_one']));
}

function edit_message($mid)
{
    global $tpl, $mysql, $lang, $twig, $config;

    $tpath = locatePluginTemplates(array('config/main', 'config/messages_edit'), 'guestbook', 1);

    if (!empty($mid) || isset($_REQUEST['id'])) {
        $id = (isset($mid)) ? intval($mid) : intval($_REQUEST['id']);
    }

    // get fields
    $fdata = $mysql->select("SELECT * FROM " . prefix . "_guestbook_fields");

    if (!empty($id)) {
        $row = $mysql->record('SELECT * FROM ' . prefix . '_guestbook WHERE id = ' . db_squote($id) . ' LIMIT 1');

        if (isset($_REQUEST['submit'])) {

            $errors = array();

            $author = $_REQUEST['author'];
            $status = $_REQUEST['status'];
            $message = str_replace(array("\r\n", "\r"), "\n", convert($_REQUEST['message']));
            $answer = str_replace(array("\r\n", "\r"), "\n", convert($_REQUEST['answer']));

            if (empty($author) || empty($message)) {
                $errors[] = $lang['gbconfig']['msge_field_required'];
            }

            $upd_rec = array(
                'message' => db_squote($message),
                'answer' => db_squote($answer),
                'author' => db_squote($author),
                'status' => db_squote($status)
            );

            if (preg_match('#^(\d+)\.(\d+)\.(\d+) +(\d+)\:(\d+)$#', $_REQUEST['cdate'], $m)) {
                $upd_rec['postdate'] = mktime($m[4], $m[5], 0, $m[2], $m[1], $m[3]) + ($config['date_adjust'] * 60);
            }

            foreach ($fdata as $fnum => $frow) {
                if (!empty($_REQUEST[$frow['id']])) {
                    $upd_rec[$frow['id']] = db_squote($_REQUEST[$frow['id']]);
                } elseif (intval($frow['required']) === 1) {
                    $errors[] = $lang['gbconfig']['msge_field_required'];
                } else {
                    $upd_rec[$frow['id']] = "''";
                }
            }

            // prepare query
            $upd_str = '';
            $count = 0;
            foreach ($upd_rec as $k => $v) {
                $upd_str .= $k . '=' . $v;
                $count++;
                if ($count < count($upd_rec)) {
                    $upd_str .= ', ';
                }
            }

            if (!count($errors)) {
                $mysql->query('UPDATE ' . prefix . '_guestbook SET ' . $upd_str . ' WHERE id = \'' . intval($id) . '\' ');
                msg(array("text" => $lang['gbconfig']['msgo_edit_success']));
                return TRUE;
            } else {
                msg(array("type" => "error", "text" => implode($errors)));
            }
        }

        // output social data
        $social = unserialize($row['social']);
        $profiles = array();
        foreach ($social as $name => $sid) {
            $img = $mysql->record("SELECT name, description FROM " . prefix . "_images WHERE id = {$sid}");
            $profiles[$name] = array(
                'photo' => $config['images_url'] . '/' . $img['name'],
                'link' => $img['description'],
            );
        }

        // output fields data
        $tFields = array();
        foreach ($fdata as $fnum => $frow) {
            $tField = array(
                'id' => $frow['id'],
                'name' => $frow['name'],
                'placeholder' => $frow['placeholder'],
                'default_value' => $frow['default_value'],
                'required' => intval($frow['required']),
                'value' => $row[$frow['id']]
            );
            $tFields[] = $tField;
        }

    } else {
        msg(array("type" => "error", "text" => "Не передан id"));
    }

    $xt = $twig->loadTemplate($tpath['config/messages_edit'] . 'config/messages_edit.tpl');

    $tVars = array(
        'skins_url' => skins_url,
        'home' => home,
        'tpl_home' => admin_url,
        'id' => $id,
        'message' => $row['message'],
        'answer' => $row['answer'],
        'author' => $row['author'],
        'status' => $row['status'],
        'ip' => $row['ip'],
        'postdate' => $row['postdate'],
        'fields' => $tFields,
        'social' => $profiles
    );

    $xg = $twig->loadTemplate($tpath['config/main'] . 'config/main.tpl');

    $tVars = array(
        'entries' => $xt->render($tVars),
    );

    print $xg->render($tVars);
}

/*
 * Bulk operaions apply callback
 */
function modify()
{
    global $mysql, $lang;

    $selected_news = $_REQUEST['selected_message'];
    $subaction = $_REQUEST['subaction'];

    if (empty($subaction)) {
        return msg(array("type" => "error", "text" => $lang['gbconfig']['msge_wrong_action']));
    }

    switch ($subaction) {
        case 'mass_approve'   :
            $active = 'status = 1';
            $msg = $lang['gbconfig']['msgo_activated'];
            break;
        case 'mass_forbidden' :
            $active = 'status = 0';
            $msg = $lang['gbconfig']['msgo_deactivated'];
            break;
        case 'mass_delete'    :
            $del = true;
            $msg = $lang['gbconfig']['msgo_deleted'];
            break;
        default               :
            return msg(array("type" => "error", "text" => $lang['gbconfig']['msge_wrong_action']));
    }

    // get messages list
    $id = implode(',', $selected_news);
    if (empty($id)) {
        return msg(array("type" => "error", "text" => $lang['gbconfig']['msge_not_selected']));
    }

    // change state
    if (isset($active)) {
        $mysql->query("UPDATE " . prefix . "_guestbook SET {$active} WHERE id IN ({$id})");
    }

    // delete
    if (isset($del)) {
        $mysql->query("DELETE FROM " . prefix . "_guestbook WHERE id IN ({$id})");
    }

    msg(array("type" => "info", "info" => sprintf($msg, $id)));
}

/*
 * Social config page
 */
function social_config()
{
    global $tpl, $mysql, $lang, $twig;

    $tpath = locatePluginTemplates(array('config/main', 'config/social'), 'guestbook', 1);

    if (isset($_REQUEST['submit'])) {
        pluginSetVariable('guestbook', 'vk_client_id', secure_html($_REQUEST['vk_client_id']));
        pluginSetVariable('guestbook', 'vk_client_secret', secure_html($_REQUEST['vk_client_secret']));

        pluginSetVariable('guestbook', 'facebook_client_id', secure_html($_REQUEST['facebook_client_id']));
        pluginSetVariable('guestbook', 'facebook_client_secret', secure_html($_REQUEST['facebook_client_secret']));

        pluginSetVariable('guestbook', 'google_client_id', secure_html($_REQUEST['google_client_id']));
        pluginSetVariable('guestbook', 'google_client_secret', secure_html($_REQUEST['google_client_secret']));

        pluginSetVariable('guestbook', 'instagram_client_id', secure_html($_REQUEST['instagram_client_id']));
        pluginSetVariable('guestbook', 'instagram_client_secret', secure_html($_REQUEST['instagram_client_secret']));

        pluginsSaveConfig();
        msg(array("text" => $lang['gbconfig']['msgo_settings_saved']));
    }

    $vk_client_id = pluginGetVariable('guestbook', 'vk_client_id');
    $vk_client_secret = pluginGetVariable('guestbook', 'vk_client_secret');

    $facebook_client_id = pluginGetVariable('guestbook', 'facebook_client_id');
    $facebook_client_secret = pluginGetVariable('guestbook', 'facebook_client_secret');

    $google_client_id = pluginGetVariable('guestbook', 'google_client_id');
    $google_client_secret = pluginGetVariable('guestbook', 'google_client_secret');

    $instagram_client_id = pluginGetVariable('guestbook', 'instagram_client_id');
    $instagram_client_secret = pluginGetVariable('guestbook', 'instagram_client_secret');

    $xt = $twig->loadTemplate($tpath['config/social'] . 'config/social.tpl');

    $tVars = array(
        'skins_url' => skins_url,
        'home' => home,
        'tpl_home' => admin_url,
        'vk_client_id' => $vk_client_id,
        'vk_client_secret' => $vk_client_secret,
        'facebook_client_id' => $facebook_client_id,
        'facebook_client_secret' => $facebook_client_secret,
        'google_client_id' => $google_client_id,
        'google_client_secret' => $google_client_secret,
        'instagram_client_id' => $instagram_client_id,
        'instagram_client_secret' => $instagram_client_secret
    );

    $xg = $twig->loadTemplate($tpath['config/main'] . 'config/main.tpl');

    $tVars = array(
        'entries' => $xt->render($tVars),
    );

    print $xg->render($tVars);
}
