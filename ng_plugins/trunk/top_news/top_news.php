<?php

/*
 * top_news for NextGeneration CMS (http://ngcms.ru/)
 * Copyright (C) 2010-2012 Alexey N. Zhukov (http://digitalplace.ru)
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
 
# Protect against hack attempts
if (!defined('NGCMS')) die ('Galaxy in danger');

add_act('index', 'top_news');

function top_news(){

	global $config, $mysql, $tpl, $template, $PFILTERS, $CurrentHandler, $catmap, $parse, 
	       $langShortMonths, $langMonths, $SYSTEM_FLAGS, $userROW, $catmap;

	$count = intval(pluginGetVariable('top_news', 'count'));

	for($i = 1; $i <= $count; $i++){

		# block name for settings
		$currentVar = 'top_news'.$i;
		
		# block name for templates
		$blockName = pluginGetVariable('top_news', "{$currentVar}_name") ? 'top_news_'.pluginGetVariable('top_news', "{$currentVar}_name") : $currentVar;

		$ifcategory = pluginGetVariable('top_news', "{$currentVar}_ifcategory");

		# if print only "in categories and news" then return
		if ($ifcategory && $CurrentHandler['params']['category'] == "" ){
			$template['vars'][$blockName] = '';
			continue;
		}
			
		# generate cache file name
		if($ifcategory)
			$cacheFileName = md5($currentVar.$CurrentHandler['params']['category']).'.txt';
		else
			$cacheFileName = md5($currentVar).'.txt';
		
		if (pluginGetVariable('top_news', 'cache')) {
			$cacheData = cacheRetrieveFile($cacheFileName, pluginGetVariable('top_news','cacheExpire'), 'top_news');
			if ($cacheData != false) {
				# get data from cache. Return that and stop
				$template['vars'][$blockName] = $cacheData;
				continue;
			}
		}
		
		# determine paths for all template files
		$tpath = LocatePluginTemplates(array('entries', 'top_news'), 'top_news', pluginGetVariable('top_news', 'localsource'), '', $blockName);
		
		# load some config
		$newslength = intval(pluginGetVariable('top_news', "{$currentVar}_newslength"));
		$maxlength  = intval(pluginGetVariable('top_news', "{$currentVar}_maxlength"));
		$number     = intval(pluginGetVariable('top_news', "{$currentVar}_number"));
		$date       = intval(pluginGetVariable('top_news', "{$currentVar}_date"));
		$type       = intval(pluginGetVariable('top_news', "{$currentVar}_type"));
		$orderby    = pluginGetVariable('top_news', "{$currentVar}_orderby");
		
		if (!$number)     { $number     = 10; }
		if (!$maxlength)  { $maxlength  = 100; }
		if (!$newslength) { $newslength = 100; }
					
		# type of sort: views, comments, random or postdate
		switch ($orderby) {
					case 'views':    $orderby = 'views';  break;
					case 'comments': $orderby = 'com';    break;
					case 'random':   $orderby = 'rand()'; break;
				default: $orderby = 'postdate';
		}
		
		# only approved news
		$filter = array ('approve = 1');
		
		# N-days news period
		if ($date > 0) $filter[] = 'postdate >= '.(time()-24*60*60*$date);

		# show news if cheked "Publish on main page"
		if (pluginGetVariable('top_news', "{$currentVar}_mainpage")) $filter[] = 'mainpage = 1';
			
		# categories filter
		if (($CurrentHandler['params']['category'] && $CurrentHandler['params']['altname'] == "" && $ifcategory))
		$filter[] = "(catid regexp '[[:<:]](".trim(array_search($CurrentHandler['params']['category'], $catmap)).")[[:>:]]')";
		else{
			$catfilter = array();
			$categories = array();

				if($SYSTEM_FLAGS['news']['db.categories'] && $ifcategory) $categories = $SYSTEM_FLAGS['news']['db.categories']; 
				elseif (pluginGetVariable('top_news', "{$currentVar}_categories"))  $categories = explode(',', pluginGetVariable('top_news', "{$currentVar}_categories")); 

				if(count($categories)){
					foreach ($categories as $cat) {
						$catfilter [] = "(catid regexp '[[:<:]](".trim($cat).")[[:>:]]')";
					} 
					if (count($catfilter))
						$filter [] = '('.join(' OR ', $catfilter).')';
				}
		}
		
		$select = array('id', 'alt_name', 'postdate', 'title', 'views', 'catid', 'author_id', 'author');
		if(getPluginStatusActive('comments'))
			$select[] = 'com';
		if (pluginGetVariable('top_news', "{$currentVar}_content")) 
			$select[] = 'content';
	
		$offset = pluginGetVariable('top_news', $currentVar.'_offset') ? abs(intval(pluginGetVariable('top_news', $currentVar.'_offset')))-1 : 0;
		
		$query = "SELECT ".implode(', ', $select)." FROM ".prefix."_news WHERE ".implode(" AND ", $filter)." ORDER BY {$orderby} DESC LIMIT {$offset}, {$number}";
		
		foreach ($mysql->select($query) as $row) {
		
			$short_news = '';
			
			if (pluginGetVariable('top_news', "{$currentVar}_content")){
				list ($short_news, $full_news) = explode('<!--more-->', $row['content'], 2);
			
				if ($config['blocks_for_reg'])    $short_news = $parse -> userblocks($short_news);
				if ($config['use_htmlformatter']) $short_news = $parse -> htmlformatter($short_news);
				if ($config['use_bbcodes'])       $short_news = $parse -> bbcodes($short_news);
				if ($config['use_smilies'])       $short_news = $parse -> smilies($short_news);
				if (strlen($short_news) > $newslength) $short_news = $parse -> truncateHTML($short_news, $newslength);
			
				# cutting images
				if (pluginGetVariable('top_news', "{$currentVar}_img")) $short_news = preg_replace('/<img.*?>/', '', $short_news);
			}
			
			$tvars['vars'] = array(
				'short_news'        =>  $short_news,
				'link'              =>  newsGenerateLink($row),
				'views'             =>  $row['views'],
				'alt.news'          =>  $row['alt_name'],
				'alt.cat'           =>  !strstr($row['catid'], ',') ?  $catmap[$row['catid']] : '',
				'comments'          =>  $row['com'],
				'author_name'       =>  $row['author'],
				'author_link'       =>  generateLink('uprofile', 'show', array('name' => $row['author'], 'id' => $row['author_id'])),
				'categories'        =>  GetCategories($row['catid'])
			);
			
			$tvars['regx']['/\[if-uprofile\](.*?)\[\/if-uprofile\]/si'] = getPluginStatusActive('uprofile') ? '$1' : '';
			
			if (strlen($row['title']) > $maxlength) {
				$tvars['vars']['title'] = substr(secure_html($row['title']), 0, $maxlength)."...";
			} else {
				$tvars['vars']['title'] = secure_html($row['title']);
			}
			
			# show edit news button
			if (is_array($userROW) && ($row['author_id'] == $userROW['id'] || $userROW['status'] == "1" || $userROW['status'] == "2")){ 
					$tvars['vars']['[edit-news]'] = "<a href='".admin_url."/admin.php?mod=news&amp;action=edit&amp;id={$row['id']}' target='_blank'>"; 
					$tvars['vars']['[/edit-news]'] = "</a>";
			} 
			else {
					$tvars['regx']["#\[edit-news\].*?\[/edit-news\]#si"] = ""; 
			}
			
			# set formatted date
			$dformat = (pluginGetVariable('top_news', "{$currentVar}_dateformat") ? pluginGetVariable('top_news', "{$currentVar}_dateformat") : '{day0}.{month0}.{year}');
			$tvars['vars']['date'] = str_replace(array('{day}', '{day0}', '{month}', '{month0}', '{year}', '{year2}', '{month_s}', '{month_l}', '{hour}', '{hour0}', '{minute0}'),
							array(date('j',$row['postdate']), date('d',$row['postdate']), date('n',$row['postdate']), date('m',$row['postdate']), date('y',$row['postdate']), date('Y',$row['postdate']), $langShortMonths[date('n',$row['postdate'])-1], $langMonths[date('n',$row['postdate'])-1], date('G', $row['postdate']), date('H', $row['postdate']), date('i', $row['postdate'])), $dformat);

			$tpl -> template('entries', $tpath['entries']);
			$tpl -> vars('entries', $tvars);
			$result .= $tpl -> show('entries');
		}
		
		unset($tvars);
		
	    $tvars['vars'] = array ( 'tpl_url' => tpl_url, 'top_news' => $result);

		$tpl -> template('top_news', $tpath['top_news']);
		$tpl -> vars('top_news', $tvars);

		$output = $tpl -> show('top_news');
		$template['vars'][$blockName] = $output;

		$result = '';
		
		# create cache
		if (pluginGetVariable('top_news','cache')) {
			cacheStoreFile($cacheFileName, $output, 'top_news');
		}
	}	
}