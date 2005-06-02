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
'admin_cp'                     => 'Admin CP',
'admin_config'                 => 'Configuration',
'admin_config_misc'            => 'Misc.',
'admin_config_ui'              => 'User interface',
'admin_config_users'           => 'Members',
'admin_config_mailing'         => 'Mailing',
'admin_config_namespaces'      => 'Namespaces',
'admin_config_uploads'         => 'Uploads',
'admin_config_paths'           => 'Paths &amp; adresses',
'admin_config_parser'          => 'Parser',
'admin_config_urlrewrite'      => 'URL rewriting',
'admin_manage'                 => 'Manage',
'admin_manage_interwiki'       => 'Interwiki',
'admin_manage_wikistyles'      => 'Wikistyles',
'admin_manage_wikistyles_add'  => 'add style',
'admin_manage_wikistyles_edit' => 'edit attributes',
'admin_manage_namespaces'      => 'Namespaces',
'admin_manage_themes'          => 'Themes',
'admin_manage_languages'       => 'Languages',
'admin_manage_snippets'        => 'Formating codes',
'admin_users'                  => 'Members',
'admin_users_groups'           => 'Member groups',
'admin_add_group'              => 'add group',
'admin_users_users'            => 'Members',
'admin_add_user'               => 'add member',
'admin_users_email'            => 'Send mail',
'admin_database'               => 'Database',
'admin_db_optimize'            => 'Optimize',
'admin_db_backup'              => 'Backup',
'admin_db_restore'             => 'restore',
'admin_admin'                  => 'Admin',
'admin_exit'                   => 'Back to Wiki',
'admin_extended'               => 'extendet',
'admin_simple'                 => 'simple',
'admin_delete'                 => 'Delete',
'admin_install'                => 'Install',
'admin_edit'                   => 'Edit',
'admin_default'                => 'Default',
'admin_perms_right'            => 'Rirhgt',
'admin_perms_set'              => 'grant',

'admin_index'                  => 'Welcome to the administration area of this wiki. Here you can find all tools to administrate this wiki online.',
'admin_index_version'          => 'Softwareversion',
'admin_index_page_count'       => 'Article count',
'admin_index_edit_count'       => 'Edits count',
'admin_index_db_size'          => 'Database size',
'admin_index_per_day'          => 'per day',

'admin_config_submit'          => 'Save settings',
'admin_config_updated'         => 'Configuration updated.',
'admin_config_misc_desc'       => 'Misc settings which can\'t be categorized.',
'admin_config_wiki_title'      => 'Wiki title',
'admin_config_default_page'    => 'Default page',
'admin_config_invalid_page'    => 'You entered an invalid page name as default page.',

'admin_config_ui_desc'         => 'Here you can modify settings which affect the appearance of the Wiki.',
'admin_config_default_lang'    => 'Default language',
'admin_config_default_theme'   => 'Default theme',
'admin_config_date_format'     => 'Date format (identical to the PHP function <a href="http://www.php.net/manual/en/function.date.php">date</a>)',
'admin_config_teaser_length'   => 'Number of characters in search teasers.',
'admin_config_items_pp'        => 'Number of entries per page on tabular pages',
'admin_config_summary_length'  => 'Maximum length of edit summaries',
'admin_config_dblclick_edits'  => 'Shall the edit form be opened if a user doubleclicks into a page?',

'admin_config_users_desc'      => 'This settings affect members and member groups aswell as session security.',
'admin_config_default_ggroup'  => 'Default guest group',
'admin_config_default_ugroup'  => 'Default members group',
'admin_config_min_pw_length'   => 'Minimum password length',
'admin_config_min_user_length' => 'Minimum username length',
'admin_config_max_user_length' => 'Maximum username length',
'admin_config_sess_lifetime'   => 'Session lifetime in seconds',
'admin_config_cookie_prefix'   => 'Cookie prefix',
'admin_config_cookie_path'     => 'Cookie path',
'admin_config_cookie_domain'   => 'Cookie domain',
'admin_config_cookie_secure'   => 'Send cookies only with secure (https) connections.',

'admin_config_mailing_desc'    => 'Here you can customize sending of mails through this Wiki.',
'admin_config_mail_from'       => 'Sender adress of the Wiki',
'admin_config_mail_name'       => 'Sender name',

