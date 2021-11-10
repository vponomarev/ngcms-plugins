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
    public function addNewsForm(&$tvars)
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

        $tvars['plugin']['nsched'] = '';

        if ($perm['personal.publish'] || $perm['personal.unpublish']) {
            $tvars['plugin']['nsched'] .= '<tbody><tr class="thead-dark">
                    <th scope="col">Управление публикацией новостей</th>
                </tr>';

            if ($perm['personal.publish']) {
                $tvars['plugin']['nsched'] .= '<tr>
                        <td>Дата включения:</td>
                        <td><input  id="nsched_activate" name="nsched_activate" class="form-control" pattern="[0-9]{2}\.[0-9]{2}\.[0-9]{4} [0-9]{2}:[0-9]{2}"/> <small>( в формате ГГГГ.ММ.ДД ЧЧ:ММ )</small></td>
                    </tr>';
            }
            if ($perm['personal.unpublish']) {
                $tvars['plugin']['nsched'] .= '<tr>
                        <td>Дата отключения:</td>
                        <td><input  id="nsched_deactivate" name="nsched_deactivate" class="form-control" pattern="[0-9]{2}\.[0-9]{2}\.[0-9]{4} [0-9]{2}:[0-9]{2}"/> <small>( в формате ГГГГ.ММ.ДД ЧЧ:ММ )</small></td>
                    </tr>';
            }
            $tvars['plugin']['nsched'] .= '</tbody>
                <script language="javascript" type="text/javascript">'
                    ."$('#nsched_activate').datetimepicker({ dateFormat: 'yy.mm.dd', timeFormat: 'hh:mm'});$('#nsched_deactivate').datetimepicker({ dateFormat: 'yy.mm.dd', timeFormat: 'hh:mm'});
                </script>";
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
        global $userROW;

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

        if ($nactivate == '0000.00.00 00:00') {
            $nactivate = '';
        }

        if ($ndeactivate == '0000.00.00 00:00') {
            $ndeactivate = '';
        }

        $tvars['plugin']['nsched'] = '';

        if ($perm[$permGroupMode.'.publish'] || $perm[$permGroupMode.'.unpublish']) {
            $tvars['plugin']['nsched'] .= '<tbody><tr class="thead-dark">
                    <th scope="col">Управление публикацией новостей</th>
                </tr>';

            if ($perm[$permGroupMode.'.publish']) {
                $tvars['plugin']['nsched'] .= '<tr>
                        <td>Дата включения:</td>
                        <td><input  name="nsched_activate" id="nsched_activate" class="form-control" pattern="[0-9]{2}\.[0-9]{2}\.[0-9]{4} [0-9]{2}:[0-9]{2}" value="'.$nactivate.'" /> <small>( в формате ГГГГ.ММ.ДД ЧЧ:ММ )</small></td>
                    </tr>';
            }

            if ($perm[$permGroupMode.'.unpublish']) {
                $tvars['plugin']['nsched'] .= '<tr>
                        <td>Дата отключения:</td>
                        <td><input  name="nsched_deactivate" id="nsched_deactivate" class="form-control" pattern="[0-9]{2}\.[0-9]{2}\.[0-9]{4} [0-9]{2}:[0-9]{2}" value="'.$ndeactivate.'" /> <small>( в формате ГГГГ.ММ.ДД ЧЧ:ММ )</small></td>
                    </tr>';
            }

            $tvars['plugin']['nsched'] .= '</tbody>
                <script language="javascript" type="text/javascript">'
                    ."$('#nsched_activate').datetimepicker({ dateFormat: 'yy.mm.dd', timeFormat: 'hh:mm', currentText: '".$nactivate."'});$('#nsched_deactivate').datetimepicker({ dateFormat: 'yy.mm.dd', timeFormat: 'hh:mm', currentText: '".$ndeactivate."'});
                </script>";
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
