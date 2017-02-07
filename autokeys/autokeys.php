<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
// Load library
include_once(root . "/plugins/autokeys/lib/class.php");

// News filtering class
class autoKeysNewsFilter extends NewsFilter {

	function addNews(&$tvars, &$SQL) {

		if ($_POST['autokeys_generate'] == 1) {
			$SQL['keywords'] = akeysGetKeys(array('content' => $SQL['content'], 'title' => $SQL['title']));
		}

		return 1;
	}

	function editNews($newsID, $SQLold, &$SQLnew, &$tvars) {

		if ($_POST['autokeys_generate'] == 1) {
			$SQLnew['keywords'] = akeysGetKeys(array('content' => $SQLnew['content'], 'title' => $SQLnew['title']));
		}

		return 1;
	}

	function editNewsForm($newsID, $SQLold, &$tvars) {

		global $twig;
		$tpath = locatePluginTemplates(array('editnews'), 'autokeys', pluginGetVariable('autokeys', 'localsource'));
		$xt = $twig->loadTemplate($tpath['editnews'] . '/editnews.tpl');
		$tvars['plugin']['autokeys'] = $xt->render(array('flags' => array('checked' => pluginGetVariable('autokeys', 'activate_edit'))));

		return 1;
	}

	function addNewsForm(&$tvars) {

		global $twig;
		$tpath = locatePluginTemplates(array('addnews'), 'autokeys', pluginGetVariable('autokeys', 'localsource'));
		$xt = $twig->loadTemplate($tpath['addnews'] . '/addnews.tpl');
		$tvars['plugin']['autokeys'] = $xt->render(array('flags' => array('checked' => pluginGetVariable('autokeys', 'activate_add'))));

		return 1;
	}
}

register_filter('news', 'autokeys', new autoKeysNewsFilter);