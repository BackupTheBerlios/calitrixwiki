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

class installer_config extends installer
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
		
		$dirname = dirname($this->server['PHP_SELF']);
		
		$tpl->assign('cfgUrlRoot',          'http://'.$this->server['HTTP_HOST'].substr($dirname, 0, strlen($dirname) - 8));
		$tpl->assign('cfgDocRoot',          realpath(dirname($this->server['SCRIPT_FILENAME']).'/..'));
		$tpl->assign('cfgDbHost',           'localhost');
		$tpl->assign('cfgDbName',           'cwiki');
		$tpl->assign('cfgDbUser',           'root');
		$tpl->assign('cfgDbPrefix',         'cwiki_');
		$tpl->assign('dbCreateChecked',     false);
		$tpl->assign('defaultPagesChecked', false);
		$tpl->assign('isError',             false);
		
		if($this->request == 'POST') {
			$this->createConfig();
		}
	}
	
	/**
	 * Validates the config values.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function createConfig()
	{
		$tpl = &singleton('template');
		
		$urlRoot      = isset($this->post['url_root'])      ? trim($this->post['url_root'])      : '';
		$docRoot      = isset($this->post['doc_root'])      ? trim($this->post['doc_root'])      : '';
		$dbHost       = isset($this->post['db_host'])       ? trim($this->post['db_host'])       : '';
		$dbName       = isset($this->post['db_name'])       ? trim($this->post['db_name'])       : '';
		$dbUser       = isset($this->post['db_user'])       ? trim($this->post['db_user'])       : '';
		$dbPass       = isset($this->post['db_pass'])       ? trim($this->post['db_pass'])       : '';
		$dbPrefix     = isset($this->post['db_prefix'])     ? trim($this->post['db_prefix'])     : '';
		$dbCreate     = isset($this->post['db_create'])     ? (bool)$this->post['db_create']     : false;
		$defaultPages = isset($this->post['default_pages']) ? (bool)$this->post['default_pages'] : false;
		
		$errors = array();
		
		if(!@mysql_connect($dbHost, $dbUser, $dbPass)) {
			$errors[] = sprintf($this->lang['config_connect_failed'], mysql_error());
		}
		
		if(!@mysql_select_db($dbName)) {
			if($dbCreate) {
				if(!@mysql_query('CREATE DATABASE '.addslashes($dbName))) {
					$errors[] = sprintf($this->lang['config_connect_failed'], mysql_error());
				}
			} else {
				$errors[] = sprintf($this->lang['config_connect_failed'], mysql_error());
			}
		}
		
		if(count($errors) > 0) {
			$tpl->assign('isError', true);
			$tpl->assign('errors',  $errors);
			return false;
		}
		
		$this->writeDbConfig($dbHost, $dbName, $dbUser, $dbPass, $dbPrefix);
		$this->writeWikiConfig($urlRoot, $docRoot);
		
		$params = array('step' => 'install', 'lang' => CWIKI_INSTALL_LANG, 'idf' => $defaultPages ? 1 : 0);
		header('Location: '.$this->genUrl($params));
		exit;
	}
	
	/**
	 * Writes the database configuration file.
	 * 
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $dbHost   Database host
	 * @param  string $dbName   Database name
	 * @param  string $dbUser   Database username
	 * @param  string $dbPass   Database password
	 * @param  string $dbPrefix Database table prefix
	 * @return void
	 **/
	function writeDbConfig($dbHost, $dbName, $dbUser, $dbPass, $dbPrefix)
	{
		$file  = "<?PHP\n";
		$file .= "define('DB_HOST',   '$dbHost');\n";
		$file .= "define('DB_NAME',   '$dbName');\n";
		$file .= "define('DB_USER',   '$dbUser');\n";
		$file .= "define('DB_PASS',   '$dbPass');\n";
		$file .= "define('DB_PREFIX', '$dbPrefix');\n";
		$file .= "?>";
		
		$fp = fopen(CWIKI_SET_DIR.'/dbconfig.php', 'w');
		fputs($fp, $file);
		fclose($fp);
	}
	
	/**
	 * Writes the wikis configuration file.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $urlRoot The wikis root url
	 * @param  string $docRoot The wikis root directory in the server fs
	 * @return void
	 **/
	function writeWikiConfig($urlRoot, $docRoot)
	{
		include CWIKI_INSTALL_DIR.'/stdconfig.php';
		
		$cfg['url_root']         = $urlRoot;
		$cfg['url_format']       = $urlRoot.'/cwiki.php?page=%1$s&action=%2$s';
		$cfg['url_format_short'] = $urlRoot.'/cwiki.php?page=%1$s';
		$cfg['doc_root']         = $docRoot;
		$cfg['actions_dir']      = $docRoot.'/actions';
		$cfg['lang_dir']         = $docRoot.'/lang';
		$cfg['lib_dir']          = $docRoot.'/libs';
		$cfg['plugins_dir']      = $docRoot.'/plugins';
		$cfg['special_dir']      = $docRoot.'/specialpages';
		$cfg['themes_dir']       = $docRoot.'/themes';
		
		$file  = "<?PHP\n";
		$file .= '$cfg = ';
		$file .= $this->rdumpArray($cfg);
		$file .= ";\n";
		$file .= '?>';
		
		$fp = fopen(CWIKI_SET_DIR.'/stdconfig.php', 'w');
		fputs($fp, $file);
		fclose($fp);
	}
	
	/**
	 * Returns the template name for this installation step.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function getTemplate()
	{
		return 'installer_config.tpl';
	}
}
?>