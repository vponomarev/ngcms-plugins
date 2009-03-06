<?php

if (!defined('NGCMS')) die ('HAL');

//
// Configuring our module
//
global $AUTH_METHOD;
global $AUTH_CAPABILITIES;
global $config;

$AUTH_METHOD['vb']	= new auth_vb;
$AUTH_CAPABILITIES['vb'] = array('login' => '1', 'db' => '1');

class auth_vb {
	var $error = 0;
	var $setremember = 16;
	var $vb_row = array();

	// Constructor
	function auth_vb() {
		global $mysql;

		// We need additional connection to DB server
		if (extra_get_param('auth_vb','extdb')) {
			$this->auth_db = new mysql;
			$this->auth_db->connect(extra_get_param('auth_vb','dbhost'), extra_get_param('auth_vb','dblogin'), extra_get_param('auth_vb','dbpass'), extra_get_param('auth_vb','dbname'), 1);
			if ($this->auth_db->error) {
				print "<br />Can't connect to SQL DB<br />\n";
				$this->error = $this->auth_db->error;
			}
		} else {
			$this->auth_db = $mysql;
		}
	}

	// Login function
	// $username	= User's login
	// $password	= User's password
	// $auto_scan	= If '1', function will search parameters in POST params, else - from function params
	function login($auto_scan = 1, $username = '', $password = '') {
		global $mysql;

		if ($this->error) { return ''; }

		if ($auto_scan) {
			$username = $_REQUEST['username'];
			$password = $_REQUEST['password'];
		}

		$dbprefix = extra_get_param('auth_vb', 'dbprefix')?extra_get_param('auth_vb', 'dbprefix'):'';

		// Check if user with such params exists
		$sql_vb = "select * from ".$dbprefix."user where username=".db_squote($username);

		// Try to fetch record from vBulletin DB
		$vb_row = $this->auth_db->record($sql_vb);

		// NO LOGIN FOUND
		if (!$vb_row) {
			return '';
		} else {
			// Check for password. Return false if password mismatch
			if ($vb_row['password'] != md5(md5($password).$vb_row['salt']))
				return '';
		}

		// Row was found. Now we meed to synchronise it with our own DB
		// 1. Try to fetch linked account data from our own DB
		if ($row = $mysql->record("select * from ".uprefix."_users where vb_userid = ".db_squote($vb_row['userid']))) {
			// Record fetched. Save punBB row and return row from our DB
			$this->vb_row = $vb_row;
			return $row;
		}

		// Row was not found. Let's block DB
		$mysql->query("lock table ".uprefix."_users write");

		// Check again (for simultaneous connections)
		if ($row = $mysql->record("select * from ".uprefix."_users where vb_userid = ".db_squote($vb_row['userid']))) {
			// Record found. Return it
			$mysql->query("unlock tables");
			$this->vb_row = $vb_row;
			return $row;
		}
		// No record. Check for DUPs
		if ($row = $mysql->record("select * from ".prefix."_users where lower(name) = lower(".db_squote($vb_row['username']).")")) {
			// DUP. Unlock table
			$mysql->query("unlock tables");

			// If passwords are equal (and record linking is allowed) - let's link
			if (extra_get_param('auth_vb', 'userjoin') && ($row['pass'] == md5(md5($password)))) {
				$mysql->query("update ".prefix."_users set vb_userid=".db_squote($vb_row['userid'])." where id=".db_squote($row['id']));
				$row['vb_userid'] = $vb_row['userid'];
				$this->vb_row = $vb_row;
				return $row;
			}
			return '';
		}

		// Exit if no NG autocreate is allowed
		if (!extra_get_param('auth_vb', 'autocreate_ng')) {
			return '';
		}

		// We don't have a record. Let's create one
		$query = "insert into ".uprefix."_users (name, pass, last, reg, ip, punbb_userid) values (".db_squote($pun_row['username']).", md5(md5(".db_squote($password).")), unix_timestamp(now()), unix_timestamp(now()),'', ".$vb_row['userid'].")";
		$mysql->query($query);
		$mysql->query("unlock tables");

		// Now let's fetch new row
		if ($row = $mysql->record("select * from ".uprefix."_users where punbb_userid = ".db_squote($vb_row['userid']))) {
			// Record found. Ok.
			$this->vb_row = $vb_row;
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
		global $ip, $config, $mysql;

		$dbprefix = extra_get_param('auth_vb', 'dbprefix')?extra_get_param('auth_vb', 'dbprefix'):'';

		// Exit if no flag 'punbb_userid' is given (i.e. no save)
		if (!$dbrow['vb_userid']) { return 0; }

		// Fetch data from vBulletin DB if we don't have data in cache
		if ($dbrow['vb_userid'] != $this->vb_row['userid']) {
			$this->vb_row = $this->auth_db->record("select * from ".$dbprefix."user where userid = ".db_squote($dbrow['vb_userid']));
		}

		// Exit if fetch attempt from cache is failed
		if (!$this->vb_row) { return 0; }

		// =====================================================
		// Generate uniq params for session
		$session_idhash = md5($_SERVER['HTTP_USER_AGENT'] . $this->fetch_substr_ip($this->fetch_alt_ip()));
		$newsessionhash = md5(time().$session_idhash.$ip.rand(1,100000));

		$params = array ( 'sessionhash' => db_squote($newsessionhash), 'userid' => db_squote($dbrow['vb_userid']), 'host' => db_squote($ip), 'idhash' => db_squote($session_idhash), 'lastactivity' => 'unix_timestamp(now())', 'languageid' => db_squote($this->vb_row['languageid']));
		$this->auth_db->query("insert into ".$dbprefix."session (".implode(", ",array_keys($params)).") values (".implode(", ",$params).")");

		// =====================================================
		// Create cookies

	        // Create random cookie (for NG)
	        $auth_cookie = md5(uniqid(rand(),1));

		$query = "update ".uprefix."_users set last = ".db_squote(time()).", ip=".db_squote($ip).", authcookie = ".db_squote($auth_cookie)." where id=".db_squote($dbrow['id']);
		$mysql->query($query);

		// Set cookie for user
		@setcookie('zz_auth', $auth_cookie, ($config['remember']?(time() + 3600 * 24 * 365):0), '/');
		@setcookie('bbsessionhash', $newsessionhash, 0, '/', extra_get_param('auth_vb','cookie_domain')?extra_get_param('auth_vb','cookie_domain'):'');

		// Set "remember" cookies if this mode is requested
		if ($this->setremember && is_array($this->remember_cookies))
			foreach ($this->remember_cookies as $c => $cv)
				@setcookie($c, $cv, 0, '/', extra_get_param('auth_vb','cookie_domain')?extra_get_param('auth_vb','cookie_domain'):'');
		return 1;
	}

	// ==================================================== //
	// Functions from core of vBulletin                     //
	// ==================================================== //
	function fetch_alt_ip()
	{
		$alt_ip = $_SERVER['REMOTE_ADDR'];
		if (isset($_SERVER['HTTP_CLIENT_IP']))
		{ $alt_ip = $_SERVER['HTTP_CLIENT_IP'];
		} else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
			// make sure we dont pick up an internal IP defined by RFC1918
			foreach ($matches[0] AS $ip) {
				if (!preg_match('#^(10|172\.16|192\.168)\.#', $ip)) {
					$alt_ip = $ip;
					break;
				}
			}
		} else if (isset($_SERVER['HTTP_FROM'])) {
			$alt_ip = $_SERVER['HTTP_FROM'];
		}
		return $alt_ip;
	}

