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
		mysql_query('UPDATE '.DB_PREFIX.'users SET user_access_mask = -1 WHERE user_access_mask = 0');
		
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
		
		$cfg['space_wiki_words']                  = 0;
		$cfg['auto_link']                         = 1;
		$cfg['display_namespaces']                = 1;
		$cfg['link_num']                          = 0;
		$cfg['link_self']                         = 1;
		$cfg['enable_url_rewriting']              = 0;
		$cfg['rewrite_rule_match']                = '^([^?./]+)$';
		$cfg['rewrite_rule_replace']              = 'cwiki.php?page=$1&%{QUERY_STRING}';
		$cfg['wiki_version']                      = '1.0 Beta 2';
		$cfg['install_time']                      = $this->time;
		$cfg['default_theme']                     = 'cw10b2';
		$cfg['themes']['cw']                      = 'CalitrixWiki 1.0 Beta 2';
		$cfg['actions']['options']                = 'options';
		$cfg['actions']['print']                  = 'view';
		$cfg['code_snippets']['link_email']       = '<a href="%1$s" class="wiki-email">%2$s</a>';
		$cfg['code_snippets']['link_external']    = '<a href="%1$s" class="wiki-external">%2$s</a>';
		$cfg['code_snippets']['trail']            = '<table cellpadding="0" cellpadding="0" border="0" style="background:#f1f1f1;border:1px #cdcdcd solid;"><tr><td width="33%%">%s&laquo;</td><td width="34%%" align="center"><a href="%s">%s</a></td><td width="33%%" align="right">&raquo;%s</td></tr></table>';
		$cfg['code_snippets']['trail_emptyleft']   = '';
		$cfg['code_snippets']['trail_emptyright']  = '';
		$cfg['code_snippets']['trail_linkleft']    = '<a href="%s">%s</a>';
		$cfg['code_snippets']['trail_linkright']   = '<a href="%s">%s</a>';
		$cfg['style_attributes']['clear']          = '/^(left|right|both|none)$/';
		$cfg['style_attributes']['vertical-align'] = '/^(top|middle|bottom)$/';
		$cfg['indent_width']                       = 30;
		$cfg['teaser_length']                      = 400;
		$cfg['match_email']                        = '/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]+$/i';
		$cfg['wiki_styles']['highlight']           = array('background-color' => '#ffd800', 'color' => '#6e0000');
		$cfg['wiki_styles']['strike']              = array('text-decoration' => 'line-through');
		$cfg['wiki_styles']['underline']           = array('text-decoration' => 'underline');
		$cfg['wiki_styles']['small']               = array('font-size' => '0.8em');
		$cfg['inter_wiki']['UseMod']               = 'http://www.usemod.com/cgi-bin/wiki.pl?%s';
		$cfg['inter_wiki']['Calitrix']             = 'http://www.calitrix.de/%s';
		$cfg['inter_wiki']['C2']                   = 'http://c2.com/cgi/wiki?%s';
		
		unset($cfg['html_newline']);
		unset($cfg['html_paragraph']);
		unset($cfg['enable_caching']);
		unset($cfg['sitemap_chars']);
		unset($cfg['actions']['perms']);
		unset($cfg['html_paragraph']);
		unset($cfg['html_newline']);
		unset($cfg['sitemap_chars']);
		unset($cfg['enable_caching']);
		
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