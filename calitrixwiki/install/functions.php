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
 * Prints the html header of the installer.
 **/
function printHeader()
{
	global $lang;
	
	?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" lang="de">
 <head>
  <title><?PHP echo $lang['title'] ?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
  <link rel="stylesheet" href="html/style.css" />
</head>

<body>

<div id="wrapper">
 <div id="header">
  <img src="html/logo.gif" alt="CalitrixWiki" id="logo" />
 </div>
<div id="content"><?PHP
}

/**
 * Prints the html footer of the installer.
 **/
function printFooter()
{
	global $lang;
	
	echo '</div></body></html>';
}

/**
 * Prints a link to the next installation step.
 **/
function printNextLink()
{
	global $lang;
	
	echo '<div style="text-align:right;margin:10px;font-weight:bold">';
	echo '<a href="'.$_SERVER['PHP_SELF'].'?step='.(INSTALLER_STEP + 1).'&amp;lang='.INSTALLER_LANG.'">'.$lang['step'.(INSTALLER_STEP + 1)].'</a>';
	echo '</div>';
}

/**
 * Checks the requirements in the first step.
 **/
function checkRequirements()
{
	global $lang, $langs;
	
	$success = true;
	
	echo $lang['welcome'].'<br /><br />';
	echo '<form method="get" action="'.$_SERVER['PHP_SELF'].'">';
	echo $lang['lang'].': <select name="lang">';
	foreach($langs as $code => $name)
	{
		echo '<option value="'.$code.'">'.$name.'</option>';
	}
	echo '</select>&nbsp;';
	echo '<input type="submit" value="'.$lang['lang_submit'].'">';
	echo '</form><br />';
	
	echo $lang['check'].'<br />';
	echo '<table cellspacing="0" cellpadding="0" border="0" width="100%" style="border-top:1px #ababab solid;border-bottom:1px #ababab solid;margin-top:10px;margin-bottom:10px;">';
	echo '<tr><td>'.$lang['check_version'].'</td><td>';
	
	if(version_compare('4.3.0', phpversion(), '>')) {
		echo $lang['failed'];
		$success = false;
	} else {
		echo $lang['passed'];
	}
	
	echo '</td></tr><tr><td>'.$lang['check_mysql'].'</td><td>';
	
	if(extension_loaded('mysql')) {
		echo $lang['passed'];
	} else {
		echo $lang['failed'];
		$success = false;
	}
	
	echo '</td></tr><tr><td>'.$lang['check_config_writeable'].'</td><td>';
		
	if(is_writeable('../settings')) {
		echo $lang['passed'];
	} else {
		echo $lang['failed'];
		$success = false;
	}
	
	echo '</td></tr><tr><td>'.$lang['check_installer_writeable'].'</td><td>';
	
	if(is_writeable('.')) {
		echo $lang['passed'];
	} else {
		echo $lang['failed'];
		$success = false;
	}
	
	echo '</td></tr></table>';
	
	if($success) {
		echo $lang['check_success'];
	} else {
		echo $lang['check_failed'];
	}
	
	return $success;
}

/**
 * Displays the license
 **/
function displayLicense()
{
	global $lang;
	
	echo $lang['license'].'<br /><br />';
	echo '<form>';
	echo '<textarea name="license" rows="20" style="width:630px;padding:5px;">';
	
	if(!file_exists('license.txt')) {
		echo $lang['nolicense'];
	} else {
		echo join('', file('license.txt'));
	}
	echo '</textarea></form>';
}

/**
 * Displays the form with the basic settings.
 **/
