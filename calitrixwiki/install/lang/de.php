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

$lang = array(
'installation'               => 'CalitrixWiki Installation',
'step_requirements'          => 'CalitrixWiki Installation',
'step_license'               => 'Lizenzbedingungen',
'step_config'                => 'Grundeinstellungen',
'step_install'               => 'Datenbank installieren',
'step_admin'                 => 'Administrator einrichten',
'step_updateb1'              => 'Aktualisieren von Beta 1',
'next'                       => 'weiter',
'ok'                         => 'OK',
'failed'                     => 'Fehlgeschlagen',
'install_form_errors'        => 'Fehler',
'install_form_errors_desc'   => 'Beim Pr&uuml;fen der Formulareingaben sind einige Fehler aufgetreten. Bitte verbessern sie diese.',
'req_desc'                   => 'Willkommen in der CalitrixWiki Installation. Dieses Installationsskript wird sie durch den weiteren Verlauf der Installation f&uuml;hren. Als erstes werden ein paar Systemanforderungen &uuml;berpr&uuml;ft. Wenn sie CalitrixWiki in der Version Beta 1 auf Beta 2 aktualisieren m&ouml;chten, so w&auml;hlen sie bei "Installationsart" den Punkt "Aktualisieren von Beta 1". Wenn sie ein neues Wiki installieren m&ouml;chten dann w&auml;hlen sie "Neue Installation".',
'req_desc2'                  => 'Wenn alle Anforderungen erf&uuml;llt sind k&ouml;nnen sie nun mit Sprache und Installationsart fortfahren.',
'req_php_version'            => 'PHP-Version >= 4.3.x',
'req_mysql_ext'              => 'MySQL-Unterst&uuml;tzung',
'req_set_dir'                => 'Konfigurationsverzeichnis beschreibbar',
'req_install_dir'            => 'Installationsverzeichnis beschreibbar',
'req_lang_and_type'          => 'Sprache und Installationsart',
'req_language'               => 'Sprache',
'req_installation_type'      => 'Installationsart',
'req_install_new'            => 'Neue Installation',
'req_update_beta1'           => 'Aktualisieren von Beta 1',
'license_desc'               => 'CalitrixWiki ist OpenSource-Software und steht unter der GNU General Public License. Wenn sie noch nicht mit dem OpenSource-Prinzip vertraut sind sollten sie die Lizenz lesen.',
'config_desc'                => 'Hier m&uuml;ssen einige der Grundeinstellungen get&auml;tigt werden. Die weitere Konfiguration des Wikis kann nach der Installation im Administrationsbereich durchgef&uuml;hrt werden.',
'config_paths'               => 'Pfade und Adressen',
'config_paths_url_root'      => 'Basisadresse des Wikis',
'config_paths_doc_root'      => 'Wurzelverzeichnis auf dem Server',
'config_db'                  => 'Datenbank',
'config_db_host'             => 'Adresse des Datenbankservers',
'config_db_name'             => 'Name der Datenbank',
'config_db_user'             => 'Benutzername',
'config_db_pass'             => 'Passwort',
'config_db_prefix'           => 'Prefix f&uuml;r Tabellen. &Auml;ndern sie dies, um mehrere CalitrixWikis in eine Datenbank zu installieren.',
'config_options'             => 'Installationsoptionen',
'config_db_create'           => 'Datenbank anlegen',
'config_db_create_desc'      => 'Wenn die Datenbank des Wikis noch nicht existiert wird versucht sie anzulegen.',
'config_default_pages'       => 'Standardseiten installieren',
'config_default_pages_desc'  => 'Aktivieren sie diese Option, wenn sie Standardseiten wie Dokumentation und Hilfeseiten installieren m&ouml;chten.',
'config_write'               => 'Konfiguration schreiben',
'config_connect_failed'      => 'Die Verbindung zur Datenbank ist fehlgeschlagen: %s',
'config_write_failed'        => 'Die Konfigurationsdateien sind nicht beschreibbar. Stellen sie sicher das %s beschreibbar ist.',
'install_desc'               => 'Als n&auml;chster Schritt nach erfolgreicher Konfiguration muss die Datenbank installiert werden. Klicken sie auf "Weiter" um dies zu tun.',
'install_error'              => 'Datenbankfehler',
'admin_desc'                 => 'Nach der Installation der Datenbank und zum Abschluss der Installation m&uuml;ssen sie noch einen Administrationszugang einrichten. Dieser Benutzeraccount wird dann mit Zugang zum Administrationsbereich ausgestattet.',
'admin_create'               => 'Zugangsdaten',
'admin_name'                 => 'Benutzername',
'admin_mail'                 => 'E-Mail Adresse',
'admin_password'             => 'Passwort',
'admin_password_c'           => 'Passwort best&auml;tigen',
'admin_submit'               => 'Admin speichern',
'admin_invalid_name'         => 'Dies ist kein g&uuml;ltiger Benutzername.',
'admin_invalid_email'        => 'Dies ist keine g&uuml;ltige E-Mail Adresse.',
'admin_password_short'       => 'Das Passwort ist zu kurz (min. %d Zeichen).',
'admin_passwords_dont_match' => 'Die Passw&ouml;rter stimmen nicht &uuml;berein.',
'admin_finished_desc'        => 'Der Administrator wurde eingerichtet. Sie sollten als erstens das neu installierte Wiki aufrufen und sich mit dem Administrationszugang anmelden um die Konfiguration im Administrationsbereich zu vervollständigen.',
'admin_wiki'                 => 'Ihr Wiki',
'admin_cwiki_home'           => 'Calitrix Homepage',
'admin_cwiki_doc'            => 'Dokumentation',
'admin_failed_desc'          => 'Beim Einrichten des Administrators ist ein Fehler aufgetreten. Stellen sie sicher das die Datenbank korrekt eingerichtet wurde.',
'updateb1_desc'              => 'Geben sie den Pfad des Verzeichnisses an in dem die Konfigurationsdateien des zu aktualisierenden Wikis liegen.',
'updateb1_paths'             => 'Verzeichnisse',
'updateb1_set_dir'           => 'Konfigurationsverzeichnis',
'updateb1_submit'            => 'Aktualisieren',
'updateb1_error'             => 'Fehler beim Aktualisieren',
'updateb1_config_failed'     => 'Die Konfigurationsdateien wurden nicht gefunden. Haben sie den richtigen Pfad zum Konfigurationsverzeichnis angegeben?',
'updateb1_update_failed'     => 'Die Aktualisierung der Datenbank ist fehlgeschlagen: %s',
'updateb1_success'           => 'Aktualisierung erfolgreich',
'updateb1_success_desc'      => 'Ihr CalitrixWiki wurde erfolgreich von Beta 1 auf Beta 2 aktualisiert.'
);
?>