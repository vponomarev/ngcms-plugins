<?php

// Protect against hack attempts
if (!defined('NGCMS')) {
    die('HAL');
}
//
// Прописываем свой модуль
//
global $AUTH_METHOD;
global $AUTH_CAPABILITIES;
global $config;

class auth_basic extends CoreAuthPlugin
{
    // Осуществить вход
    // $username	= логин
    // $password	= пароль
    // $auto_scan	= если 1, то функция сама должна найти нужные параметры среди POST'ов
    public function login($auto_scan = 1, $username = '', $password = '')
    {
        global $mysql;
        if ($auto_scan) {
            $username = $_REQUEST['username'];
            $password = $_REQUEST['password'];
        }
        $password = EncodePassword($password);
        //
        $flagError = false;
        $flagErrorText = '';
        $returnValue = '';
        $row = [];
        // Request record for specific user
        if (!is_array($row = $mysql->record('select * from '.uprefix.'_users where name ='.db_squote($username)))) {
            // ** UNKNOWN USER **
            return 'ERR:INVALID.USER ['.$username.']';
        }
        // Check password
        if ($row['pass'] != $password) {
            // ** WRONG PASSWORD **
            return 'ERR:INVALID.PASSWORD ['.$username.']';
        }
        // Check is activation is required
        if ($row['activation']) {
            return 'ERR:NEED.ACTIVATE ['.$username.']';
        }

        // Return user's record
        return $row;
    }

    //
    // Сохранить в БД информацию о том, что пользователь авторизовался
    // $dbrow	= строка из нашей таблицы пользователей
    public function save_auth($dbrow)
    {
        global $config, $mysql, $ip, $ngCookieDomain;
        // создаём random cookie
        $auth_cookie = md5((isset($config['crypto_salt']) ? $config['crypto_salt'] : '').uniqid(rand(), 1));
        // Multilogin is allowed. Session information is stored in separate table
        if (pluginGetVariable('auth_basic', 'multilogin')) {
            // Drop old session (if applicable)
            if (isset($_COOKIE['zz_auth']) && $_COOKIE['zz_auth']) {
                $mysql->query('delete from '.uprefix.'_users_sessions where authcookie = '.db_squote($_COOKIE['zz_auth']).' limit 1');
            }
            // Register new session
            $query = 'insert into '.uprefix.'_users_sessions (userID, ip, last, authcookie) values ('.db_squote($dbrow['id']).', '.db_squote($ip).', '.db_squote(time()).', '.db_squote($auth_cookie).')';
            $mysql->query($query);
            // Update user's last login time
            $query = 'update '.uprefix.'_users set last = '.db_squote(time()).' where id = '.db_squote($dbrow['id']);
            $mysql->query($query);
        } else {
            // SINGLE LOGIN mode
            $query = 'update '.uprefix.'_users set last = '.db_squote(time()).', ip='.db_squote($ip).', authcookie = '.db_squote($auth_cookie).' where id='.db_squote($dbrow['id']);
            $mysql->query($query);
        }
        // Fix: http://stackoverflow.com/a/5849409/4331870
        if ($ngCookieDomain == 'localhost') {
            $ngCookieDomain = null;
        }
        // Вставить юзеру куку
        @setcookie('zz_auth', $auth_cookie, ($config['remember'] ? (time() + 3600 * 24 * 365) : 0), '/', $ngCookieDomain, 0, 1);

        return 1;
    }

