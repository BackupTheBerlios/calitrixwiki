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

class installer
{
	var $get     = array();
	var $post    = array();
	var $request = null;
	
	/**
	 * Constructor function.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  string $step Current installation step
	 * @return void
	 **/
	function installer($step)
	{
		if(get_magic_quotes_gpc()) {
			$this->prepareGPCData($_GET);
			$this->prepareGPCData($_POST);
		}
		
		$this->get     = &$_GET;
		$this->post    = &$_POST;
		$this->request = &$_SERVER['REQUEST_METHOD'];
		$this->server  = &$_SERVER;
		$this->time    = time();
		$this->lang    = $this->getLang();
		$this->step    = $step;
		
		$this->start();
	}
	
	/**
	 * Loads the language variables.
	 * 
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return array Language variables
	 **/
	function getLang()
	{
		global $languages;
		
		if(isset($_GET['lang']) && isset($languages[$_GET['lang']])) {
			include CWIKI_INSTALL_DIR.'/lang/'.$_GET['lang'].'.php';
			define('CWIKI_INSTALL_LANG', $_GET['lang']);
		} else {
			include CWIKI_INSTALL_DIR.'/lang/'.CWIKI_DEFAULT_LANG.'.php';
			define('CWIKI_INSTALL_LANG', CWIKI_DEFAULT_LANG);
		}
		
		return $lang;
	}
	
	/**
	 * Assigns common template variables.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function assignTplVars()
	{
		global $languages;
		
		$tpl = &singleton('template');
		$tpl->assign('lang',        $this->lang);
		$tpl->assign('languages',   $languages);
		$tpl->assign('installUrl',  CWIKI_INSTALL_URL);
		$tpl->assign('pageTitle',   $this->lang['installation']);
		$tpl->assign('currentLang', CWIKI_INSTALL_LANG);
		
		if(isset($this->lang['step_'.$this->step])) {
			$tpl->assign('stepTitle', $this->lang['step_'.$this->step]);
		} else {
			$tpl->assign('stepTitle', '');
		}
	}
	
	/**
	 * Creates a lock file for the installer.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function lockInstaller()
	{
		$fp = fopen(CWIKI_INSTALL_DIR.'/install.lock', 'w');
		fputs($fp, 'Remove this file to use the installer again.');
		fclose($fp);
	}
	
	/**
	 * Displays the current template.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function display()
	{
		$template = $this->getTemplate();
		$tpl = &singleton('template');
		$tpl->display($template);
	}
	
	/**
	 * Generates a url.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  array  $params Url parameters
	 * @return string $url   Url
	 **/
	function genUrl($params)
	{
		$url = CWIKI_INSTALL_URL.'/index.php?';
		$tmp = array();
		
		foreach($params as $key => $val)
		{
			$tmp[] = $key.'='.urlencode($val);
		}
		
		$url .= join('&', $tmp);
		return $url;
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
	
	/**
	 * Strips backshlashes from GPC variables by walking recursively
	 * throug the given arrays.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param array &$var The GPC array which will be processed
	 * @return void
	 **/
	function prepareGPCData(&$var)
	{
		if(is_array($var)) {
			while(list($key, $val) = each($var))
			{
				$var[$key] = $this->prepareGPCData($val);
			}
		} else {
			$var = stripslashes($var);
		}
		
		return $var;
	}
}
?>