function displaySettings()
{
	global $lang;
	
	$urlRoot = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
	$urlRoot = substr($urlRoot, 0, strlen($urlRoot) - 8);
	$docRoot = dirname($_SERVER['SCRIPT_FILENAME']);
	$docRoot = substr($docRoot, 0, strlen($docRoot) - 8);
	
	echo $lang['settings'].'<br /><br />';
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?step='.INSTALLER_STEP.'&amp;lang='.INSTALLER_LANG.'">';
	echo $lang['set_url_root'].'<br />';
	echo '<input type="text" name="url_root" size="65" value="'.$urlRoot.'">';
	echo '<br /><br />';
	echo $lang['set_doc_root'].'<br />';
	echo '<input type="text" name="doc_root" size="65" value="'.$docRoot.'">';
	echo '<br /><br />';
	echo $lang['set_paths'];
	echo '<br><br />';
	echo $lang['set_actions_dir'].'<br />';
	echo '<input type="text" name="actions_dir" size="65" value="'.$docRoot.'/actions">';
	echo '<br /><br />';
	echo $lang['set_lang_dir'].'<br />';
	echo '<input type="text" name="lang_dir" size="65" value="'.$docRoot.'/lang">';
	echo '<br /><br />';
	echo $lang['set_lib_dir'].'<br />';
	echo '<input type="text" name="lib_dir" size="65" value="'.$docRoot.'/libs">';
	echo '<br /><br />';
	echo $lang['set_special_dir'].'<br />';
	echo '<input type="text" name="special_dir" size="65" value="'.$docRoot.'/specialpages">';
	echo '<br /><br />';
	echo $lang['set_plugins_dir'].'<br />';
	echo '<input type="text" name="plugins_dir" size="65" value="'.$docRoot.'/plugins">';
	echo '<br /><br />';
	echo $lang['set_themes_dir'].'<br />';
	echo '<input type="text" name="themes_dir" size="65" value="'.$docRoot.'/themes">';
	echo '<br /><br />';
	echo '<input type="submit" value="'.$lang['submit_settings'].'">';
}

/**
 * Saves the basic settings and writes the config file.
 **/
function saveSettings()
{
	global $lang;
	
	if(get_magic_quotes_gpc()) {
		prepareGPCData($_POST);
	}
	
	$configTemplate = join('', file(CONFIG_TEMPLATE));
	
	$urlRoot    = $_POST['url_root'];
	$docRoot    = $_POST['doc_root'];
	$actionsDir = $_POST['actions_dir'];
	$langDir    = $_POST['lang_dir'];
	$libDir     = $_POST['lib_dir'];
	$specialDir = $_POST['special_dir'];
	$pluginsDir = $_POST['plugins_dir'];
	$themesDir  = $_POST['themes_dir'];
	
	if(!is_dir($docRoot)) {
		echo sprintf($lang['set_warning'], $docRoot).'<br />';
	}
	if(!is_dir($actionsDir)) {
		echo sprintf($lang['set_warning'], $actionsDir).'<br />';
	}
	if(!is_dir($langDir)) {
		echo sprintf($lang['set_warning'], $langDir).'<br />';
	}
	if(!is_dir($libDir)) {
		echo sprintf($lang['set_warning'], $libDir).'<br />';
	}
	if(!is_dir($specialDir)) {
		echo sprintf($lang['set_warning'], $specialDir).'<br />';
	}
	if(!is_dir($pluginsDir)) {
		echo sprintf($lang['set_warning'], $pluginsDir).'<br />';
	}
	if(!is_dir($themesDir)) {
		echo sprintf($lang['set_warning'], $themesDir).'<br />';
	}
	
	$configTemplate = str_replace('%URLROOT%', $urlRoot, $configTemplate);
	$configTemplate = str_replace('%DOCROOT%', $docRoot, $configTemplate);
	$configTemplate = str_replace('%ACTIONSDIR%', $actionsDir, $configTemplate);
	$configTemplate = str_replace('%LANGDIR%', $langDir, $configTemplate);
	$configTemplate = str_replace('%LIBDIR%', $libDir, $configTemplate);
	$configTemplate = str_replace('%SPECIALDIR%', $specialDir, $configTemplate);
	$configTemplate = str_replace('%PLUGINSDIR%', $pluginsDir, $configTemplate);
	$configTemplate = str_replace('%THEMESDIR%', $themesDir, $configTemplate);
	$configTemplate = str_replace('%DEFAULTLANG%', INSTALLER_LANG, $configTemplate);
	
	$fp = fopen(CONFIG_TARGET, 'w') or installError($lang['set_cant_write']);
	fputs($fp, $configTemplate);
	fclose($fp);
	
	echo '<br />'.$lang['set_written'];
}

