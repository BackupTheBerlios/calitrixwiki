<?PHP 
// This is a auto-generated file. Do not edit it directly. 
// Instead, always use the administration area of this Wiki to change configuration settings.
// Generated on 22.03.05 04:12.

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
                                'link_email'       => '<a href="%1$s" class="wiki-email">%2$s</a>',
                                'link_external'    => '<a href="%1$s" class="wiki-external">%2$s</a>',
                                'link_internal'    => '<a href="%1$s" class="wiki-internal"%3$s>%2$s</a>',
                                'link_interwiki'   => '<a href="%1$s" class="wiki-interwiki"%3$s>%2$s</a>',
                                'TOC'              => '<div class="wiki-toc" id="toc">%1$s [<a href="javascript:void(0)" onclick="toggleBox(\'toc\');toggleBox(\'stoc\')">%2$s</a>]'."\r\n".'%3$s'."\r\n".'</div><div class="wiki-toc" id="stoc" style="display:none">%1$s [<a href="javascript:void(0)" onclick="toggleBox(\'toc\');toggleBox(\'stoc\')">%4$s</a>]</div>',
                                'trail'            => '<table cellpadding="0" cellpadding="0" border="0" style="background:#f1f1f1;border:1px #cdcdcd solid;"><tr><td width="33%%">%s&laquo;</td><td width="34%%" align="center"><a href="%s">%s</a></td><td width="33%%" align="right">&raquo;%s</td></tr></table>',
                                'trail_emptyleft'  => '',
                                'trail_emptyright' => '',
                                'trail_linkleft'   => '<a href="%s">%s</a>',
                                'trail_linkright'  => '<a href="%s">%s</a>'
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
'default_theme'        => 'cw10b2',
'default_user_group'   => '2',
'display_namespaces'   => '1',
'doc_root'             => '/var/www/dev/calitrixwiki',
'enable_url_rewriting' => '0',
'indent_width'         => '30',
'install_time'         => '1085497602',
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
'url_format'           => 'http://192.168.2.99/dev/calitrixwiki/cwiki.php?page=%1$s&action=%2$s',
'url_format_short'     => 'http://192.168.2.99/dev/calitrixwiki/cwiki.php?page=%1$s',
'url_root'             => 'http://192.168.2.99/dev/calitrixwiki',
'users_namespace'      => 'User',
'wiki_title'           => 'CalitrixWiki',
'wiki_version'         => '1.0 Beta 3',
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
                                'cw10b2' => 'CalitrixWiki 1.0 Beta 2'
                                ),
'wiki_styles'          => array(
                                'highlight' => array(
                                                     'background-color' => '#ffd800',
                                                     'color'            => '#6e0000'
                                                     ),
                                'small'     => array(
                                                     'font-size' => '0.8em'
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
