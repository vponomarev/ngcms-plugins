<?php
/*
 * bookmarks for NextGeneration CMS (http://ngcms.ru/)
 * Copyright (C) 2010 Alexey N. Zhukov (http://digitalplace.ru)
 * http://digitalplace.ru
 * 
 * code based on kt2k's plugin
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */
# protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
add_act('index', 'bookmarks_view');
register_plugin_page('bookmarks', 'modify', 'bookmarks_t', 0);
register_plugin_page('bookmarks', '', 'bookmarksPage', 0);
global $lang;
LoadPluginLang('bookmarks', 'main', '', '', ':');
$bookmarks_script = '
<script type="text/javascript">

	<!-- (ñ)habrahabr.ru -->
	function futu_alert(header, text, close, className) {
		if (!document.getElementById(\'futu_alerts_holder\')) {
			var futuAlertOuter = document.createElement(\'div\');
			futuAlertOuter.className = \'futu_alert_outer\';
			document.body.appendChild(futuAlertOuter);
			
			var futuAlertFrame = document.createElement(\'div\');
			futuAlertFrame.className = \'frame\';
			futuAlertOuter.appendChild(futuAlertFrame);
			
			var futuAlertsHolder = document.createElement(\'div\');
			futuAlertsHolder.id = \'futu_alerts_holder\';
			futuAlertsHolder.className = \'futu_alerts_holder\';
			futuAlertFrame.appendChild(futuAlertsHolder);
		}
		var futuAlert = document.createElement(\'div\');
		futuAlert.className = \'futu_alert \' + className;
		document.getElementById(\'futu_alerts_holder\').appendChild(futuAlert);
		futuAlert.id = \'futu_alert\';

		var futuAlertHeader = document.createElement(\'div\');
		futuAlertHeader.className = \'futu_alert_header\';
		futuAlert.appendChild(futuAlertHeader);
	
		futuAlertHeader.innerHTML = header;
		
		if (close) {
			var futuAlertCloseButton = document.createElement(\'a\');
			futuAlertCloseButton.href = \'#\';
			futuAlertCloseButton.className = \'futu_alert_close_button\';
			futuAlertCloseButton.onclick = function(ev) {
				if(!ev) {
					ev=window.event;
				}
				if (!document.all) ev.preventDefault(); else ev.returnValue = false;
				document.getElementById(\'futu_alerts_holder\').removeChild(futuAlert);
			}
			futuAlert.appendChild(futuAlertCloseButton);
			
			var futuAlertCloseButtonIcon = document.createElement(\'img\');
			futuAlertCloseButtonIcon.src = \'/engine/plugins/bookmarks/img/btn_close.gif\';
			futuAlertCloseButton.appendChild(futuAlertCloseButtonIcon);
		}
	
	
		var futuAlertText = document.createElement(\'div\');
		futuAlertText.className = \'futu_alert_text\';
		futuAlert.appendChild(futuAlertText);

		
		futuAlertText.innerHTML = text;
		
		futuAlert.style.position = \'relative\';
		futuAlert.style.top = \'0\';
		futuAlert.style.display = \'block\';

	
		if (!close) {
			/* addEvent("click",function(){
				document.getElementById(\'futu_alerts_holder\').removeChild(futuAlert);
			}, document.getElementById(\'futu_alert\'));*/
			setTimeout(function () { document.getElementById(\'futu_alerts_holder\').removeChild(futuAlert); }, 3000);
			
		}
	}
	
	function bookmarks(url, news, action){
		var ajaxBookmarks = new sack();
		ajaxBookmarks.onShow(""); 
		ajaxBookmarks.onComplete = function (){
										if(ajaxBookmarks.response == "limit"){
											futu_alert("' . $lang['bookmarks:msg_title'] . '", "' . $lang['bookmarks:err_add_limit'] . '", true, "message");
										}	
										else if(ajaxBookmarks.response == "notlogged"){
											futu_alert("' . $lang['bookmarks:msg_title'] . '", "' . $lang['bookmarks:err_notlogged'] . '", true, "error");
										}	
										else if(ajaxBookmarks.response == "err_add"){
											futu_alert("' . $lang['bookmarks:msg_title'] . '", "' . $lang['bookmarks:err_add'] . '", true, "error");	
										} else { 
											elementObj = document.getElementById("bookmarks_" + news);
											elementObj.innerHTML = ajaxBookmarks.response;
											elementObj = document.getElementById("bookmarks_counter_" + news);
											if(ajaxBookmarks.response.indexOf("<!-- add -->") != -1){
												futu_alert("' . $lang['bookmarks:msg_title'] . '", "' . $lang['bookmarks:msg_add'] . '", false, "save");
											}
											else{
												futu_alert("' . $lang['bookmarks:msg_title'] . '", "' . $lang['bookmarks:msg_delete'] . '", false, "save");
											}
										}
									};
		ajaxBookmarks.setVar("news", news);
		ajaxBookmarks.setVar("action", action);
		ajaxBookmarks.setVar("ajax", true);
		ajaxBookmarks.requestFile = url;
		ajaxBookmarks.method ="GET";
		ajaxBookmarks.runAJAX();
	}
</script>';
register_htmlvar('plain', $bookmarks_script);
$tpath = locatePluginTemplates(array(':bookmarks.css'), 'bookmarks', intval(pluginGetVariable('bookmarks', 'localsource')));
register_stylesheet($tpath['url::bookmarks.css'] . '/bookmarks.css');
/* declare variables to be global
 * bookmarksLoaded - flag is bookmarks already loaded
 * bookmarksList   - result of $mysql -> select
 */
global $bookmarksLoaded, $bookmarksList;
$bookmarksLoaded = 0;
$bookmarksList = array();

# generate links for add/remove bookmark 
class BookmarksNewsFilter extends NewsFilter {

	function showNews($newsID, $SQLnews, &$tvars) {

		global $lang, $bookmarksLoaded, $bookmarksList, $userROW, $tpl, $mysql, $twig;
		# determine paths for template files
		$tpath = locatePluginTemplates(array('add.remove.links.style', 'not.logged.links'), 'bookmarks', pluginGetVariable('bookmarks', 'localsource'));
		# exit if user is not logged in
		if (!is_array($userROW)) {
			# generate counter [if requested]
			if (pluginGetVariable('bookmarks', 'counter')) {
				$tVars['counter'] = $mysql->result('SELECT COUNT(*) FROM ' . prefix . '_bookmarks WHERE news_id=' . $newsID);
				$tVars['counter'] = $tVars['counter'] ? $tVars['counter'] : '';
				//$tVars['text'] = $lang['bookmarks:act_delete'];
				$xg = $twig->loadTemplate($tpath['not.logged.links'] . 'not.logged.links.tpl');
				$tvars['vars']['plugin_bookmarks_news'] = $xg->render($tVars);
			} else $tvars['vars']['plugin_bookmarks_news'] = '';

			return;
		}
		# preload user's bookmarks
		if (!$bookmarksLoaded)
			bookmarks_sql();
		# check if this news is already in bookmark
		$found = 0;
		foreach ($bookmarksList as $brow) {
			if ($brow['id'] == $newsID) {
				$found = 1;
				break;
			}
		}
		# generate link
		$link = generatePluginLink('bookmarks', 'modify', array(), array('news' => $newsID, 'action' => ($found ? 'delete' : 'add')));
		$url = generatePluginLink('bookmarks', 'modify');
		$tVars = array('news' => $newsID, 'action' => ($found ? 'delete' : 'add'), 'link' => $link, 'found' => $found, 'url' => $url, 'link_title' => ($found ? $lang['bookmarks:title_delete'] : $lang['bookmarks:title_add']));
		# generate counter [if requested]
		if (pluginGetVariable('bookmarks', 'counter')) {
			$tVars['counter'] = $mysql->result('SELECT COUNT(*) FROM ' . prefix . '_bookmarks WHERE news_id=' . $newsID);
			$tVars['counter'] = $tVars['counter'] ? $tVars['counter'] : '';
		} else $tVars['counter'] = '';
		$xg = $twig->loadTemplate($tpath['add.remove.links.style'] . 'add.remove.links.style.tpl');
		$tvars['vars']['plugin_bookmarks_news'] = $xg->render($tVars);
	}
}

register_filter('news', 'bookmarks', new BookmarksNewsFilter);
# function for fetching SQL bookmarks data
function bookmarks_sql() {

	global $mysql, $config, $userROW, $bookmarksLoaded, $bookmarksList;
	$bookmarksLoaded = 1;
	if ($userROW['id']) {
		$bookmarksList = $mysql->select("SELECT n.id, n.title, n.alt_name, n.catid, n.postdate FROM " . prefix . "_bookmarks AS b LEFT JOIN " . prefix . "_news n ON n.id = b.news_id WHERE b.user_id = " . db_squote($userROW['id']));
	}
}

# view bookmarks on sidebar
function bookmarks_view() {

	global $template, $tpl, $lang, $mysql, $config, $parse, $userROW, $bookmarksLoaded, $bookmarksList, $twig;
	# view on sidebar?
	if (!pluginGetVariable('bookmarks', 'sidebar')) {
		$template['vars']['plugin_bookmarks'] = '';

		return;
	}
	# generate cache file name
	$cacheFileName = md5('bookmarks' . $config['theme'] . $config['default_lang']) . $userROW['id'] . '.txt';
	if (pluginGetVariable('bookmarks', 'cache')) {
		$cacheData = cacheRetrieveFile($cacheFileName, pluginGetVariable('bookmarks', 'cacheExpire'), 'bookmarks');
		if ($cacheData != false) {
			# we got data from cache. Return it and stop
			$template['vars']['plugin_bookmarks'] = $cacheData;

			return;
		}
	}
	# determine paths for all template files
	$tpath = locatePluginTemplates(array('entries', 'bookmarks'), 'bookmarks', pluginGetVariable('bookmarks', 'localsource'));
	$maxlength = intval(pluginGetVariable('bookmarks', 'maxlength'));
	if (!$maxlength) {
		$maxlength = 100;
	}
	# preload user's bookmarks
	if (!$bookmarksLoaded && pluginGetVariable('bookmarks', 'sidebar'))
		bookmarks_sql();
	$output = '';
	$count = 0;
	foreach ($bookmarksList as $row) {
		$count++;
		if ($count > intval(pluginGetVariable('bookmarks', 'max_sidebar'))) break;
		if (strlen($row['title']) > $maxlength) {
			$title = substr(secure_html($row['title']), 0, $maxlength) . "...";
		} else {
			$title = secure_html($row['title']);
		}
		$result[] = array('link' => newsGenerateLink($row), 'title' => $title);
	}
	# action on "hide empty"
	if ((!$count) && pluginGetVariable('bookmarks', 'hide_empty')) {
		if (pluginGetVariable('bookmarks', 'cache')) {
			cacheStoreFile($cacheFileName, ' ', 'bookmarks');
		}
		$template['vars']['plugin_bookmarks'] = '';

		return;
	}
	/*
	unset($tvars);

	if (!$count) {
		$tvars['regx']['/\[if-bookmarks\](.*?)\[\/if-bookmarks\]/si'] = '';
		$tvars['regx']['/\[if-not-bookmarks\](.*?)\[\/if-not-bookmarks\]/si'] = '$1';
		$result = $lang['bookmarks:noentries'];
	} 
	else {
		$tvars['regx']['/\[if-bookmarks\](.*?)\[\/if-bookmarks\]/si'] = '$1';
		$tvars['regx']['/\[if-not-bookmarks\](.*?)\[\/if-not-bookmarks\]/si'] = '';
	}
	*/
	if (!$count) {
		$result = $lang['bookmarks:noentries'];
	}
	$tVars = array('tpl_url' => tpl_url, 'entries' => $result, 'bookmarks_page' => generatePluginLink('bookmarks', null));
	$tVars['count'] = $count;
	//$tVars['entries'] = $Entries;
	$xt = $twig->loadTemplate($tpath['bookmarks'] . 'bookmarks.tpl');
	$template['vars']['plugin_bookmarks'] = $xt->render($tVars);
	# create cache file
	if (pluginGetVariable('bookmarks', 'cache')) {
		cacheStoreFile($cacheFileName, $output, 'bookmarks');
	}
}

# personal plugin pages for add/remove bookmarks
function bookmarks_t() {

	global $mysql, $config, $userROW, $HTTP_REFERER, $SUPRESS_TEMPLATE_SHOW, $tpl, $lang, $bookmarksList, $bookmarksLoaded, $template, $twig;
	# news ID
	$newsID = intval($_GET['news']);
	$ajax = $_GET['ajax'];
	# process bookmarks only for logged in users
	if (!is_array($userROW)) {
		if ($ajax) die('notlogged');
		# Redirect UNREG users far away :)
		header('Location: ' . $config['home_url'] . '');

		return;
	}
	if (!$bookmarksLoaded)
		$count_list = $mysql->result('SELECT COUNT(*) FROM ' . prefix . '_bookmarks WHERE user_id = ' . db_squote($userROW['id']));
	else
		$count_list = count($bookmarksList);
	# for return reverse action
	$action = '';
	# add action
	if ($_GET['action'] == 'add') {
		# check limits
		if (intval(pluginGetVariable('bookmarks', 'bookmarks_limit')) < $count_list + 1) {
			if ($ajax) die("limit");
			else {
				# determine paths for template files
				$tpath = locatePluginTemplates(array('bookmarks.page'), 'bookmarks', pluginGetVariable('bookmarks', 'localsource'));
				$tvars['vars']['all_bookmarks'] = '';
				$tvars['vars']['no_bookmarks'] = $lang['bookmarks:err_add_limit'];
				$tpl->template('bookmarks.page', $tpath['bookmarks.page']);
				$tpl->vars('bookmarks.page', $tvars);
				$template['vars']['mainblock'] = $tpl->show('bookmarks.page');

				return;
			}
		}
		# check that this news exists & we didn't bookmarked this news earlier
		if (count($mysql->select("SELECT id FROM " . prefix . "_news WHERE id = " . $newsID)) &&
			(!count($mysql->select("SELECT * FROM " . prefix . '_bookmarks WHERE user_id = ' . db_squote($userROW['id']) . " AND news_id=" . $newsID)))
		) {
			# ok, bookmark it
			$mysql->query("INSERT INTO `" . prefix . "_bookmarks` (`user_id`,`news_id`) VALUES (" . db_squote($userROW['id']) . "," . db_squote($newsID) . ")");
			$action = 'delete';
		} else die('err_add');
		# delete action
	} elseif ($_GET['action'] == 'delete') {
		$mysql->query("DELETE FROM `" . prefix . "_bookmarks` WHERE `user_id`=" . db_squote($userROW['id']) . " AND `news_id`=" . db_squote($newsID));
		$action = 'add';
	}
	# if cache is activated - truncate cache file [ to clear cache ]
	if (pluginGetVariable('bookmarks', 'cache')) {
		$cacheFileName = md5('bookmarks' . $config['theme'] . $config['default_lang']) . $userROW['id'] . '.txt';
		cacheStoreFile($cacheFileName, '', 'bookmarks');
	}
	# make redirection back if not-ajax mode
	if (!$ajax) {
		header("Location: " . ($HTTP_REFERER ? $HTTP_REFERER : $config['home_url']));

		return;
	}
	$SUPRESS_TEMPLATE_SHOW = 1;
	# generate link
	$link = generatePluginLink('bookmarks', 'modify', array(), array('news' => $newsID, 'action' => $action));
	$url = generatePluginLink('bookmarks', 'modify');
	# determine paths for template files
	$tpath = locatePluginTemplates(array('ajax.add.remove.links.style'), 'bookmarks', pluginGetVariable('bookmarks', 'localsource'));
	$tVars = array('news' => $newsID, 'action' => $action, 'link' => $link, 'text' => ($action == 'delete' ? $lang['bookmarks:act_delete'] : $lang['bookmarks:act_add']), 'url' => $url, 'link_title' => ($action == 'delete' ? $lang['bookmarks:title_delete'] : $lang['bookmarks:title_add']));
	# generate counter [if requested]
	if (pluginGetVariable('bookmarks', 'counter')) {
		$tVars['counter'] = $mysql->result('SELECT COUNT(*) FROM ' . prefix . '_bookmarks WHERE news_id=' . $newsID);
		$tVars['counter'] = $tVars['counter'] ? $tVars['counter'] : '';
	} else $tVars['counter'] = '';
	$xt = $twig->loadTemplate($tpath['ajax.add.remove.links.style'] . 'ajax.add.remove.links.style.tpl');
	header("Content-Type: text/html; charset=Windows-1251");
	//echo iconv('WINDOWS-1251', 'UTF-8', $tpl -> show('ajax.add.remove.links.style'));
	echo $xt->render($tVars) . ($action == 'delete' ? '<!-- add -->' : '<!-- delete -->');
}

# personal plugin pages for display all user's bookmarks
function bookmarksPage() {

	global $SYSTEM_FLAGS, $lang, $userROW, $bookmarksLoaded, $bookmarksList, $template, $config, $template, $tpl, $twig;
	# process bookmarks only for logged in users
	if (!is_array($userROW)) {
		# Redirect UNREG users far away :)
		header('Location: ' . $config['home_url'] . '');

		return;
	}
	# preload user's bookmarks
	if (!$bookmarksLoaded)
		bookmarks_sql();
	# determine paths for template files
	$tpath = locatePluginTemplates(array('bookmarks.page', 'news.short'), 'bookmarks', pluginGetVariable('bookmarks', 'localsource'));
	$SYSTEM_FLAGS['info']['title']['group'] = $lang['bookmarks:pp_title'];
	if (!count($bookmarksList))
		$output_data = $lang['bookmarks:nobookmarks'];
	else {
		include_once root . 'includes/news.php';
		load_extras('news');
		# get id's news
		$ids = array();
		foreach ($bookmarksList as $brow) {
			$ids[] = $brow['id'];
		}
		# set news filter
		$filter = array('DATA', 'ID', 'IN', $ids);
		$callingParams = array('style' => 'short', 'plugin' => 'bookmarks', 'overrideTemplatePath' => (pluginGetVariable('bookmarks', 'news_short') ? $tpath['news.short'] : null));
		if (isset($_GET['page']) && (intval($_GET['page']) > 0)) {
			$callingParams['page'] = intval($_GET['page']);
		} else $callingParams['page'] = 1;
		$paginationParams = array('pluginName' => 'bookmarks', 'xparams' => array(), 'params' => array(), 'paginator' => array('page', 1, false));
		$newslist = news_showlist($filter, $paginationParams, $callingParams);
	} # end if have bookmarks
	$tVars['all_bookmarks'] = $newslist;
	$tVars['count'] = count($bookmarksList);
	$xt = $twig->loadTemplate($tpath['bookmarks.page'] . 'bookmarks.page.tpl');
	$template['vars']['mainblock'] = $xt->render($tVars);
}
