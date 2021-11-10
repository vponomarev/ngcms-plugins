<?php

// #====================================================================================#
// # Наименование плагина: nsched [ News SCHEDuller ]                                   #
// # Разрешено к использованию с: Next Generation CMS                                   #
// # Автор: Vitaly A Ponomarev, vp7@mail.ru                                             #
// #====================================================================================#
// #====================================================================================#
// # Ядро плагина                                                                       #
// #====================================================================================#

// Protect against hack attempts
if (! defined('NGCMS')) {
    die('HAL');
}

class NSchedNewsFilter extends NewsFilter
{
    public const EMPTY_DATETIME = '0000-00-00 00:00:00';

    public function addNewsForm(&$tvars)
    {
        /** @var Twig\Environment $twig */
        global $twig;

        $perm = checkPermission(
            [
                'plugin' => '#admin',
                'item' => 'news',
            ],
            null,
            [
                'personal.publish',
                'personal.unpublish',
                'other.publish',
                'other.unpublish',
            ]
        );

        $tvars['plugin']['nsched'] = '';

        if ($perm['personal.publish'] || $perm['personal.unpublish']) {
            $tvars['plugin']['nsched'] = $twig->render(
                'plugins/nsched/tpl/add_news.tpl',
                [
                    'flags' => [
                        'can_publish' => (bool) $perm['personal.publish'],
                        'can_unpublish' => (bool) $perm['personal.unpublish'],
                    ],
                ]
            );
        }

        return 1;
    }

    public function addNews(&$tvars, &$SQL)
    {
        $perm = checkPermission(
            [
                'plugin' => '#admin',
                'item' => 'news',
            ],
            null,
            [
                'personal.publish',
                'personal.unpublish',
                'other.publish',
                'other.unpublish',
            ]
        );

        if ($perm['personal.publish']) {
            $SQL['nsched_activate'] = $_REQUEST['nsched_activate'];
        }

        if ($perm['personal.unpublish']) {
            $SQL['nsched_deactivate'] = $_REQUEST['nsched_deactivate'];
        }

        return 1;
    }

    public function editNewsForm($newsID, $SQLold, &$tvars)
    {
        /** @var Twig\Environment $twig */
        global $twig, $userROW;

        $perm = checkPermission(
            [
                'plugin' => '#admin',
                'item' => 'news',
            ],
            null,
            [
                'personal.publish',
                'personal.unpublish',
                'other.publish',
                'other.unpublish',
            ]
        );

        $isOwn = ($SQLold['author_id'] == $userROW['id']) ? 1 : 0;
        $permGroupMode = $isOwn ? 'personal' : 'other';

        $ndeactivate = $SQLold['nsched_deactivate'];
        $nactivate = $SQLold['nsched_activate'];

        if (self::EMPTY_DATETIME === $nactivate) {
            $nactivate = '';
        }

        if (self::EMPTY_DATETIME === $ndeactivate) {
            $ndeactivate = '';
        }

        $tvars['plugin']['nsched'] = '';

        if ($perm[$permGroupMode.'.publish'] || $perm[$permGroupMode.'.unpublish']) {
            $tvars['plugin']['nsched'] = $twig->render(
                'plugins/nsched/tpl/edit_news.tpl',
                [
                    'nsched_activate' => $nactivate,
                    'nsched_deactivate' => $ndeactivate,
                    'flags' => [
                        'can_publish' => (bool) $perm[$permGroupMode.'.publish'],
                        'can_unpublish' => (bool) $perm[$permGroupMode.'.unpublish'],
                    ],
                ]
            );
        }

        return 1;
    }

    public function editNews($newsID, $SQLold, &$SQLnew, &$tvars)
    {
        global $userROW;

        $perm = checkPermission([
            'plugin' => '#admin',
            'item' => 'news',
        ], null, [
            'personal.publish',
            'personal.unpublish',
            'other.publish',
            'other.unpublish',
        ]);

        $isOwn = ($SQLold['author_id'] == $userROW['id']) ? 1 : 0;
        $permGroupMode = $isOwn ? 'personal' : 'other';

        if ($perm[$permGroupMode.'.publish']) {
            $SQLnew['nsched_activate'] = $_REQUEST['nsched_activate'];
        }

        if ($perm[$permGroupMode.'.unpublish']) {
            $SQLnew['nsched_deactivate'] = $_REQUEST['nsched_deactivate'];
        }

        return 1;
    }
}

register_filter('news', 'nsched', new NSchedNewsFilter);
//add_act('cron_nsched', 'plugin_nsched');

//
// Функция вызываемая по крону
//
function plugin_nsched_cron()
{
    global $mysql, $catz, $catmap;

    // Список новостей для (де)активации
    $listActivate = [];
    $dataActivate = [];
    $listDeactivate = [];
    $dataDeactivate = [];

    // Выбираем новости для которых сработал флаг "опубликовать по дате"
    foreach ($mysql->select('select * from '.prefix.'_news where (nsched_activate>0) and (nsched_activate <= now())') as $row) {
        $listActivate[] = $row['id'];
        $dataActivate[$row['id']] = $row;
        //$mysql->query("update ".prefix."_news set approve=1, nsched_activate=0 where id = ".$row['id']);
    }

    // Выбираем новости для которых сработал флаг "снять публикацию по дате"
    foreach ($mysql->select('select * from '.prefix.'_news where (nsched_deactivate>0) and (nsched_deactivate <= now())') as $row) {
        $listDeactivate[] = $row['id'];
        $dataDeactivate[$row['id']] = $row;
        //$mysql->query("update ".prefix."_news set approve=0, nsched_deactivate=0 where id = ".$row['id']);
    }

    // Проверяем, есть ли новости для (де)активации
    if (count($listActivate) || count($listDeactivate)) {
        // Загружаем необходимые плагины
        loadActionHandlers('admin');
        loadActionHandlers('admin:mod:editnews');

        // Загружаем системную библиотеку
        require_once root.'includes/inc/lib_admin.php';

        // Запускаем модификацию новостей
        if (count($listActivate)) {
            massModifyNews(
                [
                    'data' => $dataActivate,
                ],
                [
                    'approve' => 1,
                    'nsched_activate' => '',
                ],
                false
            );
        }

        if (count($listDeactivate)) {
            massModifyNews(
                [
                    'data' => $dataDeactivate,
                ],
                [
                    'approve' => 0,
                    'nsched_deactivate' => '',
                ],
                false
            );
        }
    }
}