'admin_config_namespaces_desc' => 'Here you can set the default namespaces.',
'admin_config_default_nspace'  => 'Default namespace',
'admin_config_special_nspace'  => 'Namespace for special pages',
'admin_config_user_nspace'     => 'Namespace for member pages',

'admin_config_uploads_desc'    => 'Preferences related to attachments in articles.',
'admin_config_enable_uploads'  => 'Enable uploads',
'admin_config_upload_types'    => 'Allowed file types',
'admin_config_upload_size'     => 'Maximum file size (KB)',
'admin_config_upload_list'     => 'Display a list of attachments below articles.',

'admin_config_paths_desc'      => 'You can modify the paths and adresses where the Wiki searches it\'s program files.',
'admin_config_url_root'        => 'Root adress of this Wiki',
'admin_config_doc_root'        => 'Root directory in the servers filesystem.',
'admin_config_actions_dir'     => 'Directory of action classes',
'admin_config_lang_dir'        => 'Directory of language files',
'admin_config_lib_dir'         => 'Directory of misc program libraries',
'admin_config_special_dir'     => 'Directory where special pages are stored',
'admin_config_plugins_dir'     => 'Directory where plugins are stored',
'admin_config_themes_dir'      => 'Directory where themes are stored',
'admin_config_invalid_data'    => 'You entered invalid data.',
'admin_config_missing_dir'     => 'Directory "%s" does not exist.',

'admin_config_parser_desc'     => 'Here you can customize the behaviour of the parser. Note that some settings can cause the parser or Wiki to stop working if you set them wrong. So be careful.',
'admin_config_max_includes'    => 'Maximum includes through the include plugin per request.',
'admin_config_indent_width'    => 'Depth of indents in pixels',
'admin_config_space_words'     => 'Space WikiWords?',
'admin_config_display_nspaces' => 'Display namespaces in link texts?',
'admin_config_auto_link'       => 'Auto-link WikiWords?',
'admin_config_link_num'        => 'How often shall one WikiWord be linked on one page (0 to disable this feature)?',
'admin_config_link_self'       => 'Link WikiWords which refer to the own page?',
'admin_config_title_format'    => 'Regular expression pattern to match page titles (back reference 1 must include the namespace, back reference 2 the page name)',
'admin_config_title_format_s'  => 'Regular expression pattern to search for page titles in a text (back reference 1 must include the namespace, back reference 2 the page name)',
'admin_config_thispage'        => 'Name of the special InterWiki which refers to the current page',
'admin_config_thiswiki'        => 'Name of the special InterWiki which refers to this Wiki',

'admin_config_urlrewrite_desc' => 'Here you customize the appearance of URLs in this Wiki. This can produce search-engine-friendly links which can also be remembered easier. To automaticaly write the config file for the Apache webserver, the Wiki must be able to write to it\'s root directory. You also need to mod_rewrite module for the Apache webserver. If you are good with mod_rewrite you can set some detailed settings with clicking on "extended". If you do not touch the extended settings, the Wiki will generate default values which should work in most cases. If you want to write your own htaccess file you may leave the following checkbox unchecked. The Wiki will only save the extended settings - if changed - then.',
'admin_config_enable_rewrite'  => 'Enable URL rewriting (overwrites the current htaccess file)',
'admin_config_rewrite_rule'    => 'Patterns for searching and replacing URLs.',
'admin_config_urlformat'       => 'Appearance of adresses with additional parameters. <tt>%1$s</tt> will be replaced with the target page, <tt>%2$s</tt> with the requested action (view, print, histroy, ...).',
'admin_config_urlformat_short' => 'Appearance of short adresses without parameters. <tt>%1$s</tt> will be replaced with the target page.',
'admin_config_htaccess_unwriteable' => 'Can\'t write the htaccess file.',

'admin_interwiki_add'          => 'Add InterWiki',
'admin_interwiki_add_desc'     => 'Here you can add more InterWikis. %s in the adresses will be replaced with the target page when using this InterWiki.',
'admin_interwiki_add_name'     => 'Name of the InterWiki',
'admin_interwiki_add_url'      => 'Target adress',
'admin_interwiki_submit'       => 'Save',
'admin_interwiki_name'         => 'Name',
'admin_interwiki_url'          => 'Adress',
'admin_invalid_interwiki'      => 'You entered invalid values which can\'t be saved.',
'admin_interwiki_updated'      => 'InterWikis updated.',

