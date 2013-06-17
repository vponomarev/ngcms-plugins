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
if(!defined('NGCMS')) exit('HAL');

plugins_load_config();

switch ($_REQUEST['action']) {
	case 'send_forum': send_forum(); break;
	case 'edit_forum': edit_forum(); break;
	case 'list_forum': list_forum(); break;
	case 'del_forum':  del_forum(); break;
	
	case 'send_section': send_section(); break;
	case 'edit_section': edit_section(); break;
	case 'del_section': del_section(); break;
	
	case 'list_complaints': list_complaints(); break;
	case 'closed_complaints': closed_complaints(); break;
	
	case 'permission': permission(); break;
	case 'edit_permission': edit_permission(); break;
	
	case 'list_news': list_news(); break;
	case 'new_news': new_news(); break;
	case 'del_news': del_news(); break;

	case 'ads': ads(); break;
	case 'rules': rules(); break;
	case 'moderat': moderat(); break;
	case 'url': url(); break;
	case 'title': title(); break;
	
	case 'about': about(); break;
	default: general();
}

function edit_permission(){
	global $plugin, $twig;
	
	$id = intval($_REQUEST['id']);
	if(!isset($id) & $id <> '')
		redirect_forum_config('?mod=extra-config&plugin=forum&action=permission');
	
	include_once(dirname(__FILE__).'/includes/security.php');
	include_once(dirname(__FILE__).'/includes/constants.php');
	include_once(FORUM_CACHE.'/permission.php');
	
	
	//print "<pre>".var_export($GROUP_PERM, true)."</pre>";
	$tpath = locatePluginTemplates(array('main', 'edit_permission'), $plugin, 1, '', 'config');
	$xt = $twig->loadTemplate($tpath['edit_permission'].'edit_permission.tpl');
	
	
	
	if (isset($_REQUEST['submit'])){
		if(empty($error_text)){
			$merger = array_merge($GROUP_PERM[$id], secureinput($_REQUEST['GROUP_PERM'][$id]));
			$GROUP_PERM[$id] = $merger;
			file_put_contents(FORUM_CACHE.'/permission.php', '<?php'."\n\n".'$GROUP_PERM = '.var_export($GROUP_PERM, true).';');
			redirect_forum_config('?mod=extra-config&plugin=forum&action=permission');
		}
	}
	
	$error_input = array();
	if(isset($error_text) && is_array($error_text))
		foreach($error_text as $error)
			$error_input[] = msg(array("type" => "error", "text" => $error), 0, 2);
	
	$tVars = array(
		'id' => $id,
		'name' => $GROUP_PERM[$id]['name'],
		'color' => $GROUP_PERM[$id]['color'],
		'read' => MakeDropDown(array(false => 'нет', true => 'да'), 'GROUP_PERM['.$id.'][read]', $GROUP_PERM[$id]['read']),
		'news' => MakeDropDown(array(false => 'нет', true => 'да'), 'GROUP_PERM['.$id.'][news]', $GROUP_PERM[$id]['news']),
		'search' => MakeDropDown(array(false => 'нет', true => 'да'), 'GROUP_PERM['.$id.'][search]', $GROUP_PERM[$id]['search']),
		'pm' => MakeDropDown(array(false => 'нет', true => 'да'), 'GROUP_PERM['.$id.'][pm]', $GROUP_PERM[$id]['pm']),
		'list_error' => $error_input,
	);
	
	$xg = $twig->loadTemplate($tpath['main'].'main.tpl');
	
	$tVars = array(
		'global' => 'Редактор прав',
		'entries' => $xt->render($tVars)
	);
	
	print $xg->render($tVars);
}

function permission()
{global $plugin, $twig;
	
	
	include_once(dirname(__FILE__).'/includes/security.php');
	include_once(dirname(__FILE__).'/includes/constants.php');
	include_once(FORUM_CACHE.'/permission.php');
	
	$tpath = locatePluginTemplates(array('main', 'permission'), $plugin, 1, '', 'config');
	$xt = $twig->loadTemplate($tpath['permission'].'permission.tpl');
	
	if(file_exists(FORUM_CACHE.'/permission.php'))
		include(FORUM_CACHE.'/permission.php');
	else {
		$GROUP_PERM = array('0' => 
			array(
				'name' => 'Гость',
				'read' => true, 
				'color' => 'red', 
				'news' => '1',
				'search' => '1',
				'pm' => '1',
			),'1' => 
			array(
				'name' => 'Администратор',
				'read' => true, 
				'color' => 'red', 
				'news' => '1',
				'search' => '1',
				'pm' => '1',
			),'2' => 
			array(
				'name' => 'Редактор',
				'read' => true, 
				'color' => 'green', 
				'news' => '1',
				'search' => '1',
				'pm' => '1',
			),'3' => 
			array(
				'name' => 'Журналист',
				'read' => true, 
				'color' => 'blue', 
				'news' => '1',
				'search' => '1',
				'pm' => '1',
			),'4' => 
			array(
				'name' => 'Комментатор',
				'read' => true, 
				'color' => 'gold', 
				'news' => '1',
				'search' => '1',
				'pm' => '1',
			),'5' => 
			array(
				'name' => 'Боты',
				'read' => true, 
				'color' => 'red', 
				'news' => '1',
				'search' => '1',
				'pm' => '1',
			),'moderators' => 
			array(
				'name' => 'Модератор',
			)
		);
		file_put_contents(FORUM_CACHE.'/permission.php', '<?php'."\n\n".'$GROUP_PERM = '.var_export($GROUP_PERM, true).';');
	}
	
	foreach ($GROUP_PERM as $key => $value){
		//print "<pre>".var_export($key, true).'-'.var_export($value, true)."</pre>";
		$tEntry[] = array(
			'id' => $key,
			'name' => $value['name']
		);
	}
	
	$tVars = array(
		'entries' => $tEntry,
	);
	
	$xg = $twig->loadTemplate($tpath['main'].'main.tpl');
	
	$tVars = array(
		'global' => 'Группы',
		'entries' => $xt->render($tVars)
	);
	
	print $xg->render($tVars);
}

function about()
{global $plugin, $twig;
	$tpath = locatePluginTemplates(array('main', 'about'), $plugin, 1, '', 'config');
	
	
	$xt = $twig->loadTemplate($tpath['about'].'about.tpl');
	
	$tVars = array();
	
	$xg = $twig->loadTemplate($tpath['main'].'main.tpl');
	
	$tVars = array(
		'global' => 'О плагине',
		'entries' => $xt->render($tVars)
	);
	
	print $xg->render($tVars);
}

