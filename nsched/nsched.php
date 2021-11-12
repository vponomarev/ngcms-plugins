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

use DateTime;

class NSchedNewsFilter extends NewsFilter
{
    public const EMPTY_DATETIME = '0';
    public const FORMAT_DATETIME = 'd.m.Y H:i';

    public const PERMISSION_IDENTIFIER = [
        'plugin' => '#admin',
        'item' => 'news',
    ];

    private $currentDate;

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
                    'format_datetime' => self::FORMAT_DATETIME,
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
            $publishDate = DateTime::createFromFormat(
                self::FORMAT_DATETIME, $_REQUEST['nsched_activate']
            );

            if ($publishDate && $publishDate > $this->currentDate()) {
                $SQL['nsched_activate'] = $publishDate->getTimestamp();

                if (pluginGetVariable('nsched', 'sync_dates')) {
                    $SQL['postdate'] = $SQL['nsched_activate'];
                    $SQL['editdate'] = $SQL['nsched_activate'];
                }
            } else {
                $SQL['nsched_activate'] = self::EMPTY_DATETIME;
            }
        }

        if ($permissions['personal.unpublish']) {
            $unpublishDate = DateTime::createFromFormat(
                self::FORMAT_DATETIME, $_REQUEST['nsched_deactivate']
            );

            if ($unpublishDate && $unpublishDate > $this->currentDate()) {
                $SQLnew['nsched_deactivate'] = $unpublishDate->getTimestamp();
            } else {
                $SQLnew['nsched_deactivate'] = self::EMPTY_DATETIME;
            }
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

        $nactivate = '';
        $ndeactivate = '';

        if ($SQLold['nsched_activate']) {
            $nactivate = (new DateTime)
                ->setTimestamp($SQLold['nsched_activate'])
                ->format(self::FORMAT_DATETIME);
        }

        if ($SQLold['nsched_deactivate']) {
            $ndeactivate = (new DateTime)
                ->setTimestamp($SQLold['nsched_deactivate'])
                ->format(self::FORMAT_DATETIME);
        }

        $tvars['plugin']['nsched'] = '';

        if ($permissions[$permissionGroup.'.publish'] || $permissions[$permissionGroup.'.unpublish']) {
            $tvars['plugin']['nsched'] = $twig->render(
                'plugins/nsched/tpl/edit_news.tpl',
                [
                    'nsched_activate' => $nactivate,
                    'nsched_deactivate' => $ndeactivate,
                    'format_datetime' => self::FORMAT_DATETIME,
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
            $publishDate = DateTime::createFromFormat(
                self::FORMAT_DATETIME, $_REQUEST['nsched_activate']
            );

            if ($publishDate && $publishDate > $this->currentDate()) {
                $SQLnew['nsched_activate'] = $publishDate->getTimestamp();

                if (pluginGetVariable('nsched', 'sync_dates')) {
                    $SQLnew['postdate'] = $SQLnew['nsched_activate'];
                    $SQLnew['editdate'] = $SQLnew['nsched_activate'];
                }
            } else {
                $SQLnew['nsched_activate'] = self::EMPTY_DATETIME;
            }
        }

        if ($permissions[$permissionGroup.'.unpublish']) {
            $unpublishDate = DateTime::createFromFormat(
                self::FORMAT_DATETIME, $_REQUEST['nsched_deactivate']
            );

            if ($unpublishDate && $unpublishDate > $this->currentDate()) {
                $SQLnew['nsched_deactivate'] = $unpublishDate->getTimestamp();
            } else {
                $SQLnew['nsched_deactivate'] = self::EMPTY_DATETIME;
            }
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

    private function currentDate(): DateTime
    {
        return $this->currentDate
            ?? $this->currentDate = new DateTime;
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
    foreach ($mysql->select('select * from '.prefix.'_news where (nsched_activate>0) and (nsched_activate <= unix_timestamp())') as $row) {
        $listActivate[] = $row['id'];
        $dataActivate[$row['id']] = $row;
        //$mysql->query("update ".prefix."_news set approve=1, nsched_activate=0 where id = ".$row['id']);
    }

    // Выбираем новости для которых сработал флаг "снять публикацию по дате"
    foreach ($mysql->select('select * from '.prefix.'_news where (nsched_deactivate>0) and (nsched_deactivate <= unix_timestamp())') as $row) {
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
