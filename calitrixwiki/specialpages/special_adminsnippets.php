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
 * This is the admin specialpage for modifying code snippets used by the  parser.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_adminsnippets extends admin
{
	var $cfgSnippets = array();
	
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
		$tpl->assign('isEdit',    false);
		$this->makeSnippetList();
		
		if(isset($this->get['edit'])) {
			$this->editCodeSnippet();
		}
		
		$tpl->assign('codeSnippets', $this->cfgSnippets);
	}
	
	/**
	 * Lets the admin edit one of the html code snippets.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function editCodeSnippet()
	{
		$tpl         = &singleton('template');
		$snippetName = $this->get['edit'];
		
		if(!isset($this->cfgSnippets[$snippetName])) {
			return false;
		}
		
		$tpl->assign('snippetName', $snippetName);
		$tpl->assign('snippetDesc', $this->cfgSnippets[$snippetName]['desc']);
		$tpl->assign('snippetCode', $this->cfgSnippets[$snippetName]['code']);
		
		if($this->request == 'POST') {
			$this->saveCodeSnippet($snippetName);
			$this->makeSnippetList();
		} else {
			$tpl->assign('isEdit', true);
		}
	}
	
	/**
	 * Saves a code snippet to the config.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $snippetName Name of the code snippet
	 * @return void
	 **/
	function saveCodeSnippet($snippetName)
	{
		$tpl         = &singleton('template');
		$snippetCode = isset($this->post['snippet_code']) ? $this->post['snippet_code'] : html_entities_decode($this->cfgSnippets[$snippetName]['code']);
		
		$this->setConfigItem('code_snippets', $snippetName, $snippetCode);
		$this->rewriteConfig();
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_snippet_updated']);
	}
	
	/**
	 * Makes a list of all available code snippets.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function makeSnippetList()
	{
		$config   = $this->getOrigConfig();
		$config   = $config['code_snippets'];
		$snippets = array();
		
		foreach($config as $name => $code)
		{
			$desc = isset($this->lang['admin_snippetdesc_'.$name]) ? $this->lang['admin_snippetdesc_'.$name] : '';
			
			$snippets[$name] = array('name' => $name, 'code' => htmlentities($code), 'desc' => $desc);
		}
		
		$this->cfgSnippets = $snippets;
	}
	
	/**
	 * Returns the template name for this special page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return 'admin_snippets.tpl';
	}
}
?>