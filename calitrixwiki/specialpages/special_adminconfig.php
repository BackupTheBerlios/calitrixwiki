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
 * This is the admin specialpage for changing the general configuration settings.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_adminconfig extends admin
{
	var $configUserGroups = array();
	
	/**
	 * Start function
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function start()
	{
		$db  = &singleton('database');
		$tpl = &singleton('template');
		$tpl->assign('isError',   false);
		$tpl->assign('isMessage', false);
		
		$result = $db->query('SELECT group_id, group_name FROM '.DB_PREFIX.'groups ORDER BY group_id');
		
		while($row = $db->fetch($result))
		{
			$row['group_name'] = htmlentities($row['group_name']);
			$this->configUserGroups[$row['group_id']] = $row['group_name'];
		}
		
		if($this->request == 'POST') {
			$this->saveConfig();
		}
		
		$this->setFormValues();
		$this->reloadConfig();
	}
	
	/**
	 * Decides which configuration values shall be changed.
	 *
	 * @author Johannes Klose <exe@calitrix.de> 
	 * @return void
	 **/
	function saveConfig()
	{
		$change = isset($this->post['change']) ? $this->post['change'] : '';
		
		switch($change)
		{
			case 'paths':      $this->changePaths();      break;
			case 'users':      $this->changeUsers();      break;
			case 'misc':       $this->changeMisc();       break;
			case 'ui':         $this->changeUi();         break;
			case 'mailing':    $this->changeMailing();    break;
			case 'namespaces': $this->changeNamespaces(); break;
			case 'uploads':    $this->changeUploads();    break;
			case 'urlrewrite': $this->changeUrlRewrite(); break;
			case 'parser':     $this->changeParser();     break;
		}
	}
	
	/**
	 * Saves the path settings.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @reutnr void
	 **/
	function changePaths()
	{
		$tpl = &singleton('template');
		
		$urlRoot    = isset($this->post['url_root'])    ? trim($this->post['url_root'])    : '';
		$docRoot    = isset($this->post['doc_root'])    ? trim($this->post['doc_root'])    : '';
		$actionsDir = isset($this->post['actions_dir']) ? trim($this->post['actions_dir']) : '';
		$langDir    = isset($this->post['lang_dir'])    ? trim($this->post['lang_dir'])    : '';
		$libDir     = isset($this->post['lib_dir'])     ? trim($this->post['lib_dir'])     : '';
		$specialDir = isset($this->post['special_dir']) ? trim($this->post['special_dir']) : '';
		$pluginsDir = isset($this->post['plugins_dir']) ? trim($this->post['plugins_dir']) : '';
		$themesDir  = isset($this->post['themes_dir'])  ? trim($this->post['themes_dir'])  : '';
		
		if(empty($urlRoot) || empty($docRoot) || empty($actionsDir) || empty($langDir) || 
		   empty($libDir) || empty($specialDir) || empty($pluginsDir) || empty($themesDir)) {
			$tpl->assign('isError', true);
			$tpl->assign('errors', array($this->lang['admin_config_invalid_data']));
			return false;
		}
		
		$errors  = array();
		
		if(!file_exists($docRoot)) {
			$errors[] = sprintf($this->lang['admin_config_missing_dir'], htmlentities($docRoot));
		}
		if(!file_exists($actionsDir)) {
			$errors[] = sprintf($this->lang['admin_config_missing_dir'], htmlentities($actionsDir));
		}
		if(!file_exists($langDir)) {
			$errors[] = sprintf($this->lang['admin_config_missing_dir'], htmlentities($langDir));
		}
		if(!file_exists($libDir)) {
			$errors[] = sprintf($this->lang['admin_config_missing_dir'], htmlentities($libDir));
		}
		if(!file_exists($specialDir)) {
			$errors[] = sprintf($this->lang['admin_config_missing_dir'], htmlentities($specialDir));
		}
		if(!file_exists($pluginsDir)) {
			$errors[] = sprintf($this->lang['admin_config_missing_dir'], htmlentities($pluginsDir));
		}
		if(!file_exists($themesDir)) {
			$errors[] = sprintf($this->lang['admin_config_missing_dir'], htmlentities($themesDir));
		}
		
		if(count($errors) > 0) {
			$tpl->assign('isError', true);
			$tpl->assign('errors',  $errors);
			return false;
		}
		
		$this->setConfigItem('default', 'url_root',    $urlRoot);
		$this->setConfigItem('default', 'doc_root',    $docRoot);
		$this->setConfigItem('default', 'actions_dir', $actionsDir);
		$this->setConfigItem('default', 'lang_dir',    $langDir);
		$this->setConfigItem('default', 'lib_dir',     $libDir);
		$this->setConfigItem('default', 'special_dir', $specialDir);
		$this->setConfigItem('default', 'plugins_dir', $pluginsDir);
		$this->setConfigItem('default', 'themes_dir',  $themesDir);
		
		$this->rewriteConfig();
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_config_updated']);
	}
	
	/**
	 * Changes the configuration settings for users and groups.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function changeUsers()
	{
		$tpl = &singleton('template');
		
		$config = $this->getOrigConfig();
		$config = $config['default'];
		
		$defaultGGroup = isset($this->post['default_ggroup'])   ? $this->post['default_ggroup']   : $config['default_guest_group'];
		$defaultUGroup = isset($this->post['default_ugroup'])   ? $this->post['default_ugroup']   : $config['default_user_group'];
		$minPwLength   = isset($this->post['min_pw_length'])    ? $this->post['min_pw_length']    : $config['min_password_length'];
		$minUserLength = isset($this->post['min_user_length'])  ? $this->post['min_user_length']  : $config['min_username_length'];
		$maxUserLength = isset($this->post['max_user_length'])  ? $this->post['max_user_length']  : $config['max_username_length'];
		$sessLifetime  = isset($this->post['session_lifetime']) ? $this->post['session_lifetime'] : $config['session_lifetime'];
		$cookiePrefix  = isset($this->post['cookie_prefix'])    ? $this->post['cookie_prefix']    : $config['cookie_prefix'];
		$cookiePath    = isset($this->post['cookie_path'])      ? $this->post['cookie_path']      : $config['cookie_path'];
		$cookieDomain  = isset($this->post['cookie_domain'])    ? $this->post['cookie_domain']    : $config['cookie_domain'];
		$cookieSecure  = isset($this->post['cookie_secure'])    ? 1                               : 0;
		
		if($maxUserLength > 50) {
			$maxUserLength = 50;
		}
		
		if(!isset($this->configUserGroups[$defaultGGroup])) {
			$defaultGGroup = $config['default_guest_group'];
		}
		if(!isset($this->configUserGroups[$defaultUGroup])) {
			$defaultUGroup = $config['default_user_group'];
		}
		
		$this->setConfigItem('default', 'default_guest_group', $defaultGGroup);
		$this->setConfigItem('default', 'default_user_group',  $defaultUGroup);
		$this->setConfigItem('default', 'min_password_length', $minPwLength);
		$this->setConfigItem('default', 'min_username_length', $minUserLength);
		$this->setConfigItem('default', 'max_username_length', $maxUserLength);
		$this->setConfigItem('default', 'session_lifetime',    $sessLifetime);
		$this->setConfigItem('default', 'cookie_prefix',       $cookiePrefix);
		$this->setConfigItem('default', 'cookie_path',         $cookiePath);
		$this->setConfigItem('default', 'cookie_domain',       $cookieDomain);
		$this->setConfigItem('default', 'cookie_secure',       $cookieSecure);
		
		$this->rewriteConfig();
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_config_updated']);
	}
	
	/**
	 * Changes misc configuration settings.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function changeMisc()
	{
		$tpl    = &singleton('template');
		$config = $this->getOrigConfig();
		
		$wikiTitle    = isset($this->post['wiki_title'])    ? $this->post['wiki_title']    : $config['default']['wiki_title'];
		$defaultPage  = isset($this->post['default_page'])  ? $this->post['default_page']  : $config['default']['default_page'];
		$defaultLang  = isset($this->post['default_lang'])  ? $this->post['default_lang']  : $config['default']['default_lang'];
		$defaultTheme = isset($this->post['default_theme']) ? $this->post['default_theme'] : $config['default']['default_theme'];
		$dateFormat   = isset($this->post['date_format'])   ? $this->post['date_format']   : $config['default']['date_format'];
		
		if(!isset($config['languages'][$defaultLang])) {
			$defaultLang = $config['default']['default_lang'];
		}
		if(!isset($config['themes'][$defaultTheme])) {
			$defaultTheme = $config['default']['default_theme'];
		}
		
		if(!preg_match('/^'.$config['default']['title_format'].'$/', $defaultPage)) {
			$tpl->assign('isError', true);
			$tpl->assign('errors',  array($this->lang['admin_config_invalid_page']));
			return;
		}
		
		$this->setConfigItem('default', 'wiki_title',    $wikiTitle);
		$this->setConfigItem('default', 'default_page',  $defaultPage);
		$this->setConfigItem('default', 'default_lang',  $defaultLang);
		$this->setConfigItem('default', 'default_theme', $defaultTheme);
		$this->setConfigItem('default', 'date_format',   $dateFormat);
		
		$this->rewriteConfig();
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_config_updated']);
	}
	
	/**
	 * Changes the mailing settings.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function changeMailing()
	{
		$tpl    = &singleton('template');
		$config = $this->getOrigConfig();
		
		$mailFrom = isset($this->post['mail_from']) ? $this->post['mail_from'] : $config['default']['mail_from'];
		$mailName = isset($this->post['mail_name']) ? $this->post['mail_name'] : $config['default']['mailer_from'];
		
		$this->setConfigItem('default', 'mail_from',   $mailFrom);
		$this->setConfigItem('default', 'mailer_from', $mailName);
		
		$this->rewriteConfig();
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_config_updated']);
	}
	
	/**
	 * Change default settings for namespaces.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function changeNamespaces()
	{
		$tpl    = &singleton('template');
		$config = $this->getOrigConfig();
		
		$defaultNamespace = isset($this->post['default_namespace']) ? $this->post['default_namespace'] : $config['default']['default_namespace'];
		$specialNamespace = isset($this->post['special_namespace']) ? $this->post['special_namespace'] : $config['default']['special_namespace'];
		$userNamespace    = isset($this->post['user_namespace'])    ? $this->post['user_namespace']    : $config['default']['users_namespace'];
		
		if(!in_array($defaultNamespace, $config['namespaces'])) {
			$defaultNamespace = $config['default']['default_namespace'];
		}
		if(!in_array($specialNamespace, $config['namespaces'])) {
			$specialNamespace = $config['default']['special_namespace'];
		}
		if(!in_array($userNamespace, $config['namespaces'])) {
			$userNamespace = $config['default']['users_namespace'];
		}
		
		$this->setConfigItem('default', 'default_namespace', $defaultNamespace);
		$this->setConfigItem('default', 'special_namespace', $specialNamespace);
		$this->setConfigItem('default', 'users_namespace',   $userNamespace);
		
		$this->rewriteConfig();
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_config_updated']);
	}
	
	/**
	 * Change file upload settings.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function changeUploads()
	{
		$tpl    = &singleton('template');
		$config = $this->getOrigConfig();
		
		$enableUploads = isset($this->post['enable_uploads']) ? 1 : 0;
		$uploadTypes   = isset($this->post['upload_types'])   ? $this->post['upload_types']        : $config['default']['upload_file_types'];
		$uploadSize    = isset($this->post['upload_size'])    ? intval($this->post['upload_size']) : $config['default']['upload_max_size'];
		$uploadList    = isset($this->post['upload_list'])    ? 1 : 0;
		
		$this->setConfigItem('default', 'enable_uploads',      $enableUploads);
		$this->setConfigItem('default', 'upload_file_types',   str_replace(' ', '', $uploadTypes));
		$this->setConfigItem('default', 'upload_max_size',     $uploadSize);
		$this->setConfigItem('default', 'upload_display_list', $uploadList);
		
		$this->rewriteConfig();
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_config_updated']);
	}
	
	/**
	 * Changes the settins for url url rewriting.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function changeUrlRewrite()
	{
		$tpl    = &singleton('template');
		$config = $this->getOrigConfig();
		
		$enableRewrite  = isset($this->post['enable_rewrite'])   ? 1                               : 0;
		$urlFormat      = isset($this->post['url_format'])       ? $this->post['url_format']       : $config['default']['url_format'];
		$urlFormatShort = isset($this->post['url_format_short']) ? $this->post['url_format_short'] : $config['default']['url_format_short'];
		$match          = isset($this->post['rewrite_match'])    ? $this->post['rewrite_match']    : $config['default']['rewrite_match'];
		$replace        = isset($this->post['rewrite_replace'])  ? $this->post['rewrite_replace']  : $config['default']['rewrite_replace'];
		
		// If the user disabled url rewriting we delete the .htaccess file
		// and update the config switch. If the user enabled url rewriting
		// we generate a new htaccess file and write it into the doc_root
		// of this Wiki.
		if($enableRewrite == 0 && $config['default']['enable_url_rewriting'] == 1) {
			unlink($this->cfg['doc_root'].'/.htaccess');
			$this->setConfigItem('default', 'enable_url_rewriting', "0");
		} elseif($enableRewrite == 1 && $config['default']['enable_url_rewriting'] == 0) {
			$htaccess  = '# Auto-generated htaccess file. Generated '.$this->convertTime($this->time).'.'."\n\n";
			$htaccess .= 'DirectoryIndex '.basename($this->server['PHP_SELF'])."\n";
			$htaccess .= 'RewriteEngine On'."\n";
			$htaccess .= 'RewriteRule '.$match.' '.$replace;
			
			if(!($fp = @fopen($this->cfg['doc_root'].'/.htaccess', 'w'))) {
				$tpl->assign('isMessage', true);
				$tpl->assign('message',   $this->lang['admin_config_htaccess_unwriteable']);
			} else {
				fputs($fp, $htaccess);
				fclose($fp);
				$this->setConfigItem('default', 'enable_url_rewriting', "1");
			}
		}
		
		// If the user changed one of the url formats it will be saved as 
		// the new url format. If he didn't changed one but the enable_url_rewriting
		// setting we generate default url format strings.
		if($urlFormat != $config['default']['url_format']) {
			$this->setConfigItem('default', 'url_format', $urlFormat);
		} else {
			if($enableRewrite == 0 && $config['default']['enable_url_rewriting'] == 1) {
				$urlFormat      = $this->cfg['url_root'].'/'.basename($this->server['PHP_SELF']).'?page=%1$s&action=%2$s';
				$this->setConfigItem('default', 'url_format',       $urlFormat);
			} elseif($enableRewrite == 1 && $config['default']['enable_url_rewriting'] == 0) {
				$urlFormat      = $this->cfg['url_root'].'/%1$s?action=%2$s';
				$this->setConfigItem('default', 'url_format',       $urlFormat);
			}
		}
		
		if($urlFormatShort != $config['default']['url_format_short']) {
			$this->setConfigItem('default', 'url_format_short', $urlFormatShort);
		} else {
			if($enableRewrite == 0 && $config['default']['enable_url_rewriting'] == 1) {
				$urlFormatShort = $this->cfg['url_root'].'/'.basename($this->server['PHP_SELF']).'?page=%1$s';
				$this->setConfigItem('default', 'url_format_short', $urlFormatShort);
			} elseif($enableRewrite == 1 && $config['default']['enable_url_rewriting'] == 0) {
				$urlFormatShort = $this->cfg['url_root'].'/%1$s';
				$this->setConfigItem('default', 'url_format_short', $urlFormatShort);
			}
		}
		
		$this->setConfigItem('default', 'rewrite_rule_match',   $match);
		$this->setConfigItem('default', 'rewrite_rule_replace', $replace);
		
		$this->rewriteConfig();
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_config_updated']);
	}
	
	/**
	 * Changes user interface related configuration settings.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function changeUi()
	{
		$tpl    = &singleton('template');
		$config = $this->getOrigConfig();
		
		$defaultLang     = isset($this->post['default_lang'])     ? $this->post['default_lang']     : $config['default']['default_lang'];
		$defaultTheme    = isset($this->post['default_theme'])    ? $this->post['default_theme']    : $config['default']['default_theme'];
		$dateFormat      = isset($this->post['date_format'])      ? $this->post['date_format']      : $config['default']['date_format'];
		$teaserLength    = isset($this->post['teaser_length'])    ? $this->post['teaser_length']    : $config['default']['teaser_length'];
		$itemsPP         = isset($this->post['items_pp'])         ? $this->post['items_pp']         : $config['default']['items_per_page'];
		$summaryLength   = isset($this->post['summary_length'])   ? $this->post['summary_length']   : $config['default']['max_summary_length'];
		$dblclickEditing = isset($this->post['dblclick_editing']) ? 1                               : 0;
		
		if(!isset($config['languages'][$defaultLang])) {
			$defaultLang = $config['default']['default_lang'];
		}
		if(!isset($config['themes'][$defaultTheme])) {
			$defaultTheme = $config['default']['default_theme'];
		}
		
		$this->setConfigItem('default', 'default_lang',       $defaultLang);
		$this->setConfigItem('default', 'default_theme',      $defaultTheme);
		$this->setConfigItem('default', 'date_format',        $dateFormat);
		$this->setConfigItem('default', 'teaser_length',      $teaserLength);
		$this->setConfigItem('default', 'items_per_page',     $itemsPP);
		$this->setConfigItem('default', 'max_summary_length', $summaryLength);
		$this->setConfigItem('default', 'dblclick_editing',   $dblclickEditing);
		
		$this->rewriteConfig();
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_config_updated']);
	}
	
	/**
	 * Changes the parser settings.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function changeParser()
	{
		$tpl    = &singleton('template');
		$config = $this->getOrigConfig();
		
		$maxIncludes    = isset($this->post['max_includes'])    ? $this->post['max_includes']   : $config['default']['max_includes'];
		$indentWidth    = isset($this->post['indent_width'])    ? $this->post['indent_width']   : $config['default']['indent_width'];
		$linkNum        = isset($this->post['link_num'])        ? $this->post['link_num']       : $config['default']['link_num'];
		$titleFormat    = isset($this->post['title_format'])    ? $this->post['title_format']   : $config['default']['title_format'];
		$titleFormatS   = isset($this->post['title_format_s'])  ? $this->post['title_format_s'] : $config['default']['title_format_search'];
		$thisPage       = isset($this->post['thispage'])        ? $this->post['thispage']       : $config['default']['thispage_interwiki'];
		$thisWiki       = isset($this->post['thiswiki'])        ? $this->post['thiswiki']       : $config['default']['thiswiki_interwiki'];
		$spaceWords     = isset($this->post['space_words'])     ? 1                             : 0;
		$displayNSpaces = isset($this->post['display_nspaces']) ? 1                             : 0;
		$autoLink       = isset($this->post['auto_link'])       ? 1                             : 0;
		$linkSelf       = isset($this->post['link_self'])       ? 1                             : 0;
		
		$this->setConfigItem('default', 'max_includes',        $maxIncludes);
		$this->setConfigItem('default', 'indent_width',        $indentWidth);
		$this->setConfigItem('default', 'link_num',            $linkNum);
		$this->setConfigItem('default', 'title_format',        $titleFormat);
		$this->setConfigItem('default', 'title_format_search', $titleFormatS);
		$this->setConfigItem('default', 'space_wiki_words',    $spaceWords);
		$this->setConfigItem('default', 'display_namespaces',  $displayNSpaces);
		$this->setConfigItem('default', 'auto_link',           $autoLink);
		$this->setConfigItem('default', 'link_self',           $linkSelf);
		$this->setConfigItem('default', 'thispage_interwiki',  $thisPage);
		$this->setConfigItem('default', 'thiswiki_interwiki',  $thisWiki);
		
		$this->rewriteConfig();
		
		$tpl->assign('isMessage', true);
		$tpl->assign('message',   $this->lang['admin_config_updated']);
	}
	
	/**
	 * Sets the default values for the config form elements.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function setFormValues()
	{
		$config = $this->getOrigConfig();
		$tpl    = &singleton('template');
		$db     = &singleton('database');
		
		$tpl->assign('cfgUrlRoot',          htmlentities($config['default']['url_root']));
		$tpl->assign('cfgDocRoot',          htmlentities($config['default']['doc_root']));
		$tpl->assign('cfgActionsDir',       htmlentities($config['default']['actions_dir']));
		$tpl->assign('cfgLangDir',          htmlentities($config['default']['lang_dir']));
		$tpl->assign('cfgLibDir',           htmlentities($config['default']['lib_dir']));
		$tpl->assign('cfgSpecialDir',       htmlentities($config['default']['special_dir']));
		$tpl->assign('cfgPluginsDir',       htmlentities($config['default']['plugins_dir']));
		$tpl->assign('cfgThemesDir',        htmlentities($config['default']['themes_dir']));
		$tpl->assign('userGroups',          $this->configUserGroups);
		$tpl->assign('cfgDefaultGGroup',    $config['default']['default_guest_group']);
		$tpl->assign('cfgDefaultUGroup',    $config['default']['default_user_group']);
		$tpl->assign('cfgMinPwLength',      $config['default']['min_password_length']);
		$tpl->assign('cfgMinUserLength',    $config['default']['min_username_length']);
		$tpl->assign('cfgMaxUserLength',    $config['default']['max_username_length']);
		$tpl->assign('cfgSessLifetime',     $config['default']['session_lifetime']);
		$tpl->assign('cfgCookiePrefix',     $config['default']['cookie_prefix']);
		$tpl->assign('cfgCookiePath',       $config['default']['cookie_path']);
		$tpl->assign('cfgCookieDomain',     $config['default']['cookie_domain']);
		$tpl->assign('cfgCookieSecure',     $config['default']['cookie_secure']);
		$tpl->assign('cfgWikiTitle',        htmlentities($config['default']['wiki_title']));
		$tpl->assign('cfgDefaultPage',      htmlentities($config['default']['default_page']));
		$tpl->assign('cfgLangs',            $config['languages']);
		$tpl->assign('cfgDefaultLang',      htmlentities($config['default']['default_lang']));
		$tpl->assign('cfgThemes',           $config['themes']);
		$tpl->assign('cfgDefaultTheme',     htmlentities($config['default']['default_theme']));
		$tpl->assign('cfgDateFormat',       htmlentities($config['default']['date_format']));
		$tpl->assign('cfgTeaserLength',     $config['default']['teaser_length']);
		$tpl->assign('cfgItemsPP',          $config['default']['items_per_page']);
		$tpl->assign('cfgSummaryLength',    $config['default']['max_summary_length']);
		$tpl->assign('cfgDblclickEditing',  $config['default']['dblclick_editing']);
		$tpl->assign('cfgMailFrom',         htmlentities($config['default']['mail_from']));
		$tpl->assign('cfgMailName',         htmlentities($config['default']['mailer_from']));
		$tpl->assign('cfgNamespaces',       $config['namespaces']);
		$tpl->assign('cfgDefaultNamespace', $config['default']['default_namespace']);
		$tpl->assign('cfgSpecialNamespace', $config['default']['special_namespace']);
		$tpl->assign('cfgUserNamespace',    $config['default']['users_namespace']);
		$tpl->assign('cfgEnableUrlRewrite', $config['default']['enable_url_rewriting']);
		$tpl->assign('cfgUrlFormat',        htmlentities($config['default']['url_format']));
		$tpl->assign('cfgUrlFormatShort',   htmlentities($config['default']['url_format_short']));
		$tpl->assign('cfgRewriteMatch',     htmlentities($config['default']['rewrite_rule_match']));
		$tpl->assign('cfgRewriteReplace',   htmlentities($config['default']['rewrite_rule_replace']));
		$tpl->assign('cfgMaxIncludes',      $config['default']['max_includes']);
		$tpl->assign('cfgIndentWidth',      $config['default']['indent_width']);
		$tpl->assign('cfgLinkNum',          $config['default']['link_num']);
		$tpl->assign('cfgTitleFormat',      htmlentities($config['default']['title_format']));
		$tpl->assign('cfgTitleFormatS',     htmlentities($config['default']['title_format_search']));
		$tpl->assign('cfgSpaceWords',       $config['default']['space_wiki_words']);
		$tpl->assign('cfgDisplayNSpaces',   $config['default']['display_namespaces']);
		$tpl->assign('cfgAutoLink',         $config['default']['auto_link']);
		$tpl->assign('cfgLinkSelf',         $config['default']['link_self']);
		$tpl->assign('cfgThisPage',         $config['default']['thispage_interwiki']);
		$tpl->assign('cfgThisWiki',         $config['default']['thiswiki_interwiki']);
		$tpl->assign('cfgEnableUploads',    $config['default']['enable_uploads']);
		$tpl->assign('cfgUploadTypes',      $config['default']['upload_file_types']);
		$tpl->assign('cfgUploadSize',       $config['default']['upload_max_size']);
		$tpl->assign('cfgUploadList',       $config['default']['upload_display_list']);
	}
	
	/**
	 * Returns the template name for this special page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return 'admin_config.tpl';
	}
}
?>