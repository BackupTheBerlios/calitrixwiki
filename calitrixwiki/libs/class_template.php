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

include $cfg['lib_dir'].'/tpl/Smarty.class.php';

/**
 * Smarty template engine wrapper class
 *
 * @author Johannes Klose <exe@calitrix.de>
 * @since 1.0 Beta 1 12.03.04 20:25
 **/
class template extends Smarty
{
	/**
	 * Constructor function; sets up smarty's variables and
	 * registers custom functions.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @since 1.0 Beta 1 12.03.04 20:25
	 * @return void
	 **/
	function template()
	{
		global $wiki, $cfg;
		
		$tplDir = is_object($wiki)    ? $wiki->cfg['themes_dir'] : $cfg['themes_dir'];
		$theme  = isset($wiki->theme) ? $wiki->theme             : $cfg['default_theme'];
		
		$this->template_dir    = $tplDir.'/'.$theme.'/templates';
		$this->compile_dir     = $this->template_dir.'/compiled';
		$this->use_sub_dirs    = false;        
		$this->left_delimiter  = '{';
		$this->right_delimiter = '}';
		
		$this->register_function('wikiplugin', array(&$this, 'wikiPlugin'));
		$this->register_function('wikiurl',    array(&$this, 'genTplUrl'));
		$this->register_block('checkperms',    array(&$this, 'checkPerms'));
	}
	
	/**
	 * Loads and executes a wiki plugin.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param array  $params Plugin parameters
	 * @return string        Plugin return value
	 **/
	function wikiPlugin($params)
	{
		$wiki = &$GLOBALS['wiki'];
		
		if(!isset($params['name']) || !preg_match('/^[A-Za-z0-9_]+$/', $params['name'])) {
			return '';
		}
		
		$fileName = $wiki->cfg['plugins_dir'].'/plugin_'.$params['name'].'.php';
		
		if(file_exists($fileName)) {
			$pluginName = 'plugin_'.$params['name'];
			
			include_once $fileName;
			$plugin    = new $pluginName($params);
			$returnVal = $plugin->getContent();
			
			return $returnVal;
		} else {
			return '';
		}
	}
	
	/**
	 * Template url generator.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  array $params Parameters comming from the template engine.
	 * @return string        Url
	 **/
	function genTplUrl($params)
	{
		$wiki = &$GLOBALS['wiki'];
		
		if(!isset($params['page'])) {
			return '';
		}
		
		$action   = '';
		$pageName = $params['page'];
		unset($params['page']);
		
		if(isset($params['action'])) {
			$action = $params['action'];
			unset($params['action']);
		}
		
		return $wiki->genUrl($pageName, $action, $params);
	}
	
	/**
	 * Checks the requested permissions.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  array  $params  Parameters comming from the template engine.
	 * @param  string $content Content between the template tags
	 * @return bool            True on success, false otherwise
	 **/
	function checkPerms($params, $content)
	{
		$wiki      = &$GLOBALS['wiki'];
		$perms = array();
		
		foreach($params as $param)
		{
			if(preg_match('/^[a-z_]+$/', $param)) {
				$perms[] = $param;
			}
		}
		
		if($wiki->checkPermissions($perms)) {
			return $content;
		} else {
			return '';
		}
	}
}
?>