function closed_complaints(){
global $twig, $plugin, $mysql, $userROW;
	
	$id = intval($_REQUEST['id']);
	
	if(isset($id) && $id){
		if($mysql->result('SELECT 1 FROM '.prefix.'_forum_complaints WHERE id = '.$id.' and viewed = 1 LIMIT 1')) return;
		
		$mysql->query('UPDATE '.prefix.'_forum_complaints SET 
		
		who_author_id = '.db_squote($userROW['id']).',
		who_author = '.db_squote($userROW['name']).',
		viewed = \'1\'
		
		WHERE id = '.$id.' LIMIT 1');
		$_SESSION['forum']['info'] = 'Жалоба закрыта';
		redirect_forum_config('?mod=extra-config&plugin=forum&action=list_complaints');
	
	}else{
		$_SESSION['forum']['info'] = 'Ошибка';
	}

}

function list_complaints(){
global $twig, $plugin, $config, $mysql;
	if(isset($_SESSION['forum']['info'])){
		$info =  $_SESSION['forum']['info'];
		session_destroy();
	}
	
	$tpath = locatePluginTemplates(array('main', 'complaints'), $plugin, 1, '', 'config');
	$xg = $twig->loadTemplate($tpath['complaints'].'complaints.tpl');
	include_once(dirname(__FILE__).'/includes/rewrite.php');
	include_once(dirname(__FILE__).'/includes/security.php');
	
	$news_per_page = 5;
	
	if (($news_per_page < 2)||($news_per_page > 2000)) $news_per_page = 2;
	
	$pageNo		= intval($_REQUEST['page'])?$_REQUEST['page']:0;
	if ($pageNo < 1)	$pageNo = 1;
	if (!$start_from)	$start_from = ($pageNo - 1)* $news_per_page;
	
	$count = $mysql->result('SELECT count(id) from '.prefix.'_forum_complaints');
	$countPages = ceil($count / $news_per_page);
	
	foreach ($mysql->select('SELECT * FROM '.prefix.'_forum_complaints  ORDER BY c_data DESC LIMIT '.$start_from.', '.$news_per_page) as $row){
		$tEntry[] = array(
			'id' => $row['id'],
			'author' =>  $row['author'],
			'author_id' =>  $row['author_id'],
			'who_author_id' => $row['who_author_id'],
			'who_author' => $row['who_author'],
			'author_link' => link_profile($row['author_id'], '', $row['author']),
			'who_author_link' => link_profile($row['who_author_id'], '', $row['who_author']),
			'message' => $row['message'],
			'post_link' => link_post($row['pid']),
			'home_url' => $config['home_url'],
			'viewed' => $row['viewed']
		);
	}
	
	$tVars = array(
		'entries' => isset($tEntry)?$tEntry:'',
		'pagesss' => generateAdminPagelist( array('current' => $pageNo, 'count' => $countPages, 'url' => admin_url.'/admin.php?mod=extra-config&plugin=forum&action=complaints'.($_REQUEST['news_per_page']?'&news_per_page='.$news_per_page:'').($_REQUEST['author']?'&author='.$_REQUEST['author']:'').($_REQUEST['sort']?'&sort='.$_REQUEST['sort']:'').($postdate?'&postdate='.$postdate:'').($author?'&author='.$author:'').($status?'&status='.$status:'').'&page=%page%'))
	);
	
	$xt = $twig->loadTemplate($tpath['main'].'main.tpl');
	$tVars = array(
		'info' => $info,
		'global' => 'Список жалоб',
		'entries' => $xg->render($tVars),
	);
	
	print $xt->render($tVars);
}

function del_forum(){
global $twig, $plugin, $mysql;
	
	include_once(dirname(__FILE__).'/includes/constants.php');
	include_once(dirname(__FILE__).'/includes/cache.php');
	
	$id = intval($_REQUEST['id']);
	
	$c = $mysql->result('SELECT 1 FROM '.prefix.'_forum_topics WHERE fid = '.db_squote($id).' LIMIT 1');
	
	if(!$mysql->result('SELECT 1 FROM '.prefix.'_forum_topics WHERE fid = '.db_squote($id).' LIMIT 1')){
		$mysql->query('DELETE FROM '.prefix.'_forum_forums WHERE id = '.db_squote($id).' LIMIT 1');
		$_SESSION['forum']['info'] = 'Форум удален';
		generate_index_cache(true);
	} else {
		$_SESSION['forum']['info'] = 'Форум не удален';
	}
	
	redirect_forum_config('?mod=extra-config&plugin=forum&action=list_forum');


}

function del_section(){
	global $plugin, $mysql;
	
	include_once(dirname(__FILE__).'/includes/constants.php');
	include_once(dirname(__FILE__).'/includes/cache.php');
	
	$id = intval($_REQUEST['id']);
	
	if(!$mysql->result('SELECT 1 FROM '.prefix.'_forum_forums WHERE parent = '.db_squote($id).' LIMIT 1')){
		$mysql->query('DELETE FROM '.prefix.'_forum_forums WHERE id = '.db_squote($id).' LIMIT 1');
		$_SESSION['forum']['info'] = 'Раздел удален';
		generate_index_cache(true);
	}else
		$_SESSION['forum']['info'] = 'Нельзя удалять раздел с форумом';
	
	redirect_forum_config('?mod=extra-config&plugin=forum&action=list_forum');
}

function edit_section(){
	global $twig, $plugin, $mysql;
	
	include_once(dirname(__FILE__).'/includes/constants.php');
	include_once(dirname(__FILE__).'/includes/cache.php');
	
	$tpath = locatePluginTemplates(array('main', 'edit_section'), $plugin, 1, '', 'config');
	$xg = $twig->loadTemplate($tpath['edit_section'].'edit_section.tpl');
	
	$id = intval($_REQUEST['id']);
	if(empty($id))
		redirect_forum_config('?mod=extra-config&plugin=forum&action=list_forum');
	
	$forum = $mysql->record('SELECT * FROM '.prefix.'_forum_forums WHERE id = '.db_squote($id).' LIMIT 1');
	
	$name = isset($_REQUEST['name'])?secure_html(trim($_REQUEST['name'])):secure_html(trim($forum['title']));
	$description = isset($_REQUEST['description'])?secure_html(trim($_REQUEST['description'])):secure_html(trim($forum['description']));
	$keywords = isset($_REQUEST['keywords'])?secure_html(trim($_REQUEST['keywords'])):secure_html(trim($forum['keywords']));
	
	if (isset($_REQUEST['submit'])){
		if(empty($name)) $error_text[] = 'Название раздела обязательно для заполнения';
		
		if(empty($error_text)){
			if(isset($name) && $name) $SQL['title'] = $name;
			
			if(isset($description) && $description) $SQL['description'] = $description;
			
			if(isset($keywords) && $keywords) $SQL['keywords'] = $keywords;
			
			$vnamess = array();
			foreach ($SQL as $k => $v) { $vnamess[] = $k.' = '.db_squote($v); }
				$mysql->query('update '.prefix.'_forum_forums set '.implode(', ',$vnamess).' where id = \''.intval($id).'\' LIMIT 1');
			
			generate_index_cache(true);
			redirect_forum_config('?mod=extra-config&plugin=forum&action=list_forum');
		}
	}
	
	$error_input = array();
	if(isset($error_text) && is_array($error_text))
		foreach($error_text as $error)
			$error_input[] = msg(array("type" => "error", "text" => $error), 0, 2);
	
	$tVars = array(
		'name' => $name,
		'description' => $description,
		'keywords' => $keywords,
		'list_error' => $error_input,
	);
	
	$xt = $twig->loadTemplate($tpath['main'].'main.tpl');
	$tVars = array(
		'global' => 'Редактирование раздела',
		'entries' => $xg->render($tVars)
	);
	
	print $xt->render($tVars);
}

function send_section(){
	global $twig, $plugin, $mysql;
	
	include_once(dirname(__FILE__).'/includes/constants.php');
	include_once(dirname(__FILE__).'/includes/cache.php');
	
	$tpath = locatePluginTemplates(array('main', 'send_section'), $plugin, 1, '', 'config');
	$xg = $twig->loadTemplate($tpath['send_section'].'send_section.tpl');
	
	$name = secure_html(convert($_REQUEST['name']));
	$description = secure_html(convert($_REQUEST['description']));
	$keywords = secure_html(convert($_REQUEST['keywords']));
	
	if (isset($_REQUEST['submit'])){
		if(empty($name)) $error_text[] = 'Название раздела обязательно для заполнения';
		
		if(empty($error_text)){
			$sql = 'SELECT MAX(position) FROM '.prefix.'_forum_forums where parent = \'0\'';
			$posit = $mysql-> result( $sql ) + 1;
			
			$mysql->query('INSERT INTO '.prefix.'_forum_forums
				(title, description, keywords, position)
				VALUES
				('.db_squote($name).', '.db_squote($description).', '.db_squote($keywords).', '.intval($posit).')
			');
			
			generate_index_cache(true);
			redirect_forum_config('?mod=extra-config&plugin=forum&action=list_forum');
		}
	}
	
	$error_input = array();
	if(isset($error_text) && is_array($error_text))
		foreach($error_text as $error)
			$error_input[] = msg(array("type" => "error", "text" => $error), 0, 2);
	
	$tVars = array(
		'name' => $name,
		'description' => $description,
		'keywords' => $keywords,
		'list_error' => $error_input,
	);
	
	$xt = $twig->loadTemplate($tpath['main'].'main.tpl');
	$tVars = array(
		'global' => 'Добавить раздел',
		'entries' => $xg->render($tVars)
	);
	
	print $xt->render($tVars);
}

function edit_forum(){
	global $twig, $plugin, $mysql;
	
	include_once(dirname(__FILE__).'/includes/security.php');
	include_once(dirname(__FILE__).'/includes/constants.php');
	include_once(dirname(__FILE__).'/includes/cache.php');
	
	include_once(FORUM_CACHE.'/permission.php');
	
	$tpath = locatePluginTemplates(array('main', 'edit_forum'), $plugin, 1, '', 'config');
	$xg = $twig->loadTemplate($tpath['edit_forum'].'edit_forum.tpl');
	
	
	$id = intval($_REQUEST['id']);
	if(empty($id))
		redirect_forum_config('?mod=extra-config&plugin=forum&action=list_forum');
	
	$forum = $mysql->record('SELECT * FROM '.prefix.'_forum_forums WHERE id = '.db_squote($id).' LIMIT 1');
	
	$name = isset($_REQUEST['name'])?secure_html(trim($_REQUEST['name'])):secure_html(trim($forum['title']));
	$description = isset($_REQUEST['description'])?secure_html(trim($_REQUEST['description'])):secure_html(trim($forum['description']));
	$keywords = isset($_REQUEST['keywords'])?secure_html(trim($_REQUEST['keywords'])):secure_html(trim($forum['keywords']));
	$moderators = isset($_REQUEST['moderators'])?secure_html(trim($_REQUEST['moderators'])):secure_html(trim($forum['moderators']));
	$parent = isset($_REQUEST['parent'])?(int)$_REQUEST['parent']:$forum['parent'];
	
	if (isset($_REQUEST['submit'])){
		if(empty($name)) $error_text[] = 'Название форума не заполнено';
		
		if(isset($moderators) && $moderators){
			$moder_array = array_map('trim', explode(',',$moderators));
			$moder_array = array_unique($moder_array);
			$i=0;
			foreach ($moder_array as $row){
				if(!$user[] = $mysql->record('SELECT id, name FROM '.prefix.'_users where name = LOWER(\''.$row.'\') LIMIT 1')){
					$error_text[] = 'Пользователь '.$row.' не найден';
					array_splice($user, $i, 1);
				}
				$i++;
			}
		} else $user = array();
		
		if(empty($error_text)){
			$SQL = array();
			
			$SQL['moderators'] = serialize($user);
			
			if(isset($name) && $name) $SQL['title'] = $name;
			
			if(isset($desc) && $desc) $SQL['description'] = $desc;
			
			if(isset($keyw) && $keyw) $SQL['keywords'] = $keyw;
			
			if(isset($parent) && $parent) $SQL['parent'] = $parent;
			
			foreach ($GROUP_PERM as $key => $value){
				//print "<pre>".var_export($key, true)."</pre>";
				$GROUP_PERM[$key]['forum_prem'][$id] = array(
					'forum_read' => secureinput($_REQUEST['GROUP_PERM'][$key]['forum_prem'][$id]['forum_read'] ),
					'topic_read' => secureinput($_REQUEST['GROUP_PERM'][$key]['forum_prem'][$id]['topic_read'] ),
					'topic_send' => secureinput($_REQUEST['GROUP_PERM'][$key]['forum_prem'][$id]['topic_send'] ),
					'topic_modify' => secureinput($_REQUEST['GROUP_PERM'][$key]['forum_prem'][$id]['topic_modify'] ),
					'topic_modify_your' => secureinput($_REQUEST['GROUP_PERM'][$key]['forum_prem'][$id]['topic_modify_your'] ),
					'topic_closed' => secureinput($_REQUEST['GROUP_PERM'][$key]['forum_prem'][$id]['topic_closed'] ),
					'topic_closed_your' => secureinput($_REQUEST['GROUP_PERM'][$key]['forum_prem'][$id]['topic_closed_your'] ),
					'topic_remove' => secureinput($_REQUEST['GROUP_PERM'][$key]['forum_prem'][$id]['topic_remove'] ),
					'topic_remove_your' => secureinput($_REQUEST['GROUP_PERM'][$key]['forum_prem'][$id]['topic_remove_your'] ),
					'post_send' => secureinput($_REQUEST['GROUP_PERM'][$key]['forum_prem'][$id]['post_send'] ),
					'post_modify' => secureinput($_REQUEST['GROUP_PERM'][$key]['forum_prem'][$id]['post_modify'] ),
					'post_modify_your' => secureinput($_REQUEST['GROUP_PERM'][$key]['forum_prem'][$id]['post_modify_your'] ),
					'post_remove' => secureinput($_REQUEST['GROUP_PERM'][$key]['forum_prem'][$id]['post_remove'] ),
					'post_remove_your' => secureinput($_REQUEST['GROUP_PERM'][$key]['forum_prem'][$id]['post_remove_your'] ),
				);
			}
			
			file_put_contents(FORUM_CACHE.'/permission.php', '<?php'."\n\n".'$GROUP_PERM = '.var_export($GROUP_PERM, true).';');
			
			$vnamess = array();
			foreach ($SQL as $k => $v) { $vnamess[] = $k.' = '.db_squote($v); }
				$mysql->query('update '.prefix.'_forum_forums set '.implode(', ',$vnamess).' where id = \''.intval($id).'\'');
			
			generate_index_cache(true);
			redirect_forum_config('?mod=extra-config&plugin=forum&action=list_forum');
		}
	}
	//print "<pre>".var_export($_REQUEST['GROUP_PERM'], true)."</pre>";
	foreach ($mysql-> select("SELECT id, title FROM ".prefix."_forum_forums WHERE parent = '0' ORDER BY position", 1 ) as $row){
		$tEntry[] = array(
			'id'		=>	$row['id'],
			'title'		=>	$row['title'],
			'id_set'	=> intval($parent)
		);
	}
	
	print "<pre>".var_export($GROUP_PERM, true)."</pre>";
	foreach ($GROUP_PERM as $key => $value){
		//print "<pre>".var_export($key, true).' --'.var_export($value, true)."</pre>";
		$tEntry2[] = array(
			'id' => $key,
			'name' => $value['name'],
			'forum_read' => MakeDropDown(array(false => 'нет', true => 'да'), 'GROUP_PERM['.$key.'][forum_prem]['.$id.'][forum_read]', $GROUP_PERM[$key]['forum_prem'][$id]['forum_read']),
			'topic_read' => MakeDropDown(array(false => 'нет', true => 'да'), 'GROUP_PERM['.$key.'][forum_prem]['.$id.'][topic_read]', $GROUP_PERM[$key]['forum_prem'][$id]['topic_read']),
			'topic_send' => MakeDropDown(array(false => 'нет', true => 'да'), 'GROUP_PERM['.$key.'][forum_prem]['.$id.'][topic_send]', $GROUP_PERM[$key]['forum_prem'][$id]['topic_send']),
			'topic_modify' => MakeDropDown(array(false => 'нет', true => 'да'), 'GROUP_PERM['.$key.'][forum_prem]['.$id.'][topic_modify]', $GROUP_PERM[$key]['forum_prem'][$id]['topic_modify']),
			'topic_modify_your' => MakeDropDown(array(false => 'нет', true => 'да'), 'GROUP_PERM['.$key.'][forum_prem]['.$id.'][topic_modify_your]', $GROUP_PERM[$key]['forum_prem'][$id]['topic_modify_your']),
			'topic_closed' => MakeDropDown(array(false => 'нет', true => 'да'), 'GROUP_PERM['.$key.'][forum_prem]['.$id.'][topic_closed]', $GROUP_PERM[$key]['forum_prem'][$id]['topic_closed']),
			'topic_closed_your' => MakeDropDown(array(false => 'нет', true => 'да'), 'GROUP_PERM['.$key.'][forum_prem]['.$id.'][topic_closed_your]', $GROUP_PERM[$key]['forum_prem'][$id]['topic_closed_your']),
			'topic_remove' => MakeDropDown(array(false => 'нет', true => 'да'), 'GROUP_PERM['.$key.'][forum_prem]['.$id.'][topic_remove]', $GROUP_PERM[$key]['forum_prem'][$id]['topic_remove']),
			'topic_remove_your' => MakeDropDown(array(false => 'нет', true => 'да'), 'GROUP_PERM['.$key.'][forum_prem]['.$id.'][topic_remove_your]', $GROUP_PERM[$key]['forum_prem'][$id]['topic_remove_your']),
			'post_send' => MakeDropDown(array(false => 'нет', true => 'да'), 'GROUP_PERM['.$key.'][forum_prem]['.$id.'][post_send]', $GROUP_PERM[$key]['forum_prem'][$id]['post_send']),
			'post_modify' => MakeDropDown(array(false => 'нет', true => 'да'), 'GROUP_PERM['.$key.'][forum_prem]['.$id.'][post_modify]', $GROUP_PERM[$key]['forum_prem'][$id]['post_modify']),
			'post_modify_your' => MakeDropDown(array(false => 'нет', true => 'да'), 'GROUP_PERM['.$key.'][forum_prem]['.$id.'][post_modify_your]', $GROUP_PERM[$key]['forum_prem'][$id]['post_modify_your']),
			'post_remove' => MakeDropDown(array(false => 'нет', true => 'да'), 'GROUP_PERM['.$key.'][forum_prem]['.$id.'][post_remove]', $GROUP_PERM[$key]['forum_prem'][$id]['post_remove']),
			'post_remove_your' => MakeDropDown(array(false => 'нет', true => 'да'), 'GROUP_PERM['.$key.'][forum_prem]['.$id.'][post_remove_your]', $GROUP_PERM[$key]['forum_prem'][$id]['post_remove_your']),
		);
	}
	
	if(!$_REQUEST['moderators']){
		$moderators = array();
		foreach (unserialize($forum['moderators']) as $row){
			$moderators[] = $row['name'];
		}
		
		$moderators = array_unique($moderators);
		$moderators = implode(', ',$moderators);
	}
	
	$error_input = array();
	if(isset($error_text) && is_array($error_text))
		foreach($error_text as $error)
			$error_input[] = msg(array("type" => "error", "text" => $error), 0, 2);
	
	$tVars = array(
		'name' => $name,
		'description' => $description,
		'keywords' => $keywords,
		'moderators' => $moderators,
		'list_forum' => $tEntry,
		'list_group' => $tEntry2,
		'list_error' => $error_input,
	);
	
	$xt = $twig->loadTemplate($tpath['main'].'main.tpl');
	$tVars = array(
		'global' => 'Редактировать форум',
		'entries' => $xg->render($tVars)
	);
	
	print $xt->render($tVars);
}

function send_forum(){
global $twig, $plugin, $mysql;
	
	include_once(dirname(__FILE__).'/includes/constants.php');
	include_once(dirname(__FILE__).'/includes/cache.php');
	
	$tpath = locatePluginTemplates(array('main', 'send_forum'), $plugin, 1, '', 'config');
	$xg = $twig->loadTemplate($tpath['send_forum'].'send_forum.tpl');
	
	$id = intval($_REQUEST['id']);
	if(empty($id))
		redirect_forum_config('?mod=extra-config&plugin=forum&action=list_forum');
	
	$forum = $mysql->record('SELECT * FROM '.prefix.'_forum_forums WHERE id = '.db_squote($id).' LIMIT 1');
	
	$name = isset($_REQUEST['name'])?secure_html(trim($_REQUEST['name'])):'';
	$description = isset($_REQUEST['description'])?secure_html(trim($_REQUEST['description'])):'';
	$keywords = isset($_REQUEST['keywords'])?secure_html(trim($_REQUEST['keywords'])):'';
	$moderators = isset($_REQUEST['moderators'])?secure_html(trim($_REQUEST['moderators'])):'';
	//$parent = isset($_REQUEST['parent'])?(int)$_REQUEST['parent']:'';
	
	if (isset($_REQUEST['submit'])){
		if(empty($name)) $error_text[] = 'Название форума не заполнено';
		
		if(isset($moderators) && $moderators){
			$moder_array = array_map('trim', explode(',',$moderators));
			$moder_array = array_unique($moder_array);
			$i=0;
			foreach ($moder_array as $row){
				if(!$user[] = $mysql->record('SELECT id, name FROM '.prefix.'_users where name = LOWER(\''.$row.'\') LIMIT 1')){
					$error_text[] = 'Пользователь '.$row.' не найден';
					array_splice($user, $i, 1);
				}
				$i++;
			}
		} else $user = array();
		
		//print "<pre>".var_export($user, true)."</pre>";
		if(empty($error_text)){
			$sql = 'SELECT MAX(position) FROM '.prefix.'_forum_forums where parent = '.intval($forum['id']).'';
			$posit = $mysql-> result( $sql ) + 1;
			
			$mysql->query('INSERT INTO '.prefix.'_forum_forums
				(title, description, keywords, parent, moderators, position)
				VALUES
				('.db_squote($name).', '.db_squote($description).', '.db_squote($keywords).', '.intval($forum['id']).', \''.serialize($user).'\', '.intval($posit).')
			');
			
			generate_index_cache(true);
			redirect_forum_config('?mod=extra-config&plugin=forum&action=list_forum');
		}
	}
	
	if(!$_REQUEST['moderators']){
		$moderators = array();
		foreach (unserialize($forum['moderators']) as $row){
			$moderators[] = $row['name'];
		}
		
		$moderators = array_unique($moderators);
		$moderators = implode(', ',$moderators);
	}
	
	//print "<pre>".var_export($Smoder, true)."</pre>";
	
	$error_input = array();
	if(isset($error_text) && is_array($error_text))
		foreach($error_text as $error)
			$error_input[] = msg(array("type" => "error", "text" => $error), 0, 2);
	
	$tVars = array(
		'name' => $name,
		'description' => $description,
		'keywords' => $keywords,
		'moderators' => $moderators,
		'forum_name' => $forum['title'],
		'forum_id' => $forum['id'],
		'list_forum' => $tEntry,
		'list_error' => $error_input,
	);
	
	$xt = $twig->loadTemplate($tpath['main'].'main.tpl');
	$tVars = array(
		'global' => 'Добавить форум',
		'entries' => $xg->render($tVars)
	);
	
	print $xt->render($tVars);
}

function title(){
global $twig, $plugin;
	$tpath = locatePluginTemplates(array('main', 'title'), 'forum', 1, '', 'config');
	
	if (isset($_REQUEST['submit'])){
		pluginSetVariable($plugin, 'home_title', secure_html(trim($_REQUEST['home_title'])));
		pluginSetVariable($plugin, 'forums_title', secure_html(trim($_REQUEST['forums_title'])));
		pluginSetVariable($plugin, 'topic_title', secure_html(trim($_REQUEST['topic_title'])));
		pluginSetVariable($plugin, 'userlist_title', secure_html(trim($_REQUEST['userlist_title'])));
		pluginSetVariable($plugin, 'search_title', secure_html(trim($_REQUEST['search_title'])));
		pluginSetVariable($plugin, 'register_title', secure_html(trim($_REQUEST['register_title'])));
		pluginSetVariable($plugin, 'login_title', secure_html(trim($_REQUEST['login_title'])));
		pluginSetVariable($plugin, 'profile_title', secure_html(trim($_REQUEST['profile_title'])));
		pluginSetVariable($plugin, 'out_title', secure_html(trim($_REQUEST['out_title'])));
		pluginSetVariable($plugin, 'addreply_title', secure_html(trim($_REQUEST['addreply_title'])));
		pluginSetVariable($plugin, 'newtopic_title', secure_html(trim($_REQUEST['newtopic_title'])));
		pluginSetVariable($plugin, 'delpost_title', secure_html(trim($_REQUEST['delpost_title'])));
		pluginSetVariable($plugin, 'edit_title', secure_html(trim($_REQUEST['edit_title'])));
		pluginSetVariable($plugin, 'rules_title', secure_html(trim($_REQUEST['rules_title'])));
		pluginSetVariable($plugin, 'show_new_title', secure_html(trim($_REQUEST['show_new_title'])));
		pluginSetVariable($plugin, 'markread_title', secure_html(trim($_REQUEST['markread_title'])));
		pluginSetVariable($plugin, 'rep_title', secure_html(trim($_REQUEST['rep_title'])));
		pluginSetVariable($plugin, 'addr_title', secure_html(trim($_REQUEST['addr_title'])));
		pluginSetVariable($plugin, 'news_title', secure_html(trim($_REQUEST['news_title'])));
		pluginSetVariable($plugin, 'news_feed_title', secure_html(trim($_REQUEST['news_feed_title'])));
		pluginSetVariable($plugin, 'act_title', secure_html(trim($_REQUEST['act_title'])));
		pluginSetVariable($plugin, 'thank_title', secure_html(trim($_REQUEST['thank_title'])));
		pluginSetVariable($plugin, 'complaints_title', secure_html(trim($_REQUEST['complaints_title'])));
		pluginSetVariable($plugin, 'send_pm_title', secure_html(trim($_REQUEST['send_pm_title'])));
		pluginSetVariable($plugin, 'list_pm_title', secure_html(trim($_REQUEST['list_pm_title'])));
		pluginSetVariable($plugin, 'del_pm_title', secure_html(trim($_REQUEST['del_pm_title'])));
		pluginSetVariable($plugin, 'downloads_title', secure_html(trim($_REQUEST['downloads_title'])));
		pluginSetVariable($plugin, 'erro404_title', secure_html(trim($_REQUEST['erro404_title'])));
		pluginSetVariable($plugin, 'num_title', secure_html(trim($_REQUEST['num_title'])));
		
		pluginsSaveConfig();
		redirect_forum_config('?mod=extra-config&plugin=forum&action=title');
	}
	
	$home_title = pluginGetVariable($plugin, 'home_title');
	$forums_title = pluginGetVariable($plugin, 'forums_title');
	$topic_title = pluginGetVariable($plugin, 'topic_title');
	$userlist_title = pluginGetVariable($plugin, 'userlist_title');
	$search_title = pluginGetVariable($plugin, 'search_title');
	$register_title = pluginGetVariable($plugin, 'register_title');
	$login_title = pluginGetVariable($plugin, 'login_title');
	$profile_title = pluginGetVariable($plugin, 'profile_title');
	$out_title = pluginGetVariable($plugin, 'out_title');
	$addreply_title = pluginGetVariable($plugin, 'addreply_title');
	$newtopic_title = pluginGetVariable($plugin, 'newtopic_title');
	$delpost_title = pluginGetVariable($plugin, 'delpost_title');
	$edit_title = pluginGetVariable($plugin, 'edit_title');
	$rules_title = pluginGetVariable($plugin, 'rules_title');
	$show_new_title = pluginGetVariable($plugin, 'show_new_title');
	$markread_title = pluginGetVariable($plugin, 'markread_title');
	$rep_title = pluginGetVariable($plugin, 'rep_title');
	$addr_title = pluginGetVariable($plugin, 'addr_title');
	$news_title = pluginGetVariable($plugin, 'news_title');
	$news_feed_title = pluginGetVariable($plugin, 'news_feed_title');
	$act_title = pluginGetVariable($plugin, 'act_title');
	$thank_title = pluginGetVariable($plugin, 'thank_title');
	$complaints_title = pluginGetVariable($plugin, 'complaints_title');
	$send_pm_title = pluginGetVariable($plugin, 'send_pm_title');
	$list_pm_title = pluginGetVariable($plugin, 'list_pm_title');
	$del_pm_title = pluginGetVariable($plugin, 'del_pm_title');
	$downloads_title = pluginGetVariable($plugin, 'downloads_title');
	$erro404_title = pluginGetVariable($plugin, 'erro404_title');
	$num_title = pluginGetVariable($plugin, 'num_title');
	
	$xg = $twig->loadTemplate($tpath['title'].'title.tpl');
	$tVars = array(
		'home_title' => array(
			'print' => $home_title,
			'error' => empty($home_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> %name_forum%':''
		),
		'forums_title' => array(
			'print' => $forums_title,
			'error' => empty($forums_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> %4% / %name_forum% [/ %num%]':''
		),
		'topic_title' => array(
			'print' => $topic_title,
			'error' => empty($topic_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> %name_topic% / %cat_forum% [/ %num%]':''
		),
		'userlist_title' => array(
			'print' => $userlist_title,
			'error' => empty($userlist_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Список пользователей / %name_forum%':''
		),
		'search_title' => array(
			'print' => $search_title,
			'error' => empty($search_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Поиск / %name_forum%':''
		),
		'register_title' => array(
			'print' => $register_title,
			'error' => empty($register_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Регистрация / %name_forum%':''
		),
		'login_title' => array(
			'print' => $login_title,
			'error' => empty($login_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Зайти на сайт / %name_forum%':''
		),
		'profile_title' => array(
			'print' => $profile_title,
			'error' => empty($profile_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> %others% / %name_forum%':''
		),
		'out_title' => array(
			'print' => $out_title,
			'error' => empty($out_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Выйти / %name_forum%':''
		),
		'addreply_title' => array(
			'print' => $addreply_title,
			'error' => empty($addreply_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Добавить сообщение / %name_forum%':''
		),
		'newtopic_title' => array(
			'print' => $newtopic_title,
			'error' => empty($newtopic_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Добавить тему / %name_forum%':''
		),
		'delpost_title' => array(
			'print' => $delpost_title,
			'error' => empty($delpost_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Удалить сообщение / %name_forum%':''
		),
		'edit_title' => array(
			'print' => $edit_title,
			'error' => empty($edit_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Редактировать / %name_forum%':''
		),
		'rules_title' => array(
			'print' => $rules_title,
			'error' => empty($rules_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Правила / %name_forum%':''
		),
		'show_new_title' => array(
			'print' => $show_new_title,
			'error' => empty($show_new_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Последние сообщения / %name_forum%':''
		),
		'markread_title' => array(
			'print' => $markread_title,
			'error' => empty($markread_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Всё прочитано / %name_forum%':''
		),
		'rep_title' => array(
			'print' => $rep_title,
			'error' => empty($rep_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Репутация участника %others% / %name_forum%':''
		),
		'addr_title' => array(
			'print' => $addr_title,
			'error' => empty($addr_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Добавить репутацию / %name_forum%':''
		),
		'news_title' => array(
			'print' => $news_title,
			'error' => empty($news_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> %name_news% / Новости / %name_forum%':''
		),
		'news_feed_title' => array(
			'print' => $news_feed_title,
			'error' => empty($news_feed_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Вся лента / %name_forum% [/ %num%]':''
		),
		'act_title' => array(
			'print' => $act_title,
			'error' => empty($act_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> %others% / %name_forum%':''
		),
		'thank_title' => array(
			'print' => $thank_title,
			'error' => empty($thank_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> История благодарностей участнику %others% / %name_forum%':''
		),
		'complaints_title' => array(
			'print' => $complaints_title,
			'error' => empty($complaints_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Сообщить модератору / %name_forum%':''
		),
		'send_pm_title' => array(
			'print' => $send_pm_title,
			'error' => empty($send_pm_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Новое сообщение / %name_forum%':''
		),
		'list_pm_title' => array(
			'print' => $list_pm_title,
			'error' => empty($list_pm_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Личное сообщение / %name_forum%':''
		),
		'del_pm_title' => array(
			'print' => $del_pm_title,
			'error' => empty($del_pm_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Удалить сообщение / %name_forum%':''
		),
		'downloads_title' => array(
			'print' => $downloads_title,
			'error' => empty($downloads_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Загрузка файла / %name_forum%':''
		),
		'erro404_title' => array(
			'print' => $erro404_title,
			'error' => empty($erro404_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> Информация / %name_forum%':''
		),
		'num_title' => array(
						'print' => $num_title,
						'error' => empty($num_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> / Страница %count%':''
		),
	);
	
	$xt = $twig->loadTemplate($tpath['main'].'main.tpl');
	$tVars = array(
		'global' => 'Заголовки форума',
		'entries' => $xg->render($tVars)
	);
	
	print $xt->render($tVars);
}

function url()
{global $twig, $plugin;
	$tpath = locatePluginTemplates(array('main', 'url'), 'forum', 1, '', 'config');
	
	if($_REQUEST['id']){
		$ULIB = new urlLibrary();
		$ULIB->loadConfig();
		
		$UHANDLER = new urlHandler();
		$UHANDLER->loadConfig();
		
		switch($_REQUEST['id']){
			case 'home':
				if($_REQUEST['s']){
					$ULIB->registerCommand('forum', '',
						array ('vars' =>
							array(),
							'descr'	=> array ('russian' => 'Главная страница форума'),
						)
					);
					
					$UHANDLER->registerHandler(0,
						array (
							'pluginName' => 'forum',
							'handlerName' => '',
							'flagPrimary' => true,
							'flagFailContinue' => false,
							'flagDisabled' => false,
							'rstyle' => 
							array (
							  'rcmd' => '/forum/',
							  'regex' => '#^/forum/$#',
							  'regexMap' => 
							  array (
							  ),
							  'reqCheck' => 
							  array (
							  ),
							  'setVars' => 
							  array (
							  ),
							  'genrMAP' => 
							  array (
								0 => 
								array (
								  0 => 0,
								  1 => '/forum/',
								  2 => 0,
								),
							  ),
							),
						  )
					);
				} else {
					$ULIB->removeCommand('forum', '');
					$UHANDLER->removePluginHandlers('forum', '');
				}
			break;
			case 'register':
				if($_REQUEST['s']){
					$ULIB->registerCommand('forum', 'register',
						array ('vars' =>
								array(),
								'descr'	=> array ('russian' => 'Страница регистрации на форуме'),
						)
					);
					
					$UHANDLER->registerHandler(0,
						array (
							'pluginName' => 'forum',
							'handlerName' => 'register',
							'flagPrimary' => true,
							'flagFailContinue' => false,
							'flagDisabled' => false,
							'rstyle' => 
							array (
							  'rcmd' => '/forum/register/',
							  'regex' => '#^/forum/register/$#',
							  'regexMap' => 
							  array (
							  ),
							  'reqCheck' => 
							  array (
							  ),
							  'setVars' => 
							  array (
							  ),
							  'genrMAP' => 
							  array (
								0 => 
								array (
								  0 => 0,
								  1 => '/forum/register/',
								  2 => 0,
								),
							  ),
							),
						  )
					);
				} else {
					$ULIB->removeCommand('forum', 'register');
					$UHANDLER->removePluginHandlers('forum', 'register');
				}
			break;
			case 'login':
				if($_REQUEST['s']){
					$ULIB->registerCommand('forum', 'login',
						array ('vars' =>
								array(),
								'descr'	=> array ('russian' => 'Страница авторизации на форуме'),
						)
					);
					
					$UHANDLER->registerHandler(0,
						array (
							'pluginName' => 'forum',
							'handlerName' => 'login',
							'flagPrimary' => true,
							'flagFailContinue' => false,
							'flagDisabled' => false,
							'rstyle' => 
							array (
							  'rcmd' => '/forum/login/',
							  'regex' => '#^/forum/login/$#',
							  'regexMap' => 
							  array (
							  ),
							  'reqCheck' => 
							  array (
							  ),
							  'setVars' => 
							  array (
							  ),
							  'genrMAP' => 
							  array (
								0 => 
								array (
								  0 => 0,
								  1 => '/forum/login/',
								  2 => 0,
								),
							  ),
							),
						  )
					);
				} else {
					$ULIB->removeCommand('forum', 'login');
					$UHANDLER->removePluginHandlers('forum', 'login');
				}
			break;
			case 'out':
				if($_REQUEST['s']){
					$ULIB->registerCommand('forum', 'out',
						array ('vars' =>
								array(),
								'descr'	=> array ('russian' => 'Выйти с форума'),
						)
					);
					
					$UHANDLER->registerHandler(0,
						array (
							'pluginName' => 'forum',
							'handlerName' => 'out',
							'flagPrimary' => true,
							'flagFailContinue' => false,
							'flagDisabled' => false,
							'rstyle' => 
							array (
							  'rcmd' => '/forum/out/',
							  'regex' => '#^/forum/out/$#',
							  'regexMap' => 
							  array (
							  ),
							  'reqCheck' => 
							  array (
							  ),
							  'setVars' => 
							  array (
							  ),
							  'genrMAP' => 
							  array (
								0 => 
								array (
								  0 => 0,
								  1 => '/forum/out/',
								  2 => 0,
								),
							  ),
							),
						  )
					);
				} else {
					$ULIB->removeCommand('forum', 'out');
					$UHANDLER->removePluginHandlers('forum', 'out');
				}
			break;
			case 'addreply':
				if($_REQUEST['s']){
					$ULIB->registerCommand('forum', 'addreply',
						array ('vars' =>
								array(	'id' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'id темы')),
								),
								'descr'	=> array ('russian' => 'Ссылка на новое сообщение'),
						)
					);
					
					$UHANDLER->registerHandler(0,
						 array (
							'pluginName' => 'forum',
							'handlerName' => 'addreply',
							'flagPrimary' => true,
							'flagFailContinue' => false,
							'flagDisabled' => false,
							'rstyle' => 
							array (
							  'rcmd' => '/forum/addreply/{id}/',
							  'regex' => '#^/forum/addreply/(\\d+)/$#',
							  'regexMap' => 
							  array (
								1 => 'id',
							  ),
							  'reqCheck' => 
							  array (
							  ),
							  'setVars' => 
							  array (
							  ),
							  'genrMAP' => 
							  array (
								0 => 
								array (
								  0 => 0,
								  1 => '/forum/addreply/',
								  2 => 0,
								),
								1 => 
								array (
								  0 => 1,
								  1 => 'id',
								  2 => 0,
								),
								2 => 
								array (
								  0 => 0,
								  1 => '/',
								  2 => 0,
								),
							  ),
							),
						  )
					);
				} else {
					$ULIB->removeCommand('forum', 'addreply');
					$UHANDLER->removePluginHandlers('forum', 'addreply');
				}
			break;
			case 'newtopic':
				if($_REQUEST['s']){
					$ULIB->registerCommand('forum', 'newtopic',
						array ('vars' =>
								array(	'id' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'id раздела')),
								),
								'descr'	=> array ('russian' => 'Ссылка на новую тему'),
						)
					);
					
					$UHANDLER->registerHandler(0,
						array (
							'pluginName' => 'forum',
							'handlerName' => 'newtopic',
							'flagPrimary' => true,
							'flagFailContinue' => false,
							'flagDisabled' => false,
							'rstyle' => 
							array (
							  'rcmd' => '/forum/newtopic/{id}/',
							  'regex' => '#^/forum/newtopic/(\\d+)/$#',
							  'regexMap' => 
							  array (
								1 => 'id',
							  ),
							  'reqCheck' => 
							  array (
							  ),
							  'setVars' => 
							  array (
							  ),
							  'genrMAP' => 
							  array (
								0 => 
								array (
								  0 => 0,
								  1 => '/forum/newtopic/',
								  2 => 0,
								),
								1 => 
								array (
								  0 => 1,
								  1 => 'id',
								  2 => 0,
								),
								2 => 
								array (
								  0 => 0,
								  1 => '/',
								  2 => 0,
								),
							  ),
							),
						  )
					);
				} else {
					$ULIB->removeCommand('forum', 'newtopic');
					$UHANDLER->removePluginHandlers('forum', 'newtopic');
				}
			break;
			case 'profile':
				if($_REQUEST['s']){
					$ULIB->registerCommand('forum', 'profile',
						array ('vars' =>
								array(	'id' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'id пользователя')),
										'name' => array('matchRegex' => '.+?', 'descr' => array('russian' => 'Логин пользователя')),
										'act' => array('matchRegex' => '.+?', 'descr' => array('russian' => 'Дополнительное действие')),
								),
								'descr'	=> array ('russian' => 'Ссылка на профиль'),
						)
					);
					
					$UHANDLER->registerHandler(0,
						array (
							'pluginName' => 'forum',
							'handlerName' => 'profile',
							'flagPrimary' => true,
							'flagFailContinue' => false,
							'flagDisabled' => false,
							'rstyle' => 
							array (
							  'rcmd' => '/forum/profile/{name}/[{act}/]',
							  'regex' => '#^/forum/profile/(.+?)/(?:(.+?)/){0,1}$#',
							  'regexMap' => 
							  array (
								1 => 'name',
								2 => 'act',
							  ),
							  'reqCheck' => 
							  array (
							  ),
							  'setVars' => 
							  array (
							  ),
							  'genrMAP' => 
							  array (
								0 => 
								array (
								  0 => 0,
								  1 => '/forum/profile/',
								  2 => 0,
								),
								1 => 
								array (
								  0 => 1,
								  1 => 'name',
								  2 => 0,
								),
								2 => 
								array (
								  0 => 0,
								  1 => '/',
								  2 => 0,
								),
								3 => 
								array (
								  0 => 1,
								  1 => 'act',
								  2 => 1,
								),
								4 => 
								array (
								  0 => 0,
								  1 => '/',
								  2 => 1,
								),
							  ),
							),
						  )
					);
				} else {
					$ULIB->removeCommand('forum', 'profile');
					$UHANDLER->removePluginHandlers('forum', 'profile');
				}
			break;
			case 'showforum':
				if($_REQUEST['s']){
					$ULIB->registerCommand('forum', 'showforum',
						array ('vars' =>
								array(	'id' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'id  категории')),
										'page' => array('matchRegex' => '\d{1,4}', 'descr' => array('russian' => 'Постраничная навигация')),
								),
								'descr'	=> array ('russian' => 'Ссылка на категорию'),
						)
					);
					
					$UHANDLER->registerHandler(0,
						array (
							'pluginName' => 'forum',
							'handlerName' => 'showforum',
							'flagPrimary' => true,
							'flagFailContinue' => false,
							'flagDisabled' => false,
							'rstyle' => 
							array (
							  'rcmd' => '/forum/forum{id}[p{page}].html',
							  'regex' => '#^/forum/forum(\\d+)(?:p(\\d{1,4})){0,1}.html$#',
							  'regexMap' => 
							  array (
								1 => 'id',
								2 => 'page',
							  ),
							  'reqCheck' => 
							  array (
							  ),
							  'setVars' => 
							  array (
							  ),
							  'genrMAP' => 
							  array (
								0 => 
								array (
								  0 => 0,
								  1 => '/forum/forum',
								  2 => 0,
								),
								1 => 
								array (
								  0 => 1,
								  1 => 'id',
								  2 => 0,
								),
								2 => 
								array (
								  0 => 0,
								  1 => 'p',
								  2 => 1,
								),
								3 => 
								array (
								  0 => 1,
								  1 => 'page',
								  2 => 1,
								),
								4 => 
								array (
								  0 => 0,
								  1 => '.html',
								  2 => 0,
								),
							  ),
							),
						  )
					);
				} else {
					$ULIB->removeCommand('forum', 'showforum');
					$UHANDLER->removePluginHandlers('forum', 'showforum');
				}
			break;
			case 'showtopic':
				if($_REQUEST['s']){
					$ULIB->registerCommand('forum', 'showtopic',
						array ('vars' =>
								array(	'id' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'id темы')),
										'pid' => array('matchRegex' => '\d+', 'descr' => array('russian' => 'id  сообщения')),
										'page' => array('matchRegex' => '\d{1,4}', 'descr' => array('russian' => 'Постраничная навигация')),
										's' => array('matchRegex' => '.+?', 'descr' => array('russian' => 'Поиск слов в теме')),
								),
								'descr'	=> array ('russian' => 'Ссылка на тему'),
						)
					);
					
					$UHANDLER->registerHandler(0,
						array (
							'pluginName' => 'forum',
							'handlerName' => 'showtopic',
							'flagPrimary' => true,
							'flagFailContinue' => false,
							'flagDisabled' => false,
							'rstyle' => 
							array (
							  'rcmd' => '/forum/[post{pid}][topic{id}][p{page}][/{s}].html',
							  'regex' => '#^/forum/(?:post(\\d+)){0,1}(?:topic(\\d+)){0,1}(?:p(\\d{1,4})){0,1}(?:/(.+?)){0,1}.html$#',
							  'regexMap' => 
							  array (
								1 => 'pid',
								2 => 'id',
								3 => 'page',
								4 => 's',
							  ),
							  'reqCheck' => 
							  array (
							  ),
							  'setVars' => 
							  array (
							  ),
							  'genrMAP' => 
							  array (
								0 => 
								array (
								  0 => 0,
								  1 => '/forum/',
								  2 => 0,
								),
								1 => 
								array (
								  0 => 0,
								  1 => 'post',
								  2 => 1,
								),
								2 => 
								array (
								  0 => 1,
								  1 => 'pid',
								  2 => 1,
								),
								3 => 
								array (
								  0 => 0,
								  1 => 'topic',
								  2 => 2,
								),
								4 => 
								array (
								  0 => 1,
								  1 => 'id',
								  2 => 2,
								),
								5 => 
								array (
								  0 => 0,
								  1 => 'p',
								  2 => 3,
								),
								6 => 
								array (
								  0 => 1,
								  1 => 'page',
								  2 => 3,
								),
								7 => 
								array (
								  0 => 0,
								  1 => '/',
								  2 => 4,
								),
								8 => 
								array (
								  0 => 1,
								  1 => 's',
								  2 => 4,
								),
								9 => 
								array (
								  0 => 0,
								  1 => '.html',
								  2 => 0,
								),
							  ),
							),
						  )
					 );
				} else {
					$ULIB->removeCommand('forum', 'showtopic');
					$UHANDLER->removePluginHandlers('forum', 'showtopic');
				}
			break;
		}
		
		$ULIB->saveConfig();
		$UHANDLER->saveConfig();
		
		redirect_forum_config('?mod=extra-config&plugin=forum&action=url');
	}
	
	$xg = $twig->loadTemplate($tpath['url'].'url.tpl');
	$tVars = array(
		'home_forum' => checkLinkAvailable('forum', ''),
		'register_forum' => checkLinkAvailable('forum', 'register'),
		'login_forum' => checkLinkAvailable('forum', 'login'),
		'out_forum' => checkLinkAvailable('forum', 'out'),
		'addreply_forum' => checkLinkAvailable('forum', 'addreply'),
		'newtopic_forum' => checkLinkAvailable('forum', 'newtopic'),
		'profile_forum' => checkLinkAvailable('forum', 'profile'),
		'showforum_forum' => checkLinkAvailable('forum', 'showforum'),
		'showtopic_forum' => checkLinkAvailable('forum', 'showtopic'),
	);
	
	$xt = $twig->loadTemplate($tpath['main'].'main.tpl');
	$tVars = array(
		'global' => 'ЧПУ',
		'entries' => $xg->render($tVars)
	);
	
	print $xt->render($tVars);
}

function moderat()
{global $mysql;
	include_once(dirname(__FILE__).'/includes/rewrite.php');
	
	switch ($_REQUEST['act']) {
		case 'pinned': 
			if($_REQUEST['method'] == 0)
				$method = 0;
			elseif($_REQUEST['method'] == 1)
				$method = 1;
			else
				$method = 0;
			print $confArray['predefined']['HTTP_REFERER'];
			$mysql->query('UPDATE '.prefix.'_forum_topics SET pinned = '.db_squote($method).' WHERE id = '.db_squote($_REQUEST['tid']).' LIMIT 1');
			//redirect_forum_config(link_forum($_REQUEST['tid']));
		break;
	}
}

function rules()
{global $twig, $plugin;
	if (isset($_REQUEST['submit'])){
		pluginSetVariable('forum', 'rules_on_off', (int)$_REQUEST['rules_on_off']);
		pluginSetVariable('forum', 'rules', secure_html(trim($_REQUEST['rules'])));
		pluginsSaveConfig();
		redirect_forum_config('?mod=extra-config&plugin=forum&action=rules');
	}
	
	$tpath = locatePluginTemplates(array('main', 'rules', ':'), 'forum', 1, '', 'config');
	$xg = $twig->loadTemplate($tpath['rules'].'rules.tpl');
	$tVars = array(
		'rules_on_off' => MakeDropDown(array(0 => 'Нет', 1 => 'Да'), 'rules_on_off', (int)pluginGetVariable($plugin,'rules_on_off')),
		'rules' => secure_html(trim(pluginGetVariable($plugin,'rules'))),
		'forum_tpl' => $tpath['url::'],
	);
	
	$xt = $twig->loadTemplate($tpath['main'].'main.tpl');
	$tVars = array(
		'global' => 'Правила',
		'entries' => $xg->render($tVars)
	);
	
	print $xt->render($tVars);
}

function ads()
{global $twig, $plugin;
	if (isset($_REQUEST['submit'])){
		pluginSetVariable('forum', 'announcement_on_off', (int)$_REQUEST['announcement_on_off']);
		pluginSetVariable('forum', 'announcement', secure_html(trim($_REQUEST['announcement'])));
		pluginsSaveConfig();
		//redirect_forum_config('?mod=extra-config&plugin=forum&action=ads');
	}
	
	$tpath = locatePluginTemplates(array('main', 'ads', ':'), 'forum', 1, '', 'config');
	$xg = $twig->loadTemplate($tpath['ads'].'ads.tpl');
	$tVars = array(
		'announcement_on_off' => MakeDropDown(array(0 => 'Нет', 1 => 'Да'), 'announcement_on_off', (int)pluginGetVariable($plugin,'announcement_on_off')),
		'announcement' => secure_html(trim(pluginGetVariable($plugin,'announcement'))),
		'forum_tpl' => $tpath['url::'],
	);
	
	$xt = $twig->loadTemplate($tpath['main'].'main.tpl');
	$tVars = array(
		'global' => 'Объявления',
		'entries' => $xg->render($tVars)
	);
	
	print $xt->render($tVars);
}
function new_news()
{global $twig, $plugin, $mysql, $SYSTEM_FLAGS;
	$tpath = locatePluginTemplates(array('new_news', 'main', 'htmail', ':'), 'forum', 1, '', 'config');
	$xg = $twig->loadTemplate($tpath['new_news'].'new_news.tpl');
	$xs = $twig->loadTemplate($tpath['htmail'].'htmail.tpl');
	$time = time() + ($config['date_adjust'] * 60);
	include_once(dirname(__FILE__).'/includes/rewrite.php');
	include_once(dirname(__FILE__).'/includes/security.php');
	include_once(dirname(__FILE__).'/includes/bb_code.php');
	$edit_id = intval($_REQUEST['id']);
	
	$news_ar = $mysql->record('SELECT * FROM '.prefix.'_forum_news WHERE id = '.db_squote($edit_id).' LIMIT 1');
	
	$title = isset($_REQUEST['title'])?secure_html(trim($_REQUEST['title'])):secure_html(trim($news_ar['title']));
	$content = isset($_REQUEST['content'])?secure_html(trim($_REQUEST['content'])):secure_html(trim($news_ar['content']));
	$mail = secure_html(trim($_REQUEST['mail']));
	
	if (isset($_REQUEST['submit'])){
		
		if(empty($title))
			$error_text[] = 'Титле обязательна для заполнения';
		
		if(empty($content))
			$error_text[] = 'Сообщение обязательно для заполнения';
		
		if(empty($error_text)){
			if(isset($edit_id) && $edit_id){
				
				$SQL = array();
				
				if(isset($title) && $title) $SQL['title'] = $title;
				
				if(isset($content) && $content) $SQL['content'] = $content;
				
				$vnamess = array();
				foreach ($SQL as $k => $v) { $vnamess[] = $k.' = '.db_squote($v); }
					$mysql->query('update '.prefix.'_forum_news set '.implode(', ',$vnamess).' where id = \''.intval($edit_id).'\'');
			} else {
				$mysql->query('INSERT INTO '.prefix.'_forum_news(
						title,
						content,
						c_data
					) VALUES (
						'.db_squote($title).',
						'.db_squote($content).',
						'.db_squote($time).'
					)
				');
			}
			
			if(isset($mail) && $mail){
				$news_id = $mysql->lastid('forum_news');
				foreach ($mysql->select('SELECT * FROM '.prefix.'_users') as $row){
					
					$tVars = array(
						'profile_link' => link_profile($row['id']),
						'profile' => $row['name'],
						'news_link' => link_news($news_id),
						'news_name' => $title,
						'mydomains' => $SYSTEM_FLAGS['mydomains'][0],
					);
					
					zzMail($row['mail'], 'Новая новость', $xs->render($tVars), '', 'rozard@mail.ru', 'text/html');
				}
			}
			
			redirect_forum_config('?mod=extra-config&plugin=forum&action=list_news');
		}
	}
	
	if(is_array($error_text)){
		foreach($error_text as $error)
			$error_input .= msg(array("type" => "error", "text" => $error), 0, 2);
	} else $error_input ='';
	
	$tVars = array(
		'title' => $title,
		'content' => $content,
		'forum_tpl' => $tpath['url::'],
		'preview' => array(
			'true' => isset($_REQUEST['preview'])?1:0,
			'print' => bb_codes($content)
		),
		'checked' => ($mail)?'checked="checked"':'',
		'error' => $error_input,
	);
	
	$xt = $twig->loadTemplate($tpath['main'].'main.tpl');
	$tVars = array(
		'info' => $info,
		'global' => 'Список новостей',
		'entries' => $xg->render($tVars),
	);
	
	print $xt->render($tVars);
}

function del_news(){
	global $mysql;
	
	$id = intval($_REQUEST['id']);
	
	$mysql->query('DELETE FROM '.prefix.'_forum_news WHERE id = '.db_squote($id).' LIMIT 1');
	
	$_SESSION['forum']['info'] = 'Новость удалена';
	
	redirect_forum_config('?mod=extra-config&plugin=forum&action=list_news');
}

function list_news()
{global $twig, $plugin, $mysql;
	if(isset($_SESSION['forum']['info'])){
		$info =  $_SESSION['forum']['info'];
		session_destroy();
	}
	
	$tpath = locatePluginTemplates(array('news', 'main'), 'forum', 1, '', 'config');
	$xg = $twig->loadTemplate($tpath['news'].'news.tpl');
	
	$news_per_page = 5;
	
	if (($news_per_page < 2)||($news_per_page > 2000)) $news_per_page = 2;
	
	$pageNo		= intval($_REQUEST['page'])?$_REQUEST['page']:0;
	if ($pageNo < 1)	$pageNo = 1;
	if (!$start_from)	$start_from = ($pageNo - 1)* $news_per_page;
	
	$count = $mysql->result('SELECT count(id) from '.prefix.'_forum_news');
	$countPages = ceil($count / $news_per_page);
	
	foreach ($mysql->select('SELECT * FROM '.prefix.'_forum_news ORDER BY c_data DESC LIMIT '.$start_from.', '.$news_per_page) as $row){
		$tEntry[] = array(
			'news_id' => $row['id'],
			'title' => $row['title'],
			'content' => $row['content'],
			'edit' => '<a href="?mod=extra-config&plugin=forum&action=new_news&id='.$row['id'].'";><img src="'.skins_url.'/images/configuration.gif" alt="edit" width="12" height="12" /></a>',
			'del' => '<a href="?mod=extra-config&plugin=forum&action=del_news&id='.$row['id'].'"><img src="'.skins_url.'/images/delete.gif" alt="DEL" width="12" height="12" /></a>',
		);
	}
	
	$tVars = array(
		'entries' => isset($tEntry)?$tEntry:'',
		'pagesss' => generateAdminPagelist( array('current' => $pageNo, 'count' => $countPages, 'url' => admin_url.'/admin.php?mod=extra-config&plugin=forum&action=list_news'.($_REQUEST['news_per_page']?'&news_per_page='.$news_per_page:'').($_REQUEST['author']?'&author='.$_REQUEST['author']:'').($_REQUEST['sort']?'&sort='.$_REQUEST['sort']:'').($postdate?'&postdate='.$postdate:'').($author?'&author='.$author:'').($status?'&status='.$status:'').'&page=%page%'))
	);
	
	$xt = $twig->loadTemplate($tpath['main'].'main.tpl');
	$tVars = array(
		'info' => $info,
		'global' => 'Список новостей',
		'entries' => $xg->render($tVars),
	);
	
	print $xt->render($tVars);
}

function list_forum()
{global $twig, $plugin, $mysql, $config;
	
	$tpath = locatePluginTemplates(array('main', 'list_forum', 'list_forum_entries', 'list_forum_main'), 'forum', 1, '', 'config');
	include_once(dirname(__FILE__).'/includes/rewrite.php');
	
	if(isset($_SESSION['forum']['info'])){
		$info =  $_SESSION['forum']['info'];
		session_destroy();
	}
	
	if($_POST['submit']){
		if (is_array($_POST['position'])){
			foreach ($_POST["position"] as $catid => $position) {
				if(strlen($position)) {
					$position = intval($position);
					$catid = intval($catid);
					$mysql->query("update ".prefix."_forum_forums set position = '".$position."' WHERE id = '".intval($catid)."'");
				}
			}
		}
	}
	
	$xs = $twig->loadTemplate($tpath['list_forum_entries'].'list_forum_entries.tpl');
	$xg = $twig->loadTemplate($tpath['list_forum'].'list_forum.tpl');
	$result = $mysql->select('SELECT * FROM '.prefix.'_forum_forums ORDER BY position ASC');
	$entries = array();
	foreach ( $result as $row_2 ){
		if($row_2['parent'] != 0){
			$tVarss = array(
				'forum_id' => $row_2['id'],
				'home_url' => $config['home_url'],
				'forum_link' => link_forum($row_2['id']),
				'forum_name' => $row_2['title'],
				'forum_desc' => $row_2['description'],
				'num_topic' => $row_2['int_topic'],
				'num_post' => $row_2['int_post'],
				'pos' => $row_2['position']
			);
			$entries[$row_2['parent']] .= $xs->render($tVarss);
		}
	}
	
	$output = '';
	foreach ( $result as $row ){
		
		if($row['parent'] == '0'){
			
			$tVars = array(
				'forum_id' => $row['id'], 
				'forum_name' => $row['title'],
				'pos' => $row['position'],
				'entries' => array(
					'true' => isset($entries[$row['id']])?1:0,
					'print' => isset($entries[$row['id']])?$entries[$row['id']]:''
				),
			);
			
			$output .= $xg->render($tVars);
			//print "<pre>".var_export($output, true)."</pre>";
		}
	}
	//print "<pre>".var_export($row, true)."</pre>";
	$xe = $twig->loadTemplate($tpath['list_forum_main'].'list_forum_main.tpl');
	$tVars = array(
		'entries' => $output
	);
	
	$xt = $twig->loadTemplate($tpath['main'].'main.tpl');
	$tVars = array(
		'info' => $info,
		'global' => 'Список форумов',
		'entries' => $xe->render($tVars)
	);
	
	print $xt->render($tVars);
}

function general()
{global $twig, $plugin;
	
	auxiliary_forum();
	
	if(isset($_SESSION['forum']['info'])){
		$inf =  $_SESSION['forum']['info'];
		
		if(is_array($inf)){
			$info = implode('<br />', $inf);
		} else {
			$info = $inf;
		}
		
		session_destroy();
	}
	
	if (isset($_REQUEST['submit'])){
		pluginSetVariable('forum', 'localsource', (int)$_REQUEST['localsource']);
		pluginSetVariable('forum', 'redirect_time', (int)$_REQUEST['redirect_time']);
		pluginSetVariable('forum', 'online_time', (int)$_REQUEST['online_time']);
		pluginSetVariable('forum', 'online', (int)$_REQUEST['online']);
		pluginSetVariable('forum', 'forum_title', secure_html(trim($_REQUEST['forum_title'])));
		pluginSetVariable('forum', 'forum_description', secure_html(trim($_REQUEST['forum_description'])));
		pluginSetVariable('forum', 'forum_keywords', secure_html(trim($_REQUEST['forum_keywords'])));
		pluginSetVariable('forum', 'edit_del_time', (int)($_REQUEST['edit_del_time']));
		pluginSetVariable('forum', 'localskin', secure_html(trim($_REQUEST['localskin'])));
		
		pluginSetVariable('forum', 'topic_per_page', (int)($_REQUEST['topic_per_page']));
		pluginSetVariable('forum', 'search_per_page', (int)($_REQUEST['search_per_page']));
		pluginSetVariable('forum', 'user_per_page', (int)($_REQUEST['user_per_page']));
		pluginSetVariable('forum', 'forum_per_page', (int)($_REQUEST['forum_per_page']));
		pluginSetVariable('forum', 'reput_per_page', (int)($_REQUEST['reput_per_page']));
		pluginSetVariable('forum', 'act_per_page', (int)($_REQUEST['act_per_page']));
		pluginSetVariable('forum', 'thank_per_page', (int)($_REQUEST['thank_per_page']));
		pluginSetVariable('forum', 'newpost_per_page', (int)($_REQUEST['newpost_per_page']));
		pluginSetVariable('forum', 'news_per_page', (int)($_REQUEST['news_per_page']));
		pluginSetVariable('forum', 'rss_per_page', (int)($_REQUEST['rss_per_page']));
		pluginSetVariable('forum', 'list_pm_per_page', (int)($_REQUEST['list_pm_per_page']));
		pluginSetVariable('forum', 'display_main', (int)($_REQUEST['display_main']));
		
		pluginsSaveConfig();
		redirect_forum_config('?mod=extra-config&plugin=forum');
	}
	
	$tpath = locatePluginTemplates(array('main', 'general'), 'forum', 1, '', 'config');
	$xg = $twig->loadTemplate($tpath['general'].'general.tpl');
	$tVars = array(
		'localsource' => MakeDropDown(array(0 => 'Шаблон сайта', 1 => 'Плагина'), 'localsource', (int)pluginGetVariable($plugin,'localsource')),
		'online' => MakeDropDown(array(0 => 'Нет', 1 => 'Да'), 'online', (int)pluginGetVariable($plugin,'online')),
		'redirect_time' => (int)pluginGetVariable($plugin,'redirect_time'),
		'online_time' => (int)pluginGetVariable($plugin,'online_time'),
		'forum_title' => secure_html(trim(pluginGetVariable($plugin,'forum_title'))),
		'forum_description' => secure_html(trim(pluginGetVariable($plugin,'forum_description'))),
		'forum_keywords' => secure_html(trim(pluginGetVariable($plugin,'forum_keywords'))),
		'localskin' => MakeDropDown(ListFiles('../engine/plugins/forum/tpl/skins', ''), 'localskin', pluginGetVariable($plugin,'localskin')),
		'edit_del_time' => (int)pluginGetVariable($plugin,'edit_del_time'),
		'display_main' =>  MakeDropDown(array(0 => 'Основной шаблон', 1 => 'Отдельная страница'), 'display_main', (int)pluginGetVariable($plugin,'display_main')),
		
		'topic_per_page' => (int)pluginGetVariable($plugin,'topic_per_page'),
		'search_per_page' => (int)pluginGetVariable($plugin,'search_per_page'),
		'user_per_page' => (int)pluginGetVariable($plugin,'user_per_page'),
		'forum_per_page' => (int)pluginGetVariable($plugin,'forum_per_page'),
		'reput_per_page' => (int)pluginGetVariable($plugin,'reput_per_page'),
		'act_per_page' => (int)pluginGetVariable($plugin,'act_per_page'),
		'thank_per_page' => (int)pluginGetVariable($plugin,'thank_per_page'),
		'newpost_per_page' => (int)pluginGetVariable($plugin,'newpost_per_page'),
		'news_per_page' => (int)pluginGetVariable($plugin,'news_per_page'),
		'rss_per_page' => (int)pluginGetVariable($plugin,'rss_per_page'),
		'list_pm_per_page' => (int)pluginGetVariable($plugin,'list_pm_per_page'),
	);
	
	
	
	$xt = $twig->loadTemplate($tpath['main'].'main.tpl');
	$tVars = array(
		'info' => $info,
		'global' => 'Общие',
		'entries' => $xg->render($tVars)
	);
	
	print $xt->render($tVars);
}

function auxiliary_forum(){
	if(!file_exists(files_dir.'forum'))
		$_SESSION['forum']['info'][1] = 'Критическая ошибка: не найдена папка '.files_dir . 'forum';
	
	if(!is_writable(files_dir . 'forum'))
		$_SESSION['forum']['info'][2] = 'Критическая ошибка: нет прав на запись '.files_dir . 'forum';
}

function redirect_forum_config($url){
	if (headers_sent()){
		echo "<script>document.location.href='{$url}';</script>\n";
		exit;
	} else {
		header('HTTP/1.1 301 Moved Permanently');
		header("Location: {$url}");
		exit;
	}
}