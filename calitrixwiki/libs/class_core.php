<?PHP
/*
 * CalitrixWiki (c) Copyright 2004 by Johannes Klose
 * E-Mail: exe@calitrix.de
 * Project page: http://developer.berlios.de/projects/calitrixwiki
 * 
 * CalitrixWiki is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * CalitrixWiki is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CalitrixWiki; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **/

/**
 * Core class; provides common functions.
 *
 * @author Johannes Klose <exe@calitrix.de>
 * @since 1.0 Beta 1 25.05.04 17:07
 **/
class core
{
	 var $get        = array();
	 var $post       = array();
	 var $cookie     = array();
	 var $request    = array();
	 var $server     = array();
	 var $files      = array();
	 var $cfg        = array();
	 var $lang       = array();
	 var $page       = array();
	 var $pageInfo   = array();
	 var $pageTitle  = '';
	 var $pageAction = '';
	 var $time       = 0;
	 var $sessions   = array();
	 var $session    = array();
	 var $sid        = '';
	 var $loggedIn   = false;
	 var $user       = array();
	 var $theme      = null;
	 var $langCode   = null;
	 var $accessMask = 0;
	 
	/**
	 * Class constructor; sets up basic class variables
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  array $pageInfo Informations (title, namespace etc.) about the current page.
	 * @param  array $cfg      Configuration values
	 * @return void
	 **/
	function core($pageInfo, &$cfg)
	{
		/**
		 * Setup the basic variables first.
		 **/
		$db  = &singleton('database');
		
		if(get_magic_quotes_gpc()) {
			$this->prepareGPCData($_GET);
			$this->prepareGPCData($_POST);
			$this->prepareGPCData($_COOKIE);
		}
		
		$this->get     = &$_GET;
		$this->post    = &$_POST;
		$this->cookie  = &$_COOKIE;
		$this->request = &$_SERVER['REQUEST_METHOD'];
		$this->server  = &$_SERVER;
		$this->files   = &$_FILES;
		$this->cfg     = $cfg;
		$this->time    = time();
		
		/**
		 * Load basic data ..
		 **/
		$result   = $db->query('SELECT * FROM '.DB_PREFIX.'sessions');
		
		while($row = $db->fetch($result))
		{
			$this->sessions[$row['session_id']] = $row;
		}
		
		/**
		 * Start the session management.
		 **/
		$this->loadSession();
		
		/**
		 * Now load the page data.
		 **/
		if($pageInfo['page'] === false) {
			$this->messageEnd('wiki_invalid_url');
		}
		
		if($pageInfo['namespace'] == $this->cfg['special_namespace']) {
			$this->page = $this->getPage($pageInfo['page'], false, $pageInfo['namespace']);
		} else {
			$this->page = $this->getPage($pageInfo['page'], true, $pageInfo['namespace']);
		}
		
		$this->pageInfo = $pageInfo;
	}
	