    //
    // Проверить авторизацию пользователя
    public function check_auth()
    {
        global $config, $mysql, $ip;
        $auth_cookie = isset($_COOKIE['zz_auth']) ? $_COOKIE['zz_auth'] : '';
        if (!$auth_cookie) {
            return '';
        }
        // Multilogin is allowed. Session information is stored in separate table
        if (pluginGetVariable('auth_basic', 'multilogin')) {
            $query = 'select * from '.uprefix.'_users_sessions where authcookie = '.db_squote($auth_cookie).' limit 1';
            if (is_array($sRow = $mysql->record($query))) {
                // Retrieve user row
                $query = 'select * from '.uprefix.'_users where id = '.db_squote($sRow['userID']);
                $row = $mysql->record($query);
                // COMPATIBILITY MODE - replace InMemory authcookie / ip
                if (is_array($row)) {
                    $row['ip'] = $sRow['ip'];
                    $row['authcookie'] = $sRow['authcookie'];
                }
            }
        } else {
            $query = 'select * from '.uprefix.'_users where authcookie = '.db_squote($auth_cookie).' limit 1';
            $row = $mysql->record($query);
        }
        // Check for "IPLOCK" protection
        if (pluginGetVariable('auth_basic', 'iplock') && ($ip != $row['ip'])) {
            return '';
        }
        // Auth done
        if ($row['name']) {
            // Check if we need to update last visit field
            if ((pluginGetVariable('auth_basic', 'lastupdate') > 0) && ((time() - $row['last']) > pluginGetVariable('auth_basic', 'lastupdate'))) {
                $query = 'update '.uprefix.'_users set last = '.db_squote(time()).', ip='.db_squote($ip).' where id='.db_squote($row['id']);
                $mysql->query($query);
                // Multilogin is allowed. Session information is stored in separate table
                if (pluginGetVariable('auth_basic', 'multilogin')) {
                    $query = 'update '.uprefix.'_users_sessions set last = '.db_squote(time()).' where (userID = '.db_squote($row['id']).') and (authcookie='.db_squote($row['authcookie']).')';
                    $mysql->query($query);
                }
            }

            return $row;
        }

        return '';
    }

    //
    // Отменить авторизацию
    public function drop_auth()
    {
        global $config, $mysql, $userROW;
        $auth_cookie = $_COOKIE['zz_auth'];
        if (!$auth_cookie) {
            return;
        }
        // Multilogin is allowed. Session information is stored in separate table
        if (pluginGetVariable('auth_basic', 'multilogin')) {
            $mysql->query('delete from '.prefix.'_users_sessions where authcookie = '.db_squote($userROW['authcookie']));
        } else {
            $mysql->query('update '.uprefix."_users set authcookie = '' where id=".db_squote($userROW['id']));
        }
        @setcookie('zz_auth', '', time() - 3600 * 24 * 365, '/');
    }

    //
    // Вернуть массив параметров, необходимых при регистрации
    public function get_reg_params()
    {
        global $config, $lang;
        $params = [];
        LoadPluginLang('auth_basic', 'auth', '', 'auth');
        array_push($params, ['name' => 'login', 'id' => 'reg_login', 'title' => $lang['auth_login'], 'descr' => $lang['auth_login_descr'], 'type' => 'input']);
        if ($config['register_type'] >= 3) {
            array_push($params, ['id' => 'reg_password', 'name' => 'password', title => $lang['auth_pass'], 'descr' => $lang['auth_pass_descr'], 'type' => 'password']);
            array_push($params, ['id' => 'reg_password2', 'name' => 'password2', title => $lang['auth_pass2'], 'descr' => $lang['auth_pass2_descr'], 'type' => 'password']);
        }
        array_push($params, ['name' => 'email', id => 'reg_email', 'title' => $lang['auth_email'], 'descr' => $lang['auth_email_descr'], 'type' => 'input']);

        return $params;
    }

