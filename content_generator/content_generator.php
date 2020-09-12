<?php

// Protect against hack attempts
if (!defined('NGCMS')) die('HAL');

register_plugin_page('content_generator', '', 'plugin_content_generator', 0);

function newsGenerator($count)
{

	include_once(root . 'includes/inc/lib_admin.php');
	include_once(__DIR__ . '/lib/Faker/autoload.php');

	$faker = Faker\Factory::create('ru_RU');

	for ($i = 0; $i < $count; $i++) {
		$_REQUEST['title'] = iconv("utf-8", $faker->realText(30, 1));
		$_REQUEST['ng_news_content'] = iconv("utf-8", $faker->realText());
		$_REQUEST['approve'] = 1;
		$_REQUEST['mainpage'] = 1;
		addNews(['no.token' => true]);
	}
}

function staticGenerator($count)
{

	include_once(root . 'actions/static.php');
	include_once(__DIR__ . '/lib/Faker/autoload.php');

	$faker = Faker\Factory::create('ru_RU');

	for ($i = 0; $i < $count; $i++) {
		$_REQUEST['title'] = iconv("utf-8", $faker->realText(30, 1));
		$_REQUEST['content'] = iconv("utf-8", $faker->realText());
		$_REQUEST['flag_published'] = 1;
		$_REQUEST['token'] = genUToken('admin.static');
		addStatic();
	}
}

function plugin_content_generator()
{

	global $SUPRESS_TEMPLATE_SHOW, $SYSTEM_FLAGS;

	$SUPRESS_TEMPLATE_SHOW = 1;
	$SUPRESS_MAINBLOCK_SHOW = 1;
	@header('Content-type: application/json; charset=utf-8');
	$SYSTEM_FLAGS['http.headers'] = array(
		'content-type'  => 'application/json; charset=utf-8',
		'cache-control' => 'private',
	);

	$count = (int)$_REQUEST['real_count'];

	switch ($_REQUEST['actionName']) {
		case 'generate_news':
			newsGenerator($count);
			break;
		case 'generate_static':
			staticGenerator($count);
			break;
	}

	ob_end_clean();
	echo json_encode($count);
	exit();
}
