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

/*Started Tue May 25 17:06:42 CEST 2004 */

$cfg = array(
/* Url and path settings */
'url_root'          => 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']), // root url without trailing slashes
'doc_root'          => dirname($_SERVER['SCRIPT_FILENAME']), // document root in the servers file system
'url_format'        => 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/cwiki.php?page=%s&action=%s', // usual url format
'url_format_short'  => 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/cwiki.php?page=%s', // short url format (used for internal hyperlinks
'actions_dir'       => dirname($_SERVER['SCRIPT_FILENAME']).'/actions', // dir of the action handlers
'lang_dir'          => dirname($_SERVER['SCRIPT_FILENAME']).'/lang', // dir where the language files are located
'lib_dir'           => dirname($_SERVER['SCRIPT_FILENAME']).'/libs', // libraries directory
'special_dir'       => dirname($_SERVER['SCRIPT_FILENAME']).'/specialpages', // special pages
'plugins_dir'       => dirname($_SERVER['SCRIPT_FILENAME']).'/plugins', // plugins
'themes_dir'        => dirname($_SERVER['SCRIPT_FILENAME']).'/themes', // themes

/* General settings */
'wiki_title'        => 'CalitrixWiki', // The title of the wiki
'default_page'      => 'HomePage', // default which will be used if no page name is supplied
'default_lang'      => 'de',  // default language (code)
'default_theme'     => 'cwiki', // default theme to use (directory of the theme in the themes/ dir
'default_action'    => 'view', // default action
'date_format'       => 'd.m.y H:i', // date format used for the php date() function
'html_paragraph'    => '<p></p>', // html paragraph used in wiki pages
'html_newline'      => '<br />', // manual linebreak
'indent_width'      => 50, // how many pixels should paragraphs be indented by one character of the : markup
'max_includes'      => 5, // max number of pages which can be includet in wiki pages
'teaser_length'     => 300, // length of the teaser in search results
'sitemap_chars'     => array('A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0, 'F' => 0, 'G' => 0, 'H' => 0, 'I' => 0, 'J' => 0, 'K' => 0, 'L' => 0, 'M' => 0, 'N' => 0, 'O' => 0, 'P' => 0, 'Q' => 0, 'R' => 0, 'S' => 0, 'T' => 0, 'U' => 0, 'V' => 0, 'W' => 0, 'X' => 0, 'Y' => 0, 'Z' => 0), // characters in the sitemap
'enable_caching'    => 0, // enable caching mechanism (not implemented atm)
'items_per_page'    => 20, // how many items shall be displayed per page by default?
'cookie_prefix'     => 'cwiki_', // prefix for cookies
'cookie_path'       => '/', // cookie path
'cookie_domain'     => $_SERVER['HTTP_HOST'], // cookie domain
'cookie_secure'     => 0, // send cookies only if there is a https connection?
'session_lifetime'  => 1800, // lifetime of a inactive session in seconds
'special_namespace' => 'Wiki', // namespace of special pages
'min_username_length' => 3, // minimum username length
'max_username_length' => 30,  // 50 is the maximum possible length due to restrictions of the username field in the database.
'min_password_length' => 6, // minimum password length
'default_user_group'  => 2, // user group where users get in if they register
'default_guest_group' => 1, // group which sets the permissions for guests
'users_namespace'     => 'User', // namespaces of user pages
'default_namespace'   => 'Main', // default namespace
'namespaces'          => array('User', 'Main', 'Wiki'), // accessable namespaces
'max_summary_length'  => 160, // maximum length of the "sumary" field in the edit form
'match_email'       => '/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]+$/', // regex format of emails
'dblclick_editing'  => 0, // shall a doubleclick in a page open the edit form?
'mailer_from'       => 'CalitrixWiki Mailer', // name of the mailer
'mail_from'         => 'noreply@calitrix.de', // email of the mailer
'title_format'      => '([A-Z\xc0-\xde][a-z\xdf-\xff]+:)?([A-Za-z0-9\x80-\xff_-]+)', // allowed page titles
'title_format_search' => '(?<=\s|^)([A-Z\xc0-\xde][a-z\xdf-\xff]+:)?(([A-Z\xc0-\xde][A-Za-z0-9\x80-\xff]+){2,})(?=\s|\.|,|;|:|$)', // regex used to search for page titles in wiki pages
'thispage_interwiki' => 'ThisPage', // name of the ThisPage special interwiki
'thiswiki_interwiki' => 'ThisWiki', // name of the ThisWiki special interwiki

'wiki_styles'       => array( // predefined wiki styles. name => array(css property => css value)
                       'strike'    => array('text-decoration' => 'line-through'),
                       'underline' => array('text-decoration' => 'underline')
                       ),

'languages'         => array( // available languages. lang code => lang name
                       'de' => 'Deutsch',
                       'en' => 'English'
                       ),
'themes'            => array( // available themes. theme dir => theme name
                       'cwiki' => 'CalitrixWiki'
                       ),
'items_pp_select'   => array(5, 10, 20, 50, 100), // selections of the "items per page" select input

'interwiki'         => array( // usable interwikis. %s will be substituted by the given page name
                       'Wikipedia' => 'http://de.wikipedia.org/wiki/%s',
                       'MeatBall'  => 'http://www.usemod.com/cgi-bin/mb.pl?%s',
                       'ISBN'      => 'http://www.amazon.de/exec/obidos/tg/detail/-/%s'
                       ),
                     
'style_attributes'  => array( // available wiki style attributes. css property => regex to validate value
                       'font-size'        => '/^([1-3](\.[0-9])?|0\.[5-9])em$/',
                       'color'            => '/^(#[0-9a-fA-F]{6}|[a-zA-Z-]+)$/',
                       'font-style'       => '/^(italic|normal)$/',
                       'font-weight'      => '/^(bold|normal)$/',
                       'font-family'      => '/^(serif|sans-serif|monospace)$/',
                       'text-decoration'  => '/^(underline|overline|line-through|none)$/',
                       'background-color' => '/^(#[0-9a-fA-F]{6}|[a-zA-Z-]+)$/',
                       'border'           => '/^(([1-9]|10)px (#[0-9a-fA-F]{6}|[a-zA-Z-]+) (dashed|solid|dotted|double))$/',
                       'border-left'      => '/^(([1-9]|10)px (#[0-9a-fA-F]{6}|[a-zA-Z-]+) (dashed|solid|dotted|double))$/',
                       'border-right'     => '/^(([1-9]|10)px (#[0-9a-fA-F]{6}|[a-zA-Z-]+) (dashed|solid|dotted|double))$/',
                       'border-top'       => '/^(([1-9]|10)px (#[0-9a-fA-F]{6}|[a-zA-Z-]+) (dashed|solid|dotted|double))$/',
                       'border-bottom'    => '/^(([1-9]|10)px (#[0-9a-fA-F]{6}|[a-zA-Z-]+) (dashed|solid|dotted|double))$/',
                       'margin'           => '/^([0-9]{1,3})px$/',
                       'margin-left'      => '/^([0-9]{1,3})px$/',
                       'margin-right'     => '/^([0-9]{1,3})px$/',
                       'margin-top'       => '/^([0-9]{1,3})px$/',
                       'margin-bottom'    => '/^([0-9]{1,3})px$/',
                       'padding'          => '/^([0-9]{1,3})px$/',
                       'padding-left'     => '/^([0-9]{1,3})px$/',
                       'padding-right'    => '/^([0-9]{1,3})px$/',
                       'padding-top'      => '/^([0-9]{1,3})px$/',
                       'padding-bottom'   => '/^([0-9]{1,3})px$/',
                       'text-align'       => '/^(left|center|right|justify)$/',
                       'display'          => '/^(block|inline|none)$/',
                       'float'            => '/^(left|right)$/'
                       ),
'code_snippets'     => array( // html code snippets used in the wiki parser
                       'TOC'              => '<div class="wiki-toc" id="toc">%1$s [<a href="javascript:void(0)" onclick="toggleBox(\'toc\');toggleBox(\'stoc\')">%2$s</a>]'."\n".'%3$s'."\n".'</div><div class="wiki-toc" id="stoc" style="display:none">%1$s [<a href="javascript:void(0)" onclick="toggleBox(\'toc\');toggleBox(\'stoc\')">%4$s</a>]</div>',
                       'link_internal'    => '<a href="%s" class="wiki-internal">%s</a>',
                       'link_create'      => '%2$s<a href="%1$s" class="wiki-create">?</a>',
                       'link_interwiki'   => '<a href="%s" class="wiki-interwiki">%s</a>',
                       'image'            => '%s',
                       'heading'          => '<a name="%1$s"></a><h%2$s>%3$s</h%2$s>',
                       'trail'            => '%s <a href="%s">%s</a> %s',
                       'trail_linkleft'   => '<a href="%s">%s</a>&lt;&lt; ',
                       'trail_emptyleft'  => '&lt;&lt; ',
                       'trail_linkright'  => ' &gt;&gt;<a href="%s">%s</a>',
                       'trail_emptyright' => ' &gt;&gt;',
                       ),
'actions'           => array( // available page actions. action name => internal action handler class
                       'view'      => 'view',
                       'history'   => 'history',
                       'edit'      => 'edit',
                       'print'     => 'print',
                       'perms'     => 'permissions',
                       'bookmark'  => 'view'
                       )
);

/* This are the permission flags. You really shouldn't edit these ;-) */
define('PERM_VIEW', 1);              // Permission to view pages.
define('PERM_EDIT', 2);              // Permission to edit pages.
define('PERM_HISTORY', 4);           // Permission to view the history.
define('PERM_RESTORE', 8);           // Permission to restore old versions.



define('PERM_RENAME', 128);          // Permission to rename a page.
define('PERM_DELETE', 256);          // Permission to delete a page.
define('PERM_IGNORELOCAL', 512);     // Permission to ignore local access masks.
define('PERM_SETLOCAL', 1024);       // Permission to modify local access masks.
define('PERM_USEACP', 2048);         // Permission to use the acp.
?>
