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
 * This is the "wikipage"-plugin. It loads and parses a wikipage
 * and returns the text.
 *
 * @author Johannes Klose <exe@calitrix.de>
 * @since 1.0 Beta 1 31.05.04 14:51
 **/

$GLOBALS['wikipageIncludes'] = 0;

/**
 * Plugin class.
 *
 * @author Johannes Klose <exe@calitrix.de>
 * @since 1.0 Beta 1 31.05.04 14:51
 **/
class plugin_include
{
	var $pageText = '';
	
	/**
	 * Constructor function
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @since 1.0 Beta 1 31.05.04 14:51
	 * @param array  &$params Plugin parameters
	 * @param object &$core   Core class object
	 * @return void
	 **/
	function plugin_include(&$params)
	{
		global $wikipageIncludes, $wiki;
		
		$parser = &singleton('parser');
		
		if(!isset($params['page'])) {
			return;
		}
		
		$pageName = $params['page'];
		
		if(!preg_match('/^'.$wiki->cfg['title_format'].'$/', $pageName, $match)) {
			return;
		} else {
			$namespace = substr($match[1], 0, strlen($match[1]) - 1);
			$title     = $match[2];
		}
		
		if(isset($params['text']) && $wikipageIncludes == $wiki->cfg['max_includes']) {
			return;
		}
		
		$page = $wiki->getPage($title, true, $namespace);
		
		if($page === false) {
			return;
		}
		
		if(isset($params['text'])) {
			$wikipageIncludes++;
		}
		
		$this->pageText = $parser->parseText($page);
	}
	
	/**
	 * Returns the parsed wikipage.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @since 1.0 Beta 1 25.05.04 18:52
	 * @return string Page text
	 **/
	function getContent()
	{
		return $this->pageText;
	}
}
?>