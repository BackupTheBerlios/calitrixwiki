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
 * This is the admin specialpage for adding and removing languages.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_adminlanguages extends admin
{
	var $cfgLangs = array();
	
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
		$this->makeLangList();
		
		if(isset($this->get['op'])) {
			$op = $this->get['op'];
			
			switch($op)
			{
				case 'inst': $this->opInstallLang(); break;
				case 'del':  $this->opRemoveLang();  break;
			}
		}
	}
	
	/**
	 * Removes a language from the config.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opRemoveLang()
	{
		$tpl = &singleton('template');
		
		if(!isset($this->get['lc'])) {
			return false;
		}
		
		$langCode = $this->get['lc'];
		
		if(!isset($this->cfgLangs[$langCode]) || !$this->cfgLangs[$langCode]['installed']) {
			return false;
		}
		
		if($langCode == $this->cfg['default_lang']) {
			$tpl->assign('isMessage', true);
			$tpl->assign('message',   $this->lang['admin_lang_remove_default']);
			return false;
		}
		
		$this->removeConfigItem('languages', $langCode);
		$this->rewriteConfig();
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_lang_removed']);
		
		$this->makeLangList();
	}
	
	/**
	 * Installs a language into the config.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opInstallLang()
	{
		$tpl = &singleton('template');
		
		if(!isset($this->get['lc'])) {
			return false;
		}
		
		$langCode = $this->get['lc'];
		
		if(!isset($this->cfgLangs[$langCode]) || $this->cfgLangs[$langCode]['installed']) {
			return false;
		}
		
		$this->setConfigItem('languages', $langCode, $this->cfgLangs[$langCode]['name']);
		$this->rewriteConfig();
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_lang_installed']);
		
		$this->makeLangList();
	}
	
	/**
	 * Makes the list of available languages.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function makeLangList()
	{
		$tpl = &singleton('template');
		
		$installed = $this->getOrigConfig();
		$installed = $installed['languages'];
		$available = array();
		
		$dir = opendir($this->cfg['lang_dir']);
		
		while(($file = readdir($dir)) !== false)
		{
			if($file != '.' && $file != '..' && preg_match('/^([a-z]{2}[0-9]?)\.php$/', $file, $match)) {
				$newLang = $this->readLang($match[1]);
				
				if($newLang) {
					$available[$match[1]] = $newLang;
				}
			}
		}
		
		closedir($dir);
		$langs = array();
		
		foreach($installed as $code => $name) {
			if(isset($available[$dir])) {
				unset($available[$dir]);
			}
			
			$langs[$code] = array('name' => $name, 'installed' => true);
		}
		
		$langs = array_merge($langs, $available);
		
		foreach($langs as $code => $data) {
			if(!isset($data['installed'])) {
				$langs[$code]['installed'] = isset($installed[$code]) ? true : false;
			}
			if(!isset($data['name'])) {
				$langs[$code]['name'] = $code;
			}
		}
		
		$tpl->assign('cfgLangs', $langs);
		$this->cfgLangs = $langs;
	}
	
	/**
	 * Reads the informations of a language out of the info file.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $code Language code
	 * @return void
	 **/
	function readLang($code)
	{
		$infoFile = $this->cfg['lang_dir'].'/'.$code.'.txt';
		$langFile = $this->cfg['lang_dir'].'/'.$code.'.php';
		$mailFile = $this->cfg['lang_dir'].'/'.$code.'_mails.php';
		
		if(!file_exists($infoFile) || !file_exists($langFile) || !file_exists($mailFile)) {
			return false;
		}
		
		$lines = file($infoFile);
		$info  = array();
		
		foreach($lines as $line)
		{
			$line = trim($line);
			
			if($line == '' || $line[0] == ';' || $line[0] == '#') {
				continue;
			}
			
			if(preg_match('/^([A-Za-z0-9_-]+)\s+(.+?)$/', $line, $match)) {
				$info[trim($match[1])] = trim($match[2]);
			}
		}
		
		return $info;
	}
	
	/**
	 * Returns the template name for this special page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return 'admin_languages.tpl';
	}
}
?>