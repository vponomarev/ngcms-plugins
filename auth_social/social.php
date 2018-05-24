<?php
# protect against hack attempts
if (!defined('NGCMS')) die ('Galaxy in danger');
# preload required libraries
//loadPluginLibrary('uprofile', 'lib');
//loadPluginLibrary('comments', 'lib');
loadPluginLibrary('uprofile', 'lib');
register_plugin_page('auth_social', '', 'socialAuth', 0);
add_act('usermenu', 'auth_social_links');
//register_plugin_page('auth_social', 'register' , 'socialRegister', 0);
//register_plugin_page('auth_social', 'delete' , 'loginzaDelete', 0);
function socialAuth() {

	global $config, $template, $tpl, $mysql, $userROW, $AUTH_METHOD;
	require_once ($_SERVER['DOCUMENT_ROOT']) . '/engine/plugins/auth_social/lib/SocialAuther/autoload.php';
	$adapterConfigs = array(
		'vk'            => array(
			'client_id'     => pluginGetVariable('auth_social', 'vk_client_id'),
			'client_secret' => pluginGetVariable('auth_social', 'vk_client_secret'),
			'redirect_uri'  => home . "/plugin/auth_social/?provider=vk"
		),
		'odnoklassniki' => array(
			'client_id'     => pluginGetVariable('auth_social', 'odnoklassniki_client_id'),
			'client_secret' => pluginGetVariable('auth_social', 'odnoklassniki_client_secret'),
			'redirect_uri'  => home . "/plugin/auth_social/?provider=odnoklassniki",
			'public_key'    => pluginGetVariable('auth_social', 'odnoklassniki_public_key')
		),
		'mailru'        => array(
			'client_id'     => pluginGetVariable('auth_social', 'mailru_client_id'),
			'client_secret' => pluginGetVariable('auth_social', 'mailru_client_secret'),
			'redirect_uri'  => home . "/plugin/auth_social/?provider=mailru"
		),
		'yandex'        => array(
			'client_id'     => pluginGetVariable('auth_social', 'yandex_client_id'),
			'client_secret' => pluginGetVariable('auth_social', 'yandex_client_secret'),
			'redirect_uri'  => home . "/plugin/auth_social/?provider=yandex"
		),
		'google'        => array(
			'client_id'     => pluginGetVariable('auth_social', 'google_client_id'),
			'client_secret' => pluginGetVariable('auth_social', 'google_client_secret'),
			'redirect_uri'  => home . "/plugin/auth_social/?provider=google"
		),
		'facebook'      => array(
			'client_id'     => pluginGetVariable('auth_social', 'facebook_client_id'),
			'client_secret' => pluginGetVariable('auth_social', 'facebook_client_secret'),
			'redirect_uri'  => home . "/plugin/auth_social/?provider=facebook"
		),
		'twitter'       => array(
			'client_id'     => pluginGetVariable('auth_social', 'twitter_client_id'),
			'client_secret' => pluginGetVariable('auth_social', 'twitter_client_secret'),
			'redirect_uri'  => home . "/plugin/auth_social/?provider=twitter"
		),
		'steam'         => array(
			'client_id'     => pluginGetVariable('auth_social', 'steam_client_id'),
			'client_secret' => pluginGetVariable('auth_social', 'steam_client_secret'),
			'redirect_uri'  => home . "/plugin/auth_social/?provider=steam"
		),
		'twitch'        => array(
			'client_id'     => pluginGetVariable('auth_social', 'twitch_client_id'),
			'client_secret' => pluginGetVariable('auth_social', 'twitch_client_secret'),
			'redirect_uri'  => home . "/plugin/auth_social/?provider=twitch"
		),
	);
	$adapters = array();
	foreach ($adapterConfigs as $adapter => $settings) {
		$class = 'SocialAuther\Adapter\\' . ucfirst($adapter);
		$adapters[$adapter] = new $class($settings);
	}
	if (isset($_GET['provider']) && array_key_exists($_GET['provider'], $adapters)) {
		$auther = new SocialAuther\SocialAuther($adapters[$_GET['provider']]);
		if ($auther->authenticate()) {
			//var_dump($auther->getProvider());
			//var_dump($auther->getSocialId());
			$record = $mysql->record(
				"SELECT *  FROM " . uprefix . "_users WHERE `provider` = '{$auther->getProvider()}' AND `social_id` = '{$auther->getSocialId()}' LIMIT 1"
			);
			if (!$record) {
				$values = array(
					EncodePassword(MakeRandomPassword()),
					$auther->getProvider(),
					$auther->getSocialId(),
					iconv('UTF-8', 'Windows-1251', $auther->getName()),
					$auther->getEmail(),
					$auther->getSocialPage(),
					$auther->getSex(),
					date('Y-m-d', strtotime($auther->getBirthday())),
					$auther->getAvatar(),
					time() + ($config['date_adjust'] * 60),
					time() + ($config['date_adjust'] * 60)
				);
				$query = "INSERT INTO " . uprefix . "_users (`pass`, `provider`, `social_id`, `name`, `mail`, `social_page`, `sex`, `birthday`, `avatar`, `reg`, `last`) VALUES ('";
				$query .= implode("', '", $values) . "')";
				$result = $mysql->query($query);
				$user_doreg = $mysql->record("SELECT * FROM " . uprefix . "_users WHERE social_page = " . db_squote($auther->getSocialPage()));
				$userid = $user_doreg['id'];
				$get_avatar = $auther->getAvatar();
				// если есть аватар, пробуем скачать
				if (!empty($get_avatar)) {
					addToFiles('newavatar', $get_avatar);
					// Load required library
					@include_once root . 'includes/classes/upload.class.php';
					// UPLOAD AVATAR
					if ($_FILES['newavatar']['name']) {
						// Delete an avatar if user already has it
						//uprofile_manageDelete('avatar', $currentUser['id']);
						$fmanage = new file_managment();
						$imanage = new image_managment();
						$fname = $userid . '_' . strtolower($_FILES['newavatar']['name']);
						$ftmp = $_FILES['newavatar']['tmp_name'];
						$mysql->query("insert into " . prefix . "_images (name, orig_name, folder, date, user, owner_id, category) values (" . db_squote($fname) . ", " . db_squote($fname) . ", '', unix_timestamp(now()), " . db_squote(iconv('UTF-8', 'Windows-1251', $auther->getName())) . ", " . db_squote($userid) . ", '1')");
						$rowID = $mysql->record("select LAST_INSERT_ID() as id");
						if (copy($ftmp, $config['avatars_dir'] . $fname)) {
							$sz = $imanage->get_size($config['avatars_dir'] . $fname);
							$mysql->query("update " . prefix . "_images set width=" . db_squote($sz['1']) . ", height=" . db_squote($sz['2']) . " where id = " . db_squote($rowID['id']) . " ");
							$avatar = $fname;
						}
					}
					$mysql->query("UPDATE `" . uprefix . "_users` SET `activation` = '', `avatar` = " . db_squote($avatar) . " WHERE social_page = " . db_squote($auther->getSocialPage()));
				}
			} else {
				$userFromDb = new stdClass();
				$userFromDb->provider = $record['provider'];
				$userFromDb->socialId = $record['social_id'];
				$userFromDb->name = $record['name'];
				$userFromDb->email = $record['email'];
				$userFromDb->socialPage = $record['social_page'];
				$userFromDb->sex = $record['sex'];
				$userFromDb->birthday = date('m.d.Y', strtotime($record['birthday']));
				//$userFromDb->avatar     = $record['avatar'];
			}
			$user = new stdClass();
			$user->provider = $auther->getProvider();
			$user->socialId = $auther->getSocialId();
			$user->name = iconv('UTF-8', 'Windows-1251', $auther->getName());
			$user->email = $auther->getEmail();
			$user->socialPage = $auther->getSocialPage();
			$user->sex = $auther->getSex();
			$user->birthday = $auther->getBirthday();
			//$user->avatar     = $auther->getAvatar();
			if (isset($userFromDb) && $userFromDb != $user) {
				$idToUpdate = $record['id'];
				$birthday = date('Y-m-d', strtotime($user->birthday));
				$get_avatar = $auther->getAvatar();
				// если есть аватар, пробуем скачать
				if (!empty($get_avatar)) {
					addToFiles('newavatar', $get_avatar);
					// Load required library
					@include_once root . 'includes/classes/upload.class.php';
					// UPLOAD AVATAR
					if ($_FILES['newavatar']['name']) {
						// Delete an avatar if user already has it
						//uprofile_manageDelete('avatar', $currentUser['id']);
						$fmanage = new file_managment();
						$imanage = new image_managment();
						$fname = $userid . '_' . strtolower($_FILES['newavatar']['name']);
						$ftmp = $_FILES['newavatar']['tmp_name'];
						$mysql->query("insert into " . prefix . "_images (name, orig_name, folder, date, user, owner_id, category) values (" . db_squote($fname) . ", " . db_squote($fname) . ", '', unix_timestamp(now()), " . db_squote(iconv('UTF-8', 'Windows-1251', $auther->getName())) . ", " . db_squote($idToUpdate) . ", '1')");
						$rowID = $mysql->record("select LAST_INSERT_ID() as id");
						if (copy($ftmp, $config['avatars_dir'] . $fname)) {
							$sz = $imanage->get_size($config['avatars_dir'] . $fname);
							$mysql->query("update " . prefix . "_images set width=" . db_squote($sz['1']) . ", height=" . db_squote($sz['2']) . " where id = " . db_squote($rowID['id']) . " ");
							$avatar = $fname;
						}
					}
				}
				$mysql->query(
					"UPDATE " . uprefix . "_users SET " .
					"`social_id` = '{$user->socialId}', `name` = '{$user->name}', `mail` = '{$user->email}', " .
					"`social_page` = '{$user->socialPage}', `sex` = '{$user->sex}', " .
					"`birthday` = '{$birthday}', `avatar` = '$avatar' " .
					"WHERE `id`='{$idToUpdate}'"
				);
			}
			$_SESSION['user'] = $user;
			$user_dologin = $mysql->record("SELECT * FROM " . uprefix . "_users WHERE social_page = " . db_squote($auther->getSocialPage()));
			# if user is registered yet then authorize his
			if (is_array($user_dologin)) {
				$auth = $AUTH_METHOD[$config['auth_module']];
				$auth->save_auth($user_dologin);
				header('Location: ' . $config['home_url']);

				return;
			}
		}
		header('Location: ' . $config['home_url']);
	} else {
		header('Location: ' . $config['home_url']);
	}
	/**/
}

