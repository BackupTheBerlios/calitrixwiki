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

class installer_updateb2 extends installer
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
		$tpl->assign('valSetDir', CWIKI_SET_DIR);
		$tpl->assign('isError',   false);
		$tpl->assign('updated',   false);
		
		if($this->request == 'POST') {
			$this->updateWiki();
		}
	}
	
	/**
	 * Updates the database of a calitrix wiki from beta 1 to beta 2.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function updateWiki()
	{
		$tpl = &singleton('template');
		
		$setPath = isset($this->post['set_dir']) ? trim($this->post['set_dir']) : '';
		$dbFile  = $setPath.'/dbconfig.php';
		$setFile = $setPath.'/stdconfig.php';
		
		if(!file_exists($dbFile) || !file_exists($setFile)) {
			$tpl->assign('isError', true);
			$tpl->assign('error',   $this->lang['updateb2_config_failed']);
			return false;
		}
		
		include $dbFile;
		
		mysql_connect(DB_HOST, DB_USER, DB_PASS);
		mysql_select_db(DB_NAME);
		
		// First fix the user table ...
		mysql_query('UPDATE '.DB_PREFIX.'users SET user_access_mask = -1 WHERE user_access_mask = 0');
		mysql_query('ALTER TABLE '.DB_PREFIX.'users CHANGE user_access_mask user_access_mask INT( 5 ) DEFAULT -1 NOT NULL');
		
		// Update wiki version
		mysql_query('UPDATE '.DB_PREFIX.'config SET config_value = "1.0 Beta 3" WHERE config_section = "default" AND config_item = "wiki_version"');
		
		// Rewrite the theme config due to the theme bug in beta 2
		mysql_query('DELETE FROM '.DB_PREFIX.'config WHERE config_section = "themes"');
		mysql_query('INSERT INTO '.DB_PREFIX.'config(config_section, config_item, config_value) VALUES("themes", "cw10b2", "CalitrixWiki 1.0 Beta 2")');
		
		// Merge the interwiki lists (in beta 2, the new interwikis were added to a misspelled config section)
		mysql_query('UPDATE '.DB_PREFIX.'config SET config_section = "interwiki" WHERE config_section = "inter_wiki"');
		
		$cfg = $this->createConfigTable($setFile);
		$cfg = $this->createConfigFile($cfg, $setFile);
		
		if($cfg === false) {
			$tpl->assign('isError', true);
			$tpl->assign('error',   sprintf($this->lang['updateb2_update_failed'], mysql_error()));
			return false;
		}
		
		$this->lockInstaller();
		$tpl->assign('updated', true);
	}
	
	/**
	 * Creates the sql table with configuration values from the config file.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $setFile Path to the wikis configuration file
	 * @return void
	 **/
	function createConfigTable($setFile)
	{
		include $setFile;
		
		$cfg['wiki_version'] = '1.0 Beta 3';
		$cfg['themes']       = array('cw10b2' => 'CalitrixWiki 1.0 Beta 2');
		$cfg['interwiki']    = array_merge($cfg['interwiki'], $cfg['inter_wiki']);
		
		return $cfg;
	}
	
	/**
	 * Writes the wikis new configuration file.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $cfg     The new config values
	 * @param  string $setFile The path to the config file
	 * @return void
	 **/
	function createConfigFile($cfg, $setFile)
	{
		$file  = "<?PHP\n";
		$file .= '$cfg = ';
		$file .= $this->rdumpArray($cfg);
		$file .= ";\n";
		$file .= '?>';
		
		$fp = @fopen($setFile, 'w');
		
		if(!$fp) {
			return false;
		}
		
		fputs($fp, $file);
		fclose($fp);
		
		return $cfg;
	}
	
	/**
	 * Returns the template name for this installation step.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function getTemplate()
	{
		return 'installer_updateb2.tpl';
	}
}
?>