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
 
include $cfg['lib_dir'].'/class_admin.php';

/**
 * This is the admin specialpage for editing user groups.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_adminusers extends admin
{
	var $cfgTemplate = 'admin_users.tpl';
	var $cfgUsers    = array();
	var $cfgGroups   = array();
	
	/**
	 * Start function
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function start()
	{
		$tpl = &singleton('template');
		$tpl->assign('isMessage', false);
		
		$this->loadUsers();
		
		if(isset($this->get['op'])) {
			$op = $this->get['op'];
			
			switch($op)
			{
				case 'del':  $this->opRemoveUser(); break;
				case 'edit': $this->opEditUser();   break;
				case 'add':  $this->opAddUser();    break;
			}
		}
		
		$tpl->assign('cfgUsers',  $this->cfgUsers);
		$tpl->assign('cfgGroups', $this->cfgGroups);
	}
	
	/**
	 * Lets the admin edit a user.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opEditUser()
	{
		$tpl = &singleton('template');
		$tpl->assign('isError', false);
		
		if(!isset($this->get['uid'])) {
			return false;
		}
		
		$userId = intval($this->get['uid']);
		
		if(!isset($this->cfgUsers[$userId])) {
			return false;
		}
		
		if($this->request == 'POST') {
			$this->saveUserData($userId);
			$this->loadUsers();
		} else {
			$this->displayUserData($userId);
		}
	}
	
	/**
	 * Lets the admin add a user.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opAddUser()
	{
		$tpl = &singleton('template');
		
		$tpl->assign('cfgOp',           'add');
		$tpl->assign('permUse',         'group');
		$tpl->assign('isError',         false);
		$tpl->assign('cfgUserId',       0);
		$tpl->assign('cfgGroupId',      $this->cfg['default_user_group']);
		$tpl->assign('cfgUserName',     '');
		$tpl->assign('cfgUserEmail',    '');
		$tpl->assign('cfgUseCookies',   '');
		$tpl->assign('cfgUserLang',     '');
		$tpl->assign('cfgUserTheme',    '');
		$tpl->assign('cfgItemsPP',      0);
		$tpl->assign('cfgDblClick',     '');
		$tpl->assign('cfgEnableMails',  '');
		$tpl->assign('cfgPermView',     false);
		$tpl->assign('cfgPermEdit',     false);
		$tpl->assign('cfgPermHistory',  false);
		$tpl->assign('cfgPermRestore',  false);
		$tpl->assign('cfgPermRename',   false);
		$tpl->assign('cfgPermDelete',   false);
		$tpl->assign('cfgPermIgLocal',  false);
		$tpl->assign('cfgPermSetLocal', false);
		$tpl->assign('cfgPermUseAcp',   false);
		
		$config = $this->getOrigConfig();
		
		$tpl->assign('cfgLanguages', $config['languages']);
		$tpl->assign('cfgThemes',    $config['themes']);
		$tpl->assign('cfgPPSelect',  $config['items_pp_select']);
		
		if($this->request == 'POST') {
			$this->saveUserData(0);
			$this->loadUsers();
		} else {
			$this->cfgTemplate = 'admin_users_edit.tpl';
		}
	}
	
	/**
	 * Deletes a user from the database.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opRemoveUser()
	{
		if(!isset($this->get['uid'])) {
			return false;
		}
		
		$userId = $this->get['uid'];
		
		if(!isset($this->cfgUsers[$userId])) {
			return false;
		}
		
		$db  = &singleton('database');
		$tpl = &singleton('template');
		$tpl->assign('isError',   false);
		$tpl->assign('cfgUserId', $userId);
		
		if(!isset($this->get['conf'])) {
			$tpl->assign('cfgNewName', htmlentities($this->cfgUsers[$userId]['user_name']));
			$this->cfgTemplate = 'admin_users_confirm.tpl';
		} else {
			$userName = isset($this->post['new_name']) ? trim($this->post['new_name']) : '';
			$isError  = false;
			$errors   = array();
			
			if($userName != '' && $userName != $this->cfgUsers[$userId]['user_name']) {
				if(strlen($userName) < $this->cfg['min_username_length']) {
					$errors[] = sprintf($this->lang['admin_user_short_name'], $this->cfg['min_username_length']);
					$isError  = true;
				} elseif(strlen($userName) > $this->cfg['max_username_length']) {
					$errors[] = sprintf($this->lang['admin_user_long_name'], $this->cfg['max_username_length']);
					$isError  = true;
				} elseif(!preg_match('/^'.$this->cfg['title_format'].'$/', $this->cfg['users_namespace'].':'.$userName)) {
					$errors[] = $this->lang['admin_user_invalid_name'];
					$isError  = true;
				} elseif(is_array($this->getUser($userName, true))) {
					$errors[] = $this->lang['admin_user_name_taken'];
					$isError  = true;
				}
			}
			
			if($isError) {
				$tpl->assign('isError',    true);
				$tpl->assign('errors',     $errors);
				$tpl->assign('cfgNewName', htmlentities($userName));
				$this->cfgTemplate = 'admin_users_confirm.tpl';
			} else {
				$db->query('UPDATE '.DB_PREFIX.'changelog SET '.
				'log_user_id = 0, '.
				'log_user_name = "'.addslashes($userName).'" '.
				'WHERE log_user_id = '.$userId);
				$db->query('DELETE FROM '.DB_PREFIX.'users '.
				'WHERE user_id = '.$userId);
				$db->query('DELETE FROM '.DB_PREFIX.'subscriptions '.
				'WHERE subs_user_id = '.$userId);
				$db->query('DELETE FROM '.DB_PREFIX.'sessions '.
				'WHERE session_user_id = '.$userId);
				$db->query('DELETE FROM '.DB_PREFIX.'bookmarks '.
				'WHERE bm_user_id = '.$userId);
				
				$tpl->assign('isMessage', true);
				$tpl->assign('message',   $this->lang['admin_user_deleted']);
				
				$this->loadUsers();
			}
		}
	}
		
	/**
	 * Displays the data of a user to be edited.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int $userId User id of the user to be displayed
	 * @return void
	 **/
	function displayUserData($userId)
	{
		$tpl = &singleton('template');
		
		$user = $this->cfgUsers[$userId];
		
		if($user['user_access_mask'] >= 0) {
			$accessMask = (int)$user['user_access_mask'];
			$tpl->assign('permUse', 'own');
		} else {
			$accessMask = (int)$this->cfgGroups[$user['user_group_id']]['group_access_mask'];
			$tpl->assign('permUse', 'group');
		}
		
		$tpl->assign('cfgOp',           'edit');
		$tpl->assign('cfgUserId',       $user['user_id']);
		$tpl->assign('cfgGroupId',      $user['user_group_id']);
		$tpl->assign('cfgUserName',     htmlentities($user['user_name']));
		$tpl->assign('cfgUserEmail',    htmlentities($user['user_email']));
		$tpl->assign('cfgUseCookies',   $user['user_use_cookies']);
		$tpl->assign('cfgUserLang',     $user['user_language']);
		$tpl->assign('cfgUserTheme',    $user['user_theme']);
		$tpl->assign('cfgItemsPP',      $user['user_items_pp']);
		$tpl->assign('cfgDblClick',     $user['user_dblclick_editing']);
		$tpl->assign('cfgEnableMails',  $user['user_enable_mails']);
		$tpl->assign('cfgPermView',     $this->hasPerms(PERM_VIEW,        $accessMask));
		$tpl->assign('cfgPermEdit',     $this->hasPerms(PERM_EDIT,        $accessMask));
		$tpl->assign('cfgPermHistory',  $this->hasPerms(PERM_HISTORY,     $accessMask));
		$tpl->assign('cfgPermRestore',  $this->hasPerms(PERM_RESTORE,     $accessMask));
		$tpl->assign('cfgPermRename',   $this->hasPerms(PERM_RENAME,      $accessMask));
		$tpl->assign('cfgPermDelete',   $this->hasPerms(PERM_DELETE,      $accessMask));
		$tpl->assign('cfgPermIgLocal',  $this->hasPerms(PERM_IGNORELOCAL, $accessMask));
		$tpl->assign('cfgPermSetLocal', $this->hasPerms(PERM_SETLOCAL,    $accessMask));
		$tpl->assign('cfgPermUseAcp',   $this->hasPerms(PERM_USEACP,      $accessMask));
		
		$config = $this->getOrigConfig();
		
		$tpl->assign('cfgLanguages', $config['languages']);
		$tpl->assign('cfgThemes',    $config['themes']);
		$tpl->assign('cfgPPSelect',  $config['items_pp_select']);
		
		$this->cfgTemplate = 'admin_users_edit.tpl';
	}
	
	/**
	 * Saves the edited data of an user.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int $userId User id of the user to be saved
	 * @return void
	 **/
	function saveUserData($userId)
	{
		$db  = &singleton('database');
		$tpl = &singleton('template');
		
		if($userId > 0) {
			$user = $this->cfgUsers[$userId];
		}
		
		if($userId > 0) {
			$userName        = isset($this->post['user_name'])        ? $this->post['user_name']        : $user['user_name'];
			$userMail        = isset($this->post['user_email'])       ? $this->post['user_email']       : $user['user_email'];
			$userGroup       = isset($this->post['user_group'])       ? $this->post['user_group']       : $user['user_group'];
			$userLang        = isset($this->post['language'])         ? $this->post['language']         : $user['user_language'];
			$userTheme       = isset($this->post['theme'])            ? $this->post['theme']            : $user['user_theme'];
			$userItemsPP     = isset($this->post['items_pp'])         ? $this->post['items_pp']         : $user['user_items_pp'];
			$userDblclick    = isset($this->post['dblclick_editing']) ? 1                               : 0;
			$userUseCookies  = isset($this->post['use_cookies'])      ? 1                               : 0;
			$userEnableMails = isset($this->post['enable_mails'])     ? 1                               : 0;
			$userPassword    = isset($this->post['password'])         ? $this->post['password']         : '';
			$userPasswordC   = isset($this->post['password_confirm']) ? $this->post['password_confirm'] : '';
		} else {
			$userName        = isset($this->post['user_name'])        ? $this->post['user_name']        : '';
			$userMail        = isset($this->post['user_email'])       ? $this->post['user_email']       : '';
			$userGroup       = isset($this->post['user_group'])       ? $this->post['user_group']       : '';
			$userLang        = isset($this->post['language'])         ? $this->post['language']         : '';
			$userTheme       = isset($this->post['theme'])            ? $this->post['theme']            : '';
			$userItemsPP     = isset($this->post['items_pp'])         ? $this->post['items_pp']         : 0;
			$userDblclick    = isset($this->post['dblclick_editing']) ? 1                               : 0;
			$userUseCookies  = isset($this->post['use_cookies'])      ? 1                               : 0;
			$userEnableMails = isset($this->post['enable_mails'])     ? 1                               : 0;
			$userPassword    = isset($this->post['password'])         ? $this->post['password']         : '';
			$userPasswordC   = isset($this->post['password_confirm']) ? $this->post['password_confirm'] : '';
		}
		
		$usePerms = isset($this->post['use_what']) ? $this->post['use_what'] : 'group';
		
		if($usePerms == 'own') {
			$permView     = isset($this->post['perm_view'])      ? PERM_VIEW        : 0;
			$permEdit     = isset($this->post['perm_edit'])      ? PERM_EDIT        : 0;
			$permHistory  = isset($this->post['perm_history'])   ? PERM_HISTORY     : 0;
			$permRestore  = isset($this->post['perm_restore'])   ? PERM_RESTORE     : 0;
			$permRename   = isset($this->post['perm_rename'])    ? PERM_RENAME      : 0;
			$permDelete   = isset($this->post['perm_delete'])    ? PERM_DELETE      : 0;
			$permIglocal  = isset($this->post['perm_iglocal'])   ? PERM_IGNORELOCAL : 0;
			$permSetlocal = isset($this->post['perm_setlocal'])  ? PERM_SETLOCAL    : 0;
			$permUseacp   = isset($this->post['perm_useacp'])    ? PERM_USEACP      : 0;
			
			$saveMask = $permView | $permEdit | $permHistory | $permRestore | $permRename | $permDelete | $permIglocal | $permSetlocal | $permUseacp;
		} else {
			$usePerms = 'group';
			$saveMask = -1;
		}
		
		$isError = false;
		$errors  = array();
		
		// Validate that the username is neither to short nor to long and
		// ensure it doesnt exist already.
		if(strlen($userName) < $this->cfg['min_username_length']) {
			$errors[] = sprintf($this->lang['admin_user_short_name'], $this->cfg['min_username_length']);
			$isError  = true;
		} elseif(strlen($userName) > $this->cfg['max_username_length']) {
			$errors[] = sprintf($this->lang['admin_user_long_name'], $this->cfg['max_username_length']);
			$isError  = true;
		} elseif(!preg_match('/^'.$this->cfg['title_format'].'$/', $this->cfg['users_namespace'].':'.$userName)) {
			$errors[] = $this->lang['admin_user_invalid_name'];
			$isError  = true;
		} elseif($userId > 0 && $userName != $user['user_name'] && is_array($this->getUser($userName))) {
			$errors[] = $this->lang['admin_user_name_taken'];
			$isError  = true;
		}
		
		if(($userId > 0 && $userPassword != '') || $userId == 0) {
			// Validate that the password is not to short and check wether the user
			// entered two matching passwords.
			if(strlen($userPassword) < $this->cfg['min_password_length']) {
				$errors[] = sprintf($this->lang['admin_user_short_password'], $this->cfg['min_password_length']);
				$isError = true;
			} elseif($userPassword != $userPasswordC) {
				$errors[] = $this->lang['admin_user_unmatching_pws'];
				$isError  = true;
			}
			
			$cPassword = sha1($userPassword);
		} else {
			$cPassword = $user['user_password'];
		}
		
		// Validate the email adress.
		if(!preg_match($this->cfg['match_email'], $userMail)) {
			$errors[] = $this->lang['admin_user_invalid_email'];
			$isError  = true;
		}
		
		// Validate other data.
		if(!isset($this->cfg['languages'][$userLang])) {
			$userLang = '';
		}
		if(!isset($this->cfgGroups[$userGroup])) {
			$userGroup = '';
		}
		if(!isset($this->cfg['themes'][$userTheme])) {
			$userTheme = '';
		}
		if(!in_array($userItemsPP, $this->cfg['items_pp_select'])) {
			$userItemsPP = 0;
		}
		
		if($isError) {
			$tpl->assign('isError', true);
			$tpl->assign('errors',  $errors);
			
			if($saveMask < 0) {
				if($userId > 0) {
					$accessMask = (int)$this->cfgGroups[$user['user_group_id']]['group_access_mask'];
				} else {
					$accessMask = (int)$this->cfgGroups[$this->cfg['default_guest_group']]['group_access_mask'];
				}
			} else {
				$accessMask = $saveMask;
			}
			
			$tpl->assign('permUse',         $usePerms);
			$tpl->assign('cfgUserId',       $userId);
			$tpl->assign('cfgGroupId',      $userGroup);
			$tpl->assign('cfgUserName',     htmlentities($userName));
			$tpl->assign('cfgUserEmail',    htmlentities($userMail));
			$tpl->assign('cfgUseCookies',   $userUseCookies);
			$tpl->assign('cfgUserLang',     $userLang);
			$tpl->assign('cfgUserTheme',    $userTheme);
			$tpl->assign('cfgItemsPP',      $userItemsPP);
			$tpl->assign('cfgDblClick',     $userDblclick);
			$tpl->assign('cfgEnableMails',  $userEnableMails);
			$tpl->assign('cfgPermView',     $this->hasPerms(PERM_VIEW,        $accessMask));
			$tpl->assign('cfgPermEdit',     $this->hasPerms(PERM_EDIT,        $accessMask));
			$tpl->assign('cfgPermHistory',  $this->hasPerms(PERM_HISTORY,     $accessMask));
			$tpl->assign('cfgPermRestore',  $this->hasPerms(PERM_RESTORE,     $accessMask));
			$tpl->assign('cfgPermRename',   $this->hasPerms(PERM_RENAME,      $accessMask));
			$tpl->assign('cfgPermDelete',   $this->hasPerms(PERM_DELETE,      $accessMask));
			$tpl->assign('cfgPermIgLocal',  $this->hasPerms(PERM_IGNORELOCAL, $accessMask));
			$tpl->assign('cfgPermSetLocal', $this->hasPerms(PERM_SETLOCAL,    $accessMask));
			$tpl->assign('cfgPermUseAcp',   $this->hasPerms(PERM_USEACP,      $accessMask));
			
			$config = $this->getOrigConfig();
			
			$tpl->assign('cfgLanguages', $config['languages']);
			$tpl->assign('cfgThemes',    $config['themes']);
			$tpl->assign('cfgItemsPP',   $config['items_pp_select']);
			
			$this->cfgTemplate = 'admin_users_edit.tpl';
		} else {
			if($userId > 0) {
				$db->query('UPDATE '.DB_PREFIX.'users SET '.
				'user_group_id = '.$userGroup.', '.
				'user_access_mask = '.$saveMask.', '.
				'user_name = "'.addslashes($userName).'", '.
				'user_password = "'.$cPassword.'", '.
				'user_email = "'.addslashes($userMail).'", '.
				'user_use_cookies = "'.$userUseCookies.'", '.
				'user_language = "'.$userLang.'", '.
				'user_theme = "'.$userTheme.'", '.
				'user_items_pp = "'.$userItemsPP.'", '.
				'user_enable_mails = "'.$userEnableMails.'", '.
				'user_dblclick_editing = "'.$userDblclick.'" '.
				'WHERE user_id = '.$userId);
			} else {
				$db->query('INSERT INTO '.DB_PREFIX.'users(user_group_id, user_access_mask, '.
				'user_name, user_password, user_email, user_reg_time, user_last_visit, '.
				'user_use_cookies, user_language, user_theme, user_items_pp, user_enable_mails, '.
				'user_dblclick_editing) VALUES('.$userGroup.', '.$saveMask.', "'.addslashes($userName).'", '.
				'"'.$cPassword.'", "'.addslashes($userMail).'", '.$this->time.', '.$this->time.', '.
				'"'.$userUseCookies.'", "'.$userLang.'", "'.$userTheme.'", "'.$userItemsPP.'", '.
				'"'.$userEnableMails.'", "'.$userDblclick.'")');
			}
			
			$tpl->assign('isMessage', true);
			$tpl->assign('message',   $this->lang['admin_user_saved']);
			
			if($userId > 0 && $userName != $user['user_name']) {
				$db->query('DELETE FROM '.DB_PREFIX.'sessions '.
				'WHERE session_user_id = \''.$userId.'\'');
			}
			
			$this->cfgTemplate = 'admin_users.tpl';
		}
	}
	
	/**
	 * Loads the current page of users.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function loadUsers()
	{
		$tpl = &singleton('template');
		$db  = &singleton('database');
		
		$result  = $db->query('SELECT COUNT(*) AS count FROM '.DB_PREFIX.'users');
		$row     = $db->fetch($result);
		$count   = $row['count'];
		$pageUrl = $this->genUrl($this->getUniqueName($this->page), '', array('p' => '%s'));
		$pages   = $this->makePages($count, $this->cfg['items_per_page'], $pageUrl);
		
		$this->lang['wiki_pages'] = sprintf($this->lang['wiki_pages'], $pages[4], $pages[3]);
		
		$tpl->assign('pageLinks', $pages[0]);
		$tpl->assign('numPages',  $pages[3]);
		$tpl->assign('thisPage',  $pages[4]);
		$tpl->assign('firstPage', sprintf($pageUrl, 1));
		$tpl->assign('lastPage',  sprintf($pageUrl, $pages[3]));
		
		$result = $db->query('SELECT * FROM '.DB_PREFIX.'users ORDER BY user_name');
		
		while($row = $db->fetch($result))
		{
			$row['user_name']       = htmlentities($row['user_name']);
			$row['user_email']      = htmlentities($row['user_email']);
			$row['user_reg_time']   = $this->convertTime($row['user_reg_time']);
			$row['user_last_visit'] = $this->convertTime($row['user_last_visit']);
			
			$this->cfgUsers[$row['user_id']] = $row;
		}
		
		// Load user groups
		$result = $db->query('SELECT * FROM '.DB_PREFIX.'groups ORDER BY group_id');
		
		while($row = $db->fetch($result))
		{
			$row['group_name'] = htmlentities($row['group_name']);
			$this->cfgGroups[$row['group_id']] = $row;
		}
	}
	
	/**
	 * Returns the template name for this special page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return $this->cfgTemplate;
	}
}
?>