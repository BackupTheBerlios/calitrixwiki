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
$lang['title']                      = 'CalitrixWiki Installation';
$lang['lang']                       = 'Sprache';
$lang['lang_submit']                = 'Speichern';
$lang['step0']                      = 'Willkommen';
$lang['step1']                      = 'Lizenzbestimmungen lesen';
$lang['step2']                      = 'Akzeptieren und weiter zu den Grundeinstellungen';
$lang['step3']                      = 'Datenbank erstellen';
$lang['step4']                      = 'Einen Benutzer anlegen';
$lang['welcome']                    = 'Willkommen zur CalitrixWiki Installation. Dieses Installationsscript wird ihnen helfen dieses Wiki zu installieren so das diese Installation nur ein paar Minuten benötigt.';
$lang['passed']                     = '<span style="color:green">OK</span>';
$lang['failed']                     = '<span style="color:red">Fehlgeschlagen</span>';
$lang['invalid_step']               = 'Ung&uuml;ltiger Installationsschritt.';
$lang['error']                      = 'Fehler';
$lang['check']                      = 'Als erstes werden ein paar Dinge &uuml;berpr&uuml;ft:';
$lang['check_failed']               = 'Pr&uuml;fung fehlgeschlagen. Dies bedeutet das sie unter diesen Umst&auuml;nden dieses Wiki nicht problemlos betreiben k&ouml;nnen.';
$lang['check_success']              = 'Pr&uuml;fung erfolgreich. Sie k&ouml;nnen nun die Lizenzbestimmungen lesen.';
$lang['check_version']              = 'PHP Version >= 4.3.x';
$lang['check_mysql']                = 'MySQL Unterst&uuml;tzung aktiviert';
$lang['check_config_writeable']     = 'Konfigurationsverzeichnis beschreibbar';
$lang['check_installer_writeable']  = 'Installationsverzeichnis beschreibbar';
$lang['license']                    = 'Sie m&uuml;ssen nun die Lizenzbestimmungen dieser Software lesen da es wichtig ist zu verstehen wieviele Freiheiten, und wie wenig Beschr&auml;nkungen, ihnen diese Lizenz einr&auml;umt.';
$lang['nolicense']                  = 'Lizenzdatei nicht gefunden.';
$lang['settings']                   = 'Pr&uuml;fen sie nun die Grundeinstellungen die n&ouml;tig sind um CalitrixWiki zu betreiben. In den meisten F&auml;llen erkennt das Installationsscript die notwendigen Angaben richtig.';
$lang['set_url_root']               = 'Adresse unter der ihr Wiki auffindbar sein soll';
$lang['set_doc_root']               = 'Pfad zum Wiki im Dateisystem des Webservers';
$lang['set_paths']                  = 'Sie k&ouml;nnen auch die Verzeichnisse &auml;ndern in denen CalitrixWiki seine Dateien sucht die nicht &ouml;ffentlich abrufbar sein m&uuml;ssen. Wenn sie dazu in der Lage sind sollten sie diese Verzeichnisse ausserhalb der &ouml;ffentlich einsehbaren Verzeichnisse lagern.';
$lang['set_actions_dir']            = 'Verzeichnis wo die verschiedenen Aktionsklassen (bearbeiten, Versionsgeschichte) abgelegt sind';
$lang['set_lang_dir']               = 'Verzeichnis der Sprachdateien';
$lang['set_lib_dir']                = 'Verzeichnis allgemeiner Programmbibliotheken';
$lang['set_special_dir']            = 'Verzeichnis wo Spezialseiten gesucht werden';
$lang['set_plugins_dir']            = 'Verzeichnis wo Plugins gesucht werden';
$lang['set_themes_dir']             = 'Verzeichnis wo Themes gesucht werden';
$lang['set_warning']                = 'Warnung: Verzeichnis \'%s\' existiert nicht.';
$lang['submit_settings']            = 'Einstellungen speichern';
$lang['set_written']                = 'Konfigurationsdatei geschrieben. Sie k&ouml;nnen nun mit der Datenbank fortfahren.';
$lang['set_cant_write']             = 'Konfigurationsdatei "%s" nicht beschreibbar.';
$lang['database']                   = 'Sie m&uuml;ssen nun die Daten f&uuml;r die MySQL Datenbank eingeben.';
$lang['db_host']                    = 'Datenbank host';
$lang['db_name']                    = 'Name der Datenbank';
$lang['db_user']                    = 'Benutzername';
$lang['db_pass']                    = 'Passwort der Datenbank';
$lang['db_prefix']                  = 'Prefix für Tabellen (wenn dies nicht das erste CalitrixWiki in dieser Datenbank ist sollten sie das Prefix &auml;ndern um Konflikte in Tabellennamen zu verhindern)';
$lang['db_create']                  = 'Versuchen die Datenbank zu erstellen wenn sie noch nicht existiert?';
$lang['db_submit']                  = 'Datenbank erstellen';
$lang['db_need_host']               = 'Sie m&uuml;ssen einen Datenbankhost angeben';
$lang['db_need_name']               = 'Sie m&uuml;ssen einen Datenbankname angeben';
$lang['db_trying_host']             = 'Versuche mich mit dem Datenbankhost zu verbinden ...';
$lang['db_trying_db']               = 'Versuche die Datenbank auszuw&auml;hlen ...';
$lang['db_trying_create']           = 'Versuche die Datenbank zu erstellen ...';
$lang['db_config_ok']               = 'Datenbankkonfiguration in Ordnung';
$lang['db_config_failed']           = 'Datenbankkonfiguration fehlgeschlagen. Stellen sie sicher das sie die richtigen Werte eingegeben haben.';
$lang['db_config_written']          = 'Datenbankkonfiguration gespeichert';
$lang['db_config_write_failed']     = 'Speichern fehlgeschlagen. Stellen sie sicher das die Konfigurationsdatei beschreibbar ist.';
$lang['db_creating']                = 'Schreibe Daten in die Datenbank ...';
$lang['db_success']                 = 'Die Einrichtung der Datenbank war erfolgreich. Sie k&ouml;nnen nun mit der Einrichtung eines Administrationsaccounts fortfahren.';
$lang['admin']                      = 'Sie k&ouml;nnen nun noch einen Account einrichten der mit Administrationsprivilegien ausgestattet wird. Wenn bis jetzt alles richtig gelaufen ist, dann ist ihr Wiki ab sofort benutzbar.';
$lang['admin_name']                 = 'Benutzername (muss einem g&uuml;ltigen Seitennamen entsprechen, normalerweise bestehend aus Buchstaben, Zahlen, Unterstrichen und/oder Bindestrichen).';
$lang['admin_email']                = 'E-Mail Adresse';
$lang['admin_pass']                 = 'Passwort';
$lang['admin_confirm']              = 'Passwort best&auml;tigen';
$lang['admin_submit']               = 'Administrator einrichten';
$lang['admin_invalid_name']         = 'Dies ist kein g&uuml;ltiger Benutzername.';
$lang['admin_invalid_email']        = 'Dies ist keine g&uuml;ltige E-Mail Adresse.';
$lang['admin_password_short']       = 'Das Passwort ist zu kurz (min. %d Zeichen).';
$lang['admin_passwords_dont_match'] = 'Die Passw&ouml;rter stimmen nicht &uuml;berein.';
$lang['admin_failed']               = 'Einige der Angaben sind nicht richtig. Bitte verbessern sie diese.';
$lang['admin_created']              = 'Der Administrationszugang wurde erstellt. Sie k&ouml;nnen nun ihr Wiki unter %s besuchen und sich als %s anmelden.<br />Der Administrationsbereich befindet sich unter %s.';
$lang['admin_create_failed']        = 'Der Administrationszugang konnte nicht gespeichert werden. Stellen sie sicher das die Einrichtung der Datenbank erfolgreich war.';
?>