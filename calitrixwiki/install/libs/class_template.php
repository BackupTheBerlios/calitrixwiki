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

include CWIKI_LIB_DIR.'/tpl/Smarty.class.php';

/**
 * Smarty template engine wrapper class
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class template extends Smarty
{
	/**
	 * Constructor function; sets up smarty's variables and
	 * registers custom functions.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function template()
	{	
		$this->template_dir    = CWIKI_INSTALL_DIR.'/html/templates';
		$this->compile_dir     = $this->template_dir.'/compiled';
		$this->use_sub_dirs    = false;        
		$this->left_delimiter  = '{';
		$this->right_delimiter = '}';
		$this->error_reporting = E_ALL;
		
		$this->register_function('wikiurl', array(&$this, 'genTplUrl'));
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
		global $installer;
		
		return htmlentities($installer->genUrl($params));
	}
}
?>