<?PHP 
// This is a auto-generated file. Do not edit it directly. 
// Instead, always use the administration area of this Wiki to change configuration settings.
// Generated on 12.09.04 15:15.

$cfg = array(
'actions'              => array(
                                'bookmark' => 'view',
                                'edit'     => 'edit',
                                'history'  => 'history',
                                'options'  => 'options',
                                'print'    => 'view',
                                'view'     => 'view'
                                ),
'code_snippets'        => array(
                                'heading'          => '<a name="%1$s"></a><h%2$s>%3$s</h%2$s>',
                                'image'            => '%s',
                                'link_create'      => '%2$s<a href="%1$s" class="wiki-create"%3$s>?</a>',
                                'link_internal'    => '<a href="%1$s" class="wiki-internal"%3$s>%2$s</a>',
                                'link_interwiki'   => '<a href="%1$s" class="wiki-interwiki"%3$s>%2$s</a>',
                                'TOC'              => '<div class="wiki-toc" id="toc">%1$s [<a href="javascript:void(0)" onclick="toggleBox(\'toc\');toggleBox(\'stoc\')">%2$s</a>]'."\r\n".'%3$s'."\r\n".'</div><div class="wiki-toc" id="stoc" style="display:none">%1$s [<a href="javascript:void(0)" onclick="toggleBox(\'toc\');toggleBox(\'stoc\')">%4$s</a>]</div>',
                                'trail'            => '%s <a href="%s">%s</a> %s',
                                'trail_emptyleft'  => '&lt;&lt; ',
                                'trail_emptyright' => ' &gt;&gt;',
                                'trail_linkleft'   => '<a href="%s">%s</a>&lt;&lt; ',
                                'trail_linkright'  => ' &gt;&gt;<a href="%s">%s</a>'
                                ),
'actions_dir'          => '/var/www/dev/calitrixwiki/actions',
'auto_link'            => '1',
'cookie_domain'        => 'localhost',
'cookie_path'          => '/',
'cookie_prefix'        => 'cwiki_',
'cookie_secure'        => '0',
'date_format'          => 'd.m.y H:i',
'dblclick_editing'     => '0',
'default_action'       => 'view',
'default_guest_group'  => '1',
'default_lang'         => 'de',
'default_namespace'    => 'Main',
'default_page'         => 'HomePage',
'default_theme'        => 'cw',
'default_user_group'   => '2',
'display_namespaces'   => '1',
'doc_root'             => '/var/www/dev/calitrixwiki',
'enable_caching'       => '0',
'enable_url_rewriting' => '0',
'html_newline'         => '<br />',
'html_paragraph'       => '<p></p>',
'indent_width'         => '30',
'items_per_page'       => '20',
'lang_dir'             => '/var/www/dev/calitrixwiki/lang',
'lib_dir'              => '/var/www/dev/calitrixwiki/libs',
'link_num'             => '0',
'link_self'            => '1',
'mailer_from'          => 'CalitrixWiki Mailer',
'mail_from'            => 'noreply@calitrix.de',
'match_email'          => '/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]+$/i',
'max_includes'         => '5',
'max_summary_length'   => '160',
'max_username_length'  => '30',
'min_password_length'  => '6',
'min_username_length'  => '3',
'plugins_dir'          => '/var/www/dev/calitrixwiki/plugins',
'rewrite_rule_match'   => '^([^?./]+)$',
'rewrite_rule_replace' => 'cwiki.php?page=$1&%{QUERY_STRING}',
'session_lifetime'     => '1800',
'space_wiki_words'     => '0',
'special_dir'          => '/var/www/dev/calitrixwiki/specialpages',
'special_namespace'    => 'Wiki',
'teaser_length'        => '400',
'themes_dir'           => '/var/www/dev/calitrixwiki/themes',
'thispage_interwiki'   => 'ThisPage',
'thiswiki_interwiki'   => 'ThisWiki',
'title_format'         => '([A-Z\xc0-\xde][a-z\xdf-\xff]+:)?([A-Za-z0-9\xc0-\xff_-]+)',
'title_format_search'  => '(?<=\s|^)([A-Z\xc0-\xde][a-z\xdf-\xff]+:)?(([A-Z\xc0-\xde][a-z0-9\xdf-\xff]+){2,})(?=\s|\.|,|;|:|$)',
'url_format'           => 'http://192.168.2.100/dev/calitrixwiki/cwiki.php?page=%1$s&action=%2$s',
'url_format_short'     => 'http://192.168.2.100/dev/calitrixwiki/cwiki.php?page=%1$s',
'url_root'             => 'http://192.168.2.100/dev/calitrixwiki',
'users_namespace'      => 'User',
'wiki_title'           => 'CalitrixWiki',
'interwiki'            => array(
                                'C2'        => 'http://c2.com/cgi/wiki?%s',
                                'Calitrix'  => 'http://www.calitrix.de/%s',
                                'ISBN'      => 'http://www.amazon.com/exec/obidos/tg/detail/-/%s',
                                'Meatball'  => 'http://www.usemod.com/cgi-bin/mb.pl?%s',
                                'UseMod'    => 'http://www.usemod.com/cgi-bin/wiki.pl?%s',
                                'Wikipedia' => 'http://www.wikipedia.org/wiki/%s'
                                ),
'items_pp_select'      => array(
                                '0' => '5',
                                '1' => '10',
                                '2' => '20',
                                '3' => '50',
                                '4' => '100'
                                ),
'languages'            => array(
                                'de' => 'Deutsch',
                                'en' => 'English'
                                ),
'namespaces'           => array(
                                '0' => 'User',
                                '1' => 'Main',
                                '2' => 'Wiki'
                                ),
'sitemap_chars'        => array(
                                'A' => '0',
                                'B' => '0',
                                'C' => '0',
                                'D' => '0',
                                'E' => '0',
                                'F' => '0',
                                'G' => '0',
                                'H' => '0',
                                'I' => '0',
                                'J' => '0',
                                'K' => '0',
                                'L' => '0',
                                'M' => '0',
                                'N' => '0',
                                'O' => '0',
                                'P' => '0',
                                'Q' => '0',
                                'R' => '0',
                                'S' => '0',
                                'T' => '0',
                                'U' => '0',
                                'V' => '0',
                                'W' => '0',
                                'X' => '0',
                                'Y' => '0',
                                'Z' => '0'
                                ),
'style_attributes'     => array(
                                'background-color' => '/^(#[0-9a-fA-F]{6}|[a-zA-Z-]+)$/',
                                'border'           => '/^(([1-9]|10)px (#[0-9a-fA-F]{6}|[a-zA-Z-]+) (dashed|solid|dotted|double))$/',
                                'border-bottom'    => '/^(([1-9]|10)px (#[0-9a-fA-F]{6}|[a-zA-Z-]+) (dashed|solid|dotted|double))$/',
                                'border-left'      => '/^(([1-9]|10)px (#[0-9a-fA-F]{6}|[a-zA-Z-]+) (dashed|solid|dotted|double))$/',
                                'border-right'     => '/^(([1-9]|10)px (#[0-9a-fA-F]{6}|[a-zA-Z-]+) (dashed|solid|dotted|double))$/',
                                'border-top'       => '/^(([1-9]|10)px (#[0-9a-fA-F]{6}|[a-zA-Z-]+) (dashed|solid|dotted|double))$/',
                                'clear'            => '/^(left|right|both|none)$/',
                                'color'            => '/^(#[0-9a-fA-F]{6}|[a-zA-Z-]+)$/',
                                'display'          => '/^(block|inline|none)$/',
                                'float'            => '/^(left|right)$/',
                                'font-family'      => '/^(serif|sans-serif|monospace)$/',
                                'font-size'        => '/^([1-3](\.[0-9])?|0\.[5-9])em$/',
                                'font-style'       => '/^(italic|normal)$/',
                                'font-weight'      => '/^(bold|normal)$/',
                                'margin'           => '/^(-)?([0-9]{1,3})px$/',
                                'margin-bottom'    => '/^(-)?([0-9]{1,3})px$/',
                                'margin-left'      => '/^(-)?([0-9]{1,3})px$/',
                                'margin-right'     => '/^(-)?([0-9]{1,3})px$/',
                                'margin-top'       => '/^(-)?([0-9]{1,3})px$/',
                                'padding'          => '/^(-)?([0-9]{1,3})px$/',
                                'padding-bottom'   => '/^(-)?([0-9]{1,3})px$/',
                                'padding-left'     => '/^(-)?([0-9]{1,3})px$/',
                                'padding-right'    => '/^(-)?([0-9]{1,3})px$/',
                                'padding-top'      => '/^(-)?([0-9]{1,3})px$/',
                                'text-align'       => '/^(left|center|right|justify)$/',
                                'text-decoration'  => '/^(underline|overline|line-through|none)$/',
                                'vertical-align'   => '/^(top|middle|bottom)$/'
                                ),
'themes'               => array(
                                'cw'    => 'CalitrixWiki v.2',
                                'cwiki' => 'CalitrixWiki'
                                ),
'wiki_styles'          => array(
                                'highlight' => array(
                                                     'font-weight'      => 'bold',
                                                     'background-color' => '#ffd800',
                                                     'color'            => '#6e0000'
                                                     ),
                                'strike'    => array(
                                                     'text-decoration' => 'line-through'
                                                     ),
                                'underline' => array(
                                                     'text-decoration' => 'underline'
                                                     )
                                )
);
?>