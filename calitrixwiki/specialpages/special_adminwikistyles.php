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
class special_adminwikistyles extends admin
{
	var $cfgTemplate = 'admin_wikistyles.tpl';
	
	/**
	 * Start function
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function start()
	{
		$op  = isset($this->get['op']) ? $this->get['op'] : '';
		$tpl = &singleton('template');
		$tpl->assign('isError',   false);
		$tpl->assign('isMessage', false);
		
		switch($op)
		{
			case 'editstyle':   $this->editWikiStyle();    break;
			case 'addstyle':    $this->addWikiStyle();     break;
			case 'delstyle':    $this->removeWikiStyle();  break;
			case 'editattribs': $this->editStyleAttribs(); break;
		}
		
		$this->loadWikiStyles();
	}
	
	/**
	 * Lets the admin edit the allowed style attributes.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function editStyleAttribs()
	{
		$config = $this->getOrigConfig();
		$tpl    = &singleton('template');
		$tpl->assign('cfgAttributes', $config['style_attributes']);
		
		if($this->request == 'POST') {
			$this->saveStyleAttribs($config['style_attributes']);
		}
		
		$this->cfgTemplate = 'admin_wikistyles_attribs.tpl';
	}
	
	/**
	 * Saves the allowed style attributes.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  array $origAttribs Current style attributes
	 * @return void
	 **/
	function saveStyleAttribs($origAttribs)
	{
		$tpl = &singleton('template');
		$attribs = isset($this->post['attribs']) && is_array($this->post['attribs']) ? $this->post['attribs'] : array();
		$regexes = isset($this->post['regexes']) && is_array($this->post['regexes']) ? $this->post['regexes'] : array();
		$saveAttribs = array();
		$isError = false;
		$errors  = array();
		
		for($i = 0; $i < count($attribs); $i++)
		{
			$attrib = trim($attribs[$i]);
			$regex  = isset($regexes[$i]) ? trim($regexes[$i]) : '';
			
			if($attrib == '') {
				continue;
			}
			
			if(!preg_match('/^[A-Za-z0-9-]+$/', $attrib)) {
				$isError  = true;
				$errors[] = sprintf($this->lang['admin_attribs_invalid_name'], htmlentities($attrib));
			}
			
			$saveAttribs[$attrib] = $regex;
		}
		
		if($isError) {
			$tpl->assign('isError', true);
			$tpl->assign('errors',  $errors);
		} else {
			foreach($origAttribs as $attrib => $regex)
			{
				$this->removeConfigItem('style_attributes', $attrib);
			}
			
			foreach($saveAttribs as $attrib => $regex)
			{
				$this->setConfigItem('style_attributes', $attrib, $regex);
			}
			
			$this->rewriteConfig();
			$tpl->assign('isMessage', true);
			$tpl->assign('message',   $this->lang['admin_attribs_updated']);
		}
		
		foreach($saveAttribs as $attrib => $regex)
		{
			$saveAttribs[$attrib] = htmlentities($regex);
		}
		
		$tpl->assign('cfgAttributes', $saveAttribs);
	}
	
	/**
	 * Lets the admin edit a predefined wikistyle.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function editWikiStyle()
	{
		$tpl = &singleton('template');
		$tpl->assign('cfgOp', 'editstyle');
		
		$config = $this->getOrigConfig();
		
		if(!isset($this->get['style'])) {
			return false;
		}
		
		$style = $this->get['style'];
		
		if(!isset($config['wiki_styles'][$style])) {
			return false;
		}
		
		$attribs = $config['wiki_styles'][$style];
		
		if($this->request == 'POST') {
			$this->saveWikiStyle($style, $attribs);
		} else {
			$tpl->assign('cfgStyleName',    $style);
			$tpl->assign('cfgStyleAttribs', $attribs);
			$this->cfgTemplate = 'admin_wikistyles_editstyle.tpl';
		}
	}
	
	/**
	 * Lets the admin add a wikistyle.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function addWikiStyle()
	{
		$tpl = &singleton('template');
		$tpl->assign('cfgOp', 'addstyle');
		
		if($this->request == 'POST') {
			$this->saveWikiStyle('', array());
		} else {
			$tpl->assign('cfgStyleName',    '');
			$tpl->assign('cfgStyleAttribs', array());
			$this->cfgTemplate = 'admin_wikistyles_editstyle.tpl';
		}
	}
	
	/**
	 * Removes a wikistyle.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function removeWikiStyle()
	{
		$tpl    = &singleton('template');
		$style  = isset($this->get['style']) ? $this->get['style'] : '';
		$config = $this->getOrigConfig();
		
		if($style == '' || !isset($config['wiki_styles'][$style])) {
			return false;
		}
		
		$this->removeConfigItem('wiki_styles', $style);
		$this->rewriteConfig();
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_wikistyle_updated']);
		$this->cfgTemplate = 'admin_wikistyles.tpl';
	}
	
	/**
	 * Saves a modified wikistyle to the database.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $oldName    Old name of the wikistyle
	 * @param  array  $oldAttribs Old style attributes
	 * @return void
	 **/
	function saveWikiStyle($oldName, $oldAttribs)
	{
		$tpl = &singleton('template');
		
		$newName    = isset($this->post['style_name'])    ? $this->post['style_name']    : $oldName;
		$newAttribs = isset($this->post['style_attribs']) ? $this->post['style_attribs'] : $oldAttribs;
		$isError    = false;
		$errors     = array();
		
		if(!preg_match('/^[A-Za-z0-9_-]+$/', $newName)) {
			$isError  = true;
			$errors[] = $this->lang['admin_wikistyle_invalid_name'];
		}
		
		$newAttribs = explode("\n", $newAttribs);
		$tmpAttribs = array();
		
		foreach($newAttribs as $attrib)
		{
			$attrib = trim($attrib);
			
			if($attrib == '') {
				continue;
			}
			
			$attrib = explode(':', $attrib);
			
			if(count($attrib) != 2 || !preg_match('/^[A-Za-z0-9-]+$/', $attrib[0])) {
				$isError = true;
				$errors[] = $this->lang['admin_wikistyle_invalid_attribs'];
				break;
			} else {
				$attrib[0]    = trim($attrib[0]);
				$attrib[1]    = trim($attrib[1]);
				$tmpAttribs[$attrib[0]] = $attrib[1];
			}
		}
		
		if($isError) {
			$tpl->assign('isError',         true);
			$tpl->assign('errors',          $errors);
			$tpl->assign('cfgStyleName',    htmlentities($newName));
			$tpl->assign('cfgStyleAttribs', $tmpAttribs);
			$this->cfgTemplate = 'admin_wikistyles_editstyle.tpl';
		} else {
			$this->removeConfigItem('wiki_styles', $oldName);
			$this->setConfigItem('wiki_styles', $newName, $tmpAttribs);
			$this->rewriteConfig();
			
			$tpl->assign('isMessage', true);
			$tpl->assign('message',   $this->lang['admin_wikistyle_updated']);
			$this->cfgTemplate = 'admin_wikistyles.tpl';
		}
	}
	
	/**
	 * Loads all wikistyles and assigns them to the template engine.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function loadWikiStyles()
	{
		$tpl = &singleton('template');
		$config = $this->getOrigConfig();
		
		$tpl->assign('cfgWikiStyles', $config['wiki_styles']);
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