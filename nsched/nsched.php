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
if (!defined('NGCMS')) die ('HAL');

class NSchedNewsFilter extends NewsFilter {

	function addNewsForm(&$tvars) {

		$perm = checkPermission(array('plugin' => '#admin', 'item' => 'news'), null, array('personal.publish', 'personal.unpublish', 'other.publish', 'other.unpublish'));
		$tvars['plugin']['nsched'] = '';
		if ($perm['personal.publish'] || $perm['personal.unpublish']) {
			$tvars['plugin']['nsched'] .= '<tr><td width="100%" class="contentHead"><img src="' . admin_url . '/skins/default/images/nav.gif" hspace="8" alt="" />Управление публикацией новостей</td></tr><tr><td width="100%" class="contentEntry1"><table>';
			if ($perm['personal.publish']) {
				$tvars['plugin']['nsched'] .= '<tr><td>Дата включения:</td><td><input id="nsched_activate" name="nsched_activate" /> <small>( в формате ГГГГ-ММ-ДД ЧЧ:ММ )</small></td></tr>';
			}
			if ($perm['personal.unpublish']) {
				$tvars['plugin']['nsched'] .= '<tr><td>Дата отключения:</td><td><input id="nsched_deactivate" name="nsched_deactivate"/> <small>( в формате ГГГГ-ММ-ДД ЧЧ:ММ )</small></td></tr>';
			}
			$tvars['plugin']['nsched'] .= '</table></td></tr><script language="javascript" type="text/javascript">' . "$('#nsched_activate').datetimepicker({ dateFormat: 'yy-mm-dd', timeFormat: 'hh:mm'});$('#nsched_deactivate').datetimepicker({ dateFormat: 'yy-mm-dd', timeFormat: 'hh:mm'});</script>";
		}

		return 1;
	}

	function addNews(&$tvars, &$SQL) {

		$perm = checkPermission(array('plugin' => '#admin', 'item' => 'news'), null, array('personal.publish', 'personal.unpublish', 'other.publish', 'other.unpublish'));
		if ($perm['personal.publish'])
			$SQL['nsched_activate'] = $_REQUEST['nsched_activate'];
		if ($perm['personal.unpublish'])
			$SQL['nsched_deactivate'] = $_REQUEST['nsched_deactivate'];

		return 1;
	}

	function editNewsForm($newsID, $SQLold, &$tvars) {

		global $userROW;
		$perm = checkPermission(array('plugin' => '#admin', 'item' => 'news'), null, array('personal.publish', 'personal.unpublish', 'other.publish', 'other.unpublish'));
		$isOwn = ($SQLold['author_id'] == $userROW['id']) ? 1 : 0;
		$permGroupMode = $isOwn ? 'personal' : 'other';
		$ndeactivate = $SQLold['nsched_deactivate'];
		$nactivate = $SQLold['nsched_activate'];
		if ($nactivate == '0000-00-00 00:00') {
			$nactivate = '';
		}
		if ($ndeactivate == '0000-00-00 00:00') {
			$ndeactivate = '';
		}
		$tvars['plugin']['nsched'] = '';
		if ($perm[$permGroupMode . '.publish'] || $perm[$permGroupMode . '.unpublish']) {
			$tvars['plugin']['nsched'] .= '<tr><td width="100%" class="contentHead"><img src="' . admin_url . '/skins/default/images/nav.gif" hspace="8" alt="" />Управление публикацией новостей</td></tr><tr><td width="100%" class="contentEntry1"><table>';
			if ($perm[$permGroupMode . '.publish']) {
				$tvars['plugin']['nsched'] .= '<tr><td>Дата включения:</td><td><input name="nsched_activate" id="nsched_activate" value="' . $nactivate . '" /> <small>( в формате ГГГГ-ММ-ДД ЧЧ:ММ )</small></td></tr>';
			}
			if ($perm[$permGroupMode . '.unpublish']) {
				$tvars['plugin']['nsched'] .= '<tr><td>Дата отключения:</td><td><input name="nsched_deactivate" id="nsched_deactivate" value="' . $ndeactivate . '" /> <small>( в формате ГГГГ-ММ-ДД ЧЧ:ММ )</small></td></tr>';
			}
			$tvars['plugin']['nsched'] .= '</table></td></tr><script language="javascript" type="text/javascript">' . "$('#nsched_activate').datetimepicker({ dateFormat: 'yy-mm-dd', timeFormat: 'hh:mm', currentText: '" . $nactivate . "'});$('#nsched_deactivate').datetimepicker({ dateFormat: 'yy-mm-dd', timeFormat: 'hh:mm', currentText: '" . $ndeactivate . "'});</script>";
		}

		return 1;
	}

	function editNews($newsID, $SQLold, &$SQLnew, &$tvars) {

		global $userROW;
		$perm = checkPermission(array('plugin' => '#admin', 'item' => 'news'), null, array('personal.publish', 'personal.unpublish', 'other.publish', 'other.unpublish'));
		$isOwn = ($SQLold['author_id'] == $userROW['id']) ? 1 : 0;
		$permGroupMode = $isOwn ? 'personal' : 'other';
		if ($perm[$permGroupMode . '.publish'])
			$SQLnew['nsched_activate'] = $_REQUEST['nsched_activate'];
		if ($perm[$permGroupMode . '.unpublish'])
			$SQLnew['nsched_deactivate'] = $_REQUEST['nsched_deactivate'];

		return 1;
	}
}

register_filter('news', 'nsched', new NSchedNewsFilter);
//add_act('cron_nsched', 'plugin_nsched');
//
// Функция вызываемая по крону
//
function plugin_nsched_cron() {

	global $mysql, $catz, $catmap;
	// Список новостей для (де)активации
	$listActivate = array();
	$dataActivate = array();
	$listDeactivate = array();
	$dataDeactivate = array();
	// Выбираем новости для которых сработал флаг "опубликовать по дате"
	foreach ($mysql->select("select * from " . prefix . "_news where (nsched_activate>0) and (nsched_activate <= now())") as $row) {
		$listActivate[] = $row['id'];
		$dataActivate[$row['id']] = $row;
		//$mysql->query("update ".prefix."_news set approve=1, nsched_activate=0 where id = ".$row['id']);
	}
	// Выбираем новости для которых сработал флаг "снять публикацию по дате"
	foreach ($mysql->select("select * from " . prefix . "_news where (nsched_deactivate>0) and (nsched_deactivate <= now())") as $row) {
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
		require_once(root . 'includes/inc/lib_admin.php');
		// Запускаем модификацию новостей
		if (count($listActivate)) {
			massModifyNews(array('data' => $dataActivate), array('approve' => 1, 'nsched_activate' => ''), false);
		}
		if (count($listDeactivate)) {
			massModifyNews(array('data' => $dataDeactivate), array('approve' => 0, 'nsched_deactivate' => ''), false);
		}
	}
}
