<?php
if (!defined('2z')) { die("Don't you figure you're so cool?"); }
add_act('index_post', 'simple_tags');
function simple_tags(){
		global $config, $tpl, $mysql, $parse, $template, $echo_name, $action;
	if (phpversion() >= 5)
	{ 
		$t_minus = 6;
	}
	else 
	{
		$t_minus = 2;
	}
if ($action == false)
{
		$right_title = GetNewsTitle(" =|=|= ", altname);
		$news_title = substr($right_title, strrpos($right_title, '=|=|=')+$t_minus);

		$site_title = $config['home_title'];
		if ($_GET['altname'] <> '')
		{
		$category_title = str_replace(array($news_title,' =|=|='),array('',''),$right_title);
		}
		else{
		$category_title = str_replace(' =|=|=','',$right_title);
		$news_title = '';
		}
		$template['vars']['title'] = '1111';
		define('p1', $category_title);
		define('p2', $news_title);
		define('p3', $site_title);
if ($_GET['altname'] == '' && $_GET['category'] <> '')
{
$template['vars']['titles'] = str_replace(array('%1%','%2%','%3%'),array(p1,p2,p3),extra_get_param('simple_title','c_title'));
echo $news_title;
}
elseif ($_GET['altname'] <> '')
{
$template['vars']['titles'] = str_replace(array('%1%','%2%','%3%'),array(p1,p2,p3),extra_get_param('simple_title','n_title'));
}

elseif ($_GET['altname'] == '' AND $_GET['category'] == '')
{
$template['vars']['titles'] = str_replace(array('%1%','%2%','%3%'),array(p1,p2,p3),extra_get_param('simple_title','m_title'));
}
}
elseif ($action == 'static')
{
$st_name = str_replace($config['home_title'].' :','',$template['vars']['titles']);
define('p3', $config['home_title']);
define('p4', $st_name);
$template['vars']['titles'] = str_replace(array('%4%','%3%'),array(p4,p3),extra_get_param('simple_title','static_title'));

                     }

}
?>