function displayDbSettings()
{
	global $lang;
	
	echo $lang['database'].'<br /><br />';
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?step='.INSTALLER_STEP.'&amp;lang='.INSTALLER_LANG.'">';
	echo $lang['db_host'].'<br />';
	echo '<input type="text" name="db_host" size="65" value="localhost">';
	echo '<br /><br />';
	echo $lang['db_name'].'<br />';
	echo '<input type="text" name="db_name" size="65" value="calitrixwiki">';
	echo '<br /><br />';
	echo $lang['db_user'].'<br />';
	echo '<input type="text" name="db_user" size="65" value="root">';
	echo '<br /><br />';
	echo $lang['db_pass'].'<br />';
	echo '<input type="password" name="db_pass" size="65" value="">';
	echo '<br /><br />';
	echo $lang['db_prefix'].'<br />';
	echo '<input type="text" name="db_prefix" size="65" value="cwiki_">';
	echo '<br /><br />';
	echo '<input type="checkbox" name="db_create" id="dbcreate"><label for="dbcreate">'.$lang['db_create'].'</label>';
	echo '<br /><br />';
	echo '<input type="submit" value="'.$lang['db_submit'].'">';
}

function writeDbConfig()
{
	global $lang;
	
	$dbHost   = isset($_POST['db_host'])   ? $_POST['db_host']   : '';
	$dbName   = isset($_POST['db_name'])   ? $_POST['db_name']   : '';
	$dbUser   = isset($_POST['db_user'])   ? $_POST['db_user']   : '';
	$dbPass   = isset($_POST['db_pass'])   ? $_POST['db_pass']   : '';
	$dbPrefix = isset($_POST['db_prefix']) ? $_POST['db_prefix'] : '';
	
	$dbCreate = isset($_POST['db_create'])? true : false;
	
	if($dbHost == '') {
		echo $lang['db_need_host'].'<br />';
		return false;
	}
	if($dbName == '') {
		echo $lang['db_need_name'].'<br />';
		return false;
	}
	
	$success = true;
	
	echo '<br /><table cellspacing="0" cellpadding="0" border="0" width="100%" style="border-top:1px #ababab solid;border-bottom:1px #ababab solid;margin-top:10px;margin-bottom:10px;"><tr><td>'.$lang['db_trying_host'].'</td><td>';
	
	if(@mysql_connect($dbHost, $dbUser, $dbPass)) {
		echo $lang['passed'];
	} else {
		echo $lang['failed'];
		echo '</td></tr><tr><td style="padding-left:20px">'.$lang['error'].': '.mysql_error().'</td><td>'.$lang['failed'];
		$success = false;
	}
	
	if($success) {
		echo '</td></tr><tr><td>'.$lang['db_trying_db'].'</td><td>';
	 	if(@mysql_select_db($dbName)) {
			echo $lang['passed'];
		} else {
			echo $lang['failed'];
			
			if($dbCreate) {
				echo '</td></tr><tr><td>'.$lang['db_trying_create'].'</td><td>';
				if(mysql_query('CREATE DATABASE '.addslashes($dbName))) {
					@mysql_select_db($dbName);
					echo $lang['passed'];
				} else {
					echo $lang['failed'];
					echo '</td></tr><tr><td style="padding-left:20px">'.$lang['error'].': '.mysql_error().'</td><td>'.$lang['failed'];
					$success = false;
				}
			} else {
				$success = false;
			}
		}
	}
	
	echo '</td></tr></table>';
	
	if($success) {
		echo $lang['db_config_ok'].'<br />';
	} else {
		echo $lang['db_config_failed'].'<br />';
		return false;
	}
	
	$configTemplate = join('', file(DBCONFIG_TEMPLATE));
	$configTemplate = str_replace('%DBHOST%',   $dbHost, $configTemplate);
	$configTemplate = str_replace('%DBNAME%',   $dbName, $configTemplate);
	$configTemplate = str_replace('%DBUSER%',   $dbUser, $configTemplate);
	$configTemplate = str_replace('%DBPASS%',   $dbPass, $configTemplate);
	$configTemplate = str_replace('%DBPREFIX%', $dbPrefix, $configTemplate);
	
	$fp = fopen(DBCONFIG_TARGET, 'w') or installError($lang['db_write_failed']);
	fputs($fp, $configTemplate);
	fclose($fp);
	
	echo $lang['db_config_written'];
	
	return true;
}