	function fetch_substr_ip($ip, $length = null)
	{
		if ($length === null OR $length > 3) {
			$length = extra_get_param('auth_vb','ipcheck');
		}
		return implode('.', array_slice(explode('.', $ip), 0, 4 - $length));
	}

	//
	// Check if user is authorized via vBulletin db
	function check_auth() {
	 	global $ip, $config, $mysql;

	 	// *** 1. Check for un-expired user session ***
		// Calculate session idhash (it should never be changed within session)
		$session_idhash = md5($_SERVER['HTTP_USER_AGENT'] . $this->fetch_substr_ip($this->fetch_alt_ip()));

		$dbprefix = extra_get_param('auth_vb', 'dbprefix')?extra_get_param('auth_vb', 'dbprefix'):'';
		// Get session hash
		$sessionhash = $_COOKIE['bbsessionhash'];
		$gotsession = false;
		$gotuserid = 0;
		if ($sessionhash) {
			$query = "select * from ".$dbprefix."session where sessionhash=".db_squote($sessionhash)." and lastactivity > unix_timestamp(now()) - ".intval(extra_get_param('auth_vb','cookietimeout'))." and idhash = ".db_squote($session_idhash);
			if (($session = $this->auth_db->record($query)) &&
			    ($this->fetch_substr_ip($session['host']) == $this->fetch_substr_ip($ip))) {
				// Correct session is found, update last activity !!
				$this->auth_db->query("update ".$dbprefix."session set lastactivity=unix_timestamp(now()) where sessionhash = ".db_squote($sessionhash));

				$gotsession = true;
				$gotuserid = $session['userid'];
			}
		}

		// [in case of "REMEMBER ME" flag is set]
		// Check if we have userid/password params. If so - check user against this params
		$cookie_userid   = intval($_COOKIE['bbuserid']);
		$cookie_password = $_COOKIE['bbpassword'];

		if (!$gotuserid && $cookie_userid && $cookie_password) {
			$query = "select userid from ".$dbprefix."user where userid=".$cookie_userid." and md5(password)=".db_squote($cookie_password);
		        if ($userrec = $this->auth_db->record($query)) {
		        	$gotuserid = $userrec['userid'];
		        }
		}

		// Save session if we have not (or have expired) one
		if ($gotuserid && !$gotsession) {
		        // Delete expired session [ if have ]
			if (!empty($sessionhash))
				$this->auth_db->query("delete from ".$dbprefix."session where sessionhash=".db_squote($sessionhash));

			$newsessionhash = md5(time().$session_idhash.$ip.rand(1,100000));
			$params = array ( 'sessionhash' => db_squote($newsessionhash), 'userid' => db_squote($cookie_userid), 'host' => db_squote($ip), 'idhash' => db_squote($session_idhash), 'lastactivity' => 'unix_timestamp(now())', 'languageid' => db_squote($userrec['languageid']));
			$this->auth_db->query("insert into ".$dbprefix."session (".implode(", ",array_keys($params)).") values (".implode(", ",$params).")");

		}

		// If we fetched user info - fetch our linked record
		if ($gotuserid && ($urow = $mysql->record("select * from ".uprefix."_users where vb_userid = ".db_squote($gotuserid)))) {
			return $urow;
		}

		return '';
	}

