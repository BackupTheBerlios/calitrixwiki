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

define('CWIKI_INSTALL_DIR',  dirname($_SERVER['SCRIPT_FILENAME']));
define('CWIKI_INSTALL_URL',  'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']));
define('CWIKI_STEPS_DIR',    CWIKI_INSTALL_DIR.'/steps');
define('CWIKI_DEFAULT_STEP', 'requirements');
define('CWIKI_DOC_ROOT',     realpath(CWIKI_INSTALL_DIR.'/..'));
define('CWIKI_LIB_DIR',      CWIKI_DOC_ROOT.'/libs');
define('CWIKI_SET_DIR',      CWIKI_DOC_ROOT.'/settings');
define('CWIKI_DEFAULT_LANG', 'de');
define('CWIKI_ADMIN_GROUP',  3);

$languages = array('de' => 'Deutsch');

if(file_exists(CWIKI_INSTALL_DIR.'/install.lock')) {
	die('Remove the installation lock file if you need to use the installer again.');
}

include CWIKI_INSTALL_DIR.'/libs/class_installer.php';
include CWIKI_INSTALL_DIR.'/libs/class_template.php';
include CWIKI_LIB_DIR.'/lib_instances.php';

$step = isset($_GET['step']) ? $_GET['step'] : CWIKI_DEFAULT_STEP;
$file = CWIKI_STEPS_DIR.'/installer_'.$step.'.php';

if(!ctype_alnum($step) || !file_exists($file)) {
	$step = CWIKI_DEFAULT_STEP;
	$file = CWIKI_STEPS_DIR.'/installer_'.$step.'.php';
}

$class = 'installer_'.$step;

include $file;
$installer = new $class($step);
$installer->assignTplVars();
$installer->display();
?>