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
class special_admingroups extends admin
{
	var $cfgTemplate = 'admin_groups.tpl';
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
		$tpl->assign('isError',   false);
		$tpl->assign('isMessage', false);
		
		$this->loadGroups();
		
		if(isset($this->get['op'])) {
			$op = $this->get['op'];
			
			switch($op)
			{
				case 'del':  $this->opRemoveGroup(); break;
				case 'edit': $this->opEditGroup();   break;
				case 'add':  $this->opAddGroup();    break;
			}
		}
		
		$tpl->assign('cfgGroups', $this->cfgGroups);
	}
	
	/**
	 * Loads all user groups.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function loadGroups()
	{
		$tpl = &singleton('template');
		$db  = &singleton('database');
		
		$result = $db->query('SELECT * FROM '.DB_PREFIX.'groups ORDER BY group_id');
		
		while($row = $db->fetch($result))
		{
			$row['group_name'] = htmlentities($row['group_name']);
			$this->cfgGroups[$row['group_id']] = $row;
		}
	}
	
	/**
	 * Lets the admin edit a group and it's permissions.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opEditGroup()
	{
		if(!isset($this->get['gid'])) {
			return false;
		}
		
		$groupId = intval($this->get['gid']);
		
		if(!isset($this->cfgGroups[$groupId])) {
			return false;
		}
		
		if($this->request == 'POST') {
			$this->saveGroup($groupId);
		} else {
			$this->displayGroup($groupId);
		}
	}
	
	/**
	 * Adds a new user group.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opAddGroup()
	{
		if($this->request == 'POST') {
			$this->saveGroup();
		} else {
			
			$tpl = &singleton('template');
			$tpl->assign('cfgOp',           'add');
			$tpl->assign('cfgGroupId',      0);
			$tpl->assign('cfgGroupName',    '');
			$tpl->assign('cfgPermView',     false);
			$tpl->assign('cfgPermEdit',     false);
			$tpl->assign('cfgPermHistory',  false);
			$tpl->assign('cfgPermRestore',  false);
			$tpl->assign('cfgPermRename',   false);
			$tpl->assign('cfgPermDelete',   false);
			$tpl->assign('cfgPermIgLocal',  false);
			$tpl->assign('cfgPermSetLocal', false);
			$tpl->assign('cfgPermUseAcp',   false);
			
			$this->cfgTemplate = 'admin_groups_edit.tpl';
		}
	}
	
	/**
	 * Displays the data of a group for editing.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int $groupId Id of the group which shall be edited
	 * @return void
	 **/
	function displayGroup($groupId)
	{
		$accessMask = (int)$this->cfgGroups[$groupId]['group_access_mask'];
		
		$tpl = &singleton('template');
		$tpl->assign('cfgOp',           'edit');
		$tpl->assign('cfgGroupId',      $this->cfgGroups[$groupId]['group_id']);
		$tpl->assign('cfgGroupName',    $this->cfgGroups[$groupId]['group_name']);
		$tpl->assign('cfgPermView',     $this->hasPerms(PERM_VIEW,        $accessMask));
		$tpl->assign('cfgPermEdit',     $this->hasPerms(PERM_EDIT,        $accessMask));
		$tpl->assign('cfgPermHistory',  $this->hasPerms(PERM_HISTORY,     $accessMask));
		$tpl->assign('cfgPermRestore',  $this->hasPerms(PERM_RESTORE,     $accessMask));
		$tpl->assign('cfgPermRename',   $this->hasPerms(PERM_RENAME,      $accessMask));
		$tpl->assign('cfgPermDelete',   $this->hasPerms(PERM_DELETE,      $accessMask));
		$tpl->assign('cfgPermIgLocal',  $this->hasPerms(PERM_IGNORELOCAL, $accessMask));
		$tpl->assign('cfgPermSetLocal', $this->hasPerms(PERM_SETLOCAL,    $accessMask));
		$tpl->assign('cfgPermUseAcp',   $this->hasPerms(PERM_USEACP,      $accessMask));
		
		$this->cfgTemplate = 'admin_groups_edit.tpl';
	}
	
	/**
	 * Saves the group data to the database.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int $groupId = 0 Id of the group which shall be saved
	 * @return void
	 **/
	function saveGroup($groupId = 0)
	{
		$db  = &singleton('database');
		$tpl = &singleton('template');
		
		$permView     = isset($this->post['perm_view'])      ? PERM_VIEW        : 0;
		$permEdit     = isset($this->post['perm_edit'])      ? PERM_EDIT        : 0;
		$permHistory  = isset($this->post['perm_history'])   ? PERM_HISTORY     : 0;
		$permRestore  = isset($this->post['perm_restore'])   ? PERM_RESTORE     : 0;
		$permRename   = isset($this->post['perm_rename'])    ? PERM_RENAME      : 0;
		$permDelete   = isset($this->post['perm_delete'])    ? PERM_DELETE      : 0;
		$permIglocal  = isset($this->post['perm_iglocal'])   ? PERM_IGNORELOCAL : 0;
		$permSetlocal = isset($this->post['perm_setlocal'])  ? PERM_SETLOCAL    : 0;
		$permUseacp   = isset($this->post['perm_useacp'])    ? PERM_USEACP      : 0;
		
		$newMask = $permView | $permEdit | $permHistory | $permRestore | $permRename | $permDelete | $permIglocal | $permSetlocal | $permUseacp;
		
		if($groupId > 0) {
			$groupName = isset($this->post['group_name']) && trim($this->post['group_name']) != '' ?
		                 trim($this->post['group_name']) : $this->cfgGroups[$groupId]['group_name'];
		} else {
			$groupName = isset($this->post['group_name']) ? trim($this->post['group_name']) : '';
			
			if($groupName == '') {
				$tpl->assign('isError', true);
				$tpl->assign('errors',  $this->lang['admin_no_group_name']);
				$tpl->assign('cfgGroupId',      0);
				$tpl->assign('cfgGroupName',    '');
				$tpl->assign('cfgPermView',     $this->hasPerms(PERM_VIEW,        $newMask));
				$tpl->assign('cfgPermEdit',     $this->hasPerms(PERM_EDIT,        $newMask));
				$tpl->assign('cfgPermHistory',  $this->hasPerms(PERM_HISTORY,     $newMask));
				$tpl->assign('cfgPermRestore',  $this->hasPerms(PERM_RESTORE,     $newMask));
				$tpl->assign('cfgPermRename',   $this->hasPerms(PERM_RENAME,      $newMask));
				$tpl->assign('cfgPermDelete',   $this->hasPerms(PERM_DELETE,      $newMask));
				$tpl->assign('cfgPermIgLocal',  $this->hasPerms(PERM_IGNORELOCAL, $newMask));
				$tpl->assign('cfgPermSetLocal', $this->hasPerms(PERM_SETLOCAL,    $newMask));
				$tpl->assign('cfgPermUseAcp',   $this->hasPerms(PERM_USEACP,      $newMask));
			}
		}
		
		if($groupId > 0) {
			$db->query('UPDATE '.DB_PREFIX.'groups '.
			'SET group_name = "'.addslashes($groupName).'", '.
			'group_access_mask = '.$newMask.' '.
			'WHERE group_id = '.$groupId);
			
			$tpl->assign('isMessage', true);
			$tpl->assign('message',   $this->lang['admin_group_updated']);
			
			$this->cfgGroups[$groupId]['group_name']        = $groupName;
			$this->cfgGroups[$groupId]['group_access_mask'] = $newMask;
		} else {
			$db->query('INSERT INTO '.DB_PREFIX.'groups(group_name, group_access_mask) '.
			'VALUES("'.addslashes($groupName).'", '.$newMask.')');
			
			$tpl->assign('isMessage', true);
			$tpl->assign('message',   $this->lang['admin_group_added']);
			
			$this->cfgGroups[$groupId]                      = array();
			$this->cfgGroups[$groupId]['group_id']          = $db->insertId();
			$this->cfgGroups[$groupId]['group_name']        = $groupName;
			$this->cfgGroups[$groupId]['group_access_mask'] = $newMask;
		}
	}
	
	/**
	 * Removes a user group from the database.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opRemoveGroup()
	{
		$tpl = &singleton('template');
		$db  = &singleton('database');
		
		if(!isset($this->get['gid'])) {
			return false;
		}
		
		$groupId = intval($this->get['gid']);
		
		if(!isset($this->cfgGroups[$groupId])) {
			return false;
		}
		
		if($this->request == 'POST') {
			$this->removeGroup($groupId);
			$tpl->assign('isMessage', true);
			$tpl->assign('message',   $this->lang['admin_group_updated']);
		} else {
			$result   = $db->queryRow('SELECT COUNT(user_id) as count '.
			'FROM '.DB_PREFIX.'users WHERE user_group_id = '.$groupId);
			
			if($result['count'] > 0) {
				$tpl->assign('cfgGroupId', $groupId);
				$this->cfgTemplate = 'admin_groups_confirm.tpl';
			} else {
				$this->removeGroup($groupId);
				$tpl->assign('isMessage', true);
				$tpl->assign('message',   $this->lang['admin_group_updated']);
			}
		}
	}
	
	/**
	 * Removes a user group.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int $groupId Id of the group which shall be removed
	 * @return void
	 **/
	function removeGroup($groupId)
	{
		$db = &singleton('database');
		$do = isset($this->post['do']) ? $this->post['do'] : '';
		
		if($do == 'del') {
			$db->query('DELETE FROM '.DB_PREFIX.'users '.
			'WHERE user_group_id = '.$groupId);
		} elseif($do == 'move' && isset($this->post['target_group'])) {
			$target = intval($this->post['target_group']);
			
			if(!isset($this->cfgGroups[$target])) {
				return false;
			}
			
			$db->query('UPDATE '.DB_PREFIX.'users '.
			'SET user_group_id = '.$target.' '.
			'WHERE user_group_id = '.$groupId);
		}
		
		$db->query('DELETE FROM '.DB_PREFIX.'groups WHERE group_id = '.$groupId);
		$db->query('DELETE FROM '.DB_PREFIX.'local_masks WHERE perm_group_id = '.$groupId);
		unset($this->cfgGroups[$groupId]);
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