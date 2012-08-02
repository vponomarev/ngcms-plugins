<?php

// #====================================================================================#
// # ������������ �������: nsched [ News SCHEDuller ]                                   #
// # ��������� � ������������� �: Next Generation CMS                                   #
// # �����: Vitaly A Ponomarev, vp7@mail.ru                                             #
// #====================================================================================#

// #====================================================================================#
// # ���� �������                                                                       #
// #====================================================================================#
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');

class NSchedNewsFilter extends NewsFilter {
	function addNewsForm(&$tvars) {
		$perm = checkPermission(array('plugin' => '#admin', 'item' => 'news'), null, array('personal.publish', 'personal.unpublish', 'other.publish', 'other.unpublish'));

		$tvars['plugin']['nsched']  = '';
		if ($perm['personal.publish'] || $perm['personal.unpublish']) {
			$tvars['plugin']['nsched'] .= '<tr><td width="100%" class="contentHead"><img src="'.admin_url.'/skins/default/images/nav.gif" hspace="8" alt="" />���������� ����������� ��������</td></tr><tr><td width="100%" class="contentEntry1"><table>';
			if ($perm['personal.publish']) {
				$tvars['plugin']['nsched'] .= '<tr><td width="100%" class="contentEntry1"><table><tr><td>���� ���������:</td><td><input name="nsched_activate" /> <small>( � ������� ����-��-�� ��:�� )</small></td></tr>';
			}
			if ($perm['personal.unpublish']) {
				$tvars['plugin']['nsched'] .= '<tr><td>���� ����������:</td><td><input name="nsched_deactivate" /> <small>( � ������� ����-��-�� ��:�� )</small></td></tr>';
			}
			$tvars['plugin']['nsched'] .= '</table></td></tr>';

		}

		return 1;
	}
	function addNews(&$tvars, &$SQL) {
		$perm = checkPermission(array('plugin' => '#admin', 'item' => 'news'), null, array('personal.publish', 'personal.unpublish', 'other.publish', 'other.unpublish'));
		if ($perm['personal.publish'])
			$SQL['nsched_activate']   = $_REQUEST['nsched_activate'];

		if ($perm['personal.unpublish'])
			$SQL['nsched_deactivate'] = $_REQUEST['nsched_deactivate'];
		return 1;
	}
	function editNewsForm($newsID, $SQLold, &$tvars) {
		global $userROW;

		$perm = checkPermission(array('plugin' => '#admin', 'item' => 'news'), null, array('personal.publish', 'personal.unpublish', 'other.publish', 'other.unpublish'));
		$isOwn = ($SQLold['author_id'] == $userROW['id'])?1:0;
		$permGroupMode = $isOwn?'personal':'other';

		$ndeactivate = $SQLold['nsched_deactivate'];
		$nactivate   = $SQLold['nsched_activate'];
		if ($nactivate   == '0000-00-00 00:00:00') { $nactivate   = ''; }
		if ($ndeactivate == '0000-00-00 00:00:00') { $ndeactivate = ''; }

		$tvars['plugin']['nsched']  = '';
		if ($perm[$permGroupMode.'.publish'] || $perm[$permGroupMode.'.unpublish']) {
			$tvars['plugin']['nsched'] .= '<tr><td width="100%" class="contentHead"><img src="'.admin_url.'/skins/default/images/nav.gif" hspace="8" alt="" />���������� ����������� ��������</td></tr><tr><td width="100%" class="contentEntry1"><table>';

			if ($perm[$permGroupMode.'.publish']) {
				$tvars['plugin']['nsched'] .= '<tr><td>���� ���������:</td><td><input name="nsched_activate" value="'.$nactivate.'" /> <small>( � ������� ����-��-�� ��:�� )</small></td></tr>';
			}

			if ($perm[$permGroupMode.'.unpublish']) {
				$tvars['plugin']['nsched'] .= '<tr><td>���� ����������:</td><td><input name="nsched_deactivate" value="'.$ndeactivate.'" /> <small>( � ������� ����-��-�� ��:�� )</small></td></tr>';
			}
			$tvars['plugin']['nsched'] .= '</table></td></tr>';
		}
		return 1;
	}
	function editNews($newsID, $SQLold, &$SQLnew, &$tvars) {
		global $userROW;

		$perm = checkPermission(array('plugin' => '#admin', 'item' => 'news'), null, array('personal.publish', 'personal.unpublish', 'other.publish', 'other.unpublish'));
		$isOwn = ($SQLold['author_id'] == $userROW['id'])?1:0;
		$permGroupMode = $isOwn?'personal':'other';

		if ($perm[$permGroupMode.'.publish'])
			$SQLnew['nsched_activate']   = $_REQUEST['nsched_activate'];

		if ($perm[$permGroupMode.'.unpublish'])
			$SQLnew['nsched_deactivate'] = $_REQUEST['nsched_deactivate'];

		return 1;
	}


}

register_filter('news','nsched', new NSchedNewsFilter);

//add_act('cron_nsched', 'plugin_nsched');

//
// ������� ���������� �� �����
//
function plugin_nsched_cron() {
	global $mysql, $catz, $catmap;

	// ������ �������� ��� (��)���������
	$listActivate   = array();
	$dataActivate	= array();

	$listDeactivate = array();
	$dataDeactivate	= array();

	// �������� ������� ��� ������� �������� ���� "������������ �� ����"
	foreach ($mysql->select("select * from ".prefix."_news where (nsched_activate>0) and (nsched_activate <= now())") as $row) {
		$listActivate[] = $row['id'];
		$dataActivate[$row['id']] = $row;
		//$mysql->query("update ".prefix."_news set approve=1, nsched_activate=0 where id = ".$row['id']);
	}
	// �������� ������� ��� ������� �������� ���� "����� ���������� �� ����"
	foreach ($mysql->select("select * from ".prefix."_news where (nsched_deactivate>0) and (nsched_deactivate <= now())") as $row) {
		$listDeactivate[] = $row['id'];
		$dataDeactivate[$row['id']] = $row;
		//$mysql->query("update ".prefix."_news set approve=0, nsched_deactivate=0 where id = ".$row['id']);
	}

	// ���������, ���� �� ������� ��� (��)���������
	if (count($listActivate) || count($listDeactivate)) {
		// ��������� ����������� �������
		loadActionHandlers('admin');
		loadActionHandlers('admin:mod:editnews');

		// ��������� ��������� ����������
		require_once(root.'includes/inc/lib_admin.php');

		// ��������� ����������� ��������
		if (count($listActivate)) {
			massModifyNews(array('data' => $dataActivate), array('approve' => 1, 'nsched_activate' => ''), false);
		}
		if (count($listDeactivate)) {
			massModifyNews(array('data' => $dataDeactivate), array('approve' => 0, 'nsched_deactivate' => ''), false);

		}
	}
}

