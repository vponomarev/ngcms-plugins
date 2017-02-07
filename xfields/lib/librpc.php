<?php
//
// XFields configuration manipulations
//
function xfields_rpc_group_modify($params) {

	global $userROW;
	include_once root . 'plugins/xfields/xfields.php';
	if (!is_array($xf = xf_configLoad()))
		$xf = array();
	if (!is_array($userROW) || ($userROW['status'] != 1)) {
		return array('status' => 0, 'errorCode' => 1, 'errorText' => 'Security error');
	}
	if (!is_array($params) || !isset($params['action']))
		return array('status' => 0, 'errorCode' => 2, 'errorText' => 'Activity mode is not set');
	$params = arrayCharsetConvert(1, $params);
	switch ($params['action']) {
		case 'grpAdd':
			$grpId = $params['id'];
			$grpName = $params['name'];
			// Check for correct name
			if (!preg_match('#^[a-zA-Z0-9]{2,10}$#', $grpId, $null)) {
				return array('status' => 0, 'errorCode' => 3, 'errorText' => 'Wrong GroupID, id should contain only from [a-z, 0-9] and length between 2-10 chars');
			}
			// Check for duplicates
			if (isset($xf['grp.news'][$grpId])) {
				return array('status' => 0, 'errorCode' => 4, 'errorText' => 'Duplicated group ID');
			}
			// Create group
			$xf['grp.news'][$grpId] = array('title' => $grpName, 'entries' => array());
			xf_configSave($xf);

			// Notify about changes
			return array('status' => 1, 'errorCode' => 0, 'data' => 'New group was created', 'config' => arrayCharsetConvert(0, $xf['grp.news']));
		case 'grpEdit':
			$grpId = $params['id'];
			$grpName = $params['name'];
			// Check if group exists
			if (!isset($xf['grp.news'][$grpId])) {
				return array('status' => 0, 'errorCode' => 5, 'errorText' => 'Requested group does not exist');
			}
			// Modify group
			$xf['grp.news'][$grpId]['title'] = $grpName;
			xf_configSave($xf);

			// Notify about changes
			return array('status' => 1, 'errorCode' => 0, 'data' => 'Group was changed', 'config' => arrayCharsetConvert(0, $xf['grp.news']));
		case 'grpDel':
			$grpId = $params['id'];
			// Check if group exists
			if (!isset($xf['grp.news'][$grpId])) {
				return array('status' => 0, 'errorCode' => 5, 'errorText' => 'Requested group does not exist');
			}
			unset($xf['grp.news'][$grpId]);
			xf_configSave($xf);

			// Notify about changes
			return array('status' => 1, 'errorCode' => 0, 'data' => 'Group was deleted', 'config' => arrayCharsetConvert(0, $xf['grp.news']));
		case 'fldAdd':
			$grpId = $params['id'];
			$fldId = $params['field'];
			// Check if group exists
			if (!isset($xf['grp.news'][$grpId])) {
				return array('status' => 0, 'errorCode' => 5, 'errorText' => 'Requested group does not exist');
			}
			// Check if field already exists
			if (array_search($fldId, $xf['grp.news'][$grpId]['entries']) !== false) {
				return array('status' => 0, 'errorCode' => 6, 'errorText' => 'Field already is a member of the group');
			}
			// Check if field exists
			if (!isset($xf['news'][$fldId])) {
				return array('status' => 0, 'errorCode' => 7, 'errorText' => 'Field does not exists');
			}
			array_push($xf['grp.news'][$grpId]['entries'], $fldId);
			xf_configSave($xf);

			// Notify about changes
			return array('status' => 1, 'errorCode' => 0, 'data' => 'Field was added into group', 'config' => arrayCharsetConvert(0, $xf['grp.news']));
		case 'fldDel':
		case 'fldUp':
		case 'fldDown':
			$grpId = $params['id'];
			$fldId = $params['field'];
			if (!isset($xf['grp.news'][$grpId])) {
				return array('status' => 0, 'errorCode' => 5, 'errorText' => 'Requested group does not exist');
			}
			// Check if field already exists
			if (array_search($fldId, $xf['grp.news'][$grpId]['entries']) === false) {
				return array('status' => 0, 'errorCode' => 8, 'errorText' => 'Field is not a member of the group');
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
			return array('status' => 1, 'errorCode' => 0, 'data' => 'Field was deleted/moved up/moved down', 'config' => arrayCharsetConvert(0, $xf['grp.news']));
	}

	return array('status' => 1, 'errorCode' => 0, 'data' => 'OK, ' . var_export($params, true));
}

function xfields_rpc_demo($params) {

	return array('status' => 1, 'errorCode' => 0, 'data' => var_export($params, true));
}

rpcRegisterFunction('plugin.xfields.demo', 'xfields_rpc_demo');
rpcRegisterFunction('plugin.xfields.group.modify', 'xfields_rpc_group_modify');



