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
 * This is the "permissions" action. It allows it to 
 * users which have the PERM_SETLOCAL access flag to modify
 * the access rights for this page.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class action_permissions extends core
{
	var $permGroups    = array();
	var $permsTemplate = 'action_permissions.tpl';
	
	/**
	 * Start function
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  array  &$page Page data
	 * @return void
	 **/
	function start()
	{
		$this->pageAction = $this->lang['perms'];
		
		if(!$this->hasPerms(PERM_SETLOCAL)) {
			$this->messageEnd('wiki_perm_denied');
		}
		
		$tpl = &singleton('template');
		$tpl->assign('isMessage', false);
		
		if($this->page['page_id'] == 0) {
			$this->HTTPRedirect($this->genUrl($this->getUniqueName($this->page)));
		}
		
		$this->permGroups = $this->permGetGroups();
		
		if(isset($this->get['o']) && isset($this->get['gid'])) {
			$op  = $this->get['o'];
			$gid = intval($this->get['gid']);
			
			if(isset($this->permGroups[$gid])) {
				if($op == 'change') {
					$this->editPerms($gid);
				} elseif($op == 'reset') {
					$this->resetGroupPerms($gid);
					$this->permGroups = $this->permGetGroups();
					$this->displayPerms($gid);
				}
			}
		}
		
		$tpl->assign('perms', $this->permGroups);
	}
	
	/**
	 * Edits the permissions for a group.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int $groupId Id of the group which shall be edited
	 * @return void
	 **/
	function editPerms($groupId)
	{
		if($this->request == 'POST') {
			$this->savePerms($groupId);
			$this->permGroups = $this->permGetGroups();
			$this->displayPerms($groupId);
		} else {
			$this->displayPerms($groupId);
		}
		
		$this->permsTemplate = 'action_permissions_display.tpl';
	}
	
	/**
	 * Displays the permissions of the selected group
	 * for the current page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int $groupId Id of the group which shall be displayed
	 * @return void
	 **/
	function displayPerms($groupId)
	{
		$tpl        = &singleton('template');
		$accessMask = (int)$this->permGroups[$groupId]['perm_access_mask'];
		
		// Use default access mask of the group if there is no local access mask yet
		if($accessMask == 0) {
			$accessMask = (int)$this->permGroups[$groupId]['group_access_mask'];
		}
		
		$permViewChecked    = $this->hasPerms(PERM_VIEW,        $accessMask) ? ' checked="checked"' : '';
		$permEditChecked    = $this->hasPerms(PERM_EDIT,        $accessMask) ? ' checked="checked"' : '';
		$permHistoryChecked = $this->hasPerms(PERM_HISTORY,     $accessMask) ? ' checked="checked"' : '';
		$permRestoreChecked = $this->hasPerms(PERM_RESTORE,     $accessMask) ? ' checked="checked"' : '';
		$permRenameChecked  = $this->hasPerms(PERM_RENAME,      $accessMask) ? ' checked="checked"' : '';
		$permDeleteChecked  = $this->hasPerms(PERM_DELETE,      $accessMask) ? ' checked="checked"' : '';
		
		$tpl->assign('permViewChecked',    $permViewChecked);
		$tpl->assign('permEditChecked',    $permEditChecked);
		$tpl->assign('permHistoryChecked', $permHistoryChecked);
		$tpl->assign('permRestoreChecked', $permRestoreChecked);
		$tpl->assign('permRenameChecked',  $permRenameChecked);
		$tpl->assign('permDeleteChecked',  $permDeleteChecked);
		$tpl->assign('groupId',            $groupId);
		
		$this->lang['perms_change_desc'] = sprintf($this->lang['perms_change_desc'], 
		                                           $this->permGroups[$groupId]['group_name']);
	}
	
	/**
	 * Saves a modified access mask.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int $groupId Id of the group which shall be saved
	 * @return void
	 **/
	function savePerms($groupId)
	{
		$db  = &singleton('database');
		$tpl = &singleton('template');
		
		$permView    = isset($this->post['perm_view'])    ? PERM_VIEW        : 0;
		$permEdit    = isset($this->post['perm_edit'])    ? PERM_EDIT        : 0;
		$permHistory = isset($this->post['perm_history']) ? PERM_HISTORY     : 0;
		$permRestore = isset($this->post['perm_restore']) ? PERM_RESTORE     : 0;
		$permRename  = isset($this->post['perm_rename'])  ? PERM_RENAME      : 0;
		$permDelete  = isset($this->post['perm_delete'])  ? PERM_DELETE      : 0;
		
		$newMask = $permView | $permEdit | $permHistory | $permRestore |
		           $permRename | $permDelete;
		
		$result = $db->query('SELECT * FROM '.DB_PREFIX.'local_masks '.
		'WHERE perm_page_id = '.$this->page['page_id'].' AND perm_group_id = '.$groupId);
		
		if($db->numRows($result) > 0) {
			$db->query('UPDATE '.DB_PREFIX.'local_masks '.
			'SET perm_access_mask = '.$newMask.' '.
			'WHERE perm_page_id = '.$this->page['page_id'].' AND perm_group_id = '.$groupId);
		} else {
			$db->query('INSERT INTO '.DB_PREFIX.'local_masks(perm_page_id, perm_group_id, perm_access_mask) '.
			'VALUES('.$this->page['page_id'].', '.$groupId.', '.$newMask.')');
		}
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['perms_updated']);
	}
	
	/**
	 * Removes the local access mask of a group.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int $groupId Id of the group which shall be reseted
	 * @return void
	 **/
	function resetGroupPerms($groupId)
	{
		$db  = &singleton('database');
		$tpl = &singleton('template');
		
		$db->query('DELETE FROM '.DB_PREFIX.'local_masks '.
		'WHERE perm_page_id = '.$this->page['page_id'].' '.
		'AND perm_group_id = '.$groupId);
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['perms_deleted']);
	}
	
	/**
	 * Loads all groups and their access masks for
	 * this page from the database.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return array Groups and their permissions
	 **/
	function permGetGroups()
	{
		$db    = &singleton('database');
		$perms = array();
		
		$result = $db->query('SELECT g.group_id, g.group_name, g.group_access_mask, p.perm_access_mask '.
		'FROM '.DB_PREFIX.'groups g LEFT JOIN '.DB_PREFIX.'local_masks p '.
		'ON p.perm_page_id = '.$this->page['page_id'].' AND p.perm_group_id = g.group_id '.
		'ORDER BY g.group_id');
		
		while($row = $db->fetch($result))
		{
			$row['group_name'] = htmlentities($row['group_name']);
			$perms[$row['group_id']] = $row;
		}
		
		return $perms;
	}
	
	/**
	 * Returns the template name for this action.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return $this->permsTemplate;
	}
}
?>
