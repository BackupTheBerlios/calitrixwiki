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
 * This action lets an user rename/move or delete a
 * page.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class action_options extends core
{
	var $optNamespaces = array();
	
	/**
	 * Start function
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  array  &$page Page data
	 * @return void
	 **/
	function start()
	{
		$this->pageAction = $this->lang['options'];
		
		if(!$this->hasPerms(PERM_RENAME) && !$this->hasPerms(PERM_DELETE)) {
			$this->messageEnd('wiki_perm_denied');
		}
		
		$this->optNamespaces = $this->cfg['namespaces'];
		unset($this->optNamespaces[array_search($this->cfg['special_namespace'], $this->optNamespaces)]);
		
		$tpl = &singleton('template');
		$tpl->assign('isMessage', false);
		$tpl->assign('isError',   false);
		$tpl->assign('nSpaces',   $this->optNamespaces);
		
		if($this->page['page_id'] == 0) {
			$this->HTTPRedirect($this->genUrl($this->getUniqueName($this->page), '', array(), false));
		}
		
		if($this->request == 'POST') {
			$this->doOption();
		}
	}
	
	/**
	 * Decides which option is to be executed.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function doOption()
	{
		if(!isset($this->post['do'])) {
			return false;
		}
		
		$do = $this->post['do'];
		
		if($do == 'rename') {
			$this->renamePage();
		} elseif($do == 'delete') {
			$this->deletePage();
		} else {
			return false;
		}
	}
	
	/**
	 * Renames the current page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function renamePage()
	{
		$newSpace = isset($this->post['new_space']) ? $this->post['new_space'] : $this->page['page_namespace'];
		$newName  = isset($this->post['new_name'])  ? $this->post['new_name']  : $this->page['page_name'];
		$tpl      = &singleton('template');
		$db       = &singleton('database');
		
		if($newName == $this->page['page_name'] && $newSpace == $this->page['page_namespace']) {
			return false;
		}
		
		if(!in_array($newSpace, $this->optNamespaces)) {
			$newSpace = $this->cfg['default_namespace'];
		}
		
		if(!preg_match('/^'.$this->cfg['title_format'].'$/', $newSpace.':'.$newName)) {
			$tpl->assign('isError', true);
			$tpl->assign('errors',  array($this->lang['options_invalid_page_name']));
			return false;
		}
		
		$db->query('UPDATE '.DB_PREFIX.'pages SET '.
		'page_namespace = "'.addslashes($newSpace).'", '.
		'page_name = "'.addslashes($newName).'" '.
		'WHERE page_id = '.$this->page['page_id']);
		$this->page['page_namespace'] = $newSpace;
		$this->page['page_name']      = $newName;
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['options_renamed']);
	}
	
	/**
	 * Deletes the current page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function deletePage()
	{
		if(!isset($this->post['confirm_delete'])) {
			return false;
		}
		
		$id = $this->page['page_id'];
		$db = &singleton('database');
		
		$db->query('DELETE FROM '.DB_PREFIX.'pages WHERE page_id = '.$id);
		$db->query('DELETE FROM '.DB_PREFIX.'page_texts WHERE page_id = '.$id);
		$db->query('DELETE FROM '.DB_PREFIX.'changelog WHERE log_page_id = '.$id);
		$db->query('DELETE FROM '.DB_PREFIX.'bookmarks WHERE bm_page_id = '.$id);
		$db->query('DELETE FROM '.DB_PREFIX.'local_masks WHERE perm_page_id = '.$id);
		
		$this->HTTPRedirect($this->genUrl($this->getUniqueName($this->page), '', array(), false));
	}
	
	/**
	 * Returns the template name for this action.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return 'action_options.tpl';
	}
}
?>