    //
    // Провести регистрацию
    // params = параметры полученные из get_reg_params()
    // values = значения для вышеуказанных параметрах
    // msg	= сообщение об ошибке
    // Возвращаемые значения:
    // 0 - ошибка
    // 1 - всё ok
    public function register(&$params, $values, &$msg)
    {
        global $config, $mysql, $lang, $tpl;
        LoadPluginLang('auth_basic', 'auth', '', 'auth');
        $error = 0;
        $userid = 0;
        $values['login'] = trim($values['login']);
        // Preprocess login
        if (strlen($values['login']) < 3) {
            // Слишком короткий логин
            $msg = $lang['auth_login_short'];

            return 0;
        }
        // Проверяем логин на запрещенные символы
        $csError = false;
        switch (pluginGetVariable('auth_basic', 'regcharset')) {
            case 0:
                if (!preg_match('#^[A-Za-z0-9\.\_\-]+$#s', $values['login'])) {
                    $csError = true;
                }
                break;
            case 1:
                if (!preg_match('#^[А-Яа-яёЁ0-9\.\_\-]+$#s', $values['login'])) {
                    $csError = true;
                }
                break;
            case 2:
                if (!preg_match('#^[А-Яа-яёЁA-Za-z0-9\.\_\-]+$#s', $values['login'])) {
                    echo 'CASE2-err ['.$values['login'].']';
                    $csError = true;
                }
                break;
            case 3:
                if (!preg_match('#^[\x21-\x7e\xc0-\xffёЁ]+$#s', $values['login'])) {
                    $csError = true;
                }
                break;
            case 4:
                break;
        }
        if (preg_match('/[&<>\\"'."'".']/', $values['login']) || $csError) {
            // Запрещенные HTML символы
            $msg = $lang['auth_login_html'];

            return 0;
        }
        if ($config['register_type'] >= 3) {
            if (strlen($values['password']) < 3) {
                // Слишком короткий пароль
                $msg = $lang['auth_pass_short'];

                return 0;
            } elseif ($values['password'] != $values['password2']) {
                // Несовпадение паролей
                $msg = $lang['auth_pass_diff'];

                return 0;
            }
        }
        if ((strlen($values['email']) > 70) || (!preg_match("/^[\.A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $values['email']))) {
            // Неверный email
            $msg = $lang['auth_email_wrong'];

            return 0;
        }
        $row = $mysql->record('select * from '.uprefix.'_users where lower(name)='.db_squote(strtolower($values['login'])).' or mail='.db_squote($values['email']));
        if (is_array($row)) {
            // Дублирование логина/email'а
            if (strtolower($row['mail']) == strtolower($values['email'])) {
                // email dup
                $msg = $lang['auth_email_dup'];

                return 0;
            }
            // Если не мыло, то логин
            $msg = $lang['auth_login_dup'];

            return 0;
        }
        // Всё в порядке, регим
        $add_time = time() + ($config['date_adjust'] * 60);
        // Статус пользователя по умолчанию
        $regGroup = intval(pluginGetVariable('auth_basic', 'regstatus'));
        if (!isset($UGROUP[$regGroup])) {
            // If GROUP is not defined - set "4" as default
            $regGroup = 4; // Commenter
        }
        // Определяем действия в зависимости от типа регистрации
        switch ($config['register_type']) {
            // 0 - Мгновенная [автогенерация пароля, без email нотификации]
            case 0:
                $newpassword = MakeRandomPassword();
                $mysql->query('INSERT INTO '.uprefix.'_users (name, pass, mail, status, reg, last) VALUES ('.db_squote($values['login']).', '.db_squote(EncodePassword($newpassword)).', '.db_squote($values['email']).', '.$regGroup.", '".$add_time."', '')");
                $userid = $mysql->result('select LAST_INSERT_ID()');
                msg([
                    'text' => $lang['msgo_registered'],
                    'info' => str_replace(['{login}', '{password}'], [$values['login'], $newpassword], $lang['auth_reg.success0']),
                ]);
                break;
            // 1 - Простая [автогенерация пароля, с email нотификацией]
            case 1:
                $newpassword = MakeRandomPassword();
                $mysql->query('INSERT INTO '.uprefix.'_users (name, pass, mail, status, reg, last) VALUES ('.db_squote($values['login']).', '.db_squote(EncodePassword($newpassword)).', '.db_squote($values['email']).', '.$regGroup.", '".$add_time."', '')");
                $userid = $mysql->result('select LAST_INSERT_ID()');
                $tvars['vars'] = [
                    'login'    => $values['login'],
                    'home'     => home,
                    'password' => $newpassword,
                ];
                $tvars['regx'] = [
                    '#\[activation\].+?\[\/activation]#is' => '',
                ];
                $tpl->template('register', GetPluginLangDir('auth_basic'));
                $tpl->vars('register', $tvars);
                $msg = $tpl->show('register');
                sendEmailMessage(
                    $values['email'],
                    $lang['letter_title'],
                    $msg,
                    false,
                    false,
                    'html'
                );
                msg([
                    'text' => $lang['msgo_registered'],
                    'info' => str_replace(['{login}', '{password}', '{email}'], [$values['login'], $newpassword, $values['email']], $lang['auth_reg.success1']),
                ]);
                break;
            // 2 - С подтверждением [автогенерация пароля, пароль отправляется на email адрес и не показывается в админке]
            case 2:
                // New password
                $newpassword = MakeRandomPassword();
                // Random activation code
                $actcode = MakeRandomPassword();
                // Insert into DB
                $mysql->query('INSERT INTO '.uprefix.'_users (name, pass, mail, status, reg, last, activation) VALUES ('.db_squote($values['login']).', '.db_squote(EncodePassword($newpassword)).', '.db_squote($values['email']).', '.$regGroup.", '".$add_time."', '', ".db_squote($actcode).')');
                $userid = $mysql->result('select LAST_INSERT_ID()');
                $link = generatePluginLink('core', 'activation', ['userid' => $userid, 'code' => $actcode], [], false, true);
                $tvars['vars'] = [
                    'login'        => $values['login'],
                    'home'         => home,
                    'password'     => $newpassword,
                    'activate_url' => $link,
                ];
                $tvars['regx'] = [
                    '#\[activation\](.+?)\[\/activation]#is' => '$1',
                ];
                $tpl->template('register', GetPluginLangDir('auth_basic'));
                $tpl->vars('register', $tvars);
                $msg = $tpl->show('register');
                sendEmailMessage(
                    $values['email'],
                    $lang['letter_title'],
                    $msg,
                    'html'
                );
                msg([
                    'text' => $lang['msgo_registered'],
                    'info' => str_replace(['{login}', '{password}', '{email}'], [$values['login'], $newpassword, $values['email']], $lang['auth_reg.success2']),
                ]);
                break;
            // 3 - Ручная с нотификацией [ручная генерация пароля, email нотификация]
            case 3:
                $mysql->query('INSERT INTO '.uprefix.'_users (name, pass, mail, status, reg, last) VALUES ('.db_squote($values['login']).', '.db_squote(EncodePassword($values['password'])).', '.db_squote($values['email']).', '.$regGroup.", '".$add_time."', '')");
                $userid = $mysql->result('select LAST_INSERT_ID()');
                $tvars['vars'] = [
                    'login'    => $values['login'],
                    'home'     => home,
                    'password' => $values['password'],
                ];
                $tvars['regx'] = [
                    '#\[activation\].+?\[\/activation]#is' => '',
                ];
                $tpl->template('register', GetPluginLangDir('auth_basic'));
                $tpl->vars('register', $tvars);
                $msg = $tpl->show('register');
                sendEmailMessage(
                    $values['email'],
                    $lang['letter_title'],
                    $msg,
                    'html'
                );
                msg([
                    'text' => $lang['msgo_registered'],
                    'info' => str_replace(['{login}', '{password}', '{email}'], [$values['login'], $values['password'], $values['email']], $lang['auth_reg.success3']),
                ]);
                break;
            // 4 - Ручная с подтверждением [ручная генерация пароля, подтверждение email адреса]
            case 4:
                $actcode = MakeRandomPassword();
                $mysql->query('INSERT INTO '.uprefix.'_users (name, pass, mail, status, reg, last, activation) VALUES ('.db_squote($values['login']).', '.db_squote(EncodePassword($values['password'])).', '.db_squote($values['email']).', '.$regGroup.", '".$add_time."', '', ".db_squote($actcode).')');
                $userid = $mysql->result('select LAST_INSERT_ID()');
                $link = generatePluginLink('core', 'activation', ['userid' => $userid, 'code' => $actcode], [], false, true);
                $tvars['vars'] = [
                    'login'        => $values['login'],
                    'home'         => home,
                    'password'     => $values['password'],
                    'activate_url' => $link,
                ];
                $tvars['regx'] = [
                    '#\[activation\](.+?)\[\/activation]#is' => '$1',
                ];
                $tpl->template('register', GetPluginLangDir('auth_basic'));
                $tpl->vars('register', $tvars);
                $msg = $tpl->show('register');
                sendEmailMessage(
                    $values['email'],
                    $lang['letter_title'],
                    $msg,
                    'html'
                );
                msg([
                    'text' => $lang['msgo_registered'],
                    'info' => str_replace(['{login}', '{password}', '{email}'], [$values['login'], $values['password'], $values['email']], $lang['auth_reg.success4']),
                ]);
            //print "<pre>".var_export($lang, true)."</pre>";
        }

        return ($userid > 0) ? $userid : 1;
    }

    //
    // Вернуть массив параметров, необходимых для восстановления пароля
    public function get_restorepw_params()
    {
        global $config, $lang;
        $params = [];
        LoadPluginLang('auth_basic', 'auth', '', 'auth');
        $mode = pluginGetVariable('auth_basic', 'restorepw');
        if (!$mode) {
            return false;
            //array_push($params, array('text' => $lang['auth_norestore']));
            //return $params;
        }
        array_push($params, ['text' => $lang['auth_restore_'.$mode]]);
        if ($mode != 'email') {
            array_push($params, ['name' => 'login', title => $lang['auth_login'], 'type' => 'input']);
        }
        if ($mode != 'login') {
            array_push($params, ['name' => 'email', title => $lang['auth_email'], 'type' => 'input']);
        }

        return $params;
    }

    //
    // Восстановить пароль
    public function restorepw(&$params, $values, &$msg)
    {
        global $config, $mysql, $lang, $tpl;
        $error = 0;
        $values['login'] = trim($values['login']);
        $values['email'] = trim($values['email']);
        LoadPluginLang('auth_basic', 'auth', '', 'auth');
        $mode = pluginGetVariable('auth_basic', 'restorepw');
        if (!$mode) {
            $msg = $lang['auth_norestore'];

            return 0;
        }
        $px = [];
        if ($mode != 'email') {
            if (!$values['login']) {
                $msg = $lang['auth_login_require'];

                return 0;
            }
            array_push($px, 'name = '.db_squote($values['login']));
        }
        if ($mode != 'login') {
            if (!$values['email']) {
                $msg = $lang['auth_email_require'];

                return 0;
            }
            array_push($px, 'mail = '.db_squote($values['email']));
        }
        $query = 'select * from '.uprefix.'_users where '.implode(' and ', $px);
        $row = $mysql->record($query);
        if (is_array($row)) {
            // Нашли юзера
            $newpassword = MakeRandomPassword();
            $mysql->query('UPDATE '.uprefix.'_users SET newpw='.db_squote(EncodePassword($newpassword)).' WHERE id='.$row['id']);
            $tvars['vars'] = [
                'login' => $row['name'],
                'home'  => home,
                'newpw' => $newpassword,
            ];
            $tvars['vars']['pwurl'] = generatePluginLink('core', 'lostpassword', ['userid' => $row['id'], 'code' => EncodePassword($newpassword)], [], false, true);
            $tpl->template('restorepw', GetPluginLangDir('auth_basic'));
            $tpl->vars('restorepw', $tvars);
            sendEmailMessage($row['mail'], $lang['auth_mail_subj'], $tpl->show('restorepw'));
            msg(['text' => $lang['msgo_sent']]);

            return 1;
        } else {
            $msg = $lang['auth_nouser'];

            return 0;
        }
    }

    // AJAX call - online check registration parameters for correct valuescheck if login is available
    // Input:
    // $params - array of 'fieldName' => 'fieldValue' for checking
    // Returns:
    // $result - array of 'fieldName' => status
    // List of statuses:
    // 0	- Method not implemented [ this field is not checked/can't be checked/... ] OR NOT SET
    // 1	- Occupied
    // 2	- Incorrect length
    // 3	- Incorrect format
    // 100	- Available for registration
    public function onlineCheckRegistration($params)
    {
        global $config, $mysql;
        // Prepare basic reply array
        $results = [];
        // Check for login
        if (isset($params['login'])) {
            $params['login'] = iconv('UTF-8', 'Windows-1251', trim($params['login']));
            // Check for incorrect chars
            if (strlen($params['login']) < 3) {
                // Login is too short
                $results['login'] = 2;
                goto endLoginCheck;
            }
            // Check for incorrect chars
            $csError = false;
            switch (pluginGetVariable('auth_basic', 'regcharset')) {
                case 0:
                    if (!preg_match('#^[A-Za-z0-9\.\_\-]+$#s', $params['login'])) {
                        $csError = true;
                    }
                    break;
                case 1:
                    if (!preg_match('#^[А-Яа-яёЁ0-9\.\_\-]+$#s', $params['login'])) {
                        $csError = true;
                    }
                    break;
                case 2:
                    if (!preg_match('#^[А-Яа-яёЁA-Za-z0-9\.\_\-]+$#s', $params['login'])) {
                        echo 'CASE2-err ['.$params['login'].']';
                        $csError = true;
                    }
                    break;
                case 3:
                    if (!preg_match('#^[\x21-\x7e\xc0-\xffёЁ]+$#s', $params['login'])) {
                        $csError = true;
                    }
                    break;
                case 4:
                    break;
            }
            if (preg_match('/[&<>\\"'."'".']/', $params['login']) || $csError) {
                // Incorrect chars
                $results['login'] = 3;
                goto endLoginCheck;
            }
            // Check if login is occupied
            $row = $mysql->record('select * from '.uprefix.'_users where lower(name)='.db_squote(strtolower($params['login'])));
            if (is_array($row)) {
                $results['login'] = 1;
                goto endLoginCheck;
            }
            // All tests are passed, login can be used
            $results['login'] = 100;
        }
        endLoginCheck:
        // Check for email
        if (isset($params['email'])) {
            $params['email'] = trim($params['email']);
            if (strlen($params['email']) > 70) {
                $results['email'] = 2;
                goto endEmailCheck;
            }
            if (!preg_match("/^[\.A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $params['email'])) {
                $results['email'] = 3;
                // Неверный email
                goto endEmailCheck;
            }
            $row = $mysql->record('select * from '.uprefix.'_users where lower(mail)='.db_squote($params['email']));
            if (is_array($row)) {
                $results['email'] = 1;
                goto endEmailCheck;
            }
            // All tests are passed, email can be used
            $results['email'] = 100;
        }
        endEmailCheck:

        // Return
        return $results;
    }

    //
    // Подтверждение восстановления пароля
    //
    public function confirm_restorepw(&$msg, $reqid = null, $reqsecret = null)
    {
        global $config, $mysql, $lang, $tpl;
        LoadPluginLang('auth_basic', 'auth', '', 'auth');
        $row = $mysql->record('select * from '.uprefix.'_users where id = '.db_squote($reqid));
        if (is_array($row)) {
            if ($reqsecret == $row['newpw']) {
                // OK !!!
                $msg = $lang['auth_newpw_ok'];
                $mysql->query('update '.uprefix.'_users set pass=newpw where id = '.db_squote($reqid));

                return 1;
            }
        }
        $msg = $lang['auth_newpw_fail'];

        return 0;
    }
}

$AUTH_METHOD['basic'] = new auth_basic();
$AUTH_CAPABILITIES['basic'] = ['login' => '1', 'db' => '1'];
if (pluginGetVariable('auth_basic', 'en_dbprefix')) {
    $config['uprefix'] = pluginGetVariable('auth_basic', 'dbprefix');
}