function createDatabase()
{
	global $lang;
	
	$sqlFile = @file(DATABASE_FILE);
	$sql     = '';
	$success = true;
	
	if(!$sqlFile) {
		$success = false;
		$sqlFile = array();
	}
	
	echo '<br /><table cellspacing="0" cellpadding="0" border="0" width="100%" style="border-top:1px #ababab solid;border-bottom:1px #ababab solid;margin-top:10px;margin-bottom:10px;"><tr><td>'.$lang['db_creating'].'</td><td>';
	
	foreach($sqlFile as $line)
	{
		$line = trim($line);
		
		if($line == '' || $line[0] == '#') {
			continue;
		}
		
		$sql .= $line;
		if(substr($line, strlen($line) - 1, strlen($line)) == ';') {
			if(mysql_query($sql) === false) {
				echo $lang['failed'];
				echo '</td></tr><tr><td style="padding-left:20px">'.$lang['error'].': '.mysql_error().'</td><td>'.$lang['failed'];
				$success = false;
				break;
			}
			
			$sql = '';
		}
	}
	
	if($success) {
		echo $lang['passed'];
		echo '</td></tr></table>';
		echo $lang['db_success'];
	} else {
		echo '</td></tr></table>';
	}
	
	return $success;
}

function displayAdminForm()
{
	global $lang;
	
	echo $lang['admin'].'<br /><br />';
	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'?step='.INSTALLER_STEP.'&amp;lang='.INSTALLER_LANG.'">';
	echo $lang['admin_name'].'<br />';
	echo '<input type="text" name="admin_name" size="65" value="">';
	echo '<br /><br />';
	echo $lang['admin_email'].'<br />';
	echo '<input type="text" name="admin_email" size="65" value="">';
	echo '<br /><br />';
	echo $lang['admin_pass'].'<br />';
	echo '<input type="password" name="admin_pass" size="65" value="">';
	echo '<br /><br />';
	echo $lang['admin_confirm'].'<br />';
	echo '<input type="password" name="admin_conf" size="65" value="">';
	echo '<br /><br />';
	echo '<input type="submit" value="'.$lang['admin_submit'].'">';
}

function createAdmin()
{
	global $lang;
	
	include CONFIG_TARGET;
	include DBCONFIG_TARGET;
	
	$adminName  = isset($_POST['admin_name'])  ? $_POST['admin_name']  : '';
	$adminEmail = isset($_POST['admin_email']) ? $_POST['admin_email'] : '';
	$adminPass  = isset($_POST['admin_pass'])  ? $_POST['admin_pass']  : '';
	$adminConf  = isset($_POST['admin_conf'])  ? $_POST['admin_conf']  : '';
	$success    = true;
	
	if(!preg_match('/^'.$cfg['title_format'].'$/', $cfg['users_namespace'].':'.$adminName)) {
		echo $lang['admin_invalid_name'].'<br />';
		$success = false;
	}
	
	if(!preg_match($cfg['match_email'], $adminEmail)) {
		echo $lang['admin_invalid_email'].'<br />';
		$success = false;
	}
	
	if(strlen($adminPass) < $cfg['min_password_length']) {
		echo sprintf($lang['admin_password_short'], $cfg['min_password_length']).'<br />';
		$success = false;
	}
	
	if($adminPass != $adminConf) {
		echo $lang['admin_passwords_dont_match'].'<br />';
		$success = false;
	}
	
	if(!$success) {
		echo $lang['admin_failed'];
		return false;
	}
	
	echo '<br />';
	
	mysql_connect(DB_HOST, DB_USER, DB_PASS);
	mysql_select_db(DB_NAME);
	
	$sql = 'INSERT INTO '.DB_PREFIX.'users(user_group_id, user_name, user_password, user_email, '.
	'user_reg_time, user_last_visit) VALUES('.DEFAULT_ADMINGROUP.', "'.addslashes($adminName).'", "'.sha1($adminPass).'", '.
	'"'.addslashes($adminEmail).'", '.time().', '.time().')';
	
	if(mysql_query($sql)) {
		echo sprintf($lang['admin_created'], $cfg['url_root'], htmlentities($adminName), $cfg['url_root'].'/admin/');
	} else {
		echo $lang['admin_create_failed'].'<br />'.$lang['error'].': '.mysql_error();
	}
}

/**
 * Remove magic slashes from gpc data.
 **/
function prepareGPCData(&$var)
{
	if(is_array($var)) {
		while(list($key, $val) = each($var))
		{
			$var[$key] = prepareGPCData($val);
		}
	} else {
		$var = stripslashes($var);
	}
	
	return $var;
}

/**
 * Outputs an installation error and exits.
 **/
function installError($errorMessage)
{
	echo $errorMessage;
	printFooter();
	exit;
}
?>