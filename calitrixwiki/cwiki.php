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

$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];

/**
 * This is the main script. It processes all requests and loads the
 * requested actions. This file can be renamed but you also need to change
 * the file name in the .htaccess files (rewrite rules) and in your config 
 * file (if you have written it in some config values).
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
 
error_reporting(E_ALL);
set_error_handler('myHandler');

function myHandler($errNo, $errStr)
{
	echo $errNo.': '.$errStr.'<br />';
}

include 'settings/stdconfig.php';
include 'settings/dbconfig.php';
include $cfg['lib_dir'].'/class_database.php';
include $cfg['lib_dir'].'/class_template.php';
include $cfg['lib_dir'].'/class_parser.php';
include $cfg['lib_dir'].'/class_core.php';
include $cfg['lib_dir'].'/lib_instances.php';
include $cfg['lib_dir'].'/class_diff.php';

$page   = isset($_GET['page'])   ? $_GET['page']               : $cfg['default_page'];
$action = isset($_GET['action']) ? strtolower($_GET['action']) : $cfg['default_action'];

// Extract the namespace:WikiPage components from the page parameter.
// If the page parameter doesnt match, page name and namespace are set to false.
if(preg_match('/^'.$cfg['title_format'].'$/', $page, $match)) {
	$pageName  = $match[2];
	$namespace = substr($match[1], 0, strlen($match[1]) - 1);
} else {
	$pageName  = false;
	$namespace = false;
}

// If there is a prefix and if it is the specialpage prefix we check
// wether the requested specialpage exists.
if($namespace == $cfg['special_namespace']) {
	$file  = $cfg['special_dir'].'/special_'.strtolower($pageName).'.php';
	$class = 'special_'.strtolower($pageName);
	
	if(!file_exists($file)) {
		$pageName  = false;
		$namespace = false;
	}
} else {
	if($namespace != '' && !in_array($namespace, $cfg['namespaces'])) {
		$pageName  = false;
		$namespace = false;
	}
	
	if(!isset($cfg['actions'][$action])) {
		$action = $cfg['default_action'];
	}
	
	$file  = $cfg['actions_dir'].'/action_'.$cfg['actions'][$action].'.php';
	$class = 'action_'.$cfg['actions'][$action];
	$page['action'] = $action;
}

if($pageName === false) {
	$class = 'core';
} else {
	include $file;
}

// Now, create a instance of the specialpage/action and start
// processing of the page.
$wiki = new $class(array('page' => $pageName, 'namespace' => $namespace, 'action' => $action), $cfg);

$wiki->start();
$wiki->assignTplVars();
$template = $wiki->getTemplate();

$tpl = &singleton('template');
$tpl->display($template);

$wiki->end();

/**
 * Count the scripts execution time and make a small debug output
 **/
$endtime       = explode(' ', microtime());
$endtime       = $endtime[1] + $endtime[0];
$creationtime  = round($endtime - $starttime, 3);

$db = &singleton('database');

$steps         = $creationtime / 100;
$php_percent   = round(($creationtime - $db->runtimes) / $steps, 2);
$mysql_percent = round($db->runtimes / $steps, 2);

//echo "<div style=\"text-align:left\"><br><br><span style=\"font-size:11px\">\n[Runtime: $creationtime secs | {$db->queries} db querys in ".round($db->runtimes, 3)." secs | $php_percent% PHP | $mysql_percent% MySQL]";
//echo "<br><br>".$db->query_debug."</span></div>";
?>
