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
				$moderators = unserialize($row_2['moderators']);
				foreach ($moderators as $author)
					$moder_print[] = str_replace( array('{url}', '{name}',), array( link_profile($author['id'], '', $author['name']), $author['name']), $lang_forum['moder_url']);
				
				if(array_key_exists(strtolower($userROW['name']), $moderators))
					$MODE_PS = $MODE_PERM[$row_2['id']];
				else
					$MODE_PS = array();
				
				$tVars = array(
					'forum_link' => link_forum($row_2['id']),
					'forum_name' => $row_2['title'],
					'forum_desc' => $row_2['description'],
					'num_topic' => $row_2['int_topic'],
					'num_post' => $row_2['int_post'],
					'lock_passwd' => (isset($row_2['lock_passwd']) && $row_2['lock_passwd'])?1:0,
					'status' => status_forum($row_2['l_date']),
					'moder_print' => implode(', ', $moder_print),
					'MODE_PS' => $MODE_PS,
					'last_post_forum' => array(
						'topic_name' => $row_2['l_topic_title'],
						'topic_link' => link_topic($row_2['l_post'], 'pid').'#'.$row_2['l_post'],
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
					'entries' => isset($entries[$row['id']])?$entries[$row['id']]:''
				);
				
				$output .= $xg->render($tVars);
			}
		}
	} else $output = information('Нету категорий', $title = 'Информация', false);