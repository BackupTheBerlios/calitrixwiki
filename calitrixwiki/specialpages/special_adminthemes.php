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
 * This is the admin specialpage for adding and removing themes.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_adminthemes extends admin
{
	var $cfgThemes = array();
	
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
		$this->makeThemeList();
		
		if(isset($this->get['op'])) {
			$op = $this->get['op'];
			
			switch($op)
			{
				case 'inst': $this->opInstallTheme(); break;
				case 'del':  $this->opRemoveTheme();  break;
			}
		}
	}
	
	/**
	 * Installs a theme into the config.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opInstallTheme()
	{
		$tpl = &singleton('template');
		
		if(!isset($this->get['tdir'])) {
			return false;
		}
		
		$themeDir = $this->get['tdir'];
		
		if(!isset($this->cfgThemes[$themeDir]) || $this->cfgThemes[$themeDir]['installed']) {
			return false;
		}
		
		$this->setConfigItem('themes', $themeDir, $this->cfgThemes[$themeDir]['name']);
		$this->rewriteConfig();
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_theme_installed']);
		$this->makeThemeList();
	}
	
	/**
	 * Removes a theme from the config.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function opRemoveTheme()
	{
		$tpl = &singleton('template');
		
		if(!isset($this->get['tdir'])) {
			return false;
		}
		
		$themeDir = $this->get['tdir'];
		
		if(!isset($this->cfgThemes[$themeDir]) || !$this->cfgThemes[$themeDir]['installed']) {
			return false;
		}
		
		if($themeDir == $this->cfg['default_theme']) {
			$tpl->assign('isMessage', true);
			$tpl->assign('message',   $this->lang['admin_theme_remove_default']);
			return false;
		}
		
		$this->removeConfigItem('themes', $themeDir);
		$this->rewriteConfig();
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_theme_removed']);
		$this->makeThemeList();
	}
	
	/**
	 * Makes the list of available themes.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function makeThemeList()
	{
		$tpl = &singleton('template');
		
		$installed = $this->getOrigConfig();
		$installed = $installed['themes'];
		$available = array();
		
		$dir = opendir($this->cfg['themes_dir']);
		
		while(($file = readdir($dir)) !== false)
		{
			if($file != '.' && $file != '..') {
				$newTheme = $this->readTheme($file);
				
				if($newTheme) {
					$available[$file] = $newTheme;
				}
			}
		}
		
		closedir($dir);
		$themes = array();
		
		foreach($installed as $dir => $name) {
			if(isset($available[$dir])) {
				unset($available[$dir]);
			}
			
			$themes[$dir] = array('name' => $name, 'installed' => true);
		}
		
		$themes = array_merge($themes, $available);
		
		foreach($themes as $dir => $data) {
			if(!isset($data['installed'])) {
				$themes[$dir]['installed'] = isset($installed[$dir]) ? true : false;
			}
			if(!isset($data['name'])) {
				$themes[$dir]['name'] = $dir;
			}
		}
		
		$tpl->assign('cfgThemes', $themes);
		$this->cfgThemes = $themes;
	}
	
	/**
	 * Reads the informations of a new theme out of the info file.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $dir Theme dir
	 * @return void
	 **/
	function readTheme($dir)
	{
		$infoFile = $this->cfg['themes_dir'].'/'.$dir.'/theme_info.txt';
		
		if(!file_exists($infoFile) || !is_readable($infoFile)) {
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
		return 'admin_themes.tpl';
	}
}
?>