	/**
	 * Does some actions which has to be done on every request, 
	 * like deletion of old sessions or updating the current session.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function end()
	{
		$db = &singleton('database');
		$sessionsRemove = array();
		$updateUsers    = array();
		
		foreach($this->sessions as $val)
		{
			$deathline = $this->time - $this->cfg['session_lifetime'];
			
			if($val['session_last_action'] < $deathline) {
				if($val['session_user_id'] > 0) {
					$db->query('UPDATE '.DB_PREFIX.'users '.
					'SET user_last_visit = '.$val['session_last_action'].' '.
					'WHERE user_id = '.$val['session_user_id']);
					$updateUsers[] = $val['session_user_id'];
				}
				
				$sessionsRemove[] = $val['session_id'];
			}
		}
		
		if($this->loggedIn) {
			$db->query('UPDATE '.DB_PREFIX.'sessions '.
			'SET session_last_action = '.$this->time.' '.
			'WHERE session_id = \''.$this->session['session_id'].'\'');
		}
		
		if(count($sessionsRemove) > 0) {
			$db->query('DELETE FROM '.DB_PREFIX.'sessions '.
			'WHERE session_id IN(\''.join('\', \'', $sessionsRemove).'\')');
		}
		
		if(count($updateUsers) > 0) {
			$result = $db->query('SELECT subs_user_id FROM '.DB_PREFIX.'subscriptions '.
			'WHERE subs_user_id IN('.join(', ', $updateUsers).') AND subs_sent = "1"');
			
			if($db->numRows($result) > 0) {
				$updateUsers = array();
				
				while($row = $db->fetch($result))
				{
					$updateUsers[] = $row['subs_user_id'];
				}
				
				$db->query('UPDATE '.DB_PREFIX.'subscriptions '.
				'SET subs_sent = "0" '.
				'WHERE subs_user_id IN('.join(', ', $updateUsers).')');
			}
		}
	}
	
	/**
	 * Extracts the current user session from the sessions array
	 * or creates a new one if none exist.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function loadSession()
	{
		$db = &singleton('database');
		
		$sessionCookie  = $this->cfg['cookie_prefix'].'session';
		$autoCookieUser = $this->cfg['cookie_prefix'].'autologin_user';
		$autoCookiePass = $this->cfg['cookie_prefix'].'autologin_pass';
		
		if(isset($this->get['s']) && isset($this->sessions[$this->get['s']])) {
			$this->session  = $this->sessions[$this->get['s']];
			$this->sid      = 's='.$this->get['s'];
			$this->loggedIn = true;
		} elseif(isset($this->cookie[$sessionCookie]) && isset($this->sessions[$this->cookie[$sessionCookie]])) {
			$this->session  = $this->sessions[$this->cookie[$sessionCookie]];
			$this->loggedIn = true;		
			$this->setSessionCookie($this->session['session_id']);
		} elseif(isset($this->cookie[$autoCookieUser]) && isset($this->cookie[$autoCookiePass])) {
			$userId   = intval($this->cookie[$autoCookieUser]);
			$password = $this->cookie[$autoCookiePass];
			
			$result = $db->query('SELECT user_id, user_name, user_use_cookies FROM '.DB_PREFIX.'users '.
			'WHERE user_id = '.$userId.' AND user_password = \''.addslashes($password).'\'');
			
			if($db->numRows($result) == 1) {
				$row = $db->fetch($result);
				
				if($row['user_use_cookies'] == 1) {
					$useCookies = true;
				} else {
					$useCookies = false;
				}
				
				$this->removeSession($userId);
				$this->createSession($row['user_id'], $row['user_name'], $useCookies, true, $password);
				$this->loggedIn = true;
			}
		}
		
		$this->setUserConfig();
	}
	
	/**
	 * Loads the data of a user from the database
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $userId = ''    Id or name of the user
	 * @param  bool   $isName = false Indicates wether $userId is a id or name
	 * @return array                  User data
	 **/
	function getUser($userId = 0, $isName = false)
	{
		$db = &singleton('database');
		
		if($isName && $userId != '') {
			$user = $db->queryRow('SELECT u.*, g.* FROM '.DB_PREFIX.'users u '.
			'LEFT JOIN '.DB_PREFIX.'groups g ON g.group_id = u.user_group_id '.
			'WHERE u.user_name = "'.addslashes($userId).'"');
		} elseif(!$isName && $userId > 0) {
			$user = $db->queryRow('SELECT u.*, g.* FROM '.DB_PREFIX.'users u '.
			'LEFT JOIN '.DB_PREFIX.'groups g ON g.group_id = u.user_group_id '.
			'WHERE u.user_id = '.((int)$userId));
		} else {
			$user = $db->queryRow('SELECT * FROM '.DB_PREFIX.'groups '.
			'WHERE group_id = '.$this->cfg['default_guest_group']);
		}
		
		return $user;
	}
	
