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
error_reporting(E_ALL);
include 'functions.php';

define('LICENSE_FILE',       'license.txt');
define('REQUIRE_PHPVERSION', '4.3.0');
define('DATABASE_FILE',      'database.sql');
define('SETTINGS_DIR',       '../settings');
define('CONFIG_TEMPLATE',    'stdconfig.tpl');
define('CONFIG_TARGET',      SETTINGS_DIR.'/stdconfig.php');
define('DBCONFIG_TEMPLATE',  'dbconfig.tpl');
define('DBCONFIG_TARGET',    SETTINGS_DIR.'/dbconfig.php');
define('DEFAULT_ADMINGROUP', 3);

$langs = array('en' => 'English', 'de' => 'Deutsch');

if(isset($_GET['lang']) && isset($langs[$_GET['lang']])) {
	include 'lang/'.$_GET['lang'].'.php';
	define('INSTALLER_LANG', $_GET['lang']);
} else {
	include 'lang/en.php';
	define('INSTALLER_LANG', 'en');
}

if(isset($_GET['step']) && intval($_GET['step']) > 0 && intval($_GET['step']) < 5) {
	define('INSTALLER_STEP', intval($_GET['step']));
} else {
	define('INSTALLER_STEP', 0);
}

printHeader();

if(INSTALLER_STEP == 0) {
	if(checkRequirements()) {
		printNextLink();
	}
} elseif(INSTALLER_STEP == 1) {
	displayLicense();
	printNextLink();
} elseif(INSTALLER_STEP == 2) {
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		saveSettings();
		printNextLink();
	} else {
		displaySettings();
	}
} elseif(INSTALLER_STEP == 3) {
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		if(writeDbConfig() && createDatabase()) {
			printNextLink();
		}
	} else {
		displayDbSettings();
	}
} elseif(INSTALLER_STEP == 4) {
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		createAdmin();
	} else {
		displayAdminForm();
	}
}

printFooter();
?>