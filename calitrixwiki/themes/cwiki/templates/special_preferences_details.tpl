{include file="header.tpl"}

{include file="special_preferences_links.tpl"}
{include file="inline_message.tpl"}
{include file="form_errors.tpl"}

<form method="post" action="{wikiurl page="`$cfg.special_namespace`:Preferences" op="details"}">
<input type="hidden" name="change" value="email">
<fieldset><legend>{$lang.prefs_details_email}</legend>
{$lang.prefs_details_email_desc}
<hr />
{$lang.prefs_details_new_email}<br />
<input type="text" name="email" size="30" maxlength="70" value="{$emailValue}" />
&nbsp;<input type="submit" value="{$lang.prefs_details_submit}">
</fieldset>
</form>

<form method="post" action="{wikiurl page="`$cfg.special_namespace`:Preferences" op="details"}">
<input type="hidden" name="change" value="password">
<fieldset><legend>{$lang.prefs_details_password}</legend>
{$lang.prefs_details_password_desc}
<hr />
{$lang.prefs_details_old_password}<br />
<input type="password" name="old_password" size="20" /><br /><br />
{$lang.prefs_details_new_password}<br />
<input type="password" name="new_password" size="20" /><br />
{$lang.prefs_details_password_confirm}<br />
<input type="password" name="new_confirm" size="20" /><br /><br />
<input type="submit" value="{$lang.prefs_details_submit}">
</fieldset>
</form>

{include file="footer.tpl"}