{include file="header.tpl"}

{include file="inline_message.tpl"}

{if !$loggedIn}
<form method="post" action="{wikiurl page="`$cfg.special_namespace`:Login"}">
<fieldset>
<legend>{$lang.login_login}</legend>
{$lang.login_username}<br />
<input type="text" size="25" name="username" maxlength="50" /><br /><br />
{$lang.login_password}<br />
<input type="password" size="25" name="password" /><br /><br />
<input type="checkbox" name="remember" id="r1" /><label for="r1">{$lang.login_remember}</label><br /><br />
<input type="submit" value="{$lang.login_submit}" />
</fieldset>
</form>
{/if}

{include file="footer.tpl"}