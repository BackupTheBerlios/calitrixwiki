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
 * This is the sitemap specialpage. It displays an alphabetical index of all pages.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_sitemap extends core
{
	/**
	 * Start function
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function start()
	{
		$db   = &singleton('database');
		$tpl  = &singleton('template');
		
		$chars  = $this->cfg['sitemap_chars'];
		$result = $db->query('SELECT page_namespace, page_name FROM '.DB_PREFIX.'pages '.
		'ORDER BY page_name');
		
		while($row = $db->fetch($result))
		{
			$pageName = $row['page_name'];
			$char     = $pageName[0];
			
			if(!isset($pages[$char])) {
				$pages[$char] = array();
			}
			
			if(isset($chars[$char])) {
				$chars[$char] = 1;
			}
			
			$pages[$char][] = $this->getUniqueName($row);
		}
		
		$tpl->assign('sitemap', $pages);
		$tpl->assign('chars',   $chars);
	}
	
	/**
	 * Returns the template name for this special page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return 'special_sitemap.tpl';
	}
}
?>
