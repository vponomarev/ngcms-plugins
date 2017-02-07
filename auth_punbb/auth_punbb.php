<?php
// Protect against hack attempts
if (!defined('NGCMS')) die ('HAL');
//
// Configuring our module
//
global $AUTH_METHOD;
global $AUTH_CAPABILITIES;
global $config;
$AUTH_METHOD['punbb'] = new auth_punbb;
$AUTH_CAPABILITIES['punbb'] = array('login' => '1', 'db' => '1');

class auth_punbb {

	// Constructor
	function auth_punbb() {

		global $mysql;
		$this->error = 0;
		// We need additional connection to DB server
		if (extra_get_param('auth_punbb', 'extdb')) {
			if (function_exists('DBLoad'))
				$this->auth_db = DBLoad();
			else
				$this->auth_db = new mysql;
			$this->auth_db->connect(extra_get_param('auth_punbb', 'dbhost'), extra_get_param('auth_punbb', 'dblogin'), extra_get_param('auth_punbb', 'dbpass'), extra_get_param('auth_punbb', 'dbname'), 1);
			if ($this->auth_db->error) {
				print "<br />Can't connect to SQL DB<br />\n";
				$this->error = $this->auth_db->error;
			}
		} else {
			$this->auth_db = $mysql;
		}
	}

	// Function for generating punBB password
	function punBB_gen_password($password) {

		if (function_exists('sha1'))
			return sha1($password);
		else if (function_exists('mhash'))
			return bin2hex(mhash(MHASH_SHA1, $password));
		else    return md5($password);
	}


	// Login function
	// $username	= User's login
	// $password	= User's password
	// $auto_scan	= If '1', function will search parameters in POST params, else - from function params
	function login($auto_scan = 1, $username = '', $password = '') {

		global $mysql;
		if ($this->error) {
			return '';
		}
		if ($auto_scan) {
			$username = $_REQUEST['username'];
			$password = $_REQUEST['password'];
		}
		$dbprefix = extra_get_param('auth_punbb', 'dbprefix') ? extra_get_param('auth_punbb', 'dbprefix') : '';
		// Create crypted password from punBB DB
		$crypt_password = $this->punBB_gen_password($password);
		// Fetch user with desired login
		//$sql_punbb = "select * from ".$dbprefix."users where username = ".db_squote($username)." and password=".db_squote($crypt_password);
		$sql_punbb = "select * from " . $dbprefix . "users where username = " . db_squote($username);
		$pun_row = $this->auth_db->record($sql_punbb);
		// NO LOGIN FOUND
		if (!$pun_row) {
			// Check existance of user in NG CMS DB in case when autocreate NG => punBB is allowed
			if (extra_get_param('auth_punbb', 'autocreate_punbb')) {
				$sql = "select * from " . uprefix . "_users where name = " . db_squote($username) . " and pass = " . db_squote(md5(md5($password)));
				if ($row = $mysql->record($sql)) {
					// User was found in NG. Lock table before insert
					$this->auth_db->query("lock table " . $dbprefix . "users write");
					// 1. Check again if user exists
					if ($pun_row = $this->auth_db->record($sql_punbb)) {
						// Wow, user found. Seems that we've got several simultaneous connections
						$this->auth_db->query("unlock tables");

						return '';
					}
					// Create user in punBB DB
					$punbb_password = $this->punBB_gen_password($password);
					$this->auth_db->query("insert into " . $dbprefix . "users (username, group_id, password, email, language, style, registered, registration_ip) values (" . db_squote($row['name']) . ", " . intval(extra_get_param('auth_punbb', 'initial_group_id')) . ", " . db_squote($punbb_password) . ", " . db_squote($row['email']) . ", " . db_squote(extra_get_param('auth_punbb', 'reg_lang')) . ", " . db_squote(extra_get_param('auth_punbb', 'reg_style')) . ", unix_timestamp(now()), '$ip')");
					$punbb_userid = $this->auth_db->record('select LAST_INSERT_ID() as id');
					// Link NG => punBB
					$mysql->query("update " . uprefix . "_users set punbb_userid = " . db_squote($punbb_userid['id']) . " where id = " . $row['id']);
					// Fetch record (again) from punBB DB
					$pun_row = $this->auth_db->record($sql_punbb);
					$this->pun_row = $pun_row;
					// Update $row array - write new value of 'punbb_userid' field
					$row['punbb_userid'] = $punbb_userid['id'];
					$this->auth_db->query("unlock tables");

					return $row;
				}
			}

			return '';
		} else {
			// Check for password. Return false if password mismatch
			if ($pun_row['password'] != $crypt_password)
				return '';
		}
		// Row was found. Now we meed to synchronise it with our own DB
		// 1. Try to fetch linked account data from our own DB
		if ($row = $mysql->record("select * from " . uprefix . "_users where punbb_userid = " . db_squote($pun_row['id']))) {
			// Record fetched. Save punBB row and return row from our DB
			$this->pun_row = $pun_row;

			return $row;
		}
		// Row was not found. Let's block DB
		$mysql->query("lock table " . uprefix . "_users write");
		// Check again (for simultaneous connections)
		if ($row = $mysql->record("select * from " . uprefix . "_users where punbb_userid = " . db_squote($pun_row['id']))) {
			// Record found. Return it
			$mysql->query("unlock tables");
			$this->pun_row = $pun_row;

			return $row;
		}
		// No record. Check for DUPs
		if ($row = $mysql->record("select * from " . prefix . "_users where lower(name) = lower(" . db_squote($pun_row['username']) . ")")) {
			// DUP. Unlock table
			$mysql->query("unlock tables");
			// If passwords are equal (and record linking is allowed) - let's link
			if (extra_get_param('auth_punbb', 'userjoin') && ($row['pass'] == md5(md5($password)))) {
				$mysql->query("update " . prefix . "_users set punbb_userid=" . db_squote($pun_row['id']) . " where id=" . db_squote($row['id']));
				$row['punbb_userid'] = $pun_row['id'];
				$this->pun_row = $pun_row;

				return $row;
			}

			return '';
		}
		// Exit if no NG autocreate is allowed
		if (!extra_get_param('auth_punbb', 'autocreate_ng')) {
			return '';
		}
		// We don't have a record. Let's create one
		$query = "insert into " . uprefix . "_users (name, pass, last, reg, ip, punbb_userid) values (" . db_squote($pun_row['username']) . ", md5(md5(" . db_squote($password) . ")), unix_timestamp(now()), unix_timestamp(now()),'', " . $pun_row['id'] . ")";
		$mysql->query($query);
		$mysql->query("unlock tables");
		// Now let's fetch new row
		if ($row = $mysql->record("select * from " . uprefix . "_users where punbb_userid = " . db_squote($pun_row['id']))) {
			// Record found. Ok.
			$this->pun_row = $pun_row;

			return $row;
		}
		// DB structural error - DB row was not created
		print "DB query error. Please contact developers.<br />\n";

		return '';
	}

