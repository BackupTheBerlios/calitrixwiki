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

class installer_updateb1 extends installer
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
			$tpl->assign('error',   $this->lang['updateb1_config_failed']);
			return false;
		}
		
		include $dbFile;
		
		mysql_connect(DB_HOST, DB_USER, DB_PASS);
		mysql_select_db(DB_NAME);
		
		mysql_query('CREATE TABLE '.DB_PREFIX.'config(config_section VARCHAR(30) NOT NULL, config_item '.
		'VARCHAR(100) NOT NULL, config_value TEXT NOT NULL, PRIMARY KEY (config_section, config_item))');
		
		$cfg = $this->createConfigTable($setFile);
		$cfg = $this->createConfigFile($cfg, $setFile);
		
		if($cfg === false) {
			$tpl->assign('isError', true);
			$tpl->assign('error',   sprintf($this->lang['updateb1_update_failed'], mysql_error));
			return false;
		}
		
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
		
		$cfg['default_theme']        = 'cw';
		$cfg['space_wiki_words']     = 0;
		$cfg['auto_link']            = 1;
		$cfg['display_namespaces']   = 1;
		$cfg['link_num']             = 0;
		$cfg['link_self']            = 1;
		$cfg['enable_url_rewriting'] = 0;
		$cfg['rewrite_rule_match']   = '^([^?./]+)$';
		$cfg['rewrite_rule_replace'] = 'cwiki.php?page=$1&%{QUERY_STRING}';
		
		$nCfg = array();
		
		foreach($cfg as $item => $value)
		{
			$nCfg[$item] = $value;
			
			if(is_array($value)) {
				foreach($value as $key => $val)
				{
					if(is_array($val)) {
						$val = serialize($val);
					}
					
					if(!@mysql_query('INSERT INTO '.DB_PREFIX.'config(config_section, config_item, config_value) VALUES("'.$item.'", "'.$key.'", "'.addslashes($val).'")')) {
						return false;
					}
				}
			} else {
				if(!@mysql_query('INSERT INTO '.DB_PREFIX.'config(config_section, config_item, config_value) VALUES("default", "'.$item.'", "'.addslashes($value).'")')) {
					return false;
				}
			}
		}
		
		return $nCfg;
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
		return 'installer_updateb1.tpl';
	}
}
?>