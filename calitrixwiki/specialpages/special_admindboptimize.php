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
class special_admindboptimize extends admin
{
	var $dbTableList = array();
	
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
		
		$this->dbTableList = $this->createDbTableList();
		$tpl->assign('dbTables', $this->dbTableList);
		
		if($this->request == 'POST') {
			$this->optimizeDbTables();
		}
	}
	
	/**
	 * Optimizes the selected database tables.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function optimizeDbTables()
	{
		$tables    = isset($this->post['tables']) && is_array($this->post['tables']) ? $this->post['tables'] : array();
		$optimized = array();
		$db        = &singleton('database');
		$tpl       = &singleton('template');
		
		@set_time_limit(0);
		
		foreach($tables as $table)
		{
			if(!isset($this->dbTableList[$table])) {
				continue;
			}
			
			$db->query('OPTIMIZE TABLE '.$table);
			$optimized[] = $table;
		}
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   sprintf($this->lang['admin_db_optimized'], join(', ', $optimized)));
		$this->dbTableList = $this->createDbTableList();
		$tpl->assign('dbTables', $this->dbTableList);
	}
	
	/**
	 * Returns the template name for this special page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return 'admin_dboptimize.tpl';
	}
}
?>