	//
	// Save info that user is logged in
	// $dbrow	= record from our (NG) DB
	function save_auth($dbrow) {

		global $config, $mysql;
		$dbprefix = extra_get_param('auth_punbb', 'dbprefix') ? extra_get_param('auth_punbb', 'dbprefix') : '';
		// Exit if no flag 'punbb_userid' is given (i.e. no save)
		if (!$dbrow['punbb_userid']) {
			return 0;
		}
		// Fetch data from punBB DB if we don't have data in cache
		if ($dbrow['punbb_userid'] != $this->pun_row['id']) {
			$this->pun_row = $this->auth_db->record("select * from " . $dbprefix . "users where id = " . db_squote($dbrow['punbb_userid']));
		}
		// Exit if fetch attempt from cache is failed
		if (!$this->pun_row) {
			return 0;
		}
		// Create cookie in punBB format
		$punbb_cookie = md5(extra_get_param('auth_punbb', 'cookie_seed') . $this->pun_row['password']);
		// Create random cookie (for NG)
		$auth_cookie = md5(uniqid(rand(), 1));
		$query = "update " . uprefix . "_users set last = " . db_squote(time()) . ", ip=" . db_squote($ip) . ", authcookie = " . db_squote($auth_cookie) . " where id=" . db_squote($dbrow['id']);
		$mysql->query($query);
		// Set cookie for user
		@setcookie('zz_auth', $auth_cookie, ($config['remember'] ? (time() + 3600 * 24 * 365) : 0), '/');
		@setcookie('punbb_cookie', serialize(array($this->pun_row['id'], $punbb_cookie)), ($config['remember'] ? (time() + 3600 * 24 * 365) : 0), '/', extra_get_param('auth_punbb', 'cookie_domain'));

		return 1;
	}

