<?php

class XFieldsFilterAdminCategories extends FilterAdminCategories
{
    public function addCategory(&$tvars, &$SQL)
    {
        $SQL['allow_com'] = intval($_REQUEST['allow_com']);

        return 1;
    }

    public function addCategoryForm(&$tvars)
    {
        global $lang;
        loadPluginLang('comments', 'config', '', '', ':');
        $allowCom = pluginGetVariable('comments', 'default_categories');
        $ms = '<select name="allow_com">';
        $cv = ['0' => 'запретить', '1' => 'разрешить', '2' => 'по умолчанию'];
        for ($i = 0; $i < 3; $i++) {
            $ms .= '<option value="'.$i.'"'.(($allowCom == $i) ? ' selected="selected"' : '').'>'.$cv[$i].'</option>';
        }
        $tvars['vars']['extend'] .= '<tr><td width="70%" class="contentEntry1">'.$lang['comments:categories.comments'].'<br/><small>'.$lang['comments:categories.comments#desc'].'</small></td><td width="30%" class="contentEntry2">'.$ms.'</td></tr>';

        return 1;
    }

    public function editCategoryForm($categoryID, $SQL, &$tvars)
    {
        global $lang;
        loadPluginLang('comments', 'config', '', '', ':');
        if (!isset($SQL['allow_com'])) {
            $SQL['allow_com'] = pluginGetVariable('comments', 'default_categories');
        }
        $ms = '<select name="allow_com">';
        $cv = ['0' => 'запретить', '1' => 'разрешить', '2' => 'по умолчанию'];
        for ($i = 0; $i < 3; $i++) {
            $ms .= '<option value="'.$i.'"'.(($SQL['allow_com'] == $i) ? ' selected="selected"' : '').'>'.$cv[$i].'</option>';
        }
        $tvars['vars']['extend'] .= '<tr><td width="70%" class="contentEntry1">'.$lang['comments:categories.comments'].'<br/><small>'.$lang['comments:categories.comments#desc'].'</small></td><td width="30%" class="contentEntry2">'.$ms.'</td></tr>';

        return 1;
    }

    public function editCategory($categoryID, $SQL, &$SQLnew, &$tvars)
    {
        $SQLnew['allow_com'] = intval($_REQUEST['allow_com']);

        return 1;
    }
}
