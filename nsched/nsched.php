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

    public const PERMISSION_IDENTIFIER = [
        'plugin' => '#admin',
        'item' => 'news',
    ];

    public function addNewsForm(&$tvars)
    {
        /** @var Twig\Environment $twig */
        global $twig;

        $permissions = $this->permissions('personal', [
            'publish',
            'unpublish',
        ]);

        $tvars['plugin']['nsched'] = '';

        if ($permissions['personal.publish'] || $permissions['personal.unpublish']) {
            $tvars['plugin']['nsched'] = $twig->render(
                'plugins/nsched/tpl/add_news.tpl',
                [
                    'flags' => [
                        'can_publish' => (bool) $permissions['personal.publish'],
                        'can_unpublish' => (bool) $permissions['personal.unpublish'],
                    ],
                ]
            );
        }

        return 1;
    }

    public function addNews(&$tvars, &$SQL)
    {
        $permissions = $this->permissions('personal', [
            'publish',
            'unpublish',
        ]);

        if ($permissions['personal.publish']) {
            $SQL['nsched_activate'] = $_REQUEST['nsched_activate'];
        }

        if ($permissions['personal.unpublish']) {
            $SQL['nsched_deactivate'] = $_REQUEST['nsched_deactivate'];
        }

        return 1;
    }

    public function editNewsForm($newsID, $SQLold, &$tvars)
    {
        /** @var Twig\Environment $twig */
        global $twig, $userROW;

        $permissionGroup = $SQLold['author_id'] == $userROW['id'] ? 'personal' : 'other';

        $permissions = $this->permissions($permissionGroup, [
            'publish',
            'unpublish',
        ]);

        $nactivate = $SQLold['nsched_activate'];
        $ndeactivate = $SQLold['nsched_deactivate'];

        if (self::EMPTY_DATETIME === $nactivate) {
            $nactivate = '';
        }

        if (self::EMPTY_DATETIME === $ndeactivate) {
            $ndeactivate = '';
        }

        $tvars['plugin']['nsched'] = '';

        if ($permissions[$permissionGroup.'.publish'] || $permissions[$permissionGroup.'.unpublish']) {
            $tvars['plugin']['nsched'] = $twig->render(
                'plugins/nsched/tpl/edit_news.tpl',
                [
                    'nsched_activate' => $nactivate,
                    'nsched_deactivate' => $ndeactivate,
                    'flags' => [
                        'can_publish' => (bool) $permissions[$permissionGroup.'.publish'],
                        'can_unpublish' => (bool) $permissions[$permissionGroup.'.unpublish'],
                    ],
                ]
            );
        }

        return 1;
    }

    public function editNews($newsID, $SQLold, &$SQLnew, &$tvars)
    {
        global $userROW;

        $permissionGroup = $SQLold['author_id'] == $userROW['id'] ? 'personal' : 'other';

        $permissions = $this->permissions($permissionGroup, [
            'publish',
            'unpublish',
        ]);

        if ($permissions[$permissionGroup.'.publish']) {
            $SQLnew['nsched_activate'] = $_REQUEST['nsched_activate'];
        }

        if ($permissions[$permissionGroup.'.unpublish']) {
            $SQLnew['nsched_deactivate'] = $_REQUEST['nsched_deactivate'];
        }

        return 1;
    }

    private function permissions(
        string $group, array $actions, array $user = null
    ): array {
        return checkPermission(
            self::PERMISSION_IDENTIFIER,
            $user,
            array_map(function(string $action) use ($group) {
                return sprintf('%s.%s', $group, $action);
            }, $actions)
        );
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
