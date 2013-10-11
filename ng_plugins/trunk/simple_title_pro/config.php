<?php
/*
=====================================================
 Simple Title 0.1 beta
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
if(!defined('NGCMS'))
exit('HAL');

plugins_load_config();
LoadPluginLang('simple_title_pro', 'config', '', '', '#');

switch ($_REQUEST['action']) {
	case 'list_cat':		list_cat();		break;
	case 'list_static':		list_static();	break;
	case 'list_news':		list_news();	break;
	case 'send_title':		send_title();	break;
	case 'clear_cache':		clear_cache();	break;
	case 'about':			about();		break;
	case 'license':			license();		break;
	case 'del_cat':			del_cat();		list_cat();		break;
	case 'del_static':		del_static();	list_static();	break;
	case 'del_news':		del_news();		list_news(); 	break;
	case 'del_news':		del_news();		list_news(); 	break;
	default: main();
}

function about()
{global $twig;
	$tpath = locatePluginTemplates(array('main', 'about'), 'simple_title_pro', 1);
	
	
	$xt = $twig->loadTemplate($tpath['about'].'about.tpl');
	
	$tVars = array();
	
	$xg = $twig->loadTemplate($tpath['main'].'main.tpl');
	
	$tVars = array(
		'global' => 'О плагине',
		'entries' => $xt->render($tVars)
	);
	
	print $xg->render($tVars);
}

function clear_cache()
{global $userROW;
	$dir = get_plugcache_dir('simple_title_pro');
	if (is_dir($dir)){
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				@unlink($dir.$file);                   
			}
			closedir($dh);
		}
	}
	
	$_SESSION['simple_title_pro']['info'] = $userROW['name'].' Кэш очишен';
	
	redirect_simple_title_pro('?mod=extra-config&plugin=simple_title_pro');
}

function list_news()
{global $twig, $mysql;
	$tpath = locatePluginTemplates(array('main', 'list_news', 'list_news_entries'), 'simple_title_pro', 1);
	
	$news_per_page = pluginGetVariable('simple_title_pro', 'num_news');
	
	if (($news_per_page < 2)||($news_per_page > 2000)) $news_per_page = 2;
	
	$pageNo = intval($_REQUEST['page'])?$_REQUEST['page']:0;
	if ($pageNo < 1) $pageNo = 1;
	if (!$start_from) $start_from = ($pageNo - 1)* $news_per_page;
	
	$count = $mysql->result('SELECT count(*) FROM '.prefix.'_simple_title_pro as s LEFT JOIN '.prefix.'_news as n ON (n.id = s.news_id) WHERE s.news_id != 0');
	$countPages = ceil($count / $news_per_page);
	
	foreach ($mysql->select('SELECT s.id as sid, s.title as stitle, n.title as name FROM '.prefix.'_simple_title_pro as s LEFT JOIN '.prefix.'_news as n ON (n.id = s.news_id) WHERE s.news_id != 0 ORDER BY s.id ASC LIMIT '.$start_from.', '.$news_per_page) as $row){
		$xe = $twig->loadTemplate($tpath['list_news_entries'].'list_news_entries.tpl');
		
		$tVars = array (
			'title' => $row['stitle'],
			'id' => $row['sid'],
			'name' => '<a href="?mod=extra-config&plugin=simple_title_pro&action=send_title&do=news&edit='.$row['sid'].'"  />'.$row['name'].'</a>',
			'del' => '<a href="?mod=extra-config&plugin=simple_title_pro&action=del_news&id='.$row['sid'].'"  /><img title="Удалить" alt="Удалить" src="/engine/skins/default/images/delete.gif"></a>',
		);
		
		$entries .= $xe->render($tVars);
	}
	
	$xt = $twig->loadTemplate($tpath['list_news'].'list_news.tpl');
	
	$tVars = array(
		'pagesss' => generateAdminPagelist( array(	'current' => $pageNo,
													'count' => $countPages,
													'url' => admin_url.'/admin.php?mod=extra-config&plugin=simple_title_pro&action=list_news&page=%page%'
													)
		),
		'entries' => $entries 
	);
	
	$xg = $twig->loadTemplate($tpath['main'].'main.tpl');
	
	$tVars = array(
		'global' => 'Список новостей',
		'entries' => $xt->render($tVars)
	);
	
	print $xg->render($tVars);
}

function license()
{global $twig;
	$tpath = locatePluginTemplates(array('main', 'about'), 'simple_title_pro', 1);
	
	$xg = $twig->loadTemplate($tpath['main'].'main.tpl');
	
	$tVars = array(
		'global' => 'О плагине => Лицензия',
		'entries' => nl2br(base64_decode('y+j25e3n6P8gTWljcm9zb2Z0IFB1YmxpYyBMaWNlbnNlIChNcy1QTCkKCs3g8fLu//ng/yDr6Pbl7efo/yDu7/Dl5OXr/+XyIPPx6+7i6P8g6PHv7uv85+7i4O3o/yDx7u7y4uXy8fLi8/755ePuIO/w7uPw4Ozs7e7j7iDu4eXx7+X35e3o/y4gxfHr6CDi+yDo8e/u6/zn8+Xy5SDv8O7j8ODs7O3u5SDu4eXx7+X35e3o5Swg/fLuIO7n7eD34OXyIOLg+OUg8e7j6+Dx6OUg8SDz8evu4uj/7Ogg7eDx8u7/+eXpIOvo9uXt5+joLiDF8evoIOL7IO3lIPHu4+vg8e37IPEg8/Hr7uLo/+zoIO3g8fLu//nl6SDr6Pbl7efo6Cwg7eUg6PHv7uv85/Pp8uUg7/Du4/Dg7Ozt7uUg7uHl8e/l9+Xt6OUuCgoxLiDO7/Dl5OXr5e3o/woK0uXw7Ojt+yAi4u7x7/Du6Ofi7uTo8vwiLCAi4u7x7/Du6Ofi5eTl7ejlIiwgIu/w7ujn4u7k7fvlIOjn5OXr6P8iIOggIvDg8e/w7vHy8ODt5e3o5SIg6Ozl/vIg8uDq7uUg5uUg5+3g9+Xt6OUsIOrg6iDoIOIg5+Dq7u3u5ODy5ev88fLi5SDR2MAg7uEg4OLy7vDx6u7sIO/w4OLlLgoKIsLq6+Dk7uwiIP/i6//l8vH/IO7w6OPo7eDr/O376SDv8O7j8ODs7O376SDv8O7k8+ryIOjr6CDr/uHu5SDk7uHg4uvl7ejlIOjr6CDo5+zl7eXt6OUg7/Du4/Dg7Ozt7uPuIO/w7uTz6vLgLgoKItP34PHy7ejq7uwiIP/i6//l8vH/IOv+4e7lIOvo9u4sIPDg8e/w7vHy8ODt//755eUg8eLu6SDi6uvg5CDt4CDz8evu4uj/9SDt4PHy7v/55ekg6+j25e3n6OguCgoiy+j25e3n6PDu4uDt7fvs6CDv4PLl7fLg7OgiIP/i6//+8vH/IO/g8uXt8u375SDn4P/i6ugg8/fg8fLt6OrgLCDw4PHv8O7x8vDg7f/++ejl8f8g7eXv7vHw5eTx8uLl7e3uIO3gIOXj7iDi6uvg5C4KCjIuIM/w5eTu8fLg4uvl7ejlIO/w4OIKCihBKSDP8OXk7vHy4OLr5e3o5SDg4vLu8PHq7uPuIO/w4OLgLiDCIPHu7vLi5fLx8uLo6CDxIO/u6+7m5e3o/+zoIO3g8fLu//nl6SDr6Pbl7efo6Cwg4urr/vfg/vno7Ogg8/Hr7uLo/yDoIO7j8ODt6Pfl7ej/IOvo9uXt5+joIOIg8ODn5OXr5SAzLCDq4Obk++kg8/fg8fLt6Oog7/Dl5O7x8uDi6//l8iDi4Owg7eXo8err/vfo8uXr/O3z/iwg5OXp8fLi8/758/4g4u4g4vHl9SDx8vDg7eD1LCDh5efi7ufs5efk7fP+IODi8u7w8erz/iDr6Pbl7efo/iDt4CDi7vHv8O7o5+Ll5OXt6OUg8eLu5ePuIOLq6+Dk4Cwg7+7k4+7y7uLq8yDv8O7o5+Lu5O379SDo5+Tl6+jpIO7yIP3y7uPuIOLq6+Dk4CDoIPDg8e/w7vHy8ODt5e3o5SDi6uvg5OAg6OvoIPHu5+Tg4uDl7Pv1IOLg7Ogg7/Du6Ofi7uTt+/Ug6Ofk5evo6S4KCihCKSDP8OXk7vHy4OLr5e3o5SDv4PLl7fLgLiDCIPHu7vLi5fLx8uLo6CDxIO/u6+7m5e3o/+zoIO3g8fLu//nl6SDr6Pbl7efo6Cwg4urr/vfg/vno7Ogg8/Hr7uLo/yDoIO7j8ODt6Pfl7ej/IOvo9uXt5+joIOIg8ODn5OXr5SAzLCDq4Obk++kg8/fg8fLt6Oog7/Dl5O7x8uDi6//l8iDi4Owg7eXo8err/vfo8uXr/O3z/iwg5OXp8fLi8/758/4g4u4g4vHl9SDx8vDg7eD1LCDh5efi7ufs5efk7fP+IOvo9uXt5+j+IO3gIPPx6+7i6P/1IOvo9uXt5+jw7uLg7e379SDv4PLl7fLu4iDt4CDx7ufk4O3o5Swg6PHv7uv85+7i4O3o5Swg7/Du5ODm8ywg7/Dl5Ovu5uXt6OUg5Ov/IO/w7uTg5ugsIOjs7+7w8iDoICjo6+gpIOjt7ukg8e/u8e7hIPDg8e/u8P/m5e3o/yDi6uvg5O7sIOIg7/Du4/Dg7Ozt7uUg7uHl8e/l9+Xt6OUg6OvoIO/w7ujn4u7k7fv1IOjn5OXr6Okg7vIg4urr4OTgIOIg7/Du4/Dg7Ozt7uUg7uHl8e/l9+Xt6OUuCgozLiDT8evu4uj/IOgg7uPw4O3o9+Xt6P8KCihBKSDO8vHz8vHy4ujlIOvo9uXt5+joIO3gIPLu4uDw7fvpIOft4OouIM3g8fLu//ng/yDr6Pbl7efo/yDt5SDv8OXk7vHy4OLr/+XyIOLg7CDv8ODi4CDt4CDo8e/u6/zn7uLg7ejlIOjs5e3oLCDr7uPu8ujv4CDo6+gg8u7i4PDt+/Ug5+3g6u7iIPP34PHy7ejq7uIuCgooQikgwiDx6/P34OUg7/Dl5Pr/4uvl7ej/IOLg7Ogg7+Dy5e3y7e7j7iDv8Ojy/+fg7ej/IO/w7vLo4iDq4Oru4+4t6+jh7iDz9+Dx8u3o6uAg4iDu8u3u+OXt6Ogg7+Dy5e3y7uIsIOru8u7w++Ug7+4g4uD45ezzIOzt5e3o/iDt4PDz+OD+8vH/IO/w7uPw4Ozs7fvsIO7h5fHv5ffl7ejl7Cwg5OXp8fLi6OUg4uD45ekg7+Dy5e3y7e7pIOvo9uXt5+joIO7yIOTg7e3u4+4g8/fg8fLt6OrgIO3gIO/w7uPw4Ozs7e7lIO7h5fHv5ffl7ejlIODi8u7s4PLo9+Xx6ugg5+Di5fD44OXy8f8uCgooQykgwiDx6/P34OUg8ODx7/Du8fLw4O3l7ej/IOLg7Ogg6/7h7uPuIPTw4OPs5e3y4CDv8O7j8ODs7O3u4+4g7uHl8e/l9+Xt6P8g4vsg7uH/5+Dt+yDx7vXw4O3/8vwg4vHlIPPi5eTu7Ovl7ej/IO7hIODi8u7w8ero9SDv8ODi4PUsIO/g8uXt8uD1LCDy7uLg8O379SDn7eDq4PUg6CDz8fLg7e7i6+Xt6Ogg4OLy7vDx8uLgLCDv8Ojx8/Lx8uLz/vno5SDiIO/w7uPw4Ozs7e7sIO7h5fHv5ffl7ejoLgoKKEQpIMIg8evz9+DlIPDg8e/w7vHy8ODt5e3o/yDq4Oru6S3r6OHuIPfg8fLoIO/w7uPw4Ozs7e7j7iDu4eXx7+X35e3o/yDiIOLo5OUg6PH17uTt7uPuIOru5OAg4vsg7O7m5fLlIO7x8/nl8fLi6//y/CDw4PHv8O7x8vDg7eXt6OUg8u7r/OruIO3gIPPx6+7i6P/1IO3g8fLu//nl6SDr6Pbl7efo6CDv8/Ll7CDk7uHg4uvl7ej/IO/u6+3u6SDq7u/o6CDt4PHy7v/55ekg6+j25e3n6Ogg4iDk6PHy8Ojh8/Lo4i4gwiDx6/P34OUg8ODx7/Du8fLw4O3l7ej/IOrg6u7pLevo4e4g9+Dx8ugg7/Du4/Dg7Ozt7uPuIO7h5fHv5ffl7ej/IOIg4ujk5SDq7uzv6Ovo8O7i4O3t7uPuIOjr6CDu4frl6vLt7uPuIOru5OAg4vsg7O7m5fLlIO7x8/nl8fLi6//y/CDw4PHv8O7x8vDg7eXt6OUg7eAg8/Hr7uLo//Ug6+j25e3n6OgsIPHu4uzl8fLo7O7pIPEg7eDx8u7/+eXpIOvo9uXt5+jl6S4KCihFKSDP8O7j8ODs7O3u5SDu4eXx7+X35e3o5SDr6Pbl7efo8PPl8vH/IO3gIPPx6+7i6P/1ICLq4Oog5fHy/CIuIMLx5SDw6PHq6CDiIO7y7e745e3o6CDl4+4g6PHv7uv85+7i4O3o/yDi7ufr4OPg/vLx/yDt4CDi4PEuINP34PHy7ejq6CDt5SDv8OXk7vHy4OLr//7yIOrg6uj1Levo4e4g/+Lt+/Ug4+Dw4O3y6Okg6OvoIPPx6+7i6OkuIML7IOzu5uXy5SDu4evg5ODy/CDk7u/u6+3o8uXr/O377Ogg7/Dg4uDs6CDv7vLw5eHo8uXr/ywg6u7y7vD75SDt5SDs7uPz8iDh+/L8IOjn7OXt5e37IO3g8fLu//nl6SDr6Pbl7efo5ekuIMIg7ODq8ejs4Ov87e4g5O7v8/Hy6Ozu6SDn4Oru7e7k4PLl6/zx8uLu7CDi4Pjl4+4g4+7x8+Tg8PHy4uAg8fLl7+Xt6CDz9+Dx8u3o6ugg6PHq6/734P7yIO/u5PDg5/Ps5eLg5ez75SDj4PDg7fLo6CDy7uLg8O3u8fLoLCDv8Ojj7uTt7vHy6CDk6/8g6uDq7ukt6+jh7iDu7/Dl5OXr5e3t7ukg9uXr6CDoIPHu4ev+5OXt6P8g4OLy7vDx6uj1IO/w4OIuIA=='))
	);
	
	print $xg->render($tVars);
}

function list_static()
{global $twig, $mysql;
	$tpath = locatePluginTemplates(array('main', 'list_static', 'list_static_entries'), 'simple_title_pro', 1);
	
	$news_per_page = pluginGetVariable('simple_title_pro', 'num_static');
	
	if (($news_per_page < 2)||($news_per_page > 2000)) $news_per_page = 2;
	
	$pageNo = intval($_REQUEST['page'])?$_REQUEST['page']:0;
	if ($pageNo < 1) $pageNo = 1;
	if (!$start_from) $start_from = ($pageNo - 1)* $news_per_page;
	
	$count = $mysql->result('SELECT count(*) FROM '.prefix.'_simple_title_pro as s LEFT JOIN '.prefix.'_static as n ON (n.id = s.static_id) WHERE s.static_id != 0');
	$countPages = ceil($count / $news_per_page);
	
	foreach ($mysql->select('SELECT s.id as sid, s.title as stitle, n.title as name FROM '.prefix.'_simple_title_pro as s LEFT JOIN '.prefix.'_static as n ON (n.id = s.static_id) WHERE s.static_id != 0 ORDER BY s.id ASC LIMIT '.$start_from.', '.$news_per_page) as $row){
		$xe = $twig->loadTemplate($tpath['list_static_entries'].'list_static_entries.tpl');
		
		$tVars = array(
			'title' => $row['stitle'],
			'id' => $row['sid'],
			'name' => '<a href="?mod=extra-config&plugin=simple_title_pro&action=send_title&do=static&edit='.$row['sid'].'"  />'.$row['name'].'</a>',
			'del' => '<a href="?mod=extra-config&plugin=simple_title_pro&action=del_static&id='.$row['sid'].'"  /><img title="Удалить" alt="Удалить" src="/engine/skins/default/images/delete.gif"></a>',
		);
		
		$entries .= $xe->render($tVars);
	}
	
	$xt = $twig->loadTemplate($tpath['list_static'].'list_static.tpl');
	
	$tVars = array(
		'pagesss' => generateAdminPagelist( array(	'current' => $pageNo,
													'count' => $countPages, 
													'url' => admin_url.'/admin.php?mod=extra-config&plugin=simple_title_pro&action=list_static&page=%page%'
												)
		),
		'entries' => $entries 
	);
	
	$xg = $twig->loadTemplate($tpath['main'].'main.tpl');
	
	$tVars = array(
		'global' => 'Список статиков',
		'entries' => $xt->render($tVars)
	);
	
	print $xg->render($tVars);
}

function list_cat()
{global $twig, $mysql;
	$tpath = locatePluginTemplates(array('main', 'list_cat', 'list_cat_entries'), 'simple_title_pro', 1);
	
	$news_per_page = pluginGetVariable('simple_title_pro', 'num_cat');
	
	if (($news_per_page < 2)||($news_per_page > 2000)) $news_per_page = 2;
	
	$pageNo = intval($_REQUEST['page'])?$_REQUEST['page']:0;
	if ($pageNo < 1) $pageNo = 1;
	if (!$start_from) $start_from = ($pageNo - 1)* $news_per_page;
	
	$count = $mysql->result('SELECT count(*) FROM '.prefix.'_simple_title_pro as s LEFT JOIN '.prefix.'_category as c ON (c.id = s.cat_id) WHERE s.cat_id != 0');
	$countPages = ceil($count / $news_per_page);
	
	foreach ($mysql->select('SELECT s.id as sid, s.title as stitle, c.name as cname FROM '.prefix.'_simple_title_pro as s LEFT JOIN '.prefix.'_category as c ON (c.id = s.cat_id) WHERE s.cat_id != 0 ORDER BY s.id ASC LIMIT '.$start_from.', '.$news_per_page) as $row){
		$xe = $twig->loadTemplate($tpath['list_cat_entries'].'list_cat_entries.tpl');
		
		$tVars = array (
			'title' => $row['stitle'],
			'id' => $row['sid'],
			'cat_name' => '<a href="?mod=extra-config&plugin=simple_title_pro&action=send_title&do=cat&edit='.$row['sid'].'"  />'.$row['cname'].'</a>',
			'del' => '<a href="?mod=extra-config&plugin=simple_title_pro&action=del_cat&id='.$row['sid'].'"  /><img title="Удалить" alt="Удалить" src="/engine/skins/default/images/delete.gif"></a>',
		);
		
		$entries .= $xe->render($tVars);
	}
	
	$xt = $twig->loadTemplate($tpath['list_cat'].'list_cat.tpl');
	
	$tVars = array(
		'pagesss' => generateAdminPagelist( array(	'current' => $pageNo,
													'count' => $countPages,
													'url' => admin_url.'/admin.php?mod=extra-config&plugin=simple_title_pro&action=list_cat&page=%page%'
													)
		),
		'entries' => $entries 
	);
	
	$xg = $twig->loadTemplate($tpath['main'].'main.tpl');
	
	$tVars = array(
		'global' => 'Список категорий',
		'entries' => $xt->render($tVars)
	);
	
	print $xg->render($tVars);
}

function send_title()
{global $twig, $mysql, $config;
	$tpath = locatePluginTemplates(array('main', 'send_cat', 'send_news', 'send_static'), 'simple_title_pro', 1);
	$dir = get_plugcache_dir('simple_title_pro');
	
	$id = intval($_REQUEST['id']);
	foreach ($mysql->select('SELECT * FROM '.prefix.'_simple_title_pro') as $result){
		$cat[$result['cat_id']] = true;
		$static[$result['static_id']] = true;
		$news[$result['news_id']] = true;
	}
	
	switch($_REQUEST['do']){
		case 'cat':
			$template = $tpath['send_cat'].'send_cat.tpl';
			
			if(isset($_REQUEST['edit']) & !empty($_REQUEST['edit']))
				$frow = $mysql->record('SELECT id, title, cat_id FROM '.prefix.'_simple_title_pro WHERE id = '.intval($_REQUEST['edit']).'  LIMIT 1');
			
			$options = '';
			foreach ($mysql->select('SELECT id, name FROM '.prefix.'_category') as $row){
				if(!$cat[$row['id']] or $row['id'] == $frow['cat_id'])
					$options .= '<option value="' . $row['id'] . '"'.(($row['id']==$frow['cat_id'])?'selected':'').'>' . $row['name'] . '</option>';
			}
			
			if (isset($_REQUEST['submit'])){
				$title = secure_html(trim($_REQUEST['title']));
				if(empty($title))
					$error_text[] = 'Титле обязательна для заполнения';
				
				if(empty($id))
					$error_text[] = 'id не передан';
				
				if($mysql->result('SELECT 1 FROM '.prefix.'_simple_title_pro WHERE cat_id = \'' . $id . '\' LIMIT 1') && empty($_REQUEST['edit']))
					$error_text[] = 'Для этой категории уже есть TITLE';
				
				if(empty($error_text) && empty($_REQUEST['edit'])){
					$mysql->query('INSERT INTO '.prefix.'_simple_title_pro (title, cat_id) 
						VALUES (
							'.db_squote($title).',
							'.db_squote($id).'
						)
					');
					redirect_simple_title_pro('?mod=extra-config&plugin=simple_title_pro&action=list_cat');
				}else if(empty($error_text) && !empty($_REQUEST['edit'])){
					$cacheFileName = md5('block_directory_sites_cat'.$frow['cat_id'].$config['default_lang']).'.txt';
					
					cacheStoreFile($cacheFileName, $title, 'simple_title_pro');
					$mysql->query('UPDATE '.prefix.'_simple_title_pro SET 
						title = '.db_squote($title).',
						cat_id = '.db_squote($id).'
						WHERE id = \''.intval($_REQUEST['edit']).'\'
					');
					
					redirect_simple_title_pro('?mod=extra-config&plugin=simple_title_pro&action=list_cat');
				}
			}
		break;
		case 'news':
			$template = $tpath['send_news'].'send_news.tpl';
			
			if(isset($_REQUEST['edit']) & !empty($_REQUEST['edit']))
				$frow = $mysql->record('SELECT id, title, news_id FROM '.prefix.'_simple_title_pro WHERE id = '.intval($_REQUEST['edit']).'  LIMIT 1');
			
			$options = '';
			foreach ($mysql->select('SELECT id, title FROM '.prefix.'_news') as $row){
				if(!$news[$row['id']] or $row['id'] == $frow['news_id'])
					$options .= '<option value="' . $row['id'] . '"'.(($row['id']==$frow['news_id'])?'selected':'').'>' . $row['title'] . '</option>';
			}
			if (isset($_REQUEST['submit'])){
				$title = secure_html(trim($_REQUEST['title']));
				if(empty($title))
					$error_text[] = 'Титле обязательна для заполнения';
				
				if(empty($id))
					$error_text[] = 'id не передан';
				
				if($mysql->result('SELECT 1 FROM '.prefix.'_simple_title_pro WHERE news_id = \'' . $id . '\' LIMIT 1') && empty($_REQUEST['edit']))
					$error_text[] = 'Для этой новости уже есть TITLE';
				
				if(empty($error_text) && empty($_REQUEST['edit'])){
					$mysql->query('INSERT INTO '.prefix.'_simple_title_pro (title, news_id) 
						VALUES 
						('.db_squote($title).',
							'.db_squote($id).'
						)
					');
					redirect_simple_title_pro('?mod=extra-config&plugin=simple_title_pro&action=list_news');
				}else if(empty($error_text) && !empty($_REQUEST['edit'])){
					$cacheFileName = md5('block_directory_sites_news'.$frow['news_id'].$config['default_lang']).'.txt';
					
					cacheStoreFile($cacheFileName, $title, 'simple_title_pro');
					$mysql->query('UPDATE '.prefix.'_simple_title_pro SET 
						title = '.db_squote($title).',
						news_id = '.db_squote($id).'
						WHERE id = \''.intval($_REQUEST['edit']).'\'
					');
					
					redirect_simple_title_pro('?mod=extra-config&plugin=simple_title_pro&action=list_news');
				}
			}
		break;
		case 'static':
			$template = $tpath['send_static'].'send_static.tpl';
			
			if(isset($_REQUEST['edit']) & !empty($_REQUEST['edit']))
				$frow = $mysql->record('SELECT id, title, static_id FROM '.prefix.'_simple_title_pro WHERE id = '.intval($_REQUEST['edit']).'  LIMIT 1');
			
			$options = '';
			foreach ($mysql->select('SELECT id, title FROM '.prefix.'_static') as $row){
				if(!$static[$row['id']] or $row['id'] == $frow['static_id'])
					$options .= '<option value="' . $row['id'] . '"'.(($row['id']==$frow['static_id'])?'selected':'').'>' . $row['title'] . '</option>';
			}
			
			if (isset($_REQUEST['submit'])){
				$title = secure_html(trim($_REQUEST['title']));
				if(empty($title))
					$error_text[] = 'Титле обязательна для заполнения';
				
				if(empty($id))
					$error_text[] = 'id не передан';
				
				if($mysql->result('SELECT 1 FROM '.prefix.'_simple_title_pro WHERE static_id = \'' . $id . '\' LIMIT 1') && empty($_REQUEST['edit']))
					$error_text[] = 'Для этой статики уже есть TITLE';
				
				if(empty($error_text) && empty($_REQUEST['edit'])){
					$mysql->query('INSERT INTO '.prefix.'_simple_title_pro (title, static_id) 
						VALUES 
						('.db_squote($title).',
							'.db_squote($id).'
						)
					');
					redirect_simple_title_pro('?mod=extra-config&plugin=simple_title_pro&action=list_static');
				}else if(empty($error_text) && !empty($_REQUEST['edit'])){
					$cacheFileName = md5('block_directory_sites_static'.$frow['static_id'].$config['default_lang']).'.txt';
					
					cacheStoreFile($cacheFileName, $title, 'simple_title_pro');
					$mysql->query('UPDATE '.prefix.'_simple_title_pro SET 
						title = '.db_squote($title).',
						static_id = '.db_squote($id).'
						WHERE id = \''.intval($_REQUEST['edit']).'\'
					');
					redirect_simple_title_pro('?mod=extra-config&plugin=simple_title_pro&action=list_static');
				}
			}
		break;
		default: redirect_simple_title_pro('?mod=extra-config&plugin=simple_title_pro');
	}
	
	if(!empty($error_text)){
		foreach($error_text as $error){
			$error_input .= msg(array("type" => "error", "text" => $error), 0, 2);
		}
	} else {
		$error_input ='';
	}
	
	$xt = $twig->loadTemplate($template);
	
	$tVars = array(
		'options' => $options,
		'title' => empty($frow)?$title:$frow['title'],
		'error' => $error_input ,
	);
	
	$xg = $twig->loadTemplate($tpath['main'].'main.tpl');
	
	$tVars = array(
		'global' => 'Формируем Титл',
		'entries' => $xt->render($tVars)
	);
	
	print $xg->render($tVars);
}

function list_all()
{global $mysql, $twig;
	$tpath = locatePluginTemplates(array('main', 'list_cat', 'list_cat_entries'), 'simple_title_pro', 1);
	
	foreach ($mysql->select('SELECT s.id as sid, s.title as stitle, n.title as name FROM '.prefix.'_simple_title_pro as s LEFT JOIN '.prefix.'_static as n ON (n.id = s.static_id) WHERE s.static_id != 0 ORDER BY s.id ASC') as $row){
		$xe = $twig->loadTemplate($tpath['list_cat_entries'].'list_cat_entries.tpl');
		
		$tVars = array (
			'title' => $row['stitle'],
			'id' => $row['sid'],
			'cat_name' => '<a href="?mod=extra-config&plugin=simple_title_pro&action=edit&id='.$row['sid'].'"  />'.$row['name'].'</a>',
			'cat_name_del' => '<a href="?mod=extra-config&plugin=simple_title_pro&action=del&id='.$row['id'].'" /><img title="Удалить" alt="Удалить" src="/engine/skins/default/images/delete.gif"></a>',
		);
		
		$entries .= $xe->render($tVars);
	}
	
	foreach ($mysql->select('SELECT s.id as sid, s.title as stitle, n.title as name FROM '.prefix.'_simple_title_pro as s LEFT JOIN '.prefix.'_news as n ON (n.id = s.news_id) WHERE s.news_id != 0 ORDER BY s.id ASC') as $row){
		$xe = $twig->loadTemplate($tpath['list_cat_entries'].'list_cat_entries.tpl');
		
		$tVars = array (
			'title' => $row['stitle'],
			'id' => $row['sid'],
			'cat_name' => '<a href="?mod=extra-config&plugin=simple_title_pro&action=cat_edit&id='.$row['sid'].'"  />'.$row['name'].'</a>',
			'cat_name_del' => '<a href="?mod=extra-config&plugin=simple_title_pro&action=cat_name_del&id='.$row['id'].'"  /><img title="Удалить" alt="Удалить" src="/engine/skins/default/images/delete.gif"></a>',
		);
		
		$entries .= $xe->render($tVars);
	}
	
	foreach ($mysql->select('SELECT s.id as sid, s.title as stitle, c.name as cname FROM '.prefix.'_simple_title_pro as s LEFT JOIN '.prefix.'_category as c ON (c.id = s.cat_id) WHERE s.cat_id != 0 ORDER BY s.id ASC') as $row){
		$xe = $twig->loadTemplate($tpath['list_cat_entries'].'list_cat_entries.tpl');
		
		$tVars = array (
			'title' => $row['stitle'],
			'id' => $row['sid'],
			'cat_name' => '<a href="?mod=extra-config&plugin=simple_title_pro&action=cat_edit&id='.$row['sid'].'"  />'.$row['cname'].'</a>',
			'cat_name_del' => '<a href="?mod=extra-config&plugin=simple_title_pro&action=cat_name_del&id='.$row['id'].'"  /><img title="Удалить" alt="Удалить" src="/engine/skins/default/images/delete.gif"></a>',
		);
		
		$entries .= $xe->render($tVars);
	}
	
	$xt = $twig->loadTemplate($tpath['list_cat'].'list_cat.tpl');
	
	$tVars = array(
		'entries' => $entries 
	);
	
	$xg = $twig->loadTemplate($tpath['main'].'main.tpl');
	
	$tVars = array(
		'global' => 'Список',
		'entries' => $xt->render($tVars)
	);
	
	print $xg->render($tVars);
}

function main()
{global $twig;
	
	$tpath = locatePluginTemplates(array('main', 'general.from'), 'simple_title_pro', 1);
	
	if(isset($_SESSION['simple_title_pro']['info'])){
		$info =  $_SESSION['simple_title_pro']['info'];
		unset($_SESSION['simple_title_pro']['info']);
	}
	
	if(is_file(root.'plugins/simple_title_pro/tpl/reklama')){
		$reklama = file_get_contents(root.'plugins/simple_title_pro/tpl/reklama');
		unlink(root.'plugins/simple_title_pro/tpl/reklama');
	}
	
	if (isset($_REQUEST['submit'])){
 		pluginSetVariable('simple_title_pro', 'c_title', secure_html(trim($_REQUEST['c_title'])));
		pluginSetVariable('simple_title_pro', 'n_title', secure_html(trim($_REQUEST['n_title'])));
		pluginSetVariable('simple_title_pro', 'm_title', secure_html(trim($_REQUEST['m_title'])));
		pluginSetVariable('simple_title_pro', 'static_title', secure_html(trim($_REQUEST['static_title'])));
		pluginSetVariable('simple_title_pro', 'num_title', secure_html(trim($_REQUEST['num_title'])));
		pluginSetVariable('simple_title_pro', 'num_cat', secure_html(trim($_REQUEST['num_cat'])));
		pluginSetVariable('simple_title_pro', 'num_news', secure_html(trim($_REQUEST['num_news'])));
		pluginSetVariable('simple_title_pro', 'num_static', secure_html(trim($_REQUEST['num_static'])));
		pluginSetVariable('simple_title_pro', 'o_title', secure_html(trim($_REQUEST['o_title'])));
		pluginSetVariable('simple_title_pro', 'e_title', secure_html(trim($_REQUEST['e_title'])));
		pluginSetVariable('simple_title_pro', 'html_secure', secure_html(trim($_REQUEST['html_secure'])));
		pluginSetVariable('simple_title_pro', 'p_title', secure_html(trim($_REQUEST['p_title'])));
		pluginSetVariable('simple_title_pro', 'cache', secure_html(trim($_REQUEST['cache'])));
		pluginsSaveConfig();
		redirect_simple_title_pro('?mod=extra-config&plugin=simple_title_pro');
	}
	
	$c_title = pluginGetVariable('simple_title_pro', 'c_title');
	$n_title = pluginGetVariable('simple_title_pro', 'n_title');
	$m_title = pluginGetVariable('simple_title_pro', 'm_title');
	$static_title = pluginGetVariable('simple_title_pro', 'static_title');
	$o_title = pluginGetVariable('simple_title_pro', 'o_title');
	$e_title = pluginGetVariable('simple_title_pro', 'e_title');
	$p_title = pluginGetVariable('simple_title_pro', 'p_title');
	$num_title = pluginGetVariable('simple_title_pro', 'num_title');
	$num_cat = pluginGetVariable('simple_title_pro', 'num_cat');
	$num_news = pluginGetVariable('simple_title_pro', 'num_news');
	$num_static = pluginGetVariable('simple_title_pro', 'num_static');
	$cache = pluginGetVariable('simple_title_pro', 'cache');
	$html_secure = pluginGetVariable('simple_title_pro', 'html_secure');
	
	$xt = $twig->loadTemplate($tpath['general.from'].'general.from.tpl');
	$xg = $twig->loadTemplate($tpath['main'].'main.tpl');
	
	$tVars = array(
		'c_title' => array(
						'print' => $c_title,
						'error' => empty($c_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> %home%  / %cat% %num%':''
		),
		'n_title' => array(
						'print' => $n_title,
						'error' => empty($n_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> %home%  / %cat% / %title%  %num%':''
		),
		'm_title' => array(
						'print' => $m_title,
						'error' => empty($m_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> %home% %num%':''
		),
		'static_title' => array(
						'print' => $static_title,
						'error' => empty($static_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> %home% / %static%':''
		),
		'o_title' => array(
						'print' => $o_title,
						'error' => empty($o_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> %home% / %other% %html% %num%':''
		),
		'e_title' => array(
						'print' => $e_title,
						'error' => empty($e_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> %home% / %other%':''
		),
		'num_title' => array(
						'print' => $num_title,
						'error' => empty($num_title)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> / Страница %count%':''
		),
		'num_cat' => array(
						'print' => $num_cat,
						'error' => empty($num_cat)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> 20':''
		),
		'num_news' => array(
						'print' => $num_news,
						'error' => empty($num_news)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> 20':''
		),
		'num_static' => array(
						'print' => $num_static,
						'error' => empty($num_static)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> 20':''
		),
		'html_secure' => array(
						'print' => $html_secure,
						'error' => empty($html_secure)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> / %html%':''
		),
		'p_title' => array(
						'print' => $p_title,
						'error' => ''
		),
		'cache' => array(
						'print' => $cache,
						'error' => empty($cache)?'<img src="'.skins_url.'/images/error.gif" hspace="5" alt="" />Поле не заполнено!<br /><b>Ремомендованно:</b> 1':''
		),
	);
	
	foreach($tVars as $row){
		//print "<pre>".var_export($row['error'], true)."</pre>";
		if(!empty($row['error'])){
			$info .= msg(array("type" => "info", "info" => 'У вас ошибка! Если не получается её исправить, обратитесь на форум <a href="http://ngcms.ru/forum/viewtopic.php?id=2055" target="_blank"><b>simple_title_pro</b></a> или мне на ICQ: 209388634 или jabber: rozard@ya.ru'), 0, 2);
			break;
		}
	}
	
	$tVars = array(
		'info' => array(
			'true' => !empty($info)?1:0,
			'print' => $info
		),
		'reklama' => array(
			'true' => !empty($reklama)?1:0,
			'print' => nl2br($reklama)
		),
		'global' => 'Общие',
		'entries' => $xt->render($tVars)
	);
	
	print $xg->render($tVars);
}

function del_cat()
{global $mysql;
	
	$id = intval($_REQUEST['id']);
	
	if( empty($id) )
		return msg(array("type" => "error", "text" => "Ошибка, вы не выбрали что хотите удалить"));
	
	$mysql->query("delete from ".prefix."_simple_title_pro where id = {$id} LIMIT 1");
	msg(array("type" => "info", "info" => "Запись удалена"));
	
}

function del_news()
{global $mysql;
	
	$id = intval($_REQUEST['id']);
	
	if( empty($id) )
		return msg(array("type" => "error", "text" => "Ошибка, вы не выбрали что хотите удалить"));
	
	$mysql->query("delete from ".prefix."_simple_title_pro where id = {$id} LIMIT 1");
	msg(array("type" => "info", "info" => "Запись удалена"));
	
}

function del_static()
{global $mysql;
	
	$id = intval($_REQUEST['id']);
	
	if( empty($id) )
		return msg(array("type" => "error", "text" => "Ошибка, вы не выбрали что хотите удалить"));
	
	$mysql->query("delete from ".prefix."_simple_title_pro where id = {$id} LIMIT 1");
	msg(array("type" => "info", "info" => "Запись удалена"));
}

function redirect_simple_title_pro($url){
	if (headers_sent()){
		echo "<script>document.location.href='{$url}';</script>\n";
		exit;
	} else {
		header('HTTP/1.1 302 Moved Permanently');
		header("Location: {$url}");
		exit;
	}
}