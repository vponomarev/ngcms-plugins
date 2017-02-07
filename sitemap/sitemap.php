<?php
/*
 * sitemap for Next Generation CMS (http://ngcms.ru/)
 * Copyright (C) 2010 Alexey N. Zhukov (http://digitalplace.ru), kt2k (http://kt2k.ru/)
 * http://digitalplace.ru
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
if (!defined('NGCMS')) die ('Galaxy in danger');
register_plugin_page('sitemap', '', 'generateSitemap', 0);
function generateSitemap() {

	global $template, $twig, $tpl, $lang, $mysql, $config, $parse, $catz, $SYSTEM_FLAGS, $TemplateCache;
	$tpath = locatePluginTemplates(array('sitemap', 'sitemap'), 'sitemap', intval(pluginGetVariable('sitemap', 'localsource')));
	$xt = $twig->loadTemplate($tpath['sitemap'] . 'sitemap.tpl');
	//var_dump($catz);
	/*   
	$cList = $mysql->select("select * from ".prefix."_category order by posorder");
	var_dump($cList);
	foreach ( $cList as $num => $row) {
	}
	*/
	loadPluginLang('sitemap', 'main', '', '', ':');
	$page = 1;
	if (isset($_GET['page'])) $page = intval($_GET['page']);
	if (pluginGetVariable('sitemap', 's_cache')) {
		$cacheData = cacheRetrieveFile('sitemap_' . $page . '.txt', pluginGetVariable('sitemap', 's_cacheExpire'), 'sitemap');
		if ($cacheData != false) {
			# we got data from cache. Return it and stop
			$template['vars']['mainblock'] = $cacheData;

			return 0;
		}
	}
	# news per page. Thanks, capitan :)
	$news_per_page = intval(pluginGetVariable('sitemap', 'news_per_page')) ? intval(pluginGetVariable('sitemap', 'news_per_page')) : 200;
	# range of messages
	$limit = 'LIMIT ' . ($page - 1) * $news_per_page . ', ' . $news_per_page;
	# count of all news
	$countNews = $mysql->result('SELECT COUNT(*) FROM ' . prefix . '_news');
	$news = $mysql->select('SELECT n.title, n.postdate, n.views,' . (getPluginStatusActive('comments') ? " n.com, " : "") . ' n.catid, n.id, n.alt_name, c.name, c.alt, c.parent, c.posorder, c.poslevel FROM ' . prefix . '_news AS n LEFT JOIN ' . prefix . '_category c on n.catid = c.id WHERE `approve` = 1 ORDER BY posorder, catid, pinned DESC, postdate DESC, editdate DESC ' . $limit);
	$tpath = locatePluginTemplates(array('sitemap_news', 'sitemap'), 'sitemap', intval(pluginGetVariable('sitemap', 'localsource')));
	foreach ($news as $row) {
		if ($cu_c <> $row['name']) {
			$delimiter = "";
			for ($o = 0; $o < $row['poslevel']; $o++)
				$delimiter .= $lang['sitemap:delimiter'];
			$link_rss = '';
			if (getPluginStatusActive('rss_export')) {
				$link_rss = generateLink('rss_export', 'category', array('category' => $row['alt']));
				$link_rss = '<a href="' . $link_rss . '">' . $lang['sitemap:label_rss'] . '</a>';
			}
			$tEntry['cat_' . $row['catid']]['cat_id'] = $row['catid'];
			$tEntry['cat_' . $row['catid']]['cat_link'] = GetCategories($row['catid']);
			foreach ($catz as $cat_item) {
				if ($cat_item['id'] == $row['catid']) {
					$tEntry['cat_' . $row['catid']]['cat_info'] = $cat_item;
				}
			}
			$cu_c = $row['name'];
		}
		$tEntry['news_' . $row['id']]['news_id'] = $row['id'];
		$tEntry['news_' . $row['id']]['news_title'] = $row['title'];
		$tEntry['news_' . $row['id']]['news_date'] = $row['postdate'];
		$tEntry['news_' . $row['id']]['news_views'] = $row['views'];
		$tEntry['news_' . $row['id']]['news_comms'] = $row['com'];
		$tEntry['news_' . $row['id']]['news_cat'] = GetCategories($row['catid']);
		$tEntry['news_' . $row['id']]['news_link'] = newsGenerateLink(array('catid' => $row['catid'], 'alt_name' => $row['alt_name'], 'id' => $row['id'], 'postdate' => $row['postdate']), false, 0, true);
	}
	$countStatic = $mysql->result("select COUNT(*) from " . prefix . "_static ");
	$countCatz = count($catz);
	$pages_count = ceil($countNews / $news_per_page);
	if ($pages_count == $page) {
		foreach ($catz as $cat_item) {
			if ($cat_item['posts'] == "0") {
				$tEntry['cat_' . $cat_item['id']]['cat_id'] = $cat_item['id'];
				$tEntry['cat_' . $cat_item['id']]['cat_link'] = GetCategories($cat_item['id']);
				$tEntry['cat_' . $cat_item['id']]['cat_info'] = $cat_item;
			}
		}
		$static = $mysql->select("select * from " . prefix . "_static order by title");
		foreach ($static as $row) {
			$tEntry['static_' . $row['id']]['static_id'] = $row['id'];
			$tEntry['static_' . $row['id']]['static_alt'] = $row['alt_name'];
			$tEntry['static_' . $row['id']]['static_title'] = $row['title'];
			$tEntry['static_' . $row['id']]['static_date'] = $row['postdate'];
			$link = checkLinkAvailable('static', '') ?
				generateLink('static', '', array('altname' => $row['alt_name'], 'id' => $row['id']), array(), false, true) :
				generateLink('core', 'plugin', array('plugin' => 'static'), array('altname' => $row['alt_name'], 'id' => $row['id']), false, true);
			$tEntry['static_' . $row['id']]['static_link'] = $link;
		}
	}
	$paginationParams = array('pluginName' => 'sitemap', 'params' => array(), 'xparams' => array(), 'paginator' => array('page', 0, false));
	unset($tVars);
	# generate pagination if count of pages > 1
	if ($pages_count > 1) {
		templateLoadVariables(true);
		$navigations = $TemplateCache['site']['#variables']['navigation'];
		$tVars['pagination'] = generatePagination($page, 1, $pages_count, 9, $paginationParams, $navigations);
		# set plugin title
		$SYSTEM_FLAGS['info']['title']['group'] = str_replace('{page}', $page, $lang['sitemap:title_multiple']);
	} else {
		$tVars['pagination'] = '';
		# set plugin title
		$SYSTEM_FLAGS['info']['title']['group'] = $lang['sitemap:title_single'];
	}
	$tVars['entries'] = isset($tEntry) ? $tEntry : '';
	$tVars['counts'] = array(
		'countCatz'   => $countCatz,
		'countNews'   => $countNews,
		'countStatic' => $countStatic
	);
	$tVars['news_per_page'] = $news_per_page;
	$tVars['pages_count'] = $pages_count;
	$tVars['page'] = $page;
	if (pluginGetVariable('sitemap', 's_cache'))
		cacheStoreFile('sitemap_' . $page . '.txt', $result, 'sitemap');
	$template['vars']['mainblock'] = $xt->render($tVars);
}
