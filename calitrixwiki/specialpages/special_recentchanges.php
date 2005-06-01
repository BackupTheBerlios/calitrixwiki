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
 * This is the recentchanges specialpage. It displays the last sidewide changes in wiki pages.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_recentchanges extends core
{
	/**
	 * Start function
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function start()
	{
		$db     = &singleton('database');
		$tpl    = &singleton('template');
		
		$result  = $db->query('SELECT COUNT(*) AS count FROM '.DB_PREFIX.'changelog');
		$row     = $db->fetch($result);
		$count   = $row['count'];
		$pageUrl = $this->genUrl($this->getUniqueName($this->page), '', array('p' => '%s'), true, true);
		$pages   = $this->makePages($count, $this->cfg['items_per_page'], $pageUrl);
		
		$this->lang['wiki_pages'] = sprintf($this->lang['wiki_pages'], $pages[4], $pages[3]);
		
		$tpl->assign('pageLinks', $pages[0]);
		$tpl->assign('numPages',  $pages[3]);
		$tpl->assign('thisPage',  $pages[4]);
		$tpl->assign('firstPage', sprintf($pageUrl, 1));
		$tpl->assign('lastPage',  sprintf($pageUrl, $pages[3]));
		
		$changes = array();
		$result  = $db->query('SELECT l.log_page_id, l.log_page_version, '.
		'l.log_time, l.log_user_name, l.log_summary, p.page_namespace, p.page_name, u.user_name '.
		'FROM '.DB_PREFIX.'changelog l LEFT JOIN '.DB_PREFIX.'pages p '.
		'ON p.page_id = l.log_page_id '.
		'LEFT JOIN '.DB_PREFIX.'users u ON u.user_id = l.log_user_id '.
		'ORDER BY log_time DESC LIMIT '.$pages[1].','.$pages[2]);
		
		while($row = $db->fetch($result))
		{
			if($row['log_user_name'] != '' && $row['user_name'] == '') {
				$row['user_name'] = $row['log_user_name'];
			}
			
			$row['page_name_raw'] = $this->getUniqueName($row);
			$row['page_name']     = htmlentities(str_replace('_', ' ', $this->getUniqueName($row)));
			$row['user_name_raw'] = $row['user_name'];
			$row['user_name']     = htmlentities(str_replace('_', ' ', $row['user_name']));
			$row['log_summary']   = htmlentities($row['log_summary']);
			$row['log_time']      = $this->convertTime($row['log_time']);
			$row['page_url']      = $this->genUrl($row['page_name']);
			$row['diff_url']      = $this->genUrl($row['page_name'], 'history',
			                        array('o' => 'diff', 'orig' => '0', 'final' => $row['log_page_version']));
			$row['history_url']   = $this->genUrl($row['page_name'], 'history');
			$changes[]            = $row;
		}
		
		$tpl->assign('changes', $changes);
	}
	
	/**
	 * Returns the template name for this special page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return 'special_recentchanges.tpl';
	}
}
?>
