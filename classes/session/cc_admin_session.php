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
|	cc_admin_session.php
|   ========================================
|	Admin Authentication and Permissions
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");

class admin_session {

	var $config;
	var $db;
	var $glob;
	var $ini;

	function admin_session() {
		$this->__construct();
	}

	function __construct() {
		global $config, $db, $glob, $ini;

		$this->config	= $config;
		$this->db		= &$db;
		$this->glob		= $glob;
		$this->ini		= $ini;

		define('LANG_FOLDER', $this->config['defaultLang']);
	}

	function get_session_data() {
		$session_id = (isset($GLOBALS[CC_ADMIN_SESSION_NAME])) ? trim($GLOBALS[CC_ADMIN_SESSION_NAME]) : false;
		if (empty($GLOBALS[CC_ADMIN_SESSION_NAME])) {
			## If no session redirect to login screen
			httpredir($GLOBALS['rootRel'].$this->glob['adminFile'].'?_g=login&goto='.urlencode(currentPage()));
		} else {
			## Get session information as array
			$query = sprintf('SELECT * FROM '.$this->glob['dbprefix'].'CubeCart_admin_users WHERE `sessId` = %s', $this->db->mySQLSafe($session_id));
			$ccAdminData = $this->db->select($query);
			if(!$ccAdminData) {
				$this->logout();
			}

			## Security checks
			$client_ip	= get_ip_address();
			/*
			if (strpos($_SERVER['HTTP_USER_AGENT'],'AOL') == false && $ccAdminData[0]['sessIp'] !== $client_ip || empty($_SERVER['HTTP_USER_AGENT']) || $ccAdminData[0]['browser'] !== $_SERVER['HTTP_USER_AGENT']) {
				$this->logout();
			}
			*/
			## Find permissions for those who are not super users
			if (!(bool)$ccAdminData[0]['isSuper']) {
				$query = sprintf("SELECT %1\$sCubeCart_admin_sections.sectId, `name`, `read`, `write`, `edit`, `delete` FROM %1\$sCubeCart_admin_sections LEFT JOIN %1\$sCubeCart_admin_permissions ON %1\$sCubeCart_admin_sections.sectId = %1\$sCubeCart_admin_permissions.sectId WHERE `adminId` = %2\$s", $this->glob['dbprefix'], $this->db->mySQLSafe($ccAdminData[0]['adminId']));
				$permissionArray = $this->db->select($query);

				if (is_array($permissionArray)) {
					for ($i = 0, $maxi = count($permissionArray); $i < $maxi; ++$i) {
						foreach ($permissionArray[$i] as $key => $value) {
							$masterKey = $permissionArray[$i]['name'];
							$ccAdminData[0][$masterKey][$key] = $value;
						}
					}
				}
			}
			return $ccAdminData[0];
		}
	}

	function makeSessId() {
		session_start();
		session_regenerate_id(true);
		return session_id();
	}

	function logout() {
		## reset session data
		$record['sessId']	= 'NULL';
		$record['sessIp']	= 'NULL';
		$record['browser']	= 'NULL';

		$this->db->update($this->glob['dbprefix'].'CubeCart_admin_users', $record,'`sessId` = '.$this->db->MySQLSafe($GLOBALS[CC_ADMIN_SESSION_NAME]));

		$this->set_cc_admin_cookie(CC_ADMIN_SESSION_NAME, '');
		httpredir($GLOBALS['rootRel'].$this->glob['adminFile'].'?_g=login');
	}

	function createSalt($username,$password) {
		$salt = randomPass(6);
		$pass_hash = md5(md5($salt).md5($password));
		$this->db->update($this->glob['dbprefix'].'CubeCart_admin_users', array('password' => $this->db->mySQLSafe($pass_hash),'salt' => $this->db->mySQLSafe($salt)),'`username` = '.$this->db->mySQLSafe($username));
		$this->login($username,$password);
	}

	function login($username, $password) {
		$query = 'SELECT `adminId`, `salt` FROM '.$this->glob['dbprefix'].'CubeCart_admin_users WHERE `username`='.$this->db->mySQLSafe($username);
		$salt = $this->db->select($query);

		if($salt[0]['adminId']>0 && empty($salt[0]['salt'])) {
			$query = sprintf('SELECT `adminId` FROM %sCubeCart_admin_users WHERE `username` = %s AND `password` = %s AND `failLevel` < %s AND `blockTime` < %s', $this->glob['dbprefix'], $this->db->mySQLSafe($username), $this->db->mySQLSafe(md5($password)), $this->ini['bfattempts'], time());
			$result = $this->db->select($query);
			if($result[0]['adminId']>0) {
				$this->createSalt($username,$password);
			} else {
				return false;
			}
		} else {
			$query = sprintf('SELECT `adminId` FROM %sCubeCart_admin_users WHERE `username` = %s AND `password` = %s AND `failLevel` < %s AND `blockTime` < %s', $this->glob['dbprefix'], $this->db->mySQLSafe($username), $this->db->mySQLSafe(md5(md5($salt[0]['salt']).md5($password))), $this->ini['bfattempts'], time());
			$result = $this->db->select($query);
		}

		return $result;
	}

	function createSession($admin_id) {
		$sessionId = $this->makeSessId();
		$this->set_cc_admin_cookie(CC_ADMIN_SESSION_NAME, $sessionId);

		## set session global var because cookie won't show until next page load
		$GLOBALS[CC_ADMIN_SESSION_NAME] = $sessionId;

		$record['sessId'] = "'".$sessionId."'";
		## log browser & ip for security purposes no session hijacking here :)
		$record['sessIp'] = $this->db->MySQLSafe(get_ip_address());
		$record['browser'] = $this->db->MySQLSafe($_SERVER['HTTP_USER_AGENT']);
		$this->db->update($this->glob['dbprefix'].'CubeCart_admin_users', $record, '`adminId` = '.$admin_id);
	}

	/* defunct
	function get_cookie_domain($domain) {
		$cookie_domain = str_replace(array('http://', 'https://', 'www.'), '', strtolower($domain));
		$cookie_domain = explode("/",$cookie_domain);
		$cookie_domain = explode(":", $cookie_domain[0]);
		return '.'.$cookie_domain[0];
	}
	*/

	function set_cc_admin_cookie($name, $value) {
		$expires = 0; ## remember session until browser is closed
		$urlParts = parse_url($GLOBALS['storeURL']);
		$domain = (empty($urlParts['host']) || !strpos($urlParts['host'], '.')) ? false : str_replace('www.','.',$urlParts['host']);
		setcookie($name, $value, $expires, $GLOBALS['rootRel'], $domain);
	}
}
?>