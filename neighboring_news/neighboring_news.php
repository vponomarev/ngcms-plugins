<?php
/*
 * NeighboringNews for NGCMS
 * Copyright (C) 2010 Alexey N. Zhukov (http://digitalplace.ru)
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
// Protect against hack attempts
if (!defined('NGCMS')) die ('Galaxy in danger');

class NeighboringNewsFilter extends NewsFilter {

	function showNews($newsID, $SQLnews, &$tvars, $mode = array()) {

		global $mysql, $config, $tpl, $catz, $catmap, $CurrentHandler;
		// Determine paths for all template files
		$tpath = locatePluginTemplates(array('neighboring_news', 'next_news', 'previous_news'), 'neighboring_news', pluginGetVariable('neighboring_news', 'localsource'));
		// full_mode
		if (pluginGetVariable('neighboring_news', 'full_mode') && $mode['style'] == 'full') {
			if (intval($SQLnews['catid']) != null) {
				// get main category id
				$fcat = array_shift(explode(",", $SQLnews['catid']));
				// complete or incomplete comparison
				if (intval(pluginGetVariable('neighboring_news', 'compare')) == 1) $id = $fcat;
				else $id = "'" . $SQLnews['catid'] . "'";
				// example: $sort[0] = postdate $sort[1] = desc
				$sort = explode(" ", $catz[$catmap[$fcat]]['orderby']);
				// next news
				if ($sort[1] == 'asc') {
					$asc_desc = 'desc';
					$more_less = '<';
				} else {
					$asc_desc = 'asc';
					$more_less = '>';
				}
				$query = "SELECT * FROM " . prefix . "_news WHERE APPROVE = '1' AND " . $sort[0] . " " . $more_less . " '" . $SQLnews[$sort[0]] . "' AND catid = " . $id . " ORDER BY " . $sort[0] . " " . $asc_desc . " LIMIT 1";
				$row = $mysql->record($query);
				if ($row['alt_name']) {
					$tpl->template('next_news', $tpath['next_news']);
					if ($row['author_id'] && getPluginStatusActive('uprofile')) {
						$author_link = checkLinkAvailable('uprofile', 'show') ?
							generateLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id'])) :
							generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('id' => $row['author_id']));
						$author_link = "<a href = " . $config['home_url'] . $author_link . ">" . $row['author'] . "</a>";
					} else $author_link = $row['author'];
					$tpl->vars('next_news', array(
						'vars' => array(
							'link'   => newsGenerateLink(array('id' => $row['id'], 'alt_name' => $row['alt_name'], 'catid' => $row['catid'], 'postdate' => $row['postdate']), false, 0, true),
							'date'   => langdate('d.m.Y', $row['postdate']),
							'author' => $author_link,
							'title'  => str_replace(array("'", "\""), array("&#039;", "&quot;"), $row['title'])
						)
					));
					$next_news = $tpl->show('next_news');
				} else $next_news = "";
				// previous news
				if ($sort[1] == 'desc') {
					$asc_desc = 'desc';
					$more_less = '<';
				} else {
					$asc_desc = 'asc';
					$more_less = '>';
				}
				$query = "SELECT * FROM " . prefix . "_news WHERE APPROVE = '1' AND " . $sort[0] . " " . $more_less . " '" . $SQLnews[$sort[0]] . "' AND catid = " . $id . " ORDER BY " . $sort[0] . " " . $asc_desc . " LIMIT 1";
				$row = $mysql->record($query);
				if ($row['alt_name']) {
					$tpl->template('previous_news', $tpath['previous_news']);
					if ($row['author_id'] && getPluginStatusActive('uprofile')) {
						$author_link = checkLinkAvailable('uprofile', 'show') ?
							generateLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id'])) :
							generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('id' => $row['author_id']));
						$author_link = "<a href = " . $config['home_url'] . $author_link . ">" . $row['author'] . "</a>";
					} else $author_link = $row['author'];
					$tpl->vars('previous_news', array(
						'vars' => array(
							'link'   => newsGenerateLink(array('id' => $row['id'], 'alt_name' => $row['alt_name'], 'catid' => $row['catid'], 'postdate' => $row['postdate']), false, 0, true),
							'date'   => langdate('d.m.Y', $row['postdate']),
							'author' => $author_link,
							'title'  => str_replace(array("'", "\""), array("&#039;", "&quot;"), $row['title'])
						)
					));
					$previous_news = $tpl->show('previous_news');
				} else $previous_news = "";
				$tpl->template('neighboring_news', $tpath['neighboring_news']);
				$tpl->vars('neighboring_news', array(
					'vars' => array(
						'next_news'     => $next_news,
						'previous_news' => $previous_news
					)
				));
				if ($next_news != "" || $previous_news != "")
					$tvars['vars']['neighboring_news'] = $tpl->show('neighboring_news');
				else $tvars['vars']['neighboring_news'] = "";

				return 1;
			} else {
				$tvars['vars']['neighboring_news'] = "";

				return 1;
			}
		} //full mode end
		if (pluginGetVariable('neighboring_news', 'short_mode') && $mode['style'] == 'short') {
			if (intval($SQLnews['catid']) != null) {
				// get category id
				$fcat = array_shift(explode(",", $SQLnews['catid']));
				// complete or incomplete comparison
				if (intval(pluginGetVariable('neighboring_news', 'compare')) == 1) $id = $fcat;
				else $id = "'" . $SQLnews['catid'] . "'";
				if ($CurrentHandler['params']['category'] == "") {
					// example: $sort[0] = postdate $sort[1] = desc
					$sort = explode(" ", $config['default_newsorder']);
					$id_cat = $id;
				} else {
					// example: $sort[0] = postdate $sort[1] = desc
					$sort = explode(" ", $catz[$CurrentHandler['params']['category']]['orderby']);
					$id = $catz[$CurrentHandler['params']['category']]['id'];
				}
				if ($sort[1] == 'asc') {
					$asc_desc = 'desc';
					$more_less = '<';
				} else {
					$asc_desc = 'asc';
					$more_less = '>';
				}
				if ($CurrentHandler['params']['category'] != "")
					$news = $mysql->query("SELECT * FROM " . prefix . "_news WHERE APPROVE = '1' AND " . $sort[0] . " " . $more_less . " '" . $SQLnews[$sort[0]] . "' AND (catid LIKE '%," . $id . ",%'  OR catid LIKE '%," . $id . "'  OR catid LIKE '" . $id . ",%' OR catid = " . $id . ") ORDER BY " . $sort[0] . " " . $asc_desc . " LIMIT 1");
				else
					$news = $mysql->query("SELECT * FROM " . prefix . "_news WHERE APPROVE = '1' AND " . $sort[0] . " " . $more_less . " '" . $SQLnews[$sort[0]] . "' AND catid = " . $id . " ORDER BY " . $sort[0] . " " . $asc_desc . " LIMIT 1");
				$row = mysql_fetch_array($news);
				if ($row['alt_name']) {
					$tpl->template('next_news', $tpath['next_news']);
					if ($row['author_id'] && getPluginStatusActive('uprofile')) {
						$author_link = checkLinkAvailable('uprofile', 'show') ?
							generateLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id'])) :
							generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('id' => $row['author_id']));
						$author_link = "<a href = " . $config['home_url'] . $author_link . ">" . $row['author'] . "</a>";
					} else $author_link = $row['author'];
					$tpl->vars('next_news', array(
						'vars' => array(
							'link'   => newsGenerateLink(array('id' => $row['id'], 'alt_name' => $row['alt_name'], 'catid' => $row['catid'], 'postdate' => $row['postdate']), false, 0, true),
							'date'   => langdate('d.m.Y', $row['postdate']),
							'author' => $author_link,
							'title'  => str_replace(array("'", "\""), array("&#039;", "&quot;"), $row['title'])
						)
					));
					$next_news = $tpl->show('next_news');
				} else $next_news = "";
				// previous news
				if ($sort[1] == 'desc') {
					$asc_desc = 'desc';
					$more_less = '<';
				} else {
					$asc_desc = 'asc';
					$more_less = '>';
				}
				if ($CurrentHandler['params']['category'] != "")
					$news = $mysql->query("SELECT * FROM " . prefix . "_news WHERE APPROVE = '1' AND " . $sort[0] . " " . $more_less . " '" . $SQLnews[$sort[0]] . "' AND (catid LIKE '%," . $id . ",%'  OR catid LIKE '%," . $id . "'  OR catid LIKE '" . $id . ",%' OR catid = " . $id . ") ORDER BY " . $sort[0] . " " . $asc_desc . " LIMIT 1");
				else
					$news = $mysql->query("SELECT * FROM " . prefix . "_news WHERE APPROVE = '1' AND " . $sort[0] . " " . $more_less . " '" . $SQLnews[$sort[0]] . "' AND catid = " . $id . " ORDER BY " . $sort[0] . " " . $asc_desc . " LIMIT 1");
				$row = mysql_fetch_array($news);
				if ($row['alt_name']) {
					$tpl->template('previous_news', $tpath['previous_news']);
					if ($row['author_id'] && getPluginStatusActive('uprofile')) {
						$author_link = checkLinkAvailable('uprofile', 'show') ?
							generateLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id'])) :
							generateLink('core', 'plugin', array('plugin' => 'uprofile', 'handler' => 'show'), array('id' => $row['author_id']));
						$author_link = "<a href = " . $config['home_url'] . $author_link . ">" . $row['author'] . "</a>";
					} else $author_link = $row['author'];
					$tpl->vars('previous_news', array(
						'vars' => array(
							'link'   => newsGenerateLink(array('id' => $row['id'], 'alt_name' => $row['alt_name'], 'catid' => $row['catid'], 'postdate' => $row['postdate']), false, 0, true),
							'date'   => langdate('d.m.Y', $row['postdate']),
							'author' => $author_link,
							'title'  => str_replace(array("'", "\""), array("&#039;", "&quot;"), $row['title'])
						)
					));
					$previous_news = $tpl->show('previous_news');
				} else $previous_news = "";
				$tpl->template('neighboring_news', $tpath['neighboring_news']);
				$tpl->vars('neighboring_news', array(
					'vars' => array(
						'next_news'     => $next_news,
						'previous_news' => $previous_news
					)
				));
				if ($next_news != "" || $previous_news != "")
					$tvars['vars']['neighboring_news'] = $tpl->show('neighboring_news');
				else $tvars['vars']['neighboring_news'] = "";

				return 1;
			} else {
				$tvars['vars']['neighboring_news'] = "";

				return 1;
			}
		} //short_mode end
	}// showNews end
}// class end
register_filter('news', 'neighboring_news', new NeighboringNewsFilter);
?>