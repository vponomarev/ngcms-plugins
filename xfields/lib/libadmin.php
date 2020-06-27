<?php

class XFieldsFilterAdminCategories extends FilterAdminCategories {

	function addCategory(&$tvars, &$SQL) {

		$SQL['allow_com'] = intval($_REQUEST['allow_com']);

		return 1;
	}

	function addCategoryForm(&$tvars) {

		global $lang;
		loadPluginLang('comments', 'config', '', '', ':');
		$allowCom = pluginGetVariable('comments', 'default_categories');
		$ms = '<select name="allow_com">';
		$cv = array('0' => 'Р·Р°РїСЂРµС‚РёС‚СЊ', '1' => 'СЂР°Р·СЂРµС€РёС‚СЊ', '2' => 'РїРѕ СѓРјРѕР»С‡Р°РЅРёСЋ');
		for ($i = 0; $i < 3; $i++) {
			$ms .= '<option value="' . $i . '"' . (($allowCom == $i) ? ' selected="selected"' : '') . '>' . $cv[$i] . '</option>';
		}
		$tvars['vars']['extend'] .= '<tr><td width="70%" class="contentEntry1">' . $lang['comments:categories.comments'] . '<br/><small>' . $lang['comments:categories.comments#desc'] . '</small></td><td width="30%" class="contentEntry2">' . $ms . '</td></tr>';

		return 1;
	}

	function editCategoryForm($categoryID, $SQL, &$tvars) {

		global $lang;
		loadPluginLang('comments', 'config', '', '', ':');
		if (!isset($SQL['allow_com'])) {
			$SQL['allow_com'] = pluginGetVariable('comments', 'default_categories');
		}
		$ms = '<select name="allow_com">';
		$cv = array('0' => 'Р·Р°РїСЂРµС‚РёС‚СЊ', '1' => 'СЂР°Р·СЂРµС€РёС‚СЊ', '2' => 'РїРѕ СѓРјРѕР»С‡Р°РЅРёСЋ');
		for ($i = 0; $i < 3; $i++) {
			$ms .= '<option value="' . $i . '"' . (($SQL['allow_com'] == $i) ? ' selected="selected"' : '') . '>' . $cv[$i] . '</option>';
		}
		$tvars['vars']['extend'] .= '<tr><td width="70%" class="contentEntry1">' . $lang['comments:categories.comments'] . '<br/><small>' . $lang['comments:categories.comments#desc'] . '</small></td><td width="30%" class="contentEntry2">' . $ms . '</td></tr>';

		return 1;
	}

	function editCategory($categoryID, $SQL, &$SQLnew, &$tvars) {

		$SQLnew['allow_com'] = intval($_REQUEST['allow_com']);

		return 1;
	}
}
