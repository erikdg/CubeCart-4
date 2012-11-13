<?php
/*
+--------------------------------------------------------------------------
|   CubeCart 4
|   ========================================
|	CubeCart is a registered trade mark of Devellion Limited
|   Copyright Devellion Limited 2010. All rights reserved.
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Email: sales@devellion.com
|	License Type: CubeCart is NOT Open Source. Unauthorized reproduction is not allowed.
|   Licence Info: http://www.cubecart.com/v4-software-license
+--------------------------------------------------------------------------
|	cc_session.php
|   ========================================
|	Front Session Class
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");

class session {

	var $ccUserData;
	var $ccUserBlocked = false;

	var $config;
	var $db;
	var $glob;
	var $ini;

	function session() {
	#	$this->__construct();
	#}

	#function __construct() {
		global $config, $db, $glob, $ini;

		$this->config	= $config;
		$this->db		= $db;
		$this->glob		= $glob;
		$this->ini		= $ini;

		if (isset($_GET[CC_SESSION_NAME])) {
			$this->set_cc_cookie(CC_SESSION_NAME, $_GET[CC_SESSION_NAME], $this->config['sqlSessionExpiry']);
		} else {
			$results = false;
			## see if session is still in db
			if(!empty($GLOBALS[CC_SESSION_NAME])) {
				$query = sprintf('SELECT `sessId` FROM %sCubeCart_sessions WHERE `sessId` = %s', $this->glob['dbprefix'], $this->db->mySQLSafe($GLOBALS[CC_SESSION_NAME]));
				$results = $this->db->select($query);
			}

			## !empty($results[0]['sessId']) critical in case results=true if session DB table has an empty sessionId!!
			if ($results && !empty($results[0]['sessId'])) {
				$data['timeLast'] = $this->db->mySQLSafe(time());
				$data['location'] = $this->db->mySQLSafe(currentPage());
				$update = $this->db->update($this->glob['dbprefix'].'CubeCart_sessions', $data, '`sessId` = '.$this->db->mySQLSafe($results[0]['sessId']));
			} else {
				$this->makeSession();
			}
		}

		## get all session data and store as class array
		$query = sprintf("SELECT * FROM %1\$sCubeCart_sessions LEFT JOIN %1\$sCubeCart_customer ON %1\$sCubeCart_sessions.customer_id = %1\$sCubeCart_customer.customer_id WHERE `sessId` = %2\$s", $this->glob['dbprefix'], $this->db->mySQLSafe($GLOBALS[CC_SESSION_NAME]));
		$result = $this->db->select($query);
		// security checks

		/*
		$client_ip = get_ip_address();
		if (strpos($_SERVER['HTTP_USER_AGENT'],'AOL') == false && !empty($result[0]['ip']) && ($result[0]['ip'] !== $client_ip || $result[0]['browser'] !== $_SERVER['HTTP_USER_AGENT'])) {
			$this->destroySession($GLOBALS[CC_SESSION_NAME]);
		}
		*/
		$this->ccUserData = $result[0];
		if (empty($this->ccUserData['email']) && isset($_COOKIE['username']) && isset($_COOKIE['password'])) {
			$this->authenticate($_COOKIE['username'], $_COOKIE['password'], true, true);
		}

		if (empty($result[0]['lang'])) {
			define('LANG_FOLDER', $this->config['defaultLang']);
		} else {
			define('LANG_FOLDER', $result[0]['lang']);
		}

		if (empty($result[0]['skin'])) {
			define('SKIN_FOLDER', $this->config['skinDir']);
		} else  {
			define('SKIN_FOLDER', $result[0]['skin']);
		}
	}

	function destroySession($sessionId) {

		## removed to keep basket data
		// $this->set_cc_cookie(CC_SESSION_NAME, '', time()-3600);
		$this->set_cc_cookie('username', '', time()-3600);
		$this->set_cc_cookie('password', '', time()-3600);

		$data['customer_id'] = '0';
		$update = $this->db->update($this->glob['dbprefix'].'CubeCart_sessions', $data,'`sessId` = '.$this->db->mySQLSafe($GLOBALS[CC_SESSION_NAME]));
		return ($update) ? true : false;
	}

	function makeSession() {
		$sessionId = $this->makeSessId();
		$this->set_cc_cookie(CC_SESSION_NAME, $sessionId, $this->config['sqlSessionExpiry']);

		## set session global var because cookie won't show until next page load
		$GLOBALS[CC_SESSION_NAME] = $sessionId;

		## insert sessionId into db
		$data['sessId'] 		= 	$this->db->mySQLSafe($sessionId);
		$timeNow 				= 	$this->db->mySQLSafe(time());
		$data['timeStart'] 		= 	$timeNow;
		$data['timeLast'] 		= 	$timeNow;
		$data['customer_id'] 	= 	0;
		$data['ip'] 			= 	$this->db->mySQLSafe(get_ip_address());
		$data['browser'] 		= 	$this->db->mySQLSafe($_SERVER['HTTP_USER_AGENT']);

		$insert = $this->db->insert($this->glob['dbprefix'].'CubeCart_sessions', $data);
		$this->deleteOldSessions();
	}

	function deleteOldSessions() {
		$expiredSessTime = time() - $this->config['sqlSessionExpiry'];
		## delete sessions older than time set in config file
		$delete = $this->db->delete($this->glob['dbprefix'].'CubeCart_sessions', '`timeLast` < '.$expiredSessTime);
	}

	function createSalt($user,$pass,$remember) {
		$salt = randomPass(6);
		$pass_hash = md5(md5($salt).md5($pass));
		$this->db->update($this->glob['dbprefix'].'CubeCart_customer', array('password' => $this->db->mySQLSafe($pass_hash),'salt' => $this->db->mySQLSafe($salt)),'`email` = '.$this->db->mySQLSafe($user));
		$this->authenticate($user,$pass,$remember);
	}

	function authenticate($user, $pass, $remember = false, $cookie_login = false) {
		global $glob, $config;
		if ($cookie_login) {
			$user		= sanitizeVar($_COOKIE['username']);
			$passMD5	= sanitizeVar($_COOKIE['password']);
		} else {
			$user		= sanitizeVar($user);
			$passMD5	= md5(sanitizeVar($pass));
		}

		$query = 'SELECT `customer_id`, `salt` FROM '.$this->glob['dbprefix'].'CubeCart_customer WHERE `type`>0 AND `email`='.$this->db->mySQLSafe($user);
		$salt = $this->db->select($query);

		if($salt[0]['customer_id']>0 && empty($salt[0]['salt']) && $cookie_login == false) {
			$query = 'SELECT `customer_id` FROM '.$this->glob['dbprefix'].'CubeCart_customer WHERE `email` = '.$this->db->mySQLSafe($user).' AND `password` = '.$this->db->mySQLSafe($passMD5).' AND `type` > 0';
			if(($customer = $this->db->select($query)) !== false) {
				$this->createSalt($user,$pass,$remember);
			} else {
				return false;
			}
		} else {
			$passMD5 = md5(md5($salt[0]['salt']).md5($pass));
			$query = 'SELECT `customer_id` FROM '.$this->glob['dbprefix'].'CubeCart_customer WHERE `email` = '.$this->db->mySQLSafe($user).' AND `password` = '.$this->db->mySQLSafe($passMD5).' AND `type` > 0';
			$customer = $this->db->select($query);
		}

		if (!$customer) {
			if ($this->db->blocker($user, $this->ini['bfattempts'], $this->ini['bftime'], false, 'f')) {
				$this->ccUserBlocked = true;
			}
		} else if ($customer[0]['customer_id']>0) {

			// remember user for as long as sessions are allowed in DB
			if ($remember) {
				$this->set_cc_cookie('username', $user, $this->config['sqlSessionExpiry']);
				$this->set_cc_cookie('password', $passMD5, $this->config['sqlSessionExpiry']);
				$this->set_cc_cookie(CC_SESSION_NAME, $GLOBALS[CC_SESSION_NAME], $this->config['sqlSessionExpiry']);
			}

			if ($this->db->blocker($user, $this->ini['bfattempts'], $this->ini['bftime'], true, 'f')) {
				$this->ccUserBlocked = true;
			} else {
				$data['customer_id'] 	= $customer[0]['customer_id'];
				$data['ip'] 			= $this->db->mySQLSafe(get_ip_address());
				$data['browser'] 		= $this->db->mySQLSafe($_SERVER['HTTP_USER_AGENT']);
				$update = $this->db->update($this->glob['dbprefix'].'CubeCart_sessions', $data,'sessId='.$this->db->mySQLSafe($GLOBALS[CC_SESSION_NAME]));

				## Make sure customer is type 1 & not ghost type 2 (if it is first login from express checkout welcome email)
				$update = $this->db->update($this->glob['dbprefix'].'CubeCart_customer', array('type'=>1),'customer_id='.$customer[0]['customer_id']);

				## 'login','reg','unsubscribe','forgotPass' etc..
				$redir = sanitizeVar(urldecode($_GET['redir']));

				## prevent phishing attacks
				if (preg_match('/^http(s?):\/\//i', $redir) && !preg_match('/^'.$glob['storeURL'].'|^'.$config['storeURL_SSL'].'/i', $redir)) {
					httpredir($GLOBALS['rootRel'].'index.php');
				}
				if (isset($_GET['redir']) && !empty($_GET['redir']) && !preg_match('/logout|login|forgotPass|changePass/i', $redir)) {
					httpredir($redir);
				} else {
					httpredir($GLOBALS['rootRel'].'index.php');
				}
			}
		} else if (stripos(urldecode($_GET['redir']), 'step1') !== false) {
			httpredir($GLOBALS['rootRel'].'index.php?_g=co&_a=step1');
		}
	}


	function makeSessId() {
		session_start();
		session_regenerate_id(true);
		return session_id();
	}

	/* defunct
	function get_cookie_domain($domain) {
		$cookie_domain = str_replace(array('http://', 'https://', 'www.'), '', strtolower($domain));
		$cookie_domain = explode("/", $cookie_domain);
		$cookie_domain = explode(":", $cookie_domain[0]);
		return '.'.$cookie_domain[0];
	}
	*/

	function set_cc_cookie($name, $value, $length = 0) {
		## only set the cookie if the visitor is not a spider or search engine system is off
		if (!$this->user_is_search_engine() || !$this->config['sef']) {
			$expires = ($length>0) ? (time()+$length) : 0;
			$urlParts = parse_url($GLOBALS['storeURL']);
			$domain = (empty($urlParts['host']) || !strpos($urlParts['host'], '.')) ? false : str_replace('www.','.',$urlParts['host']);

			setcookie($name, $value, $expires, $GLOBALS['rootRel'], $domain, false, true); // http only cookies for security
		}
	}

	function user_is_search_engine() {
		//Speed this puppy up alot
		$user_agent		= trim(strtolower(strtolower($_SERVER['HTTP_USER_AGENT'])));

		if ((!empty($user_agent)) && ($user_agent != 'null')) {
			$spiders	= file(CC_ROOT_DIR.CC_DS.'spiders.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			$test = implode('|', $spiders);
			if (strpos($test, '/') !== false) {
				$test = str_replace('/', '\/', $test);
			}
			$test = '/('.$test.')/i';

			return (bool)preg_match($test, $user_agent);
		}
		return false;
	}
}

?>
