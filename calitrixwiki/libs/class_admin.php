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
 * Common functions for the admin special pages
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class admin extends core
{
	var $origConfig = array();
	var $newConfig  = array();
	
	/**
	 * Constructor function; calls the core constructor and does
	 * some common admin tasks
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  array $pageInfo Informations (title, namespace etc.) about the current page.
	 * @param  array $cfg      Configuration values
	 * @return void
	 **/
	function admin($pageInfo, &$cfg)
	{
		$this->core($pageInfo, $cfg);
		
		if(!$this->hasPerms(PERM_USEACP)) {
			$this->messageEnd('wiki_perm_denied');
		}
		
		$this->loadOrigConfig();
	}
	
	/**
	 * This function reloads the most important configuration settings
	 * after the configuration has changed to ensure the Wiki continues
	 * working during the request where the configuration changes.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function reloadConfig()
	{
		include CWIKI_STDCONFIG;
		
		// Reload url rewriting settings to make sure the Wiki
		// generates working urls in the case it removed the htaccess
		// and short urls aren't anymore rewritten into normal urls.
		$this->cfg['enable_url_rewriting'] = $cfg['enable_url_rewriting'];
		$this->cfg['url_format']           = $cfg['url_format'];
		$this->cfg['url_format_short']     = $cfg['url_format_short'];
		$this->cfg['rewrite_rule_match']   = $cfg['rewrite_rule_match'];
		$this->cfg['rewrite_rule_replace'] = $cfg['rewrite_rule_replace'];
	}
	
	/**
	 * This function loads the original config values from the database
	 * to make sure the admin scripts access config values which aren't
	 * changed by other functions.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function loadOrigConfig()
	{
		$db = &singleton('database');
		$result = $db->query('SELECT * FROM '.DB_PREFIX.'config ORDER BY config_section, config_item');
		
		while($row = $db->fetch($result))
		{
			if(!isset($this->origConfig[$row['config_section']])) {
				$this->origConfig[$row['config_section']] = array();
			}
			
			if($row['config_section'] == 'wiki_styles') {
				$row['config_value'] = unserialize($row['config_value']);
			}
			
			$this->origConfig[$row['config_section']][$row['config_item']] = $row['config_value'];
		}
	}
	
	/**
	 * Returns the original config values.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return array Original config values
	 **/
	function getOrigConfig()
	{
		return $this->origConfig;
	}
	
	/**
	 * Creates a list of tables in the database with some aditional data.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function createDbTableList()
	{
		$db     = &singleton('database');
		$result = $db->query('SHOW TABLE STATUS FROM '.DB_NAME);
		$tables = array();
		
		while($row = $db->fetch($result))
		{
			$tables[$row['Name']]['table_name']       = $row['Name'];
			$tables[$row['Name']]['table_overhead']   = $this->HRFileSize($row['Data_free']);
			$tables[$row['Name']]['table_overheaded'] = $row['Data_free'] > 0 ? true : false;
			$tables[$row['Name']]['table_size']       = $this->HRFileSize($row['Data_length'] + $row['Index_length']);
			$tables[$row['Name']]['table_rows']       = $row['Rows'];
			$tables[$row['Name']]['auto_increment']   = (int)$row['Auto_increment'];
			$tables[$row['Name']]['table_type']       = $row['Type'];
		}
		
		return $tables;
	}
	
	/**
	 * Changes a configuration item.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $section Configuration section of the item
	 * @param  string $item    Name of the config item
	 * @param  string $value   New value of the config item
	 * @return void
	 **/
	function setConfigItem($section, $item, $value)
	{
		if(count($this->newConfig) == 0) {
			$this->newConfig = $this->origConfig;
		}
		
		if(!isset($this->newConfig[$section])) {
			return false;
		}
		
		if($item == '') {
			$this->newConfig[$section][] = $value;
		} else {
			$this->newConfig[$section][$item] = $value;
		}
	}
	
	/**
	 * Removes a configuration item.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function removeConfigItem($section, $item)
	{
		if(count($this->newConfig) == 0) {
			$this->newConfig = $this->origConfig;
		}
		
		if(!isset($this->newConfig[$section])) {
			return false;
		}
		
		unset($this->newConfig[$section][$item]);
	}
	
	/**
	 * Takes $this->newConfig and writes changed sections to the
	 * database and config file.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function rewriteConfig()
	{
		foreach($this->newConfig as $section => $items)
		{
			if(count($this->origConfig[$section]) != count($this->newConfig[$section])) {
				$this->rewriteSection($section);
				continue;
			}
						
			foreach($items as $item => $value)
			{
				if(!isset($this->origConfig[$section][$item]) || $this->origConfig[$section][$item] != $value) {
					$this->rewriteSection($section);
					break;
				}
			}
		}
		
		$this->origConfig = $this->newConfig;
		$this->writeConfigFile();
	}
	
	/**
	 * Rewrites a config section in the database.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $section Name of the section.
	 * @return void
	 **/
	function rewriteSection($section)
	{
		$db = &singleton('database');
		
		$items = $this->newConfig[$section];
		$sql   = 'INSERT INTO '.DB_PREFIX.'config('.
		'config_section, config_item, config_value) VALUES';
		
		foreach($items as $item => $value)
		{
			if(is_array($value)) {
				$value = serialize($value);
			}
			
			$sql .= '("'.$section.'", "'.$item.'", "'.addslashes($value).'"), ';
		}
		
		$sql = substr($sql, 0, strlen($sql) - 2);
		
		$db->query('DELETE FROM '.DB_PREFIX.'config WHERE config_section = "'.$section.'"');
		$db->query($sql);
	}
	
	/**
	 * Writes the current configuration into the config file.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function writeConfigFile()
	{
		$array = array();
		
		foreach($this->origConfig as $section => $items)
		{
			if($section == 'default') {
				$array = array_merge($array, $items);
			} else {
				$array[$section] = $items;
			}
		}
		
		$file = $this->rdumpArray($array);
		$file = '<?PHP '."\n".'// This is a auto-generated file. Do not edit it directly. '."\n".
		'// Instead, always use the administration area of this Wiki to change configuration settings.'."\n".
		'// Generated on '.$this->convertTime($this->time).'.'."\n\n".
		'$cfg = '.$file.';'."\n".'?>';
		
		if(!($fp = @fopen(CWIKI_STDCONFIG, 'w'))) {
			$tpl = &singleton('template');
			$tpl->assign('isMessage', true);
			$tpl->assign('message',   $this->lang['admin_config_unwriteable']);
		} else {
			fputs($fp, $file);
			fclose($fp);
		}
	}
	
	/**
	 * Transforms a array into php-code.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  array  $array  Array to dump
	 * @return string         PHP code
	 **/
	function rdumpArray($array, $indent = 0)
	{
		$code = 'array('."\n";
		
		$maxLen = 0;
		
		foreach($array as $key => $val)
		{
			$maxLen = strlen($key) > $maxLen ? strlen($key) : $maxLen;
		}
		
		foreach($array as $key => $val)
		{
			if(is_array($val)) {
				$indent2 = $maxLen - strlen($key);
				$nIndent = $indent + strlen($key) + 12 + $indent2;
				$code   .= str_repeat(' ', $indent).'\''.$key.'\''.str_repeat(' ', $indent2).' => '.$this->rdumpArray($val, $nIndent).",\n";
			} else {
				$val      = str_replace('\'',   '\\\'',         $val);
				$val      = str_replace("\r\n", '\'."\r\n".\'', $val);
				$val      = str_replace("\n",   '\'."\n".\'',   $val);
				$indent2  = $maxLen - strlen($key);
				$code    .= str_repeat(' ', $indent).'\''.$key.'\''.str_repeat(' ', $indent2).' => \''.$val.'\','."\n";
			}
		}
		
		$code = substr($code, 0, strlen($code) - 2)."\n".str_repeat(' ', $indent).')';
		return $code;
	}
}
?>