	/**
	 * Updates the default config with the current users config settings.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function setUserConfig()
	{
		if($this->loggedIn) {
			$this->user = $this->getUser($this->session['session_user_id']);
			
			if($this->user['user_language'] == '' || !isset($this->cfg['languages'][$this->user['user_language']])) {
			$this->langCode = $this->cfg['default_lang'];
			} else {
				$this->langCode = $this->user['user_language'];
			}
			
			$this->lang = $this->getLang($this->langCode);
			
			if($this->user['user_theme'] == '' || !isset($this->cfg['themes'][$this->user['user_theme']])) {
				$this->theme = $this->cfg['default_theme'];
			} else {
				$this->theme = $this->user['user_theme'];
			}
			
			if($this->user['user_items_pp'] > 0) {
				$this->cfg['items_per_page'] = $this->user['user_items_pp'];
			}
			
			$this->cfg['dblclick_editing'] = $this->user['user_dblclick_editing'];
			
			if($this->user['user_access_mask'] >= 0) {
				$this->accessMask = (int)$this->user['user_access_mask'];
			} else {
				$this->accessMask = (int)$this->user['group_access_mask'];
			}
		} else {
			$this->theme      = $this->cfg['default_theme'];
			$this->langCode   = $this->cfg['default_lang'];
			$this->user       = $this->getUser();
			$this->lang       = $this->getLang($this->langCode);
			$this->accessMask = (int)$this->user['group_access_mask'];
		}
	}
	
	/**
	 * Creates a new session and saves it to the database.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int    $userId            User id owning the session.
	 * @param  string $userName          User owning the session.
	 * @param  bool   $autoLogin = false Set a auto-login cookie?
	 * @param  string $password = ''     Auto-login password
	 * @return array                     Session information
	 **/
	function createSession($userId, $userName, $useCookies, $autoLogin = false, $password = '')
	{
		$db = &singleton('database');
		$sessionId = $this->genRandomString(20);
		
		$db->query('INSERT INTO '.DB_PREFIX.'sessions(session_id, '.
		'session_user_id, session_last_action, session_user_name) '.
		'VALUES(\''.$sessionId.'\', '.$userId.', '.$this->time.', '.
		'\''.addslashes($userName).'\')');
		
		$this->session = $db->queryRow('SELECT * FROM '.DB_PREFIX.'sessions '.
		'WHERE session_id = \''.$sessionId.'\'');
		
		if($useCookies) {
			$this->setSessionCookie($sessionId);
		} else {
			$this->sid = 's='.$sessionId;
		}
		
		if($autoLogin) {
			$this->setAutoLoginCookie($userId, $password);
		}
	}
	
	/**
	 * Removes the current session from the database and session array.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int $userId User id of the session(s) which should be removed.
	 * @return void
	 **/
	function removeSession($userId)
	{
		$db = &singleton('database');
		$db->query('DELETE FROM '.DB_PREFIX.'sessions '.
		'WHERE session_user_id = \''.$userId.'\'');
		$this->loggedIn   = false;
		$this->user       = $this->getUser();
		$this->accessMask = intval($this->user['group_access_mask']);
		$this->session    = array();
		$this->sid        = '';
		
		$this->removeSessionCookie();
		$this->removeAutoLoginCookie();
	}
	
	/**
	 * Sets a auto-login cookie.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int    $userId User id
	 * @param  string $pwHash Password (sha1) hash
	 * @return void
	 **/
	function setAutoLoginCookie($userId, $pwHash)
	{
		$cookieName1   = $this->cfg['cookie_prefix'].'autologin_user';
		$cookieName2   = $this->cfg['cookie_prefix'].'autologin_pass';
		$cookieVal1    = $userId;
		$cookieVal2    = $pwHash;
		$cookieExpire  = $this->time + (365 * 24 * 60 *60);
		$cookiePath    = $this->cfg['cookie_path'];
		$cookieDomain  = $this->cfg['cookie_domain'];
		$cookieSecure  = $this->cfg['cookie_secure'];
	
		setcookie($cookieName1, $cookieVal1, $cookieExpire);
		setcookie($cookieName2, $cookieVal2, $cookieExpire);
	}
	