	//
	// Drop auth
	function drop_auth() {
	 	global $config, $mysql;

		foreach (array('bbsessionhash', 'bbuserid', 'bbpassword', 'zz_auth') as $c)
			if (isset($_COOKIE[$c]))
				@setcookie($c, '', time() - 3600 * 24 * 365, '/', extra_get_param('auth_vb','cookie_domain')?extra_get_param('auth_vb','cookie_domain'):'');
	 	return;
	}

	//
	// Return a list of required for registration params
	function get_reg_params() {
		global $config, $lang;
		$params = array();

		// No reg params
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

		// Registration is not supported
		return 0;
	}

	//
	// Return a list of params required for password recovery
	function get_restorepw_params() {
		global $config, $lang;
		$params = array();

		LoadPluginLang('auth_vb', 'auth','','auth');

		// Password recovery is restricted. Recovery should be used via vBulletin
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

		LoadPluginLang('auth_basic', 'auth','','auth');
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
	 		if (!($urow = $mysql->record("select * from ".uprefix."_users where id = ".db_squote($userid)))) {
	 			// Return if fetch attempt failed
	 			return 0;
	 		}
		}

		// Save password if new one is given
		if ($urow['vb_userid'] && $values['password']) {
			$dbprefix = extra_get_param('auth_vb', 'dbprefix')?extra_get_param('auth_vb', 'dbprefix'):'';

			// Fetch user row
			if ($this->vb_row = $this->auth_db->record("select * from ".$dbprefix."user where userid=".db_squote($urow['vb_userid']))) {
				$vb_password = md5(md5($values['password']).$this->vb_row['salt']);
				$sql = "update ".$dbprefix."user set password=".db_squote($vb_password)." where userid = ".db_squote($urow['vb_userid']);
				$this->auth_db->query($sql);
			}
		}
		return 1;
	}
}
