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
'error_unknown_request' => 'Ung&uuml;tiger Seitenname.',

'search'                    => 'Search',
'search_submit'             => 'Go',
'search_title'              => 'Search page titles',
'search_fulltext'           => 'Full-text search',
'search_results'            => 'Search results',
'search_no_results'         => 'Your search has not matched any pages. Try to choose a different search term.',
'wiki_toc'                  => 'Table of contents',
'wiki_hide_toc'             => 'hide',
'wiki_show_toc'             => 'show',
'wiki_redirected'           => '(redirected from %s)',
'wiki_page_name'            => 'Page',
'wiki_date'                 => 'Date',
'wiki_pages'                => 'Displaying page %s of %s',
'wiki_first'                => 'first',
'wiki_last'                 => 'last',
'wiki_login'                => 'Login',
'wiki_logout'               => 'Logout',
'wiki_register'             => 'Register',
'wiki_user_prefs'           => 'Preferences',
'wiki_admin_cp'             => 'Admin CP',
'wiki_username'             => 'Member',
'wiki_registered_since'     => 'Registered since',
'wiki_bookmark_page'        => 'Bookmark',
'wiki_message'              => 'Alert',
'wiki_invalid_url'          => 'This is an invalid page name',
'wiki_perm_denied'          => 'You can not use this function. You may need to register first or to be in a higher user group.',
'create_page'               => 'This page does not exist yet. Click "Edit page" at the bottom of the page to start it.',
'wiki_old_version'          => 'You are viewing the old version %s (%s).',
'wiki_edit_old_version'     => 'You are editing the old version %s (%s). If you submit this text the current version will be overwritten.',
'wiki_edit_perms'           => 'Permissions',
'wiki_view_source'          => 'View source',
'wiki_page_already_bookmarked' => 'You have already bookmarked this page.',
'wiki_page_bookmarked'      => 'Added this page to your bookmarks.',
'wiki_form_errors'          => 'Error',
'wiki_form_errors_desc'     => 'There occured some errors while validating your inputs.',
'wiki_inline_message'       => 'Note',
'wiki_select_all'           => 'Select all',
'wiki_deselect_all'         => 'Deselect all',
'wiki_selected'             => 'Selected',
'wiki_delete'               => 'Delete',
'wiki_back'                 => 'Back',
'wiki_addpage_submit'       => 'Create',
'edit_page'                 => 'Edit page',
'edit_locked'               => 'This page is locked and can not be edited but you may view the source text.',
'edit_author'               => 'Author',
'edit_summary'              => 'Summary',
'edit_submit'               => 'Save page',
'edit_preview'              => 'Create preview',
'edit_previewing'           => 'Page preview',
'edit_conflicts'            => 'This page has been modified while you were editing. The differences are shown below. The left version is the one you are editing now. The right version ist the one which has been submited meanwhile.',
'edit_username_taken'       => 'This username is already used by a registered member. Please use a different one.',
'print_page'                => 'Printer friendly',
'last_modified'             => 'Last modified',
'page_version'              => 'Version',
'history'                   => 'Versions',
'history_page_name'         => 'Page',
'history_differences'       => 'Differences',
'history_date'              => 'Date',
'history_version'           => 'Version',
'history_author'            => 'Author',
'history_view'              => 'view',
'history_restore'           => 'restore',
'history_info'              => 'Info',
'history_version_info'      => 'Version numbers',
'history_version_desc'      => 'The first version number counts changes higher then 25% of the text, the second number changes between 5% and 25%, the last one changes less then 5% or 6 lines.',
'history_restore_info'      => 'Restore',
'history_restore_desc'      => 'Older versions can be restored due to mistaken edits or vandalism. If you want to restore an old version simply click the restore link and you will be transfered to an edit form containing the old page text.',
'history_show'              => 'Display differences',
'history_original'          => 'Version %1$s',
'history_final'             => 'Differences to %2$s',
'history_color_info'        => 'The colors are highlighting the changes needet to get from the left to the right text.',
'history_edited'            => 'This line must be edited.',
'history_addition'          => 'This line must be added.',
'history_substraction'      => 'This line is to be deleted',
'history_no_change'         => 'This line is unchanged.',
'login_login'               => 'Login',
'login_username'            => 'Username',
'login_password'            => 'Password',
'login_remember'            => 'Remember my login? (Cookies must be activated)',
'login_submit'              => 'Login',
'login_invalid'             => 'Invalid username or invalid password!',
'login_already_logged_in'   => 'You are already logged in.',
'login_success'             => 'You where logged in successfully.',
'logout'                    => 'You where logged out successfully.',
'register_account'          => 'New Account',
'register_registered'       => 'You are already registerd.',
'register_username'         => 'Username',
'register_email'            => 'Email adress',
'register_password'         => 'Password',
'register_password_confirm' => 'Confirm password',
'register_use_cookies'      => 'Use cookies for logging in?',
'register_submit'           => 'Register',
'register_short_username'   => 'Your username is too short(min %d characters)',
'register_long_username'    => 'Your username is too long(max %d characters)',
'register_invalid_username' => 'Your username is invalid. It must be a valid page title, normaly alphanumeric characters, numbers, underscores and dashes',
'register_username_taken'   => 'This username is already taken',
'register_short_password'   => 'This password is too short (min %d characters)',
'register_wrong_password'   => 'The passwords does not match',
'register_invalid_email'    => 'The entered email adress is invalid',
'register_done'             => 'Your registration was successful. You can now login with your new username.',
'prefs_welcome'             => 'In this pages you can edit your details, customize the user interface or manage your bookmarks. Choose one of the tasks from the above links.',
'prefs_edit_details'        => 'Change my details',
'prefs_edit_prefs'          => 'Edit preferences',
'prefs_edit_bookmarks'      => 'Manage bookmarks',
'prefs_details_email'       => 'Change email',
'prefs_details_email_desc'  => 'You can change your email adress. Make sure the new adress is valid to make sure possible notifications make it to your inbox.',
'prefs_details_new_email'   => 'New email',
'prefs_details_submit'      => 'Save',
'prefs_details_password'         => 'Change password',
'prefs_details_password_desc'    => 'You can change your password here. Just enter the old one and provide a new one twice.',
'prefs_details_old_password'     => 'Old password',
'prefs_details_new_password'     => 'New password',
'prefs_details_password_confirm' => 'Confirm password',
'prefs_details_invalid_email'  => 'The entered email adress is invalid',
'prefs_details_updated'        => 'Your details have been updated.',
'prefs_details_invalid_pw'     => 'The entered password does not match your current.',
'prefs_details_short_pw'       => 'The new password is too short (min %d characters).',
'prefs_details_different_pws'  => 'The new passwords does not match',
'prefs_bookmarks_add'          => 'Add bookmark',
'prefs_bookmarks_add_desc'     => 'Enter the name of a page to add it to your bookmarks list.',
'prefs_bookmarks_add_page'     => 'Page name',
'prefs_bookmarks_add_submit'   => 'Add',
'prefs_bookmarks_page'         => 'Page',
'prefs_bookmarks_version'      => 'Version',
'prefs_bookmarks_last_mod'     => 'Last modified',
'prefs_bookmarks_none'         => 'You have not set any bookmarks yet.',
'prefs_bookmarks_no_page'      => 'This page does not exist.',
'prefs_bookmarks_info'         => 'Pages with this icon have changed since your last visit.',
'prefs_bookmarks_del_mark'     => 'Remove icon',
'prefs_bookmarks_change_submit' => 'Update',
'prefs_bookmarks_changed'      => 'Your bookmarks have been updated.',
'prefs_prefs_interface'        => 'User interface',
'prefs_prefs_interface_desc'   => 'You can choose the interface language and design here.',
'prefs_prefs_language'         => 'Language',
'prefs_prefs_language_desc'    => 'Select the language in which the interface should be displayed.',
'prefs_prefs_theme'            => 'Design',
'prefs_prefs_theme_desc'       => 'Choose a design to be used for displaying this Wiki.',
'prefs_prefs_items_pp'         => 'Items per page',
'prefs_prefs_items_pp_desc'    => 'You can choose the number of items which shall be displayed in result lists (recent changes, search results, ..) per page.',
'prefs_prefs_submit'           => 'Save',
'prefs_prefs_mailing'          => 'Mailing',
'prefs_prefs_mailing_desc'     => 'You can set if and how emails and notifications are sent by this Wiki.',
'prefs_prefs_enable_subs'      => 'I want to receive notifications about edits in wiki pages.',
'prefs_prefs_receive_news'     => 'Administrators may send me emails like newsletters.',
'prefs_prefs_misc'             => 'Misc. settings',
'prefs_prefs_use_cookies'      => 'Use cookies for logging in (prevents the session id from being transmitted via the pages url).',
'prefs_prefs_dblclick_editing' => 'Enable double-click editing',
'prefs_prefs_default'          => 'Default',
'prefs_prefs_updated'          => 'Your preferences have been updated. Interface changes will be used from your next click on.',
'perms'                        => 'change permissions',
'perms_group_name'             => 'Usergroup',
'perms_access_mask'            => 'Accessmask',
'perms_mask_unchanged'         => 'Group default',
'perms_edit_perms'             => 'change',
'perms_reset_perms'            => 'reset',
'perms_right'                  => 'Right',
'perms_set'                    => 'grant',
'perms_change_desc'            => 'Group: %s',
'perms_right_view'             => 'The group can view this page',
'perms_right_edit'             => 'The group can edit this page',
'perms_right_history'          => 'The group can use the history',
'perms_right_restore'          => 'The group can restore old page versions',
'perms_right_rename'           => 'The group can rename this page',
'perms_right_delete'           => 'The group can delete this page',
'perms_submit'                 => 'Update permissions',
'perms_updated'                => 'Permissions updated.',
'perms_deleted'                => 'The local permissions for the selected group has been deleted.',
'options'                      => 'Options',
'options_rename'               => 'Rename page',
'options_rename_desc'          => 'You can set the page name or namespace here.',
'options_invalid_page_name'    => 'Invalid page name.',
'options_rename_submit'        => 'Rename',
'options_renamed'              => 'The page was renamed.',
'options_delete'               => 'Remove page',
'options_delete_desc'          => 'To remove to page, check the following checkbox and klick "Remove". This will completely remove the page from the database. Note that the script won\'t ask for a confirmation.',
'options_delete_submit'        => 'Entfernen'
);
?>