{include file="header.tpl"}

{$loginMsg}
{if !$loggedIn}
<form method="post" action="{wikiurl page="`$cfg.special_namespace`:Login"}">
{$lang.login_username}<br />
<input type="text" size="25" name="username" maxlength="50" /><br />
{$lang.login_password}<br />
<input type="password" size="25" name="password" /><br />
<input type="checkbox" name="remember" id="r1" /><label for="r1">{$lang.login_remember}</label><br />
<input type="submit" value="{$lang.login_submit}" />
</form>
<span style="font-size:0.9em">({$lang.login_cookies})</span>
{/if}

{include file="footer.tpl"}