'admin_namespace_add'          => 'Add namespace',
'admin_namespace_add_desc'     => 'You can add new namespaces here.',
'admin_add_namespace'          => 'Namespace',
'admin_namespace_submit'       => 'Save',
'admin_namespace_name'         => 'Namespace',
'admin_namespace_count'        => 'Includet pages',
'admin_namespaces_updated'     => 'Namespaces updated',
'admin_duplicated_namespaces'  => 'This namespace already exists',
'admin_namespace_confirm'      => 'Confirm delete',
'admin_namespace_confirm_desc' => 'The namespace you are trying to delete already includes some pages. You must select what shall be done with this pages.',
'admin_namespace_del_pages'    => 'Delete all pages in this namespace (will remove all pages in this namespace permamently)',
'admin_namespace_move_pages'   => 'Move pages to the following namespace:',

'admin_group_id'               => '#',
'admin_group_name'             => 'Name',
'admin_group_mask'             => 'Access mask',
'admin_perms_right_view'       => 'This group can view pages',
'admin_perms_right_edit'       => 'This group can edit pages',
'admin_perms_right_history'    => 'This group can use the version history',
'admin_perms_right_restore'    => 'This group can restore old page versions',
'admin_perms_right_rename'     => 'This group can rename pages',
'admin_perms_right_delete'     => 'This group can delete pages',
'admin_perms_right_ignore_local' => 'This group can ignore local page permissions',
'admin_perms_right_set_local'  => 'This group can modify local permissions',
'admin_perms_right_use_acp'    => 'This group can use this admin control panel',
'admin_group_submit'           => 'Save group',
'admin_group_updated'          => 'Group updated.',
'admin_no_group_name'          => 'You need to enter a group name.',
'admin_group_added'            => 'Group added.',
'admin_group_confirm'          => 'Confirm delete',
'admin_group_confirm_desc'     => 'This group contains some members. What shall be done with them?',
'admin_delete_contained'       => 'Delete members.',
'admin_move_contained'         => 'Move members to the following group:',
'admin_group_removed'          => 'Group deleted.',

'admin_wikistyle_name'         => 'Style class',
'admin_wikistyle_name_desc'    => 'The class will be used with this name in Wiki pages.',
'admin_wikistyle_attribs'      => 'Style attributes',
'admin_wikistyle_attribs_desc' => 'Enter the style attributes here. One attribute per line. Seperate attribute names and values with an colon.',
'admin_wikistyle_attribs'      => 'Attributes',
'admin_wikistyle_display'      => 'Appearance',
'admin_wikistyle_submit'       => 'Save class',
'admin_wikistyle_test'         => 'Test class',
'admin_wikistyle_invalid_name' => 'Invalid class name.',
'admin_wikistyle_invalid_attribs' => 'Some attributes are invalid. Make sure all entered attributes have valid names and values.',
'admin_wikistyle_updated'      => 'Style class updated.',

'admin_style_attribs'          => 'Style attributes',
'admin_style_attribs_desc'     => 'Here you can modify the pre-defined style attributes which can be used in WikiStyles. The names in the left input fields must refer to valid CSS attributes. In the right input fields you need to place a valid regular expression pattern to validate the attribute values.',
'admin_attribs_submit'         => 'Save',
'admin_attribs_invalid_name'   => 'Invalid attribute name: %s.',
'admin_attribs_updated'        => 'Attributes updated.',

