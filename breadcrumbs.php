<?php

/*
 * Breadcrumbs for Next Generation CMS 0.9.3
 * Copyright (C) 2010-2011 Alexey N. Zhukov (http://digitalplace.ru)
 * web:    http://digitalplace.ru
 * e-mail: zhukov.alexei@gmail.com
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

add_act('index', 'breadcrumbs');

LoadPluginLang('breadcrumbs', 'main', '', 'bc', ':');

function breadcrumbs(){
	global  $lang, $catz, $catmap, $template, $CurrentHandler, $config,
	        $SYSTEM_FLAGS, $tpl, $systemAccessURL;
	
	$tpath = locatePluginTemplates(
			array('breadcrumbs'), 'breadcrumbs', 
			pluginGetVariable('breadcrumbs', 'template_source')
	);
	
	# block output in main page (if requested)
	if (($systemAccessURL == '/') && pluginGetVariable('breadcrumbs', 'block_main')) {
		$template['vars']['breadcrumbs'] = '';
		return;
	}
	
	$location      = array();
	$location_last = '';
	
	# processing 404 page
	if ($SYSTEM_FLAGS['info']['title']['group'] == $lang['404.title']){
		$location[] = str_replace(
						array(
							'{home_url}',
							'{home_title}'
						), 
						array(
							$config['home_url'], 
							$lang['bc:mainpage']
						), 
						$lang['bc:page_404']
		);
						
		$location_last = $lang['404.title'];
	}
	else {
		
		if($CurrentHandler){
			$params     = $CurrentHandler['params'];
			$pluginName = $CurrentHandler['pluginName'];
		}
		
		# generate main page with or without link
		$main_page = ( $systemAccessURL != '/' 
					 ? str_replace(
							array(
								'{home_url}', 
								'{home_title}'
							), 
							array(
								$config['home_url'], 
								$lang['bc:mainpage']
							), 
							$lang['bc:page_404']
					 ) 
					 : $lang['bc:mainpage']
					 );
	
		$location[] = $location_last = $main_page;

		# if category
		if ($CurrentHandler['handlerName'] == 'by.category') {
			$location_last = GetCategories($catz[$params['category']]['id'], true);
			# show full path [if requested]
			if ($catz[$params['category']]['parent'] != 0 && !pluginGetVariable('breadcrumbs', 'block_full_path')){
				$id = $catz[$params['category']]['parent'];
				
				do{
					$location_tmp[] = GetCategories($id, false);
					$id = $catz[$catmap[$id]]['parent']; 
				} while($id != 0);
			$location = array_merge($location, array_reverse($location_tmp));
			}
		} 
		
		# news by date	 
		elseif ($params['year']) {
			# if we have only year then $year = plain text, if we have month then $year = link
			$year = (!$params['month']) 
				    ? $params['year'] 
				    : str_replace(
				            array(
				              '{year_url}', 
				              '{year}'
				            ), 
				            array(
				              generateLink('news', 'by.year', array('year' => $params['year'])), 
				              $params['year']
				            ), 
				            $lang['bc:by.year']
				    );

			$month_p = LangDate("F", mktime(0, 0, 0, $params['month'], 7, 0));
			
			# if we have only year and month then $month = plain text, if we have day then $month = link
			$month = (!$params['day']) 
				     ? $month_p
				     : str_replace(
				            array(
				              '{month_url}',
				              '{month_p}'
				            ), 
				            array(
				              generateLink('news', 'by.month', array('year' => $params['year'], 'month' => $params['month'])), 
				              $month_p
				            ), 
				            $lang['bc:by.month']
				     );
	   
			$day = $params['day'];
		
			$location_last = $year;
		
			if($params['month']){
				$location[]    = $year;
				$location_last = $month;
			}
		
			if($params['day']){
				$location[]    = $month;
				$location_last = $day;
			}
		
		# plugin, static, etc.
		} elseif ($pluginName != 'news') { 
			if ($pluginName == "static") $location_last = $SYSTEM_FLAGS['info']['title']['item'];
		
			elseif ($params['plugin']){
				# if plugin provide put some info
				if($SYSTEM_FLAGS['info']['breadcrumbs']){
					# plugin name becomes link
					$location[] = str_replace(
					                    array(
					                      '{plugin_url}', 
					                      '{plugin}'
					                    ), 
					                    array(
					                       generatePluginLink($params['plugin'], '', array(), array(), false, true), 
					                       $SYSTEM_FLAGS['info']['title']['group'] != $lang['loc_plugin'] ? $SYSTEM_FLAGS['info']['title']['group'] : $params['plugin']
					                    ), 
					                 $lang['bc:plugin']
					              );
					
					$count = count($SYSTEM_FLAGS['info']['breadcrumbs']) - 1;
					# all items except last become links
					for($i = 0; $i < $count ; $i++)
						$location[] = str_replace(
						                    array(
						                      '{plugin_url}', 
						                      '{plugin}'
						                    ), 
						                    array(
						                      $SYSTEM_FLAGS['info']['breadcrumbs'][$i]['link'],
						                      $SYSTEM_FLAGS['info']['breadcrumbs'][$i]['text']), 
						                    $lang['bc:plugin']
						              );
					# last item becomes plain text
					$location_last = $SYSTEM_FLAGS['info']['breadcrumbs'][$i]['text']; print_r($location);
			} 
			else {
				if ($SYSTEM_FLAGS['info']['title']['group'] != $lang['loc_plugin'])
					$location_last = $SYSTEM_FLAGS['info']['title']['group'];
				else 
					$location_last = $params['plugin'];
			}
		}
		
		elseif (($pluginName == 'uprofile' && $CurrentHandler['handlerName'] == 'edit') || $pluginName == 'search')
			$location_last = $SYSTEM_FLAGS['info']['title']['group'];
		elseif ($pluginName == 'uprofile' && $CurrentHandler['handlerName'] == 'show')
			$location_last = $SYSTEM_FLAGS['info']['title']['group'].' '.$SYSTEM_FLAGS['info']['title']['item'];
		elseif ($pluginName == 'core' && (in_array($CurrentHandler['handlerName'], array('registration', 'lostpassword', 'login'))))
			$location_last = $SYSTEM_FLAGS['info']['title']['group'];
	
		# full new
		} elseif($CurrentHandler['pluginName'] == 'news' && $CurrentHandler['handlerName'] == 'news'){
			$catids = $SYSTEM_FLAGS['news']['db.categories'];
			
			$location_last = $SYSTEM_FLAGS['info']['title']['item'];
			
			if(count($catids) != 1 || pluginGetVariable('breadcrumbs', 'block_full_path')){ 
				if ($CurrentHandler['params']['category'] != 'none') 
					$location[] = GetCategories(implode(",", $catids), false);
			}	
			else {
				$id = $catz[$params['category']]['parent'];
				$location_tmp[] = GetCategories($catz[$params['category']]['id'], false);

				do{
					$location_tmp[] = GetCategories($id, false);
					$id = $catz[$catmap[$id]]['parent'];
				} while($id != 0);

				$location = array_merge($location, array_reverse($location_tmp));
			}
		}
	}
	
	$separator = pluginGetVariable('breadcrumbs', 'separator');
	if($location[0] == $location_last){
		$location_last = ''; 
		$separator = '';
	}

	$location = implode(pluginGetVariable('breadcrumbs', 'separator'), $location);
		
	$tpl -> template('breadcrumbs', $tpath['breadcrumbs']);
	$tpl -> vars('breadcrumbs', array ('vars' => array (
					'location'      => $location,
					'location_last' => $location_last,
					'separator'     => $separator
			)));
	
	$template['vars']['breadcrumbs'] = $tpl -> show('breadcrumbs');
}