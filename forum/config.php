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
	case 'list_forum': list_forum(); break;
	case 'del_forum':  del_forum(); break;
	
	case 'list_complaints': list_complaints(); break;
	case 'closed_complaints': closed_complaints(); break;
	
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
	$f = $mysql->result('SELECT 1 FROM '.prefix.'_forum_forums WHERE parent = '.db_squote($id).' LIMIT 1');
	
	if($c != 1 and $f != 1){
		$mysql->query('DELETE FROM '.prefix.'_forum_forums WHERE id = '.db_squote($id).' LIMIT 1');
		$_SESSION['forum']['info'] = 'Форум удален';
		generate_index_cache(true);
	} else {
		$_SESSION['forum']['info'] = 'Форум не удален';
	}
	
	redirect_forum_config('?mod=extra-config&plugin=forum&action=list_forum');


}

function send_forum(){
global $twig, $plugin, $mysql;
	
	include_once(dirname(__FILE__).'/includes/constants.php');
	include_once(dirname(__FILE__).'/includes/cache.php');
	
	$tpath = locatePluginTemplates(array('main', 'send_forum'), $plugin, 1, '', 'config');
	$xg = $twig->loadTemplate($tpath['send_forum'].'send_forum.tpl');
	
	$name = secure_html(convert($_REQUEST['name']));
	$desc = secure_html(convert($_REQUEST['desc']));
	$keyw = secure_html(convert($_REQUEST['keyw']));
	$moder = secure_html(convert($_REQUEST['moder']));
	$type = (int)$_REQUEST['type'];
	$edit_id = intval($_REQUEST['id']);
	
	if (isset($_REQUEST['submit'])){
		if(empty($name)) $error_text[] = 'Название форума не заполнено';
		
		if($type <> 0 or $type == null){
			if(isset($moder) && $moder){
				$moder = array_map('trim', explode(',',$moder));
				
				$i=0;
				foreach ($moder as $row){
					if(!$user[] = $mysql->record('SELECT id, name FROM '.prefix.'_users where name = LOWER(\''.$row.'\') LIMIT 1')){
						$error_text[] = 'Пользователь '.$row.' не найден';
						array_splice($user, $i, 1);
					}
					$i++;
				}
			} else {
				$user = null;
			}
		}
		
		//print "<pre>".var_export($user, true)."</pre>";
		if(empty($error_text)){
			if(isset($edit_id) && $edit_id){
				$SQL = array();
				
				$SQL['moderators'] = serialize($user);
				
				if(isset($name) && $name) $SQL['title'] = $name;
				
				if(isset($desc) && $desc) $SQL['description'] = $desc;
				
				if(isset($keyw) && $keyw) $SQL['keywords'] = $keyw;
				
				if(isset($type) && $type) $SQL['parent'] = $type;
				
				$vnamess = array();
				foreach ($SQL as $k => $v) { $vnamess[] = $k.' = '.db_squote($v); }
					$mysql->query('update '.prefix.'_forum_forums set '.implode(', ',$vnamess).' where id = \''.intval($edit_id).'\'');
			
			} else {
				$sql = 'SELECT MAX(position) FROM '.prefix.'_forum_forums where parent = '.intval($type).'';
				$Sposit = $mysql-> result( $sql ) + 1;
				
				$mysql->query('INSERT INTO '.prefix.'_forum_forums
					(title, description, keywords, parent, position)
					VALUES
					('.db_squote($name).', '.db_squote($desc).', '.db_squote($keyw).', '.intval($type).', '.intval($Sposit).')
				');
			}
			generate_index_cache(true);
			//redirect_forum_config('?mod=extra-config&plugin=forum&action=list_forum');
		}
	}
	
	$print_forum = 1;
	$print_forum_et = true;
	if(isset($edit_id) && $edit_id){
		$F = $mysql->record('SELECT * FROM '.prefix.'_forum_forums WHERE id = '.db_squote($edit_id));
		$c = $mysql->result('SELECT 1 FROM '.prefix.'_forum_topics WHERE fid = '.db_squote($edit_id));
		$f = $mysql->result('SELECT 1 FROM '.prefix.'_forum_forums WHERE parent = '.db_squote($edit_id));
		
		
		if($F['parent'] == 0 && $f == 1){
			$print_forum = 0; 
		} elseif($F['parent'] > 0){
			$print_forum = 1;
			$print_forum_et = false; 
		}
		
		$Sname = $F['title'];
		$Sdesc = $F['description'];
		$Skeyw = $F['keywords'];
		
		$moder = array();
		foreach (unserialize($F['moderators']) as $row){
			$moder[] = $row['name'];
		}
		
		$moder = array_unique($moder);
		$Smoder = implode(', ',$moder);
	}
	
	//print "<pre>".var_export($Smoder, true)."</pre>";
	foreach ($mysql-> select("SELECT id, title FROM ".prefix."_forum_forums WHERE parent = '0' and id != ".db_squote($edit_id)." ORDER BY position", 1 ) as $row){
		$tEntry[] = array(
			'id'		=>	$row['id'],
			'title'		=>	$row['title'],
			'id_set'	=> intval($edit_id)
		);
	}
	
	$error_input = array();
	if(isset($error_text) && is_array($error_text))
		foreach($error_text as $error)
			$error_input[] = msg(array("type" => "error", "text" => $error), 0, 2);
	
	$tVars = array(
		'Sname' => $Sname,
		'Sdesc' => $Sdesc,
		'Skeyw' => $Skeyw,
		'Sposit' => $Sposit,
		'Smoder' => $Smoder,
		'list_forum' => $tEntry,
		'list_error' => $error_input,
		'print' => $print_forum,
		'print_et' => $print_forum_et
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
			'error' => empty($forums_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> %cat_forum% / %name_forum% [/ %num%]':''
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