'admin_user_id'                => '#',
'admin_user_name'              => 'User name',
'admin_user_group'             => 'User group',
'admin_user_email'             => 'Email',
'admin_user_mask'              => 'Access mask',
'admin_user_mask_unchanged'    => 'Group defaults',
'admin_user_reg_time'          => 'Registered since',
'admin_user_last_visit'        => 'Last visit',
'admin_user_details'           => 'User details',
'admin_user_details_desc'      => 'You can edit or add members here.',
'admin_user_language'          => 'Interface language',
'admin_user_theme'             => 'Theme',
'admin_user_items_pp'          => 'Items per page on tabular pages.',
'admin_user_dblclick_editing'  => 'Enable doubleclick editing?',
'admin_user_use_cookies'       => 'Use cookies for loging in?',
'admin_user_enable_mails'      => 'Allow admins to send newsletters to my adress',
'admin_user_change_pw'         => 'Change password',
'admin_user_change_pw_desc'    => 'You can change the users\'s password here. Note that the user won\'t be notified about this. If you leave this fields empty the password will remain unchanged.',
'admin_user_password'          => 'New password',
'admin_user_password_confirm'  => 'Confirm new password',
'admin_user_permissions'       => 'Permissions',
'admin_user_permissions_desc'  => 'Normaly a user inherits the permissions of it\'s group. If you select "Use own permissions", the user will get a own access mask which overwrites the groups permissions.',
'admin_user_use_group'         => 'Use the groups permissions',
'admin_user_use_own'           => 'Use own permissions',
'admin_user_right_view'        => 'The user can view pages',
'admin_user_right_edit'        => 'The user can edit pages',
'admin_user_right_history'     => 'The user can use the version history of pages',
'admin_user_right_restore'     => 'The user can restore old page versions',
'admin_user_right_rename'      => 'The user can rename pages',
'admin_user_right_delete'      => 'The user can delete pages',
'admin_user_right_ignore_local' => 'The user can ignore local permissions',
'admin_user_right_set_local'   => 'The user can modify local permissions',
'admin_user_right_use_acp'     => 'The user can use this admin control panel',
'admin_user_submit'            => 'Save user',
'admin_user_invalid_name'      => 'The username must be a valid page title.',
'admin_user_short_name'        => 'The username is too short (min. %d characters).',
'admin_user_long_name'         => 'The username is too long (max. %d characters).',
'admin_user_name_taken'        => 'The username is already taken.',
'admin_user_unmatching_pws'    => 'The new passwords does not match.',
'admin_user_short_password'    => 'The new password is too short (min. %d characters).',
'admin_user_invalid_email'     => 'This is not a valid email adress',
'admin_user_saved'             => 'The users has been saved.',
'admin_user_confirm'           => 'Confirm delete',
'admin_user_confirm_desc'      => 'A user leaves some data in the Wiki which can\'t be removed. This includes page versions which are made with his name. This data can be anonymized with a different and unregistered username. To do so, enter a different username in the input below or leave it empty to completely anonymize the user.',
'admin_user_new_name'          => 'New username',
'admin_user_confirm_submit'    => 'Delete',
'admin_user_deleted'           => 'User deleted.',
'admin_theme_name'             => 'Theme',
'admin_theme_dir'              => 'Directory',
'admin_theme_installed'        => 'Added the theme to the configuration.',
'admin_theme_removed'          => 'The theme has been deleted from the configuration. To completely remove it, delete the themes directory in the themes directory.',
'admin_theme_remove_default'   => 'This theme is the currently used default theme. To delete it you must first choose another default theme.',
'admin_lang_name'              => 'Language name',
'admin_lang_code'              => 'Language code',
'admin_lang_installed'         => 'Language installed.',
'admin_lang_removed'           => 'The language has been deleted from the configuration. To completely remove it, delete the language files.',
'admin_lang_remove_default'    => 'This language is the current default language. You need to choose another default language to remove it.',

