<?php

//
// XFields configuration manipulations
//
function xfields_rpc_group_modify($params)
{
    global $userROW;
    include_once root.'plugins/xfields/xfields.php';
    if (!is_array($xf = xf_configLoad())) {
        $xf = [];
    }
    if (!is_array($userROW) || ($userROW['status'] != 1)) {
        return ['status' => 0, 'errorCode' => 1, 'errorText' => 'Security error'];
    }
    if (!is_array($params) || !isset($params['action'])) {
        return ['status' => 0, 'errorCode' => 2, 'errorText' => 'Activity mode is not set'];
    }
    switch ($params['action']) {
        case 'grpAdd':
            $grpId = $params['id'];
            $grpName = $params['name'];
            // Check for correct name
            if (!preg_match('#^[a-zA-Z0-9]{2,10}$#', $grpId, $null)) {
                return ['status' => 0, 'errorCode' => 3, 'errorText' => 'Wrong GroupID, id should contain only from [a-z, 0-9] and length between 2-10 chars'];
            }
            // Check for duplicates
            if (isset($xf['grp.news'][$grpId])) {
                return ['status' => 0, 'errorCode' => 4, 'errorText' => 'Duplicated group ID'];
            }
            // Create group
            $xf['grp.news'][$grpId] = ['title' => $grpName, 'entries' => []];
            xf_configSave($xf);

            // Notify about changes
            return ['status' => 1, 'errorCode' => 0, 'errorText' => 'New group was created', 'config' => $xf['grp.news']];
        case 'grpEdit':
            $grpId = $params['id'];
            $grpName = $params['name'];
            // Check if group exists
            if (!isset($xf['grp.news'][$grpId])) {
                return ['status' => 0, 'errorCode' => 5, 'errorText' => 'Requested group does not exist'];
            }
            // Modify group
            $xf['grp.news'][$grpId]['title'] = $grpName;
            xf_configSave($xf);

            // Notify about changes
            return ['status' => 1, 'errorCode' => 0, 'errorText' => 'Group was changed', 'config' => $xf['grp.news']];
        case 'grpDel':
            $grpId = $params['id'];
            // Check if group exists
            if (!isset($xf['grp.news'][$grpId])) {
                return ['status' => 0, 'errorCode' => 5, 'errorText' => 'Requested group does not exist'];
            }
            unset($xf['grp.news'][$grpId]);
            xf_configSave($xf);

            // Notify about changes
            return ['status' => 1, 'errorCode' => 0, 'errorText' => 'Group was deleted', 'config' => $xf['grp.news']];
        case 'fldAdd':
            $grpId = $params['id'];
            $fldId = $params['field'];
            // Check if group exists
            if (!isset($xf['grp.news'][$grpId])) {
                return ['status' => 0, 'errorCode' => 5, 'errorText' => 'Requested group does not exist'];
            }
            // Check if field already exists
            if (array_search($fldId, $xf['grp.news'][$grpId]['entries']) !== false) {
                return ['status' => 0, 'errorCode' => 6, 'errorText' => 'Field already is a member of the group'];
            }
            // Check if field exists
            if (!isset($xf['news'][$fldId])) {
                return ['status' => 0, 'errorCode' => 7, 'errorText' => 'Field does not exists'];
            }
            array_push($xf['grp.news'][$grpId]['entries'], $fldId);
            xf_configSave($xf);

            // Notify about changes
            return ['status' => 1, 'errorCode' => 0, 'errorText' => 'Field was added into group', 'config' => $xf['grp.news']];
        case 'fldDel':
        case 'fldUp':
        case 'fldDown':
            $grpId = $params['id'];
            $fldId = $params['field'];
            if (!isset($xf['grp.news'][$grpId])) {
                return ['status' => 0, 'errorCode' => 5, 'errorText' => 'Requested group does not exist'];
            }
            // Check if field already exists
            if (array_search($fldId, $xf['grp.news'][$grpId]['entries']) === false) {
                return ['status' => 0, 'errorCode' => 8, 'errorText' => 'Field is not a member of the group'];
            }
            $position = array_search($fldId, $xf['grp.news'][$grpId]['entries']);
            // Decide an action
            if ($params['action'] == 'fldDel') {
                unset($xf['grp.news'][$grpId]['entries'][$position]);
            }
            if (($params['action'] == 'fldUp') && ($position > 0)) {
                $tmp = $xf['grp.news'][$grpId]['entries'][$position - 1];
                $xf['grp.news'][$grpId]['entries'][$position - 1] = $xf['grp.news'][$grpId]['entries'][$position];
                $xf['grp.news'][$grpId]['entries'][$position] = $tmp;
            }
            if (($params['action'] == 'fldDown') && (($position + 1) < count($xf['grp.news'][$grpId]['entries']))) {
                $tmp = $xf['grp.news'][$grpId]['entries'][$position + 1];
                $xf['grp.news'][$grpId]['entries'][$position + 1] = $xf['grp.news'][$grpId]['entries'][$position];
                $xf['grp.news'][$grpId]['entries'][$position] = $tmp;
            }
            $xf['grp.news'][$grpId]['entries'] = array_values($xf['grp.news'][$grpId]['entries']);
            xf_configSave($xf);

            // Notify about changes
            return ['status' => 1, 'errorCode' => 0, 'errorText' => 'Field was deleted/moved up/moved down', 'config' => $xf['grp.news']];
    }

    return ['status' => 1, 'errorCode' => 0, 'errorText' => 'OK, '.var_export($params, true)];
}

function xfields_rpc_demo($params)
{
    return ['status' => 1, 'errorCode' => 0, 'errorText' => var_export($params, true)];
}

rpcRegisterFunction('plugin.xfields.demo', 'xfields_rpc_demo');
rpcRegisterFunction('plugin.xfields.group.modify', 'xfields_rpc_group_modify');
