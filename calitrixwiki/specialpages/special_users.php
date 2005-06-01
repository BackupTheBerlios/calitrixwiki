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
 * This is the users specialpage displaying a list of registered users.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_users extends core
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
		
		$result  = $db->query('SELECT COUNT(*) AS count FROM '.DB_PREFIX.'users');
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
		
		$users  = array();
		$result = $db->query('SELECT user_name, user_reg_time '.
		'FROM '.DB_PREFIX.'users ORDER BY user_name LIMIT '.$pages[1].','.$pages[2]);
		
		while($row = $db->fetch($result))
		{
			$row['user_reg_time'] = $this->convertTime($row['user_reg_time']);
			$row['user_name_raw'] = $row['user_name'];
			$row['user_name']     = htmlentities($row['user_name']);
			$users[]              = $row;
		}
		
		$tpl->assign('users', $users);
	}
	
	/**
	 * Returns the template name for this special page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return 'special_users.tpl';
	}
}
?>
