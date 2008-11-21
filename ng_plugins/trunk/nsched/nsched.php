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
		$tvars['vars']['nsched']  = '<tr><td width="100%" class="contentHead"><img src="'.admin_url.'/skins/default/images/nav.gif" hspace="8" alt="" />���������� ����������� ��������</td></tr>';
		$tvars['vars']['nsched'] .= '<tr><td width="100%" class="contentEntry1"><table><tr><td>���� ���������:</td><td><input name="nsched_activate" /> <small>( � ������� ����-��-�� ��:�� )</small></td></tr><tr><td>���� ����������:</td><td><input name="nsched_deactivate" /></td></tr></table></td></tr>';
		return 1;
	}
	function addNews(&$tvars, &$SQL) { 
	        $SQL['nsched_activate']   = $_REQUEST['nsched_activate'];
		$SQL['nsched_deactivate'] = $_REQUEST['nsched_deactivate']; 	
		return 1;
	}
	function editNewsForm($newsID, $SQLold, &$tvars) {
		$ndeactivate = $SQLold['nsched_deactivate'];
		$nactivate   = $SQLold['nsched_activate'];
		if ($nactivate   == '0000-00-00 00:00:00') { $nactivate   = ''; }
		if ($ndeactivate == '0000-00-00 00:00:00') { $ndeactivate = ''; }
		$tvars['vars']['nsched']  = '<tr><td width="100%" class="contentHead"><img src="'.admin_url.'/skins/default/images/nav.gif" hspace="8" alt="" />���������� ����������� ��������</td></tr>';
		$tvars['vars']['nsched'] .= '<tr><td width="100%" class="contentEntry1"><table><tr><td>���� ���������:</td><td><input name="nsched_activate" value="'.$nactivate.'" /> <small>( � ������� ����-��-�� ��:�� )</small></td></tr><tr><td>���� ����������:</td><td><input name="nsched_deactivate" value="'.$ndeactivate.'" /></td></tr></table></td></tr>';
		return 1;
	}
	function editNews($newsID, $SQLold, &$SQLnew, &$tvars) { 
	        $SQLnew['nsched_activate']   = $_REQUEST['nsched_activate'];
		$SQLnew['nsched_deactivate'] = $_REQUEST['nsched_deactivate']; 	
		return 1; 
	}


}

register_filter('news','nsched', new NSchedNewsFilter);

add_act('cron_nsched', 'plugin_nsched');

//
// ������� ���������� �� �����
//
function plugin_nsched() {
 global $mysql;
 
 // �������� ������� ��� ������� �������� ���� "������������ �� ����"
 foreach ($mysql->select("select * from ".prefix."_news where (nsched_activate>0) and (nsched_activate <= now())") as $row) {
 	$mysql->query("update ".prefix."_news set approve=1, nsched_activate=0 where id = ".$row['id']);
 }
 // �������� ������� ��� ������� �������� ���� "����� ���������� �� ����"
 foreach ($mysql->select("select * from ".prefix."_news where (nsched_deactivate>0) and (nsched_deactivate <= now())") as $row) {
 	$mysql->query("update ".prefix."_news set approve=0, nsched_deactivate=0 where id = ".$row['id']);
 }
}

