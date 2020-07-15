<?php

if (!defined('NGCMS')) {
    exit('HAL');
}
$lang = LoadLang('users', 'admin');
LoadPluginLang('clear_config', 'config', '', 'с_с', ':');
switch ($_REQUEST['action']) {
    case 'delete':
        delete();
        break;
    default:
        showlist();
}
function showlist()
{
    global $tpl, $PLUGINS, $lang;
    plugins_load_config();
    $ULIB = new urlLibrary();
    $ULIB->loadConfig();
    $plug = [];
    $conf = [];
    if (isset($PLUGINS['active']['active']) && is_array($PLUGINS['active']['active'])) {
        foreach ($PLUGINS['active']['active'] as $key => $row) {
            $plug[] = $key;
            $conf[$key][] = 'active';
        }
    }
    if (isset($PLUGINS['active']['actions']) && is_array($PLUGINS['active']['actions'])) {
        foreach ($PLUGINS['active']['actions'] as $key => $row) {
            if (!is_array($row)) {
                continue;
            }
            foreach ($row as $kkey => $rrow) {
                if (!in_array($kkey, $plug)) {
                    $plug[] = $kkey;
                }
                if (!in_array('actions', $conf[$kkey])) {
                    $conf[$kkey][] = 'actions';
                }
            }
        }
    }
    if (isset($PLUGINS['active']['installed']) && is_array($PLUGINS['active']['installed'])) {
        foreach ($PLUGINS['active']['installed'] as $key => $row) {
            if (!in_array($key, $plug)) {
                $plug[] = $key;
            }
            $conf[$key][] = 'installed';
        }
    }
    if (isset($PLUGINS['active']['libs']) && is_array($PLUGINS['active']['libs'])) {
        foreach ($PLUGINS['active']['libs'] as $key => $row) {
            if (!in_array($key, $plug)) {
                $plug[] = $key;
            }
            $conf[$key][] = 'libs';
        }
    }
    if (isset($PLUGINS['config']) && is_array($PLUGINS['config'])) {
        foreach ($PLUGINS['config'] as $key => $row) {
            if (!in_array($key, $plug)) {
                $plug[] = $key;
            }
            $conf[$key][] = 'config';
        }
    }
    if (isset($ULIB->CMD) && is_array($ULIB->CMD)) {
        foreach ($ULIB->CMD as $key => $row) {
            if ($key != 'core' && $key != 'static' && $key != 'search' && $key != 'news' && !in_array($key, $plug)) {
                $plug[] = $key;
            }
            $conf[$key][] = 'urlcmd';
        }
    }
    $tpath = locatePluginTemplates(['conf.list', 'conf.list.row'], 'clear_config');
    $output = '';
    sort($plug);
    foreach ($plug as $key => $row) {
        $pvars['vars']['id'] = $row;
        $pvars['vars']['conf'] = '';
        foreach ($conf[$row] as $kkey => $rrow) {
            $pvars['vars']['conf'] .=
                '<a href="/engine/admin.php?mod=extra-config&plugin=clear_config&action=delete&id='.$row.
                '&conf='.$rrow.
                '" title="'.$lang['с_с:'.$rrow].
                '" onclick="return confirm(\''.sprintf($lang['с_с:confirm'], $lang['с_с:'.$rrow], $row).'\');" '.
                '><img src="/engine/plugins/clear_config/tpl/images/'.$rrow.'.png" /></a>&#160;';
        }
        $tpl->template('conf.list.row', $tpath['conf.list.row']);
        $tpl->vars('conf.list.row', $pvars);
        $output .= $tpl->show('conf.list.row');
    }
    $tvars['vars']['entries'] = $output;
    $tpl->template('conf.list', $tpath['conf.list']);
    $tpl->vars('conf.list', $tvars);
    echo $tpl->show('conf.list');
}

function delete()
{
    global $PLUGINS, $lang;
    if (!isset($_REQUEST['id']) || !isset($_REQUEST['conf'])) {
        msg(['type' => 'info', 'info' => $lang['с_с:error']]);
        showlist();

        return false;
    }
    $id = secure_html(convert($_REQUEST['id']));
    $conf = secure_html(convert($_REQUEST['conf']));
    switch ($conf) {
        case 'active':
            if (isset($PLUGINS['active']['active'][$id])) {
                unset($PLUGINS['active']['active'][$id]);
                msg(['type' => 'info', 'info' => sprintf($lang['с_с:del_ok'], 'active', $id)]);
            } else {
                msg(['type' => 'info', 'info' => sprintf($lang['с_с:del_er'], 'active', $id)]);
            }
            break;
        case 'actions':
            $if_delete = false;
            if (isset($PLUGINS['active']['actions']) && is_array($PLUGINS['active']['actions'])) {
                foreach ($PLUGINS['active']['actions'] as $key => $row) {
                    if (isset($PLUGINS['active']['actions'][$key][$id])) {
                        unset($PLUGINS['active']['actions'][$key][$id]);
                        $if_delete = true;
                    }
                }
            }
            if ($if_delete) {
                msg(['type' => 'info', 'info' => sprintf($lang['с_с:del_ok'], 'actions', $id)]);
            } else {
                msg(['type' => 'info', 'info' => sprintf($lang['с_с:del_er'], 'actions', $id)]);
            }
            break;
        case 'installed':
            if (isset($PLUGINS['active']['installed'][$id])) {
                unset($PLUGINS['active']['installed'][$id]);
                msg(['type' => 'info', 'info' => sprintf($lang['с_с:del_ok'], 'installed', $id)]);
            } else {
                msg(['type' => 'info', 'info' => sprintf($lang['с_с:del_er'], 'installed', $id)]);
            }
            break;
        case 'libs':
            if (isset($PLUGINS['active']['libs'][$id])) {
                unset($PLUGINS['active']['libs'][$id]);
                msg(['type' => 'info', 'info' => sprintf($lang['с_с:del_ok'], 'libs', $id)]);
            } else {
                msg(['type' => 'info', 'info' => sprintf($lang['с_с:del_er'], 'libs', $id)]);
            }
            break;
        case 'config':
            if (isset($PLUGINS['config'][$id])) {
                unset($PLUGINS['config'][$id]);
                msg(['type' => 'info', 'info' => sprintf($lang['с_с:del_ok'], 'config', $id)]);
            } else {
                msg(['type' => 'info', 'info' => sprintf($lang['с_с:del_er'], 'config', $id)]);
            }
            break;
        case 'urlcmd':
            $ULIB = new urlLibrary();
            $ULIB->loadConfig();
            if (isset($ULIB->CMD[$id])) {
                unset($ULIB->CMD[$id]);
                msg(['type' => 'info', 'info' => sprintf($lang['с_с:del_ok'], 'urlcmd', $id)]);
            } else {
                msg(['type' => 'info', 'info' => sprintf($lang['с_с:del_er'], 'urlcmd', $id)]);
            }
            $ULIB->saveConfig();
            break;
    }
    pluginsSaveConfig();
    savePluginsActiveList();
    showlist();
}
