{include file="admin_header.tpl"}

{include file="form_errors.tpl"}
{include file="inline_message.tpl"}

<a name="config-misc"></a>
<form method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminConfig"}">
<input type="hidden" name="change" value="misc" />
<fieldset>
<legend>{$lang.admin_config_misc}</legend>
<span class="light-grey">{$lang.admin_config_misc_desc}</span><br /><br />

{$lang.admin_config_wiki_title}<br />
<input type="text" name="wiki_title" size="50" value="{$cfgWikiTitle}" /><br /><br />

{$lang.admin_config_default_page}<br />
<input type="text" name="default_page" size="50" value="{$cfgDefaultPage}" /><br /><br />

<input type="submit" value="{$lang.admin_config_submit}" />
</fieldset>
</form>

<a name="config-ui"></a>
<form method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminConfig"}">
<input type="hidden" name="change" value="ui" />
<fieldset>
<legend>{$lang.admin_config_ui}</legend>
<span class="light-grey">{$lang.admin_config_ui_desc}</span><br /><br />

{$lang.admin_config_default_lang}<br />
<select name="default_lang">
{foreach from="$cfgLangs" key="langCode" item="langName"}
<option value="{$langCode}"{if $langCode == $cfgDefaultLang} selected="selected"{/if}>{$langName}</option>
{/foreach}
</select><br /><br />

{$lang.admin_config_default_theme}<br />
<select name="default_theme">
{foreach from="$cfgThemes" key="themeDir" item="themeName"}
<option value="{$themeDir}"{if $themeDir == $cfgDefaultTheme} selected="selected"{/if}>{$themeName}</option>
{/foreach}
</select><br /><br />

{$lang.admin_config_date_format}<br />
<input type="text" name="date_format" size="50" value="{$cfgDateFormat}" /><br /><br />

{$lang.admin_config_teaser_length}<br />
<input type="text" name="teaser_length" size="5" value="{$cfgTeaserLength}" /><br /><br />

{$lang.admin_config_items_pp}<br />
<input type="text" name="items_pp" size="5" value="{$cfgItemsPP}" /><br /><br />

{$lang.admin_config_summary_length}<br />
<input type="text" name="summary_length" size="5" value="{$cfgSummaryLength}" /><br /><br />

<input type="checkbox" name="dblclick_editing" id="dblclick-editing"{if $cfgDblclickEditing == 1} checked="checked"{/if} /><label for="dblclick-editing">{$lang.admin_config_dblclick_edits}</label><br /><br />

<input type="submit" value="{$lang.admin_config_submit}" />
</fieldset>
</form>

<a name="config-users"></a>
<form method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminConfig"}">
<input type="hidden" name="change" value="users" />
<fieldset>
<legend>{$lang.admin_config_users}</legend>
<span class="light-grey">{$lang.admin_config_users_desc}</span><br /><br />

{$lang.admin_config_default_ggroup}<br />
<select name="default_ggroup">
{foreach from="$userGroups" key="groupId" item="groupName"}
<option value="{$groupId}"{if $groupId == $cfgDefaultGGroup} selected="selected"{/if}>{$groupName}</option>
{/foreach}
</select><br /><br />

{$lang.admin_config_default_ugroup}<br />
<select name="default_ugroup">
{foreach from="$userGroups" key="groupId" item="groupName"}
<option value="{$groupId}"{if $groupId == $cfgDefaultUGroup} selected="selected"{/if}>{$groupName}</option>
{/foreach}
</select><br /><br />

<hr />

{$lang.admin_config_min_pw_length}<br />
<input type="text" name="min_pw_length" size="5" value="{$cfgMinPwLength}" /><br /><br />

{$lang.admin_config_min_user_length}<br />
<input type="text" name="min_user_length" size="5" value="{$cfgMinUserLength}" /><br /><br />

{$lang.admin_config_max_user_length}<br />
<input type="text" name="max_user_length" size="5" value="{$cfgMaxUserLength}" /><br /><br />

<hr />

{$lang.admin_config_sess_lifetime}<br />
<input type="text" name="session_lifetime" size="10" value="{$cfgSessLifetime}" /><br /><br />

