{include file="header.tpl"}

<p>
{$lang.admin_desc}
</p>

{include file="form_errors.tpl"}

<form method="post" action="{wikiurl step="admin" lang="`$currentLang`"}">
<fieldset>
<legend>{$lang.admin_create}</legend>
{$lang.admin_name}<br />
<input type="text" name="admin_name" value="{$valName}" size="40" /><br /><br />

{$lang.admin_mail}<br />
<input type="text" name="admin_mail" value="{$valMail}" size="40" /><br /><br />

{$lang.admin_password}<br />
<input type="password" name="admin_pass" value="" size="40" /><br /><br />

{$lang.admin_password_c}<br />
<input type="password" name="admin_pass_c" value="" size="40" /><br /><br />

<input type="submit" value="{$lang.admin_submit}" />
</fieldset>
</form>

{include file="footer.tpl"}