'admin_snippet_name'           => 'HTML snippets',
'admin_snippetdesc_heading'    => 'This snippet is used to generate headings. <tt>%1$s</tt> will be replaced with the anchor of the heading, <tt>%2$s</tt> with the depth and <tt>%3$s</tt> wth the heading text.',
'admin_snippetdesc_image'      => 'This code is used to display images.',
'admin_snippetdesc_link_create' => 'This code is used to display links to pages which to not exist yet. <tt>%1$s</tt> will be replaced with the target page, <tt>%2$s</tt> with the link text.',
'admin_snippetdesc_link_internal' => 'This code is used to display links to existing pages. <tt>%1$s</tt> will be replaced with the target page, <tt>%2$s</tt> with the link text.',
'admin_snippetdesc_link_interwiki' => 'This code will be used for InterWiki links. <tt>%1$s</tt> will be replaced with the target page, <tt>%2$s</tt> with the link text.',
'admin_snippetdesc_link_external' => 'This code is used to display links to external pages. <tt>%1$s</tt> will be replaced with the target adress, <tt>%2$s</tt> with the link text.',
'admin_snippetdesc_link_email' => 'This code is used to display links to email adresses. <tt>%1$s</tt> will be replaced with the target adress, <tt>%2$s</tt> with the link text.',
'admin_snippetdesc_TOC'        => 'This is used to display the TOC formating code. <tt>%1$s</tt> refers to the word "Index" in the current language, <tt>%2$s</tt> refers to "hide", <tt>%3$s</tt> will be replaced with a numeric list in Wiki syntax, <tt>%4$s</tt> refers to "display".',
'admin_snippetdesc_trail'      => 'This code is used to display WikiTrails. <tt>%1$s</tt> will be replaced with a link to the previous page, <tt>%2$s</tt> with the adress of the trail page, <tt>%3$s</tt> with the name of the trail page, <tt>%4$s</tt> with a link to the next page.',
'admin_snippetdesc_trail_emptyleft' => 'This code is used to display the link to the previous page if the current page is the first in the trail.',
'admin_snippetdesc_trail_emptyright' => 'This code is used to display the link to the next page if the current page is the last in the trail.',
'admin_snippetdesc_trail_linkleft' => 'This code is used to display the link to the previous page. <tt>%1$s</tt> will be replaced with the adress of this page, <tt>%2$s</tt> with the name of this page.',
'admin_snippetdesc_trail_linkright' => 'This code is used to display the link to the next page. <tt>%1$s</tt> will be replaced with the adress of this page, <tt>%2$s</tt> with the name of this page.',
'admin_edit_snippet'           => 'Edit code snippet',
'admin_submit_snippet'         => 'Save',
'admin_snippet_updated'        => 'Code snippet updated.',

'admin_db_tables'              => 'Tables',
'admin_db_tables_desc'         => 'This is a list of all tables in the database the Wiki is installed. Tables with a data overhead a highlighted and selected.',
'admin_db_table_name'          => 'Table name',
'admin_db_table_rows'          => 'Number of rows',
'admin_db_table_size'          => 'Table size',
'admin_db_table_overhead'      => 'Overhead',
'admin_db_optimize_selected'   => 'Optimize selected',
'admin_db_optimized'           => 'The following tables has been optimized: %s',
'admin_db_backup_tables'       => 'Backup database',
'admin_db_backup_desc'         => 'This is a list of all tables in the database the Wiki is installed in. Check the checkbox near the table name to add it to the backup. Note that the creation of backups through this interface is restricted to a specific database size. This is because of the PHP setting <tt>memory_limit</tt> (%s).',
'admin_db_backup_selected'     => 'Create backup',
'admin_db_backup_options'      => 'Options',
'admin_db_backup_options_desc' => '"Display backup" should only be used with small backups. For larger backups, always choose a compressed file.',
'admin_db_send_gzipped'        => 'Send a gzip-compressed file',
'admin_db_send_raw'            => 'Send a uncompressed file',
'admin_db_send_display'        => 'Display backup as text',
'admin_db_restore_tables'      => 'Restore database',
'admin_db_restore_desc'        => 'You can restore database backups made by this admin cp here. Note that restoring backups through this interface is restricted to a specific backup size. This is because of the PHP setting <tt>upload_max_filsize</tt>, which restricts the size of a uploaded file (%s at the moment).',
'admin_db_restore_submit'      => 'Restore',
'admin_db_unknown_backup'      => 'Unknown file typ - is this a valid backup?',
'admin_db_backup_restored'     => 'Backup restored.',
'admin_mail_recipient'         => 'Recipient',
'admin_mail_recipient_desc'    => 'You can send an email to one or more single members aswell as to one or more member groups. Note that only members will receive this email which enabled this feature in their preferences area. If you want to send this email to more than one single members, provide a semicolon sperated list of members.',
'admin_mail_groups'            => 'Recipient groups',
'admin_mail_users'             => 'and/or single members',
'admin_mail_subject'           => 'Subject',
'admin_mail_body'              => 'Message',
'admin_mail_body_desc'         => 'Provide the text of the email here. You can user the following variables to personalize the email: <tt>{username}</tt> (username), <tt>{userid}</tt> (user id), <tt>{useremail}</tt> (user email), <tt>{regdate}</tt> (date of registration) und <tt>{lastvisit}</tt> (date of last visit).',
'admin_mail_submit'            => 'Send email',
'admin_mail_no_rcpt'           => 'You didn\'t provide at least one valid recipient.',
'admin_mail_sent'              => 'This mail was sent to %d recipients.'
);
?>