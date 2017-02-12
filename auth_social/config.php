<?php
if (!defined('NGCMS')) {
	exit('HAL');
}
plugins_load_config();
LoadPluginLang('auth_social', 'config', '', '', '#');
switch ($_REQUEST['action']) {
	case 'options':
		show_options();
		break;
	default:
		show_options();
}
function show_options() {

	global $tpl, $mysql, $lang, $twig;
	$tpath = locatePluginTemplates(array('config/main', 'config/general.from'), 'auth_social', 1);
	if (isset($_REQUEST['submit'])) {
		pluginSetVariable('auth_social', 'vk_client_id', secure_html($_REQUEST['vk_client_id']));
		pluginSetVariable('auth_social', 'vk_client_secret', secure_html($_REQUEST['vk_client_secret']));
		//pluginSetVariable('auth_social', 'vk_redirect_uri', intval($_REQUEST['vk_redirect_uri']));
		pluginSetVariable('auth_social', 'odnoklassniki_client_id', secure_html($_REQUEST['odnoklassniki_client_id']));
		pluginSetVariable('auth_social', 'odnoklassniki_client_secret', secure_html($_REQUEST['odnoklassniki_client_secret']));
		//pluginSetVariable('auth_social', 'odnoklassniki_redirect_uri', intval($_REQUEST['odnoklassniki_redirect_uri']));
		pluginSetVariable('auth_social', 'odnoklassniki_public_key', secure_html($_REQUEST['odnoklassniki_public_key']));
		pluginSetVariable('auth_social', 'mailru_client_id', secure_html($_REQUEST['mailru_client_id']));
		pluginSetVariable('auth_social', 'mailru_client_secret', secure_html($_REQUEST['mailru_client_secret']));
		//pluginSetVariable('auth_social', 'mailru_redirect_uri', intval($_REQUEST['mailru_redirect_uri']));
		pluginSetVariable('auth_social', 'yandex_client_id', secure_html($_REQUEST['yandex_client_id']));
		pluginSetVariable('auth_social', 'yandex_client_secret', secure_html($_REQUEST['yandex_client_secret']));
		//pluginSetVariable('auth_social', 'yandex_redirect_uri', intval($_REQUEST['yandex_redirect_uri']));
		pluginSetVariable('auth_social', 'google_client_id', secure_html($_REQUEST['google_client_id']));
		pluginSetVariable('auth_social', 'google_client_secret', secure_html($_REQUEST['google_client_secret']));
		//pluginSetVariable('auth_social', 'google_redirect_uri', intval($_REQUEST['google_redirect_uri']));
		pluginSetVariable('auth_social', 'facebook_client_id', secure_html($_REQUEST['facebook_client_id']));
		pluginSetVariable('auth_social', 'facebook_client_secret', secure_html($_REQUEST['facebook_client_secret']));
		//pluginSetVariable('auth_social', 'facebook_redirect_uri', intval($_REQUEST['facebook_redirect_uri']));
		pluginSetVariable('auth_social', 'twitter_client_id', secure_html($_REQUEST['twitter_client_id']));
		pluginSetVariable('auth_social', 'twitter_client_secret', secure_html($_REQUEST['twitter_client_secret']));
		pluginSetVariable('auth_social', 'steam_client_secret', secure_html($_REQUEST['steam_client_secret']));
		pluginSetVariable('auth_social', 'twitch_client_id', secure_html($_REQUEST['twitch_client_id']));
		pluginSetVariable('auth_social', 'twitch_client_secret', secure_html($_REQUEST['twitch_client_secret']));
		pluginsSaveConfig();
		redirect_auth_social('?mod=extra-config&plugin=auth_social');
	}
	$vk_client_id = pluginGetVariable('auth_social', 'vk_client_id');
	$vk_client_secret = pluginGetVariable('auth_social', 'vk_client_secret');
	//$vk_redirect_uri = pluginGetVariable('auth_social', 'vk_redirect_uri');
	$odnoklassniki_client_id = pluginGetVariable('auth_social', 'odnoklassniki_client_id');
	$odnoklassniki_client_secret = pluginGetVariable('auth_social', 'odnoklassniki_client_secret');
	//$odnoklassniki_redirect_uri = pluginGetVariable('auth_social', 'odnoklassniki_redirect_uri');
	$odnoklassniki_public_key = pluginGetVariable('auth_social', 'odnoklassniki_public_key');
	$mailru_client_id = pluginGetVariable('auth_social', 'mailru_client_id');
	$mailru_client_secret = pluginGetVariable('auth_social', 'mailru_client_secret');
	//$mailru_redirect_uri = pluginGetVariable('auth_social', 'mailru_redirect_uri');
	$yandex_client_id = pluginGetVariable('auth_social', 'yandex_client_id');
	$yandex_client_secret = pluginGetVariable('auth_social', 'yandex_client_secret');
	//$yandex_redirect_uri = pluginGetVariable('auth_social', 'yandex_redirect_uri');
	$google_client_id = pluginGetVariable('auth_social', 'google_client_id');
	$google_client_secret = pluginGetVariable('auth_social', 'google_client_secret');
	//$google_redirect_uri = pluginGetVariable('auth_social', 'google_redirect_uri');	
	$facebook_client_id = pluginGetVariable('auth_social', 'facebook_client_id');
	$facebook_client_secret = pluginGetVariable('auth_social', 'facebook_client_secret');
	//$facebook_redirect_uri = pluginGetVariable('auth_social', 'facebook_redirect_uri');
	$twitter_client_id = pluginGetVariable('auth_social', 'twitter_client_id');
	$twitter_client_secret = pluginGetVariable('auth_social', 'twitter_client_secret');
	$steam_client_secret = pluginGetVariable('auth_social', 'steam_client_secret');
	$twitch_client_id = pluginGetVariable('auth_social', 'twitch_client_id');
	$twitch_client_secret = pluginGetVariable('auth_social', 'twitch_client_secret');
	$xt = $twig->loadTemplate($tpath['config/general.from'] . 'config/general.from.tpl');
	$tVars = array(
		'skins_url' => skins_url,
		'home'      => home,
		'tpl_home'  => admin_url,
		'vk_client_id'     => $vk_client_id,
		'vk_client_secret' => $vk_client_secret,
		//'vk_redirect_uri' => $vk_redirect_uri,
		'odnoklassniki_client_id'     => $odnoklassniki_client_id,
		'odnoklassniki_client_secret' => $odnoklassniki_client_secret,
		//'odnoklassniki_redirect_uri' => $odnoklassniki_redirect_uri,
		'odnoklassniki_public_key'    => $odnoklassniki_public_key,
		'mailru_client_id'     => $mailru_client_id,
		'mailru_client_secret' => $mailru_client_secret,
		//'mailru_redirect_uri' => $mailru_redirect_uri,
		'yandex_client_id'     => $yandex_client_id,
		'yandex_client_secret' => $yandex_client_secret,
		//'yandex_redirect_uri' => $yandex_redirect_uri,
		'google_client_id'     => $google_client_id,
		'google_client_secret' => $google_client_secret,
		//'google_redirect_uri' => $google_redirect_uri,
		'facebook_client_id'     => $facebook_client_id,
		'facebook_client_secret' => $facebook_client_secret,
		//'facebook_redirect_uri' => $facebook_redirect_uri,
		'twitter_client_id'     => $twitter_client_id,
		'twitter_client_secret' => $twitter_client_secret,
		'steam_client_secret' => $steam_client_secret,
		'twitch_client_id'     => $twitch_client_id,
		'twitch_client_secret' => $twitch_client_secret,
	);
	$xg = $twig->loadTemplate($tpath['config/main'] . 'config/main.tpl');
	$tVars = array(
		'entries' => $xt->render($tVars),
	);
	print $xg->render($tVars);
}

function redirect_auth_social($url) {

	if (headers_sent()) {
		echo "<script>document.location.href='{$url}';</script>\n";
	} else {
		header('HTTP/1.1 302 Moved Permanently');
		header("Location: {$url}");
	}
}

