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
 * This is the admin specialpage which provides the admin index page.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_admininterwiki extends admin
{
	/**
	 * Start function
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function start()
	{
		$tpl = &singleton('template');
		
		$tpl->assign('cfgInterWikiName', '');
		$tpl->assign('cfgInterWikiUrl',  '');
		$tpl->assign('cfgOldInterWiki',  '');
		$tpl->assign('isMessage',        false);
		$tpl->assign('isError',          false);
		
		if($this->request == 'POST' || isset($this->get['op'])) {
			$this->updateInterWikis();
		}
		
		$config = $this->getOrigConfig();
		$tpl->assign('interWikis', $config['interwiki']);
	}
	
	/**
	 * This function decides wether an interwiki has to be
	 * created, edited or deleted.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function updateInterWikis()
	{
		if($this->request == 'POST') {
			$this->saveInterWiki();
		} else {
			$op = $this->get['op'];
			
			if($op == 'del') {
				$this->deleteInterWiki();
			} elseif($op == 'edit') {
				$this->editInterWiki();
			}
		}
	}
	
	/**
	 * This function saves a new or edited interwiki.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function saveInterWiki()
	{
		$tpl = &singleton('template');
		
		$interWikiName = isset($this->post['interwiki_name']) ? trim($this->post['interwiki_name']) : '';
		$interWikiUrl  = isset($this->post['interwiki_url'])  ? trim($this->post['interwiki_url'])  : '';
		
		if($interWikiName == '' || $interWikiUrl == '' || preg_match('/[^\x00-\xff]/', $interWikiName)) {
			$tpl->assign('isError', true);
			$tpl->assign('errors',  array($this->lang['admin_invalid_interwiki']));
		}
		
		if(isset($this->post['old_wiki']) && $this->post['old_wiki'] != '') {
			$this->removeConfigItem('interwiki', $this->post['old_wiki']);
		}
		
		$this->setConfigItem('interwiki', $interWikiName, $interWikiUrl);
		$this->rewriteConfig();
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_interwiki_updated']);
	}
	
	/**
	 * Enters an existing InterWiki into the edit form.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function editInterWiki()
	{
		$tpl = &singleton('template');
		
		if(!isset($this->get['wiki'])) {
			return false;
		}
		
		$config = $this->getOrigConfig();
		$wiki   = $this->get['wiki'];
		
		if(isset($config['interwiki'][$wiki])) {
			$tpl->assign('cfgInterWikiName', $wiki);
			$tpl->assign('cfgInterWikiUrl',  htmlentities($config['interwiki'][$wiki]));
			$tpl->assign('cfgOldInterWiki',  $wiki);
		}
	}
	
	/**
	 * Removes a interwiki.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function deleteInterWiki()
	{
		$tpl = &singleton('template');
		
		if(!isset($this->get['wiki'])) {
			return false;
		}
		
		$wiki = $this->get['wiki'];
		
		$this->removeConfigItem('interwiki', $wiki);
		$this->rewriteConfig();
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_interwiki_updated']);
	}
	
	/**
	 * Returns the template name for this special page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return 'admin_interwiki.tpl';
	}
}
?>