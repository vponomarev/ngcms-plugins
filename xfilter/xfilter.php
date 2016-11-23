<?php

if (!defined('NGCMS')) die ('Galaxy in danger');

include root.'includes/news.php';
add_act('index', 'xfilter');

LoadPluginLang('xfilter', 'config', '', 'xfl', ':');

function xfilter($params) {
global $twig, $template, $mysql, $tpl, $lang, $CurrentHandler;
	$filter = array(); 
	$tVars = array();

	include root.'conf/extras/xfields/config.php';
	
	// fill plugin parameters
	foreach (array('order', 'showNumber', 'skipcat', 'showAllCat') as $k)
		if (!isset($params[$k])) $params[$k] = pluginGetVariable('xfilter', "{$currentVar}_".$k);

	if ($params['showAllCat']) $do = $params['showAllCat']; 

	// check additional categories and extend the "skipcat" array of categories to be skipped
	if ($params['skipcat']) {
	    foreach ($mysql->select("SELECT id AS catid FROM ".prefix."_category WHERE parent in (".$params['skipcat'].")") as $row) { 
		    if ($row['catid'] != '') { 
				$params['skipcat'] .= ",".$row['catid']; 
			}
		}

		$skipcat = explode(',', $params['skipcat']); 
	}
	
	// generate the list of categories excluding 'skipcat' list
	if (count($_REQUEST) && count($skipcat)) {
		foreach ($skipcat as $skip) {
			array_push($filter, array('SQL', "catid not like ('%".$skip."%')"));
		}
	}	
	// generate the "select" list of the categories 
	$tVars["catlist"]= makeCategoryList( array ('name' => 'catid', 'selected' => $_REQUEST['catid'], 'skip'=>$skipcat, 'doall' => $do, 'class' => 'mw_search_f'));

	// if category is selected, then check it, use 'like' to check additional categories too
	if ($_REQUEST['catid']) {
		array_push($filter, array('DATA', 'catid', 'LIKE', '%'.secure_html($_REQUEST['catid']).'%'));	 
	}

	// processing xfields
	foreach ($xarray['news'] as $id => $data) {
		switch ($data['type']) {
			case 'text'  : 	$val = '<select name="xfields_'.$id.'" >';
					if (!$data['required']) $val .= '<option value="">'.$lang['sh_all'].'</option>';
					foreach ($mysql->select("SELECT DISTINCT xfields_".$id." AS xtext FROM ".prefix."_news ORDER BY xfields_".$id." ASC") as $row) { 
                    if ($row['xtext'] != '') {
  						$val .="<option value=\"".$row['xtext']."\"".(($_REQUEST["xfields_$id"] == $row["xtext"])?" selected=\"selected\"":"").">".$row['xtext']."&nbsp;"."</option>";
						}
					}
					$val .= '</select>';
				break;
							
			case 'select': 	$val = '<select name="xfields_'.$id.'" >';
						if (!$data['required']) $val .= '<option value="">'.$lang['sh_all'].'</option>';
						if (is_array($data['options']))
								foreach ($data['options'] as $k => $v) {
										$val .= '<option value="'.secure_html(($data['storekeys'])?$k:$v).'"'.((($data['storekeys'] && ($xdata[$id] == $k))||(!$data['storekeys'] && ($xdata[$id] == $v) || ($_REQUEST["xfields_$id"] == $v)))?' selected="selected"':'').'>'.$v.'&nbsp;'.'</option>';
								}
								$val .= '</select>';
						break;
			case 'textarea'	: 	$val = '';
							    break;
			case 'images'	:	$val = '';
								break;
		}
	
		$tVars["xfields_$id"] = $val;

		$xpaginparams = array("catid" => $_REQUEST["catid"], "xfields_$id" => $_REQUEST["xfields_$id"]);

		$tpath = LocatePluginTemplates(array('xfilter', 'xfilter_form'), 'xfilter', pluginGetVariable('xfilter', 'localsource'));

		$xf = $twig->loadTemplate($tpath['xfilter_form'].'xfilter_form.tpl');
		$template['vars']['xfilter'] = $xf->render($tVars);	

		if ($_REQUEST["xfields_$id"]) {
			array_push($filter, array('DATA',"xfields_$id", '=', secure_html($_REQUEST["xfields_$id"])));	 
		}       

	}

	// sort news
	$orderAllowed = array('id_desc' => 'id desc', 'id_asc' => 'id asc', 'postdate_desc' => 'postdate desc', 'postdate_asc' => 'postdate asc', 'title_desc' => 'title desc', 'title_asc' => 'title asc');

	if ($params['order'] && isset($orderAllowed[$params['order']])) {
		$newsOrder = $orderAllowed[$params['order']];
	} else {
		$newsOrder = $orderAllowed['id desc'];
	}

	$paginationParams = array('pluginName' => 'news', 'pluginHandler' => 'main', 'xparams' => $xpaginparams, 'paginator' => array('page', 1, false));
	$callingParams = array('style' => 'short', 'customCategoryTemplate' => true, 'newsOrder' => $newsOrder);
	
	// set number of news per page
	$callingParams['showNumber'] = ($params['showNumber']) ? intval($params['showNumber']) : '';
	
	if ($_REQUEST['page']) {
		 $callingParams['page'] = intval($_REQUEST['page']);
	}

	
	if ($filter && $CurrentHandler['pluginName'] == 'news' ) {
		 array_unshift($filter, 'AND'); 
		 $filtered = news_showlist($filter, $paginationParams, $callingParams);
	
		if ($filtered) {
			$template['vars']['mainblock'] = $filtered;
		}

		else {
			$template['vars']['mainblock'] = '';
			msg(array("type" => "info", "info" => $lang['xfl:no_news']));
		}
	}

}
