{include file="header.tpl"}

{include file="form_errors.tpl"}

{if $regDone}
{$lang.register_done}
{/if}

{if !$loggedIn && !$regDone}
<form method="post" action="{wikiurl page="`$cfg.special_namespace`:Register"}">
<fieldset>
<legend>{$lang.register_account}</legend>
{$lang.register_username}<br />
<input type="text" size="25" name="username" maxlength="50" value="{$valUser}" /><br /><br />
{$lang.register_email}<br />
<input type="text" size="25" name="email" maxlength="70" value="{$valEmail}" /><br /><br />
{$lang.register_password}<br />
<input type="password" size="25" name="password" /><br /><br />
{$lang.register_password_confirm}<br />
<input type="password" size="25" name="confirm" /><br /><br />
<input type="checkbox" name="use_cookies" id="usecookies" {$cookiesChecked} /><label for="usecookies">{$lang.register_use_cookies}</label><br /><br />
<input type="submit" value="{$lang.register_submit}" />
</fieldset>
</form>
{/if}

{include file="footer.tpl"}