	/**
	 * Removes a auto-login cookie.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function removeAutoLoginCookie()
	{
		$cookieName1  = $this->cfg['cookie_prefix'].'autologin_user';
		$cookieName2  = $this->cfg['cookie_prefix'].'autologin_pass';
		$cookieExpire = $this->time - (365 * 24 * 60 *60);
		
		if(isset($this->cookie[$cookieName1]) && isset($this->cookie[$cookieName2])) {
			setcookie($cookieName1, $this->cookie[$cookieName1], $cookieExpire);
			setcookie($cookieName2, $this->cookie[$cookieName2], $cookieExpire);
		}
	}
	
	/**
	 * Sets a session cookie.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $sessionId Session id
	 * @return void
	 **/
	function setSessionCookie($sessionId)
	{
		$cookieName   = $this->cfg['cookie_prefix'].'session';
		$cookieVal    = $sessionId;
		$cookieExpire = $this->time + $this->cfg['session_lifetime'];
		$cookiePath   = $this->cfg['cookie_path'];
		$cookieDomain = $this->cfg['cookie_domain'];
		$cookieSecure = $this->cfg['cookie_secure'];
		
		setcookie($cookieName, $cookieVal, $cookieExpire);
	}
	
	/**
	 * Removes a session cookie.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function removeSessionCookie()
	{
		$cookieName   = $this->cfg['cookie_prefix'].'session';
		$cookiePath   = $this->cfg['cookie_path'];
		$cookieDomain = $this->cfg['cookie_domain'];
		$cookieSecure = $this->cfg['cookie_secure'];
		$cookieExpire = $this->time - $this->cfg['session_lifetime'];
		
		if(isset($this->cookie[$cookieName])) {
			setcookie($cookieName, $this->cookie[$cookieName], $cookieExpire);
		}
	}
	
	/**
	 * Includes a language file and returns the language variables includet.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $file Language file name
	 * @return array        Language variables
	 **/
	function getLang($file)
	{
		$fileName  = $this->cfg['lang_dir'].'/'.$file.'.php';
		$lang      = array();
		
		if(file_exists($fileName)) {
			include $fileName;
			return array_merge($this->lang, $lang);
		} else {
			trigger_error('Unable to load language file \''.$file.'\'', E_USER_ERROR);
		}
	}
	
	/**
	 * This function creates a older version of the current page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $version Version number of the old text
	 * @return array           Text, version and date of the old text
	 **/
	function getOldPageText($version)
	{
		$tpl    = &singleton('template');
		
		$versions = diff::getVersions();
		
		if(!isset($versions[$version])) {
			return false;
		}
		
		$oldText = diff::createVersion($this->page['page_text'], $versions, $version);
		
		return array('text' => $oldText, 'version' => $version, 'time' => $versions[$version]['log_time']);
	}
	
	/**
	 * Generates a url.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $pageName           Name of the target wiki page
	 * @param  string $action = ''        Page action
	 * @param  array  $params = array()   Additional url parameters
	 * @param  bool   $xhtmlCompat = true Sets wether urls shall be xhtml compatible
	 * @return string                     Url
	 **/
	function genUrl($pageName, $action = '', $params = array(), $xhtmlCompat = true)
	{
		if($action == '') {
			$url = sprintf($this->cfg['url_format_short'], $pageName);
		} else {
			$url = sprintf($this->cfg['url_format'], $pageName, $action);
		}
		
		if(isset($params['hide_session'])) {
			$hide_session = true;
			unset($params['hide_session']);
		} else {
			$hide_session = false;
		}
		
		if($xhtmlCompat) {
			$seperator = '&amp;';
		} else {
			$seperator = '&';
		}
		
		if(count($params) > 0) {
			$url     .= strpos($url, '?') !== false ? '&' : '?';
			$nparams  = array();
			
			foreach($params as $key => $val)
			{
				$nparams[] = $key.'='.$val;
			}
			
			$url .= join($seperator, $nparams);
		}
		
		if($this->loggedIn && $this->sid != '' && !$hide_session) {
			if(strpos($url, '?') !== false) {
				$url .= $seperator.$this->sid;
			} else {
				$url .= '?'.$this->sid;
			}
		}
		
		return $url;
	}
	
