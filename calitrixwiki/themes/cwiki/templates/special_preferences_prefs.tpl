{include file="header.tpl"}

{include file="special_preferences_links.tpl"}
{include file="inline_message.tpl"}
{include file="form_errors.tpl"}

<form method="post" action="{wikiurl page="`$cfg.special_namespace`:Preferences" op="prefs"}">
<input type="hidden" name="change" value="interface" />
<fieldset><legend>{$lang.prefs_prefs_interface}</legend>
{$lang.prefs_prefs_interface_desc}
<hr />

<strong>{$lang.prefs_prefs_language}</strong><br />
{$lang.prefs_prefs_language_desc}<br />
<select name="language">
<option value=""{if $user.user_language == ''} selected="selected"{/if}>{$lang.prefs_prefs_default}</option>
{foreach from="`$cfg.languages`" key="langCode" item="langName"}
<option value="{$langCode}"{if $user.user_language == $langCode} selected="selected"{/if}>{$langName}</option>
{/foreach}
</select>

<hr />

<strong>{$lang.prefs_prefs_theme}</strong><br />
{$lang.prefs_prefs_theme_desc}<br />
<select name="theme">
<option value=""{if $user.user_theme == ''} selected="selected"{/if}>{$lang.prefs_prefs_default}</option>
{foreach from="`$cfg.themes`" key="themeDir" item="themeName"}
<option value="{$themeDir}"{if $user.user_theme== $themeDir} selected="selected"{/if}>{$themeName}</option>
{/foreach}
</select>

<hr />

<strong>{$lang.prefs_prefs_items_pp}</strong><br />
{$lang.prefs_prefs_items_pp_desc}<br />
<select name="items_pp">
<option value=""{if $user.user_items_pp == 0} selected="selected"{/if}>{$lang.prefs_prefs_default}</option>
{foreach from="`$cfg.items_pp_select`" item="itemsNum"}
<option value="{$itemsNum}"{if $user.user_items_pp == $itemsNum} selected="selected"{/if}>{$itemsNum}</option>
{/foreach}
</select>

<hr />

<input type="submit" value="{$lang.prefs_prefs_submit}" />
</fieldset>
</form>

<form method="post" action="{wikiurl page="`$cfg.special_namespace`:Preferences" op="prefs"}">
<input type="hidden" name="change" value="mailing" />
<fieldset><legend>{$lang.prefs_prefs_mailing}</legend>
{$lang.prefs_prefs_mailing_desc}
<hr />
<input type="checkbox" name="enable_subs" id="prefs1"{if $subsChecked} checked="checked"{/if} /><label for="prefs1">{$lang.prefs_prefs_enable_subs}</label><br />
<input type="checkbox" name="receive_news" id="prefs2"{if $user.user_enable_mails == 1}checked="checked"{/if} /><label for="prefs2">{$lang.prefs_prefs_receive_news}</label><br /><br />
<input type="submit" value="{$lang.prefs_prefs_submit}" />
</fieldset>
</form>

<form method="post" action="{wikiurl page="`$cfg.special_namespace`:Preferences" op="prefs"}">
<input type="hidden" name="change" value="misc" />
<fieldset><legend>{$lang.prefs_prefs_misc}</legend>
<input type="checkbox" name="use_cookies" id="prefs3"{if $user.user_use_cookies == 1} checked="checked"{/if} /><label for="prefs3">{$lang.prefs_prefs_use_cookies}</label><br />
<input type="checkbox" name="dblclick_editing" id="prefs4"{if $user.user_dblclick_editing == 1} checked="checked"{/if} /><label for="prefs4">{$lang.prefs_prefs_dblclick_editing}</label><br /><br />
<input type="submit" value="{$lang.prefs_prefs_submit}" />
</fieldset>
</form>

{include file="footer.tpl"}