<?php
	/*
	=====================================================
	 NG FORUM v.alfa
	-----------------------------------------------------
	 Author: Nail' R. Davydov (ROZARD)
	-----------------------------------------------------
	 Jabber: ROZARD@ya.ru
	 E-mail: ROZARD@list.ru
	-----------------------------------------------------
	 © Настоящий программист никогда не ставит 
	 комментариев. То, что писалось с трудом, должно 
	 пониматься с трудом. :))
	-----------------------------------------------------
	 Данный код защищен авторскими правами
	=====================================================
	*/
	if (!defined('NGCMS')) die ('HAL');
	
	if(checkLinkAvailable('forum', '')){
		if($CurrentHandler['handlerParams']['value']['pluginName'] == 'core')
			return redirect_forum(link_home());
	}
	
	$tpath = locatePluginTemplates(array('show_index', 'entries'), 'forum', pluginGetVariable('forum', 'localsource'), pluginGetVariable('forum','localskin'), 'show_index');
	
	generate_index_cache();
	
	if( file_exists(FORUM_CACHE.'/cache_index.php') )
		include (FORUM_CACHE.'/cache_index.php');
	
	if(isset($result) && is_array($result)){
		$xt = $twig->loadTemplate($tpath['entries'].'entries.tpl');
		$xg = $twig->loadTemplate($tpath['show_index'].'show_index.tpl');
		
		$entries = array();
		foreach ( $result as $row_2 ){
			if($row_2['parent'] != 0){
				if(!$GROUP_PERM[$GROUP_STATUS]['forum_prem'][$row_2['id']]['forum_read']) continue;
				$moder_array = unserialize($row_2['moderators']);
				foreach ($moder_array as $author){
					$moder_print[] = str_replace( array('{url}', '{name}',), array( link_profile($author['id'], '', $author['name']), $author['name']), $lang_forum['moder_url']);
				}
				
				//print "<pre>".var_export($lang_forum['moder_url'], true)."</pre>";
				$tVars = array(
					'forum_link' => link_forum($row_2['id']),
					'forum_name' => $row_2['title'],
					'forum_desc' => $row_2['description'],
					'num_topic' => $row_2['int_topic'],
					'num_post' => $row_2['int_post'],
					'status' => status_forum($row_2['l_date']),
					'moder_print' => implode(', ', $moder_print),
					'last_post_forum' => array(
						'topic_name' => $row_2['l_topic_title'],
						'topic_link' => link_topic($row_2['l_post'], 'pid').'#'.$row_2['l_post'],
						//'date' => show_date($row_2['l_date']),
						'date' => $row_2['l_date'],
						'profile_link' => link_profile($row_2['l_author_id'], '', $row_2['l_author']),
						'profile' => $row_2['l_author'],
						'profile_avatar' => array(
							'true' => ($row_2['avatar'] != '')?1:0,
							'print' => ($row_2['avatar'] != '')?avatars_url.'/'.$row_2['avatar']:avatars_url,
						)
					),
				);
				$moder_print = array();
				$entries[$row_2['parent']] .= $xt->render($tVars);
			}
		}
		
		$output = '';
		foreach ( $result as $row ){
			if($row['parent'] == '0'){
				$tVars = array(
					'cat_id' => $row['id'], 
					'cat_name' => $row['title'],
					'cat_desc' => $row['description'],
					'entries' => array(
						'true' => isset($entries[$row['id']])?1:0,
						'print' => isset($entries[$row['id']])?$entries[$row['id']]:''
					),
				);
				
				$output .= $xg->render($tVars);
			}
		}
	} else $output = information('Нету категорий', $title = 'Информация', false);