	/**
	 * Loads a page from the sql database
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $pageName          Page name
	 * @param  string $loadFromDb = true Shall we load the page from the database or return an new page?
	 * @param  string  $namespace = ''   Page namespace
	 * @return array                     Page data on success, false otherwise
	 **/
	function getPage($pageName, $loadFromDb = true, $namespace = '')
	{
		if($namespace == '') {
			$namespace = $this->cfg['default_namespace'];
		}
		
		$page = array(
		              'page_id'          => 0,
		              'page_namespace'   => $namespace,
		              'page_name'        => $pageName,
		              'page_text'        => $this->lang['create_page'],
		              'page_cache'       => '',
		              'page_version'     => '0.0.0',
		              'page_last_change' => 0
		);
		
		if($loadFromDb) {
			$db = &singleton('database');
			$result = $db->query('SELECT p.*, pt.*, m.perm_access_mask '.
			'FROM '.DB_PREFIX.'pages p LEFT JOIN '.DB_PREFIX.'page_texts pt ON pt.page_id = p.page_id '.
			'LEFT JOIN '.DB_PREFIX.'local_masks m ON m.perm_page_id = pt.page_id AND m.perm_group_id = '.$this->user['group_id'].' '.
			'WHERE p.page_namespace = \''.addslashes($namespace).'\' AND p.page_name = \''.addslashes($pageName).'\'');
			
			if($db->numRows($result) != 1) {
				return $page;
			}
			
			$row = $db->fetch($result);
			
			if($row['perm_access_mask'] == null) {
				$localMask = -1;
			} else {
				$localMask = (int)$row['perm_access_mask'];
			}
			
			if($localMask >= 0 && !$this->hasPerms(PERM_IGNORELOCAL)) {
				$this->accessMask = $this->mergePerms($this->accessMask, $localMask);
			}
			
			return $row;
		} else {
			return $page;
		}
	}
	