class SocialAuthCoreFilter extends CoreFilter {

	function showUserMenu(&$tVars) {

		global $mysql, $userROW, $lang;
		require_once root . 'plugins/auth_social/lib/SocialAuther/autoload.php';
		$adapterConfigs = array(
			'vk'            => array(
				'client_id'     => pluginGetVariable('auth_social', 'vk_client_id'),
				'client_secret' => pluginGetVariable('auth_social', 'vk_client_secret'),
				'redirect_uri'  => home . "/plugin/auth_social/?provider=vk"
			),
			'odnoklassniki' => array(
				'client_id'     => pluginGetVariable('auth_social', 'odnoklassniki_client_id'),
				'client_secret' => pluginGetVariable('auth_social', 'odnoklassniki_client_secret'),
				'redirect_uri'  => home . "/plugin/auth_social/?provider=odnoklassniki",
				'public_key'    => pluginGetVariable('auth_social', 'odnoklassniki_public_key')
			),
			'mailru'        => array(
				'client_id'     => pluginGetVariable('auth_social', 'mailru_client_id'),
				'client_secret' => pluginGetVariable('auth_social', 'mailru_client_secret'),
				'redirect_uri'  => home . "/plugin/auth_social/?provider=mailru"
			),
			'yandex'        => array(
				'client_id'     => pluginGetVariable('auth_social', 'yandex_client_id'),
				'client_secret' => pluginGetVariable('auth_social', 'yandex_client_secret'),
				'redirect_uri'  => home . "/plugin/auth_social/?provider=yandex"
			),
			'google'        => array(
				'client_id'     => pluginGetVariable('auth_social', 'google_client_id'),
				'client_secret' => pluginGetVariable('auth_social', 'google_client_secret'),
				'redirect_uri'  => home . "/plugin/auth_social/?provider=google"
			),
			'facebook'      => array(
				'client_id'     => pluginGetVariable('auth_social', 'facebook_client_id'),
				'client_secret' => pluginGetVariable('auth_social', 'facebook_client_secret'),
				'redirect_uri'  => home . "/plugin/auth_social/?provider=facebook"
			),
			'twitter'       => array(
				'client_id'     => pluginGetVariable('auth_social', 'twitter_client_id'),
				'client_secret' => pluginGetVariable('auth_social', 'twitter_client_secret'),
				'redirect_uri'  => home . "/plugin/auth_social/?provider=twitter"
			),
			'steam'         => array(
				'client_id'     => pluginGetVariable('auth_social', 'steam_client_id'),
				'client_secret' => pluginGetVariable('auth_social', 'steam_client_secret'),
				'redirect_uri'  => home . "/plugin/auth_social/?provider=steam"
			),
			'twitch'        => array(
				'client_id'     => pluginGetVariable('auth_social', 'twitch_client_id'),
				'client_secret' => pluginGetVariable('auth_social', 'twitch_client_secret'),
				'redirect_uri'  => home . "/plugin/auth_social/?provider=twitch"
			)
		);
		$adapters = array();
		foreach ($adapterConfigs as $adapter => $settings) {
			$class = 'SocialAuther\Adapter\\' . ucfirst($adapter);
			$adapters[$adapter] = new $class($settings);
		}
		foreach ($adapters as $title => $adapter) {
			$tVars['p']['auth_social'][$title] = array(
				'authUrl' => $adapter->getAuthUrl(),
				'title'   => ucfirst($title)
			);
		}
	}
}