	//
	// Check if user is authorized via punBB db
	function check_auth() {

		global $config, $mysql;
		list ($punbb_userid, $punbb_cookie) = @unserialize($_COOKIE['punbb_cookie']);
		if (!$punbb_userid) {
			return '';
		}
		$dbprefix = extra_get_param('auth_punbb', 'dbprefix') ? extra_get_param('auth_punbb', 'dbprefix') : '';
		$query = "select * from " . $dbprefix . "users where id = " . db_squote($punbb_userid);
		// Return if no user found
		if (!($row = $this->auth_db->record($query))) {
			return '';
		}
		$this->pun_row = $row;
		// Return if password is incorrect
		if ($punbb_cookie != md5(extra_get_param('auth_punbb', 'cookie_seed') . $row['password'])) {
			return '';
		}
		// Fetch row from our DB
		if ($urow = $mysql->record("select * from " . uprefix . "_users where punbb_userid = " . db_squote($punbb_userid))) {
			return $urow;
		};

		return '';
	}

	//
	// Drop auth
	function drop_auth() {

		global $config, $mysql;
		if ($userid) {
			$mysql->query("update " . uprefix . "_users set authcookie = '' where userid=" . db_squote($userid));
		}
		@setcookie('zz_auth', '', time() - 3600 * 24 * 365, '/');
		@setcookie('punbb_cookie', '', time() - 3600 * 24 * 365, '/', extra_get_param('auth_punbb', 'cookie_domain'));

		return;
	}

	//
	// Return a list of required for registration params
	function get_reg_params() {

		global $config, $lang;
		$params = array();
		LoadPluginLang('auth_punbb', 'auth', '', 'auth');
		array_push($params, array('name' => 'login', title => $lang['auth_login'], 'descr' => $lang['auth_login_descr'], 'type' => 'input'));
		if ($config['register_type'] == "3") {
			array_push($params, array('name' => 'password', title => $lang['auth_pass'], 'descr' => $lang['auth_pass_descr'], 'type' => 'password'));
			array_push($params, array('name' => 'password2', title => $lang['auth_pass2'], 'descr' => $lang['auth_pass2_descr'], 'type' => 'password'));
		}
		array_push($params, array('name' => 'email', title => $lang['auth_email'], 'descr' => $lang['auth_email_descr'], 'type' => 'input'));

		return $params;
	}

