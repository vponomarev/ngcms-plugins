<?php

// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

// Initiate interceptors
registerActionHandler('index', 'site_stats');

// Load lang files
LoadPluginLang('site_stats', 'site', '', '', ':');

function filter_bots($u_agent)
{
    // To extend the list bots use
    // https://github.com/JayBizzle/Crawler-Detect
    $engines = array(
        'YandexBot' => 'YandexBot',
        'YandexDirect' => 'YandexDirect',
        'Googlebot' => 'Googlebot',
    );

    foreach ($engines as $key => $value) {
        if (stripos($u_agent, $key)) {
            return ($value);
        }
    }

    return false;
}

//
// Функция вызываемая по крону
function plugin_site_stats_cron()
{
    global $mysql;

    // Очищаем таблицу "site_stats" для пользователей
    $mysql->query('DELETE FROM ' . prefix . '_site_stats');
}

// Получение статистики сайта
function getSiteStats($templateName)
{

    global $config, $mysql, $lang;

    // Prepare keys for cacheing
    $site_exists = pluginGetVariable('site_stats', 'site_exists');
    $cacheKeys = [];
    $cacheKeys[] = $site_exists;
    $configKeys = array('static', 'category', 'news', 'news_na', 'comments', 'images', 'files', 'users', 'users_na', 'ipban');
    foreach($configKeys as $value){
        if(pluginGetVariable('site_stats', $value))
            $cacheKeys[] = $value;
    }

    // Generate cache file name [ we should take into account SWITCHER plugin ]
    $cacheEnabled = intval(pluginGetVariable('site_stats', 'cache'));
    $cacheExpire = intval(pluginGetVariable('site_stats', 'cacheExpire'));
    $cacheFileName = md5('site_stats'.$config['theme'].$templateName.$config['default_lang'].join('', $cacheKeys)).'.txt';

    if ($cacheEnabled and $cacheExpire > 0) {
        $cacheData = cacheRetrieveFile($cacheFileName, $cacheExpire, 'site_stats');
        if ($cacheData != false) {
            // We got data from cache. Return it and stop
            return $cacheData;
        }
    }

    if ($site_exists) {
        // Подсчет количества дней существования сайта
        $site_exists = explode('.', pluginGetVariable('site_stats', 'site_exists'));
        $site_exists = mktime(0, 0, 0, $site_exists[1], $site_exists[0], $site_exists[2]);
        $site_exists = floor((time() - $site_exists) / 86400);
        $stats['0']['count'] = $site_exists . ' ' . Padeg($site_exists, $lang['site_stats:day_skl']);
        $stats['0']['title'] = $lang['site_stats:site_exists'];
    }
    
    if (pluginGetVariable('site_stats', 'static')) {
        // Подсчет количества статических страниц
        $stats['1'] = $mysql->record("SELECT COUNT(*) AS count FROM " . prefix . "_static");
        $stats['1']['title'] = $lang['site_stats:static'];
    }

    if (pluginGetVariable('site_stats', 'category')) {
        // Подсчет количества категорий
        $stats['2'] = $mysql->record("SELECT COUNT(*) AS count FROM " . prefix . "_category");
        $stats['2']['title'] = $lang['site_stats:category'];
    }

    if (pluginGetVariable('site_stats', 'news')) {
        // Подсчет количества новостей
        $stats['3'] = $mysql->record("SELECT COUNT(*) AS count FROM " . prefix . "_news");
        $stats['3']['title'] = $lang['site_stats:news'];
    }

    if (pluginGetVariable('site_stats', 'news_na')) {
        // Подсчет неопубликованных новостей
        $stats['4'] = $mysql->record("SELECT COUNT(*) AS count FROM " . prefix . "_news WHERE approve='0'");
        $stats['4']['title'] = $lang['site_stats:news_na'];
    }

    if (pluginGetVariable('site_stats', 'comments') and getPluginStatusActive('comments')) {
        // Подсчет комментариев
        $stats['5'] = $mysql->record("SELECT COUNT(*) AS count FROM " . prefix . "_comments");
        $stats['5']['title'] = $lang['site_stats:comments'];
    }

    if (pluginGetVariable('site_stats', 'images')) {
        // Подсчет загруженных изображений
        $stats['6'] = $mysql->record("SELECT COUNT(*) AS count FROM " . prefix . "_images");
        $stats['6']['title'] = $lang['site_stats:images'];
    }

    if (pluginGetVariable('site_stats', 'files')) {
        // Подсчет загруженных файлов
        $stats['7'] = $mysql->record("SELECT COUNT(*) AS count FROM " . prefix . "_files");
        $stats['7']['title'] = $lang['site_stats:files'];
    }

    if (pluginGetVariable('site_stats', 'users')) {
        // Подсчет зарегестрированных пользователей
        $stats['8'] = $mysql->record("SELECT COUNT(*) AS count FROM " . prefix . "_users");
        $stats['8']['title'] = $lang['site_stats:users'];
    }

    if (pluginGetVariable('site_stats', 'users_na')) {
        // Подсчет неактивных пользователей
        $stats['9'] = $mysql->record("SELECT COUNT(*) AS count FROM " . prefix . "_users WHERE activation != ''");
        $stats['9']['title'] = $lang['site_stats:users_na'];
    }

    if (pluginGetVariable('site_stats', 'ipban')) {
        // Подсчет количества банов по айпи
        $stats['10'] = $mysql->record("SELECT COUNT(*) AS count FROM " . prefix . "_ipban");
        $stats['10']['title'] = $lang['site_stats:ipban'];
    }

    $cacheData = serialize($stats);

    if ($cacheEnabled and $cacheExpire > 0) {
        cacheStoreFile($cacheFileName, $cacheData, 'site_stats');
    }
    
    return $cacheData;
}

