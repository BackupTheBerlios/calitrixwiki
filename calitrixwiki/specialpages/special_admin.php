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
class special_admin extends admin
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
		$db  = &singleton('database');
		
		$row1 = $db->queryRow('SELECT COUNT(*) AS count FROM '.DB_PREFIX.'pages');
		$row2 = $db->queryRow('SELECT COUNT(*) AS count FROM '.DB_PREFIX.'changelog');
		
		$daysInstalled = $this->time - $this->cfg['install_time'];
		$daysInstalled = $daysInstalled / 60 / 60 / 24;
		
		$tpl->assign('pageCount',   $row1['count']);
		$tpl->assign('pagesPerDay', round(($row1['count'] / $daysInstalled), 2));
		$tpl->assign('editCount',   $row2['count']);
		$tpl->assign('editsPerDay', round(($row2['count'] / $daysInstalled), 2));
		$tpl->assign('dbSize',      $this->getDbSize());
	}
	
	/**
	 * Returns the template name for this special page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return 'admin_index.tpl';
	}
}
?>