	//
	// Make registration
	// params = params received from get_reg_params()
	// values = values for this params
	// msg	  = error messages
	// Returning value:
	// 0 - error (not registered)
	// 1 - ok (registered)
	function register(&$params, $values, &$msg) {

		global $config, $mysql, $lang, $ip;
		$error = 0;
		$values['login'] = trim($values['login']);
		// Preprocess login
		if (strlen($values['login']) < 3) {
			// Login is too short
			$msg = $lang['auth_login_short'];

			return 0;
		}
		if (preg_match('/[&<>' . "'" . ']/', $values['login'])) {
			// Restricted HTML symbols
			$msg = $lang['auth_login_html'];

			return 0;
		}
		if ($config['register_type'] == "3") {
			if (strlen($values['password']) < 3) {
				// Password is too short
				$msg = $lang['auth_pass_short'];

				return 0;
			} else if ($values['password'] != $values['password2']) {
				// Mistype in password in fields 'password' and 'password2'
				$msg = $lang['auth_pass_diff'];

				return 0;
			}
		}
		if ((strlen($values['email']) > "70") || (!preg_match("/^[\.A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $values['email']))) {
			// Wrong email format
			$msg = $lang['auth_email_wrong'];

			return 0;
		}
		// Check for dups in NG
		$row = $mysql->record("select * from " . uprefix . "_users where lower(name)=" . db_squote(strtolower($values['login'])) . " or mail=" . db_squote($values['email']));
		if (is_array($row)) {
			// Email dup
			if (strtolower($row['mail']) == strtolower($values['email'])) {
				// email dup
				$msg = $lang['auth_email_dup'];

				return 0;
			}
			// Login dup
			$msg = $lang['auth_login_dup'];

			return 0;
		}
		$dbprefix = extra_get_param('auth_punbb', 'dbprefix') ? extra_get_param('auth_punbb', 'dbprefix') : '';
		// Check for dups in punBB
		$pun_row = $this->auth_db->record("select * from " . $dbprefix . "users where lower(username)=" . db_squote(strtolower($values['login'])) . " or lower(email)=" . db_squote($values['email']));
		if (is_array($pun_row)) {
			// Email dup
			if (strtolower($pun_row['email']) == strtolower($values['email'])) {
				// email dup
				$msg = $lang['auth_email_dup'];

				return 0;
			}
			// Login dup
			$msg = $lang['auth_login_dup'];

			return 0;
		}
		// Everything is fine. Let's register
		$add_time = time() + ($config['date_adjust'] * 60);
		if ($config['register_type'] == "0") {
			$newpassword = MakeRandomPassword();
			$punbb_password = $this->punBB_gen_password($newpassword);
			$this->auth_db->query("insert into " . $dbprefix . "users (username, group_id, password, email, language, style, registered, registration_ip) values (" . db_squote($values['login']) . ", " . intval(extra_get_param('auth_punbb', 'initial_group_id')) . ", " . db_squote($punbb_password) . ", " . db_squote($values['email']) . ", " . db_squote(extra_get_param('auth_punbb', 'reg_lang')) . ", " . db_squote(extra_get_param('auth_punbb', 'reg_style')) . ", unix_timestamp(now()), '$ip')");
			$punbb_userid = $this->auth_db->record('select LAST_INSERT_ID() as id');
			$mysql->query("INSERT INTO " . uprefix . "_users (name, pass, mail, status, reg, last, punbb_userid) VALUES (" . db_squote($values['login']) . ", " . db_squote(EncodePassword($newpassword)) . ", " . db_squote($values['email']) . ", '4', '" . $add_time . "', '', " . db_squote($punbb_userid['id']) . ")");
			msg(array("text" => $lang['msgo_registered'], "info" => sprintf($lang['msgo_info1'], $newpassword)));
		}
		if ($config['register_type'] == "1") {
			$newpassword = MakeRandomPassword();
			$punbb_password = $this->punBB_gen_password($newpassword);
			$this->auth_db->query("insert into " . $dbprefix . "users (username, group_id, password, email, language, style, registered, registration_ip) values (" . db_squote($values['login']) . ", " . intval(extra_get_param('auth_punbb', 'initial_group_id')) . ", " . db_squote($punbb_password) . ", " . db_squote($values['email']) . ", " . db_squote(extra_get_param('auth_punbb', 'reg_lang')) . ", " . db_squote(extra_get_param('auth_punbb', 'reg_style')) . ", unix_timestamp(now()), '$ip')");
			$punbb_userid = $this->auth_db->record('select LAST_INSERT_ID() as id');
			$mysql->query("INSERT INTO " . uprefix . "_users (name, pass, mail, status, reg, last, punbb_userid) VALUES (" . db_squote($values['login']) . ", " . db_squote(EncodePassword($newpassword)) . ", " . db_squote($values['email']) . ", '4', '" . $add_time . "', '', " . db_squote($punbb_userid['id']) . ")");
			zzMail($values['email'], $lang['letter_title'], sprintf($lang['letter_text'], home, home) . sprintf($lang['your_info'], $regusername, $newpassword), 'html');
			msg(array("text" => $lang['msgo_registered'], "info" => $lang['msgo_info2']));
		}
		if ($config['register_type'] == "2") {
			$newpassword = MakeRandomPassword();
			$actcode = MakeRandomPassword();
			$punbb_password = $this->punBB_gen_password($newpassword);
			$this->auth_db->query("insert into " . $dbprefix . "users (username, group_id, password, email, language, style, registered, registration_ip) values (" . db_squote($values['login']) . ", " . intval(extra_get_param('auth_punbb', 'initial_group_id')) . ", " . db_squote($punbb_password) . ", " . db_squote($values['email']) . ", " . db_squote(extra_get_param('auth_punbb', 'reg_lang')) . ", " . db_squote(extra_get_param('auth_punbb', 'reg_style')) . ", unix_timestamp(now()), '$ip')");
			$punbb_userid = $this->auth_db->record('select LAST_INSERT_ID() as id');
			$mysql->query("INSERT INTO " . uprefix . "_users (name, pass, mail, status, reg, last, activation, punbb_userid) VALUES (" . db_squote($values['login']) . ", " . db_squote(EncodePassword($newpassword)) . ", " . db_squote($values['email']) . ", '4', '" . $add_time . "', '', '" . $actcode . "', " . db_squote($punbb_userid['id']) . ")");
			$userid = $mysql->record('select LAST_INSERT_ID() as id');
			$actlink = ($config['mod_rewrite'] == "1") ? '<a href="' . home . '/activation/' . $userid['id'] . '/' . $actcode . '">' . home . '/activation/' . $userid['id'] . '/' . $actcode . '</a>' : '<a href="' . home . '/?action=activation&userid=' . $userid['id'] . '&code=' . $actcode . '">' . home . '/?action=activation&userid=' . $userid['id'] . '&code=' . $actcode . '</a>';
			zzMail($values['email'], $lang['letter_title'], sprintf($lang['letter_text'], home, home) . sprintf($lang['your_info'], $values['login'], $newpassword) . sprintf($lang['activate'], $actlink), 'html');
			msg(array("text" => $lang['msgo_registered'], "info" => $lang['msgo_info3']));
		}
		if ($config['register_type'] == "3") {
			$punbb_password = $this->punBB_gen_password($values['password']);
			$this->auth_db->query("insert into " . $dbprefix . "users (username, group_id, password, email, language, style, registered, registration_ip) values (" . db_squote($values['login']) . ", " . intval(extra_get_param('auth_punbb', 'initial_group_id')) . ", " . db_squote($punbb_password) . ", " . db_squote($values['email']) . ", " . db_squote(extra_get_param('auth_punbb', 'reg_lang')) . ", " . db_squote(extra_get_param('auth_punbb', 'reg_style')) . ", unix_timestamp(now()), '$ip')");
			$punbb_userid = $this->auth_db->record('select LAST_INSERT_ID() as id');
			$mysql->query("INSERT INTO " . uprefix . "_users (name, pass, mail, status, reg, last, punbb_userid) VALUES (" . db_squote($values['login']) . ", " . db_squote(EncodePassword($values['password'])) . ", " . db_squote($values['email']) . ", '4', '" . $add_time . "', '', " . db_squote($punbb_userid['id']) . ")");
			zzMail($values['email'], $lang['letter_title'], sprintf($lang['letter_text'], home, home) . sprintf($lang['your_info'], $values['login'], $values['password']), 'html');
			msg(array("text" => $lang['msgo_registered']));
		}

		return 1;
	}

	//
	// Return a list of params required for password recovery
	function get_restorepw_params() {

		global $config, $lang;
		$params = array();
		LoadPluginLang('auth_punbb', 'auth', '', 'auth');
		// Password recovery is restricted. Recovery should be used via punBB
		array_push($params, array('text' => $lang['auth_norestore']));

		return $params;
	}

	//
	// Restore password (restricted, no action)
	function restorepw(&$params, $values, &$msg) {

		global $config, $mysql, $lang, $tpl;

		return 0;
	}

	//
	// Confirm of password recovery
	function confirm_restorepw(&$msg) {

		global $config, $mysql, $lang, $tpl;
		LoadPluginLang('auth_basic', 'auth', '', 'auth');
		$msg = $lang['auth_newpw_fail'];

		return 0;
	}

	//
	// Save user profile. Function is called on "Update profile" in NG.
	function save_profile($userid, $values) {

		global $mysql;
		// Check if we need to change a profile of currently logged in user of profile of anyone else
		if ($userid == $userROW['id']) {
			$urow = $userROW;
		} else {
			// Anyone else. Let's fetch a row from our DB
			if (!($urow = $mysql->record("select * from " . uprefix . "_users where id = " . db_squote($userid)))) {
				// Return if fetch attempt failed
				return 0;
			}
		}
		// Save password if new one is given
		if ($urow['punbb_userid'] && $values['password']) {
			$dbprefix = extra_get_param('auth_punbb', 'dbprefix') ? extra_get_param('auth_punbb', 'dbprefix') : '';
			$punbb_password = $this->punBB_gen_password($values['password']);
			$sql = "update " . $dbprefix . "users set password=" . db_squote($punbb_password) . " where id = " . db_squote($urow['punbb_userid']);
			$this->auth_db->query($sql);
			// Now let's update auth cookie
			$punbb_cookie = md5(extra_get_param('auth_punbb', 'cookie_seed') . $punbb_password);
			@setcookie('punbb_cookie', serialize(array($urow['punbb_userid'], $punbb_cookie)), ($config['remember'] ? (time() + 3600 * 24 * 365) : 0), '/', extra_get_param('auth_punbb', 'cookie_domain'));
		}

		return 1;
	}
}