register_filter('core.userMenu', 'auth_social', new SocialAuthCoreFilter);
if (class_exists('p_uprofileFilter')) {
	class uSocialFilter extends p_uprofileFilter {

		function showProfile($userID, $SQLrow, &$tvars) {
			/*
			if (empty($SQLrow['loginza_id'])) {
				$tvars['regx']['/\[if-loginza\](.*?)\[\/if-loginza\]/si'] = '';
				$tvars['vars']['loginza_account'] = '';
			}
			else {
				$tvars['regx']['/\[if-loginza\](.*?)\[\/if-loginza\]/si'] = '$1';
				$tvars['vars']['loginza_account'] = $SQLrow['loginza_id'];
			}
			*/
		}

		function editProfileForm($userID, $SQLrow, &$tvars) {
			/*
			if (empty($SQLrow['loginza_id'])) {
				$tvars['regx']['/\[if-loginza\](.*?)\[\/if-loginza\]/si'] = '';
				$tvars['regx']['/\[if-not-loginza\](.*?)\[\/if-not-loginza\]/si'] = '$1';
				$tvars['vars']['loginza_account'] = '';
			}
			else {
				$tvars['regx']['/\[if-loginza\](.*?)\[\/if-loginza\]/si'] = '$1';
				$tvars['regx']['/\[if-not-loginza\](.*?)\[\/if-not-loginza\]/si'] = '';
				$tvars['vars']['loginza_account'] = $SQLrow['loginza_id'];
			}
			*/
		}

		function editProfile($userID, $SQLrow, &$SQLnew) {

			global $lang, $config, $mysql;
			$SQLnew['sex'] = secure_html($_REQUEST['editsex']);
			$SQLnew['birthday'] = secure_html($_REQUEST['editbirthday']);
		}
	}

	register_filter('plugin.uprofile', 'auth_social', new uSocialFilter);
}
/**
 * Add to $_FILES from external url
 * sample usage: addToFiles('google_favicon', 'http://google.com/favicon.ico');
 * @since  17.12.12 17:23
 * @author mekegi
 *
 * @param string $key
 * @param string $url sample http://some.tld/path/to/file.ext
 */
function addToFiles($key, $url) {

	$tempName = tempnam(ini_get('upload_tmp_dir'), 'upload_');
	//$tempName = tempnam('/tmp', 'php_files');
	$originalName = basename(parse_url($url, PHP_URL_PATH));
	$imgRawData = file_get_contents($url);
	file_put_contents($tempName, $imgRawData);
	$info = getimagesize($tempName);
	$_FILES[$key] = array(
		'name'     => $originalName,
		'type'     => $info['mime'],
		'tmp_name' => $tempName,
		'error'    => 0,
		'size'     => strlen($imgRawData),
	);
	//return $_FILES[$key];
}
