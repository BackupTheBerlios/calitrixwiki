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

$lang = array();
$lang['title']                      = 'CalitrixWiki installer';
$lang['lang']                       = 'Language';
$lang['lang_submit']                = 'Save';
$lang['step0']                      = 'Welcome';
$lang['step1']                      = 'Read license';
$lang['step2']                      = 'Accept and proceed to basic settings';
$lang['step3']                      = 'Create database';
$lang['step4']                      = 'Create a user account';
$lang['welcome']                    = 'Welcome to the CalitrixWiki installer. This installer will guide you through the rest of the installation which will only take a few minutes.';
$lang['passed']                     = '<span style="color:green">OK</span>';
$lang['failed']                     = '<span style="color:red">FAILED</span>';
$lang['invalid_step']               = 'Invalid installation step.';
$lang['error']                      = 'Error';
$lang['check']                      = 'First i will do a few checks:';
$lang['check_failed']               = 'The check failed. This means you cannot install and run this Software on your server without any problems. Make sure all requirements are satisfied.';
$lang['check_success']              = 'The check was successful. You may now read the license.';
$lang['check_version']              = 'PHP Version >= 4.3.x';
$lang['check_mysql']                = 'MySQL support enabled';
$lang['check_config_writeable']     = 'Config directory writeable';
$lang['check_installer_writeable']  = 'Installer directory writeable';
$lang['license']                    = 'You now have to read the license of this software. It is important for you to unterstand the freedom you have using this software and the few restrictions.';
$lang['nolicense']                  = 'Can not open license file.';
$lang['settings']                   = 'You may now check the basic settings needed to run the wiki. In most cases the installer sets them correctly. If you are unsure about what to enter here leave the default values.';
$lang['set_url_root']               = 'URL where your Wiki shall be accessed';
$lang['set_doc_root']               = 'Path to your Wiki in the servers file system.';
$lang['set_paths']                  = 'You can also set the directory where CalitrixWiki searches its files which do not need to be publicly accessable. If you are able to do so you may want to store these files in directories outside the public accessable areas.';
$lang['set_actions_dir']            = 'Directory where action classes are located';
$lang['set_lang_dir']               = 'Directory where the language files are located';
$lang['set_lib_dir']                = 'Directory of general wiki classes';
$lang['set_special_dir']            = 'Directory where the scripts for special pages are located';
$lang['set_plugins_dir']            = 'Directory where plugins are searched';
$lang['set_themes_dir']             = 'Directory where themes are searched';
$lang['set_warning']                = 'Warning: directory \'%s\' does not exist.';
$lang['submit_settings']            = 'Save settings';
$lang['set_written']                = 'Config file is written. You can now proceed to the database setup.';
$lang['set_cant_write']             = 'Can not write config file "%s".';
$lang['database']                   = 'Now provide the host of your MySQL server, the database name, user and password.';
$lang['db_host']                    = 'Database host';
$lang['db_name']                    = 'Database name';
$lang['db_user']                    = 'Database user';
$lang['db_pass']                    = 'Database password';
$lang['db_prefix']                  = 'Prefix for tables (change this if this is not the first CalitrixWiki in this database to avoid table name conflicts)';
$lang['db_create']                  = 'Attempt to create the database if it does not exist yet?';
$lang['db_submit']                  = 'Create database';
$lang['db_need_host']               = 'You must provide a database host';
$lang['db_need_name']               = 'You must provide a database name';
$lang['db_trying_host']             = 'Trying to connect to the database host ...';
$lang['db_trying_db']               = 'Trying to select the database ...';
$lang['db_trying_create']           = 'Trying to create the database ...';
$lang['db_config_ok']               = 'Database configuration ok';
$lang['db_config_failed']           = 'Database configuration failed. Make sure you entered the right values.';
$lang['db_config_written']          = 'Database configuration written';
$lang['db_config_write_failed']     = 'Failed to write database configuration. Make sure the configuration file is writeable.';
$lang['db_creating']                = 'Writting data to database ...';
$lang['db_success']                 = 'The database was setup successful. You may now proceed and create an administrator account.';
$lang['admin']                      = 'Now you need to create an user account which will get administrator privileges. This is the last step in this installation. If anything went right your Wiki is ready now.';
$lang['admin_name']                 = 'Administrator user name (must be a valid page title, usualy containing alphanumeric characters, numbers, underscores and/or dashes).';
$lang['admin_email']                = 'The admins email adress';
$lang['admin_pass']                 = 'Password';
$lang['admin_confirm']              = 'Confirm password';
$lang['admin_submit']               = 'Create account';
$lang['admin_invalid_name']         = 'This is not a valid user name.';
$lang['admin_invalid_email']        = 'This is not a valid email adress.';
$lang['admin_password_short']       = 'The password is too short (min. %d characters).';
$lang['admin_passwords_dont_match'] = 'The passwords do not match.';
$lang['admin_failed']               = 'Failed to validate the admin account. Make sure you entered the right values.';
$lang['admin_created']              = 'Your account has been created. You may now visit your new Wiki at %s and login as %s.<br />The admin control panel can be found at %s.';
$lang['admin_create_failed']        = 'Failed to create the admin account. Make sure the database was setup correctly.';
?>