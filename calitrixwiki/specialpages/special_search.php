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
 * This is the search specialpage. It Provides the main search function.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_search extends core
{
	/**
	 * Start function
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param object &$core Core class object
	 * @return void
	 **/
	function start()
	{
		$parser = &singleton('parser');
		$tpl    = &singleton('template');
		$db     = &singleton('database');
		$query  = $this->getSearchQuery();
		$search = false;
		
		if($query != '') {
			$search = true;
			$pages  = array();
			$result = $db->query('SELECT pt.page_text, p.page_id, p.page_namespace, p.page_name, '.
			'MATCH(pt.page_text) AGAINST(\''.addslashes($query).'\') as relevancy '.
			'FROM '.DB_PREFIX.'page_texts pt LEFT JOIN '.DB_PREFIX.'pages p ON p.page_id = pt.page_id '.
			'LEFT JOIN '.DB_PREFIX.'local_masks m ON m.perm_page_id = p.page_id AND m.perm_group_id = '.$this->user['group_id'].' '.
			'WHERE MATCH(pt.page_text) AGAINST(\''.addslashes($query).'\') '.
			'AND (m.perm_access_mask IS NULL OR '.PERM_VIEW.' & m.perm_access_mask = '.PERM_VIEW.') '.
			'ORDER BY relevancy DESC');
			
			while($row = $db->fetch($result))
			{
				$searchSuccess    = true;
				$row['page_text'] = $parser->stripCodes($row['page_text']);
				$row['page_text'] = substr($row['page_text'], 0, $this->cfg['teaser_length']);
				$row['page_name'] = $this->getUniqueName($row);
				
				$pages[] = $row;
			}
			
			$tpl->assign('searchResult', $pages);
		}
		
		$tpl->assign('search', $search);
	}
	
	/**
	 * Extracts the serach query from the url-
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param object &$core Core class object
	 * @return string Template name
	 **/
	function getSearchQuery()
	{
		$query = isset($this->post['q']) && trim($this->post['q']) != '' ? $this->post['q'] : '';
		
		return $query;
	}
	
	/**
	 * Returns the template name for this special page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return 'special_search.tpl';
	}
}
?>