{$lang.admin_config_cookie_prefix}<br />
<input type="text" name="cookie_prefix" size="50" value="{$cfgCookiePrefix}" /><br /><br />

{$lang.admin_config_cookie_path}<br />
<input type="text" name="cookie_path" size="50" value="{$cfgCookiePath}" /><br /><br />

{$lang.admin_config_cookie_domain}<br />
<input type="text" name="cookie_domain" size="50" value="{$cfgCookieDomain}" /><br /><br />

<input type="checkbox" name="cookie_secure" id="cookie-secure"{if $cfgCookieSecure == 1} checked="checked"{/if} /><label for="cookie-secure">{$lang.admin_config_cookie_secure}</label><br /><br />
<input type="submit" value="{$lang.admin_config_submit}" />
</fieldset>
</form>

<a name="config-mailing"></a>
<form method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminConfig"}">
<input type="hidden" name="change" value="mailing" />
<fieldset>
<legend>{$lang.admin_config_mailing}</legend>
<span class="light-grey">{$lang.admin_config_mailing_desc}</span><br /><br />

{$lang.admin_config_mail_from}<br />
<input type="text" name="mail_from" size="50" value="{$cfgMailFrom}" /><br /><br />

{$lang.admin_config_mail_name}<br />
<input type="text" name="mail_name" size="50" value="{$cfgMailName}" /><br /><br />
<input type="submit" value="{$lang.admin_config_submit}" />
</fieldset>
</form>

<a name="config-namespaces"></a>
<form method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminConfig"}">
<input type="hidden" name="change" value="namespaces" />
<fieldset>
<legend>{$lang.admin_config_namespaces}</legend>
<span class="light-grey">{$lang.admin_config_namespaces_desc}</span><br /><br />

{$lang.admin_config_default_nspace}<br />
<select name="default_namespace">
{foreach from="$cfgNamespaces" item="namespace"}
<option value="{$namespace}"{if $namespace == $cfgDefaultNamespace} selected="selected"{/if}>{$namespace}</option>
{/foreach}
</select><br /><br />

{$lang.admin_config_special_nspace}<br />
<select name="special_namespace">
{foreach from="$cfgNamespaces" item="namespace"}
<option value="{$namespace}"{if $namespace == $cfgSpecialNamespace} selected="selected"{/if}>{$namespace}</option>
{/foreach}
</select><br /><br />

{$lang.admin_config_user_nspace}<br />
<select name="user_namespace">
{foreach from="$cfgNamespaces" item="namespace"}
<option value="{$namespace}"{if $namespace == $cfgUserNamespace} selected="selected"{/if}>{$namespace}</option>
{/foreach}
</select><br /><br />

<input type="submit" value="{$lang.admin_config_submit}" />
</fieldset>
</form>

<a name="config-paths"></a>
<form method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminConfig"}">
<input type="hidden" name="change" value="paths" />
<fieldset>
<legend>{$lang.admin_config_paths}</legend>
<span class="light-grey">{$lang.admin_config_paths_desc}</span><br /><br />

{$lang.admin_config_url_root}<br />
<input type="text" name="url_root" size="50" value="{$cfgUrlRoot}" /><br /><br />

{$lang.admin_config_doc_root}<br />
<input type="text" name="doc_root" size="50" value="{$cfgDocRoot}" /><br /><br />

{$lang.admin_config_actions_dir}<br />
<input type="text" name="actions_dir" size="50" value="{$cfgActionsDir}" /><br /><br />

{$lang.admin_config_lang_dir}<br />
<input type="text" name="lang_dir" size="50" value="{$cfgLangDir}" /><br /><br />

{$lang.admin_config_lib_dir}<br />
<input type="text" name="lib_dir" size="50" value="{$cfgLibDir}" /><br /><br />

{$lang.admin_config_special_dir}<br />
<input type="text" name="special_dir" size="50" value="{$cfgSpecialDir}" /><br /><br />

{$lang.admin_config_plugins_dir}<br />
<input type="text" name="plugins_dir" size="50" value="{$cfgPluginsDir}" /><br /><br />

