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

class installer_install extends installer
{
	/**
	 * Does everything needed to be done for this step.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function start()
	{
		$tpl = &singleton('template');
		
		$tpl->assign('dbCreateChecked',     false);
		$tpl->assign('defaultPagesChecked', false);
		
		$defaultPages = isset($this->get['idf']) ? (bool)$this->get['idf'] : false;
		$tpl->assign('defaultPages', $defaultPages);
		
		if(isset($this->get['start']) && $this->get['start'] == 1) {
			$success = $this->installDatabase($defaultPages);
			
			if(!$success) {
				$tpl->assign('isError', true);
				$tpl->assign('error',   mysql_error());
			} else {
				$params = array('step' => 'admin', 'lang' => CWIKI_INSTALL_LANG);
				header('Location: '.$this->genUrl($params));
			}
		}
	}
	
	/**
	 * Installs the database.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  bool $defaultPages Enables installation of default wiki pages if set to true
	 * @return void
	 **/
	function installDatabase($defaultPages)
	{
		include CWIKI_INSTALL_DIR.'/mysql.php';
		include CWIKI_SET_DIR.'/dbconfig.php';
		
		mysql_connect(DB_HOST, DB_USER, DB_PASS);
		mysql_select_db(DB_NAME);
		
		foreach($struct as $tbl => $sql)
		{
			if(!@mysql_query('CREATE TABLE '.DB_PREFIX.$tbl.$sql)) {
				return false;
			}
		}
		
		foreach($data['groups'] as $sql)
		{
			if(!@mysql_query('INSERT INTO '.DB_PREFIX.'groups'.$sql)) {
				return false;
			}
		}
		
		unset($data['groups']);
		
		if($defaultPages) {
			foreach($data as $tbl => $stmts)
			{
				foreach($stmts as $sql) {
					if(!@mysql_query('INSERT INTO '.DB_PREFIX.$tbl.$sql)) {
						return false;
					}
				}
			}
		}
		
		return $this->createConfigTable();
	}
	
	/**
	 * Creates the sql table with configuration values from the config file.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function createConfigTable()
	{
		include CWIKI_SET_DIR.'/stdconfig.php';
		
		foreach($cfg as $item => $value)
		{
			if(is_array($value)) {
				foreach($value as $key => $val)
				{
					if(is_array($val)) {
						$val = serialize($val);
					}
					
					if(!mysql_query('INSERT INTO '.DB_PREFIX.'config(config_section, config_item, config_value) VALUES("'.$item.'", "'.$key.'", "'.addslashes($val).'")')) {
						return false;
					}
				}
			} else {
				if(!mysql_query('INSERT INTO '.DB_PREFIX.'config(config_section, config_item, config_value) VALUES("default", "'.$item.'", "'.addslashes($value).'")')) {
					return false;
				}
			}
		}
		
		return true;
	}
	
	/**
	 * Returns the template name for this installation step.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function getTemplate()
	{
		return 'installer_install.tpl';
	}
}
?>