	/**
	 * Checks if the visitor has one or more permissions.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int  $perms           Permission bits
	 * @param  int  $accessMask = -1 Use this access mask instead of the normal one
	 * @return bool                  True if the permissions are given, false otherwise
	 **/
	function hasPerms($perms, $accessMask = -1)
	{
		$perms = (int)$perms;
		
		
		if($accessMask >= 0) {
			if(($perms & $accessMask) == $perms) {
				return true;
			} else {
				return false;
			}
		} else {
			if(($perms & $this->accessMask) == $perms) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Disables permissions in $global which arent set in $local
	 * and activates permissions which arent set in $global but in $local.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int $global Global bit-mask
	 * @param  int $local  Local bit-mask
	 * @return int         Merged bit-mask
	 **/
	function mergePerms($global, $local)
	{
		$newMask = $global | $local;
		
		$newMask -= $this->hasPerms(PERM_VIEW)        && !$this->hasPerms(PERM_VIEW,        $local) ? PERM_VIEW        : 0;
		$newMask -= $this->hasPerms(PERM_EDIT)        && !$this->hasPerms(PERM_EDIT,        $local) ? PERM_EDIT        : 0;
		$newMask -= $this->hasPerms(PERM_HISTORY)     && !$this->hasPerms(PERM_HISTORY,     $local) ? PERM_HISTORY     : 0;
		$newMask -= $this->hasPerms(PERM_RESTORE)     && !$this->hasPerms(PERM_RESTORE,     $local) ? PERM_RESTORE     : 0;
		$newMask -= $this->hasPerms(PERM_RENAME)      && !$this->hasPerms(PERM_RENAME,      $local) ? PERM_RENAME      : 0;
		$newMask -= $this->hasPerms(PERM_DELETE)      && !$this->hasPerms(PERM_DELETE,      $local) ? PERM_DELETE      : 0;
		$newMask -= $this->hasPerms(PERM_IGNORELOCAL) && !$this->hasPerms(PERM_IGNORELOCAL, $local) ? PERM_IGNORELOCAL : 0;
		$newMask -= $this->hasPerms(PERM_SETLOCAL)    && !$this->hasPerms(PERM_SETLOCAL,    $local) ? PERM_SETLOCAL    : 0;
		
		return $newMask;
	}
	
	/**
	 * Returnes the shortest name which identifies a page uniquely.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  array &$pageData Array containing at least namespace and page name
	 * @return string           Unique page name
	 **/
	function getUniqueName(&$pageData)
	{
		if($pageData['page_namespace'] == $this->cfg['default_namespace']) {
			return $pageData['page_name'];
		} else {
			return $pageData['page_namespace'].':'.$pageData['page_name'];
		}
	}
	
	/**
	 * Formats a unix timestamp into a human readable date string
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int    $timestamp Unix timestamp
	 * @return string            Date string
	 **/
	function convertTime($timestamp)
	{
		return date($this->cfg['date_format'], $timestamp);
	}
	
	/**
	 * Assigns the default template variables.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function assignTplVars()
	{
		$tpl = &singleton('template') ;
		$tpl->assign('lang',          $this->lang);
		$tpl->assign('cfg',           $this->cfg);
		$tpl->assign('urlRoot',       $this->cfg['url_root']);
		$tpl->assign('pageId',        $this->page['page_id']);
		$tpl->assign('lastModified',  $this->convertTime($this->page['page_last_change']));
		$tpl->assign('pageVersion',   $this->page['page_version']);
		$tpl->assign('wikiTitle',     $this->cfg['wiki_title']);
		$tpl->assign('loggedIn',      $this->loggedIn);
		$tpl->assign('user',          $this->user);
		$tpl->assign('pageName',      $this->getUniqueName($this->page));
		$tpl->assign('pageNameOnly',  $this->page['page_name']);
		$tpl->assign('pageNamespace', $this->page['page_namespace']);
		$tpl->assign('pageAction',    $this->pageInfo['action']);
		$tpl->assign('actionTitle',   $this->pageAction);
		$tpl->assign('wikiVersion',   $this->cfg['wiki_version']);
		
		if($this->pageTitle == '') {
			$tpl->assign('pageTitle', $this->getUniqueName($this->page));
		} else {
			$tpl->assign('pageTitle', $this->pageTitle);
		}
		
		if($this->loggedIn && $this->user['user_use_cookies'] == 0) {
			$tpl->assign('sessionId', $this->session['session_id']);
		} else {
			$tpl->assign('sessionId', '');
		}
		
		$tpl->assign('canEdit',     $this->hasPerms(PERM_EDIT));
		$tpl->assign('canHistory',  $this->hasPerms(PERM_HISTORY));
		$tpl->assign('canSetLocal', $this->hasPerms(PERM_SETLOCAL));
		$tpl->assign('canUseAcp',   $this->hasPerms(PERM_USEACP));
		$tpl->assign('canDelete',   $this->hasPerms(PERM_DELETE));
		$tpl->assign('canRename',   $this->hasPerms(PERM_RENAME));
	}
	
	/**
	 * Generates the navigation for multiple pages.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param int $row_count
	 * @param int $rows_per_page
	 * @param string $url
	 * @return array navigation
	 **/
	function makePages($row_count, $rows_per_page, $url)
	{      
		$num_pages = ceil(($row_count == 0 ? 1 : $row_count) / $rows_per_page);
		$page      = isset($this->get['p']) && is_numeric($this->get['p']) ? $this->get['p'] : 1;
		$page      = $page > $num_pages ? 1 : $page;
		$page      = $page < 1 ? 1 : $page;
		$num_end   = $page >= $num_pages - 3 ? $num_pages : $page + 3;
		$num_start = $page - 3 <= 0 ? 1 : $page - 3;
		$pagelinks = array();
        
		for($i = $num_start; $i < $num_end+1; $i++)
		{
			if($i == $page)  {
				$pagelinks[] = '<span class="pageCurrent">['.$i.']</span>&nbsp;';
			} else {
				$pagelinks[] = '<a href="'.sprintf($url, $i).'" class="pageLink">'.$i.'</a>&nbsp;';
			}
		}
		    
        $page--;  
        $limit_start = $page * $rows_per_page;
        $limit_end   = $rows_per_page;
        
        return array($pagelinks, $limit_start, $limit_end, $num_pages, ++$page);
	}
	
	/**
	 * Ends the script execution with an error message.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param string $msgId = '' Name of the language phrase of the error message
	 **/
	function messageEnd($msgId)
	{
		$tpl = &singleton('template');
		$tpl->assign('lang',         $this->lang);
		$tpl->assign('cfg',          $this->cfg);
		$tpl->assign('urlRoot',      $this->cfg['url_root']);
		$tpl->assign('wikiTitle',    $this->cfg['wiki_title']);
		$tpl->assign('loggedIn',     $this->loggedIn);
		$tpl->assign('user',         $this->user);
		
		if($this->loggedIn && $this->user['user_use_cookies'] == 0) {
			$tpl->assign('sessionId', $this->session['session_id']);
		} else {
			$tpl->assign('sessionId', '');
		}
		
		$tpl->assign('message', $this->lang[$msgId]);
		
		die($tpl->fetch('message.tpl'));
	}
	
	/**
	 * Executes a HTTP redirect
	 *
	 * @param string $URL Redirect url
	 * @return void
	 **/
	function HTTPRedirect($URL)
	{
		if(!headers_sent()) {
			header('Location: '.$URL);
		}
		exit;
	}
	
	/**
	 * Formats a filesize from bytes to an human readable
	 * string and adds the byte units (kilo, mega, giga, ...).
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int    $bytes         Number of bytes to be formated
	 * @param  int    $precision = 2 Number of digits after the decimal points
	 * @return string                String with filesize and unit
	 **/
	function HRFileSize($bytes, $precision = 2)
	{
		if($bytes < 1024) {
			return $bytes.' Bytes';
		}
		
		$bytes = round($bytes / 1024, $precision);
		
		if($bytes < 1024) {
			return $bytes.' KB';
		}
		
		$bytes = round($bytes / 1024, $precision);
		
		if($bytes < 1024) {
			return $bytes.' MB';
		}
		
		$bytes = round($bytes / 1024, $precision);
		
		if($bytes < 1024) {
			return $bytes.' GB';
		}
		
		$bytes = round($bytes / 1024, $precision);
		
		if($bytes < 1024) {
			return $bytes.' TB';
		}
	}
	
	/**
	 * Generates a string with random characters.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int $length Length of the random string
	 * @return string      Random string
	 **/
	function genRandomString($length)
	{
		$chars  = array("a","b","c","d","e","f","g","h","i","j",
		"k","l","m","n","o","p","q","r","s","t","u","v","w","x",
		"y","z","0","1","2","3","4","5","6","7","8","9");
		$string = "";

		for($i = 0; $i < $length; $i++)
		{
			mt_srand((double)microtime()*1000000);
			$randNum = floor(mt_rand(0,count($chars)-1));
			$string  .= $chars[$randNum];
		}

		return $string;
	}
	
	/**
	 * Strips backshlashes from GPC variables by walking recursively
	 * throug the given arrays.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param array &$var The GPC array which will be processed
	 * @return void
	 **/
	function prepareGPCData(&$var)
	{
		if(is_array($var)) {
			while(list($key, $val) = each($var))
			{
				$var[$key] = $this->prepareGPCData($val);
			}
		} else {
			$var = stripslashes($var);
		}
		
		return $var;
	}
	
	/**
	 * Outputs an array as a preformated html string
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param array $array A PHP array
	 * @return void
	 **/
	function outputArray($array)
	{
		echo '<pre>';
		print_r($array);
		echo '</pre>';
	}
}
?>