{$lang.admin_config_themes_dir}<br />
<input type="text" name="themes_dir" size="50" value="{$cfgThemesDir}" /><br /><br />
<input type="submit" value="{$lang.admin_config_submit}" />
</fieldset>
</form>

<a name="config-parser"></a>
<form method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminConfig"}">
<input type="hidden" name="change" value="parser" />
<fieldset>
<legend>{$lang.admin_config_parser}</legend>
<span class="light-grey">{$lang.admin_config_parser_desc}</span><br /><br />

{$lang.admin_config_max_includes}<br />
<input type="text" name="max_includes" size="5" value="{$cfgMaxIncludes}" /><br /><br />

{$lang.admin_config_indent_width}<br />
<input type="text" name="indent_width" size="5" value="{$cfgIndentWidth}" /><br /><br />

{$lang.admin_config_link_num}<br />
<input type="text" name="link_num" size="5" value="{$cfgLinkNum}" /><br /><br />

{$lang.admin_config_title_format}<br />
<input type="text" name="title_format" size="50" value="{$cfgTitleFormat}" /><br /><br />

{$lang.admin_config_title_format_s}<br />
<input type="text" name="title_format_s" size="50" value="{$cfgTitleFormatS}" /><br /><br />

{$lang.admin_config_thispage}<br />
<input type="text" name="thispage" size="50" value="{$cfgThisPage}" /><br /><br />

{$lang.admin_config_thiswiki}<br />
<input type="text" name="thiswiki" size="50" value="{$cfgThisWiki}" /><br /><br />

<input type="checkbox" name="space_words" id="space-words"{if $cfgSpaceWords == 1} checked="checked"{/if} /><label for="space-words">{$lang.admin_config_space_words}</label><br /><br />

<input type="checkbox" name="display_nspaces" id="display-namespaces"{if $cfgDisplayNSpaces == 1} checked="checked"{/if} /><label for="display-namespaces">{$lang.admin_config_display_nspaces}</label><br /><br />

<input type="checkbox" name="auto_link" id="auto-link"{if $cfgAutoLink == 1} checked="checked"{/if} /><label for="auto-link">{$lang.admin_config_auto_link}</label><br /><br />

<input type="checkbox" name="link_self" id="link-self"{if $cfgLinkSelf == 1} checked="checked"{/if} /><label for="link-self">{$lang.admin_config_link_self}</label><br /><br />

<input type="submit" value="{$lang.admin_config_submit}" />
</fieldset>
</form>

<a name="config-urlrewrite"></a>
<form method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminConfig"}">
<input type="hidden" name="change" value="urlrewrite" />
<fieldset>
<legend>{$lang.admin_config_urlrewrite}</legend>
<span class="light-grey">{$lang.admin_config_urlrewrite_desc}</span><br /><br />

<input type="checkbox" name="enable_rewrite" id="enable-rewrite"{if $cfgEnableUrlRewrite == 1} checked="checked"{/if} /><label for="enable-rewrite">{$lang.admin_config_enable_rewrite}</label><br /><br />

<div id="rewrite-toggle">
<a href="javascript:void(0)" onclick="toggleBox('rewrite-extended');toggleBox('rewrite-toggle');" style="font-weight:bold">{$lang.admin_extended}</a><br /><br />
</div>
<div id="rewrite-extended" style="display:none">
<a href="javascript:void(0)" onclick="toggleBox('rewrite-extended');toggleBox('rewrite-toggle');" style="font-weight:bold">{$lang.admin_simple}</a><br /><br />
{$lang.admin_config_rewrite_rule}<br />
<input type="text" name="rewrite_match" size="25" value="{$cfgRewriteMatch}" /> =&gt;  <input type="text" name="rewrite_replace" size="25" value="{$cfgRewriteReplace}" /><br /><br />

{$lang.admin_config_urlformat}<br />
<input type="text" name="url_format" size="50" value="{$cfgUrlFormat}" /><br /><br />

{$lang.admin_config_urlformat_short}<br />
<input type="text" name="url_format_short" size="50" value="{$cfgUrlFormatShort}" /><br /><br />
</div>
<input type="submit" value="{$lang.admin_config_submit}" />
</fieldset>
</form>

{include file="admin_footer.tpl"}