function site_stats()
{

    global $config, $mysql, $twig, $template, $lang, $userROW, $ip;

    $tpath = locatePluginTemplates(array('site_stats'), 'site_stats', pluginGetVariable('site_stats', 'localsource'));
    $templateName = $tpath['site_stats'] . 'site_stats.tpl';

    $time = time() + ($config['date_adjust'] * 60);
    $last_time = $time - (pluginGetVariable('site_stats', 'last_time') ? pluginGetVariable('site_stats', 'last_time') : 500);

    $bot = filter_bots($_SERVER['HTTP_USER_AGENT']);

    if (is_array($userROW)) {
        $id = '';
        $ips = 0;
        $users = $userROW['name'];
        $users_id = $userROW['id'];
        $users_status = $userROW['status'];
    } elseif ($bot) {
        $id = md5($bot);
        $ips = $ip;
        $users = $bot;
        $users_id = 0;
        $users_status = 5;
    } else {
        $id = session_id();
        $ips = $ip;
        $users = $lang['site_stats:guest'];
        $users_id = 0;
        $users_status = 0;
    }

    if ($result = $mysql->select('SELECT * FROM ' . prefix . '_site_stats')) {
        $user = array();
        $sess = array();
        $ipis = array();
        $count = count($result);
        if (isset($count) and ($count > 20000)) {
            $mysql->query('DELETE FROM ' . prefix . '_site_stats WHERE users_id = 0 ORDER BY last_time ASC LIMIT 10000');
        }

        foreach ($result as $row) {
            $user[$row['users_id']] = $row['last_time'];
            $sess[$row['sess_id']] = $row['last_time'];
            $ipis[$row['ip']] = $row['last_time'];
        }
        if (is_array($userROW)) {
            if ($user[$userROW['id']]) {
                if ($user[$userROW['id']] < $last_time) {
                    while ($count >= 0) {
                        $res = $result[$count];
                        if (isset($res['users_id']) and ($res['users_id'] == $userROW['id'])) {
                            $result[$count] = array('sess_id' => $id, 'last_time' => $time, 'ip' => $ips, 'users' => $userROW['name'], 'users_id' => $userROW['id'], 'users_status' => $userROW['status']);
                            $mysql->query('UPDATE ' . prefix . '_site_stats SET sess_id = ' . db_squote($id) . ', last_time = ' . db_squote($time) . ', ip = ' . db_squote($ips) . ', users_status = ' . db_squote($userROW['status']) . ' WHERE users_id = ' . db_squote($userROW['id']) . ' LIMIT 1');
                            break;
                        }
                        $count--;
                    }
                }
            } elseif ($ipis[$ip]) {
                while ($count >= 0) {
                    $res = $result[$count];
                    if (isset($res['ip']) and ($res['ip'] == $ip)) {
                        $result[$count] = array('sess_id' => $id, 'last_time' => $time, 'users' => $users, 'users_id' => $users_id, 'users_status' => $users_status, 'ip' => $ips);
                        $mysql->query('UPDATE ' . prefix . '_site_stats SET sess_id = ' . db_squote($id) . ', last_time = ' . intval($time) . ', users = ' . db_squote($userROW['name']) . ', users_id = ' . db_squote($userROW['id']) . ', users_status = ' . db_squote($userROW['status']) . ', ip = ' . db_squote($ips) . ' WHERE ip = ' . db_squote($ip) . ' LIMIT 1');
                        break;
                    }
                    $count--;
                }
            } else {
                $result[] = array('sess_id' => $id, 'last_time' => $time, 'ip' => $ips, 'users' => $userROW['name'], 'users_id' => $userROW['id'], 'users_status' => $userROW['status']);
                $mysql->query('INSERT INTO ' . prefix . '_site_stats (sess_id, last_time, ip, users, users_id, users_status) 
                    VALUES (' . db_squote($id) . ', ' . intval($time) . ', ' . db_squote($ips) . ', ' . db_squote($userROW['name']) . ', ' . db_squote($userROW['id']) . ', ' . db_squote($userROW['status']) . ')');
            }
        } else {
            if ($sess[$id]) {
                if ($sess[$id] < $last_time)
                    while ($count >= 0) {
                        $res = $result[$count];
                        if (isset($res['sess_id']) and ($res['sess_id'] == $id)) {
                            $result[$count] = array('sess_id' => $id, 'last_time' => $time, 'ip' => $ips, 'users' => $users, 'users_id' => $users_id, 'users_status' => $users_status);
                            $mysql->query('UPDATE ' . prefix . '_site_stats SET last_time = ' . intval($time) . ', ip = ' . db_squote($ips) . ' WHERE sess_id = ' . db_squote($id) . ' LIMIT 1');
                            break;
                        }
                        $count--;
                    }
            } else {
                if ($ipis[$ip]) {
                    if ($ipis[$ip] < $last_time) {
                        while ($count >= 0) {
                            $res = $result[$count];
                            if (isset($res['ip']) and ($res['ip'] == $ip)) {
                                $result[$count] = array('sess_id' => $id, 'last_time' => $time, 'ip' => $ips, 'users' => $users, 'users_id' => $users_id, 'users_status' => $users_status);
                                $mysql->query('UPDATE ' . prefix . '_site_stats SET sess_id = ' . db_squote($id) . ', last_time = ' . intval($time) . ' WHERE ip = ' . db_squote($ip) . ' LIMIT 1');
                                break;
                            }
                            $count--;
                        }
                    }
                } else {
                    $result[] = array('sess_id' => $id, 'last_time' => $time, 'ip' => $ips, 'users' => $users, 'users_id' => $users_id, 'users_status' => $users_status);
                    $mysql->query('INSERT INTO ' . prefix . '_site_stats (sess_id, last_time, ip, users, users_id, users_status) 
                        VALUES (' . db_squote($id) . ', ' . intval($time) . ', ' . db_squote($ips) . ', ' . db_squote($users) . ', ' . db_squote($users_id) . ', ' . db_squote($users_status) . ')');
                }
            }
        }
    } else {
        $result[] = array('sess_id' => $id, 'last_time' => $time, 'ip' => $ips, 'users' => $users, 'users_id' => $users_id, 'users_status' => $users_status);
        $mysql->query('INSERT INTO ' . prefix . '_site_stats (sess_id, last_time, ip, users, users_id, users_status) 
            VALUES (' . db_squote($id) . ', ' . intval($time) . ', ' . db_squote($ips) . ', ' . db_squote($users) . ', ' . db_squote($users_id) . ', ' . db_squote($users_status) . ')');
    }

    $num_guest = 0;
    $num_team = 0;
    $num_user = 0;
    $num_bot = 0;
    $num_today = 0;
    if (is_array($result)) {
        foreach ($result as $row) {
            $profile_link = checkLinkAvailable('uprofile', 'show') ?
                generateLink('uprofile', 'show', array('name' => $row['users'], 'id' => $row['users_id'])) :
                generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('id' => $row['users_id']));
            if ($row['last_time'] > $last_time) {
                if ($row['users_status'] == 0) {
                    $num_guest++;
                } elseif ($row['users_status'] == 1) {
                    $color_start = '<span style="color:red;">';
                    $color_end = '</span>';
                    $team_url[] = str_replace(array('{url}', '{name}', '{color_start}', '{color_end}'), array($profile_link, $row['users'], $color_start, $color_end), $lang['site_stats:team_url']);
                    $num_team++;
                } elseif ($row['users_status'] == 2) {
                    $color_start = '<span style="color:green;">';
                    $color_end = '</span>';
                    $team_url[] = str_replace(array('{url}', '{name}', '{color_start}', '{color_end}'), array($profile_link, $row['users'], $color_start, $color_end), $lang['site_stats:team_url']);
                    $num_team++;
                } elseif ($row['users_status'] == 3) {
                    $color_start = '<span style="color:blue;">';
                    $color_end = '</span>';
                    $team_url[] = str_replace(array('{url}', '{name}', '{color_start}', '{color_end}'), array($profile_link, $row['users'], $color_start, $color_end), $lang['site_stats:team_url']);
                    $num_team++;
                } elseif ($row['users_status'] == 4) {
                    $color_start = '<span style="color:#8b4500;">';
                    $color_end = '</span>';
                    $user_url[] = str_replace(array('{url}', '{name}', '{color_start}', '{color_end}'), array($profile_link, $row['users'], $color_start, $color_end), $lang['site_stats:user_url']);
                    $num_user++;
                } elseif ($row['users_status'] == 5) {
                    $bot_url[] = str_replace(array('{name}'), array($lang['site_stats:'.$row['users']]), $lang['site_stats:bot_url']);
                    $num_bot++;
                }
            }
            if ($row['users_status'] == 0) {
                $num_guest_today++;
            } elseif ($row['users_status'] == 1 or $row['users_status'] == 2 or $row['users_status'] == 3 or $row['users_status'] == 4) {
                switch ($row['users_status']) {
                    case 1:
                        $color_start = '<span style="color:red;">';
                        $color_end = '</span>';
                        break;
                    case 2:
                        $color_start = '<span style="color:green;">';
                        $color_end = '</span>';
                        break;
                    case 3:
                        $color_start = '<span style="color:blue;">';
                        $color_end = '</span>';
                        break;
                    default:
                        $color_start = '<span style="color:#8b4500;">';
                        $color_end = '</span>';
                }
                $last_date = date('H:i:s', intval($row['last_time']));
                $num_today++;
                $today_users[] = str_replace(array('{url}', '{name}', '{date}', '{color_start}', '{color_end}'), array($profile_link, $row['users'], $last_date, $color_start, $color_end), $lang['site_stats:today_url']);
            } elseif ($row['users_status'] == 5) {
                $last_date = date('H:i:s', intval($row['last_time']));
                $num_today++;
                $today_users[] = str_replace(array('{name}', '{date}'), array($lang['site_stats:'.$row['users']], $last_date), $lang['site_stats:today_bot_url']);
            }
        }
    }

    $online[] = array(
        'title' => $lang['site_stats:online_all'],
        'count' => $num_guest + $num_team + $num_user + $num_bot,
        );
    if($num_guest) {
        $online[] = array(
            'title' => $lang['site_stats:online_guest'],
            'count' => $num_guest,
            );
    }
    if($num_team) {
        $online[] = array(
            'title' => $lang['site_stats:team_list'],
            'count' => $num_team,
            'content' => implode(", ", $team_url),
            );
    }
    if($num_user) {
        $online[] = array(
            'title' => $lang['site_stats:user_list'],
            'count' => $num_user,
            'content' => implode(", ", $user_url),
            );
    }
    if($num_bot) {
        $online[] = array(
            'title' => $lang['site_stats:bot_list'],
            'count' => $num_bot,
            'content' => implode(", ", $bot_url),
            );
    }
    if($num_today) {
        $online[] = array(
            'title' => $lang['site_stats:today_list'],
            'count' => $num_today,
            'content' => implode(", ", $today_users),
            );
    }

    $count = count($online);
    
    for ($i = 0; $i < $count; $i++) {
        if (is_array($online[$i])) {
            $tVars['online'][] = array(
                'title' => $online[$i]['title'],
                'count' => $online[$i]['count'],
                'content' => $online[$i]['content'],
            );
        }
    }

    $stats = unserialize(getSiteStats($templateName));
    $count = $stats ? count($stats) : [];

    $outNW = intval(pluginGetVariable('site_stats', 'outNW'));
    
    for ($i = 0; $i<=10; $i++) {
        if (is_array($stats[$i])) {
            if ($stats[$i]['count'] == '0') {
                switch ($outNW) {
                    case 0:
                        $stats[$i]['count'] = '0';
                        break;
                    case 1:
                        $stats[$i]['count'] = $lang['noa'];
                        break;
                }
            }

            $tVars['stats'][] = array(
                'title' => $stats[$i]['title'],
                'count' => $stats[$i]['count'],
            );
        }
    }
    
    $xt = $twig->loadTemplate($templateName);
    $template['vars']['site_stats'] = $xt->render($tVars);
}
