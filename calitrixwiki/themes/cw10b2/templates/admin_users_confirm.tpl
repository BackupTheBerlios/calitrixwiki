{include file="admin_header.tpl"}

{include file="form_errors.tpl"}

<form method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminUsers" op="del" uid="`$cfgUserId`" conf="1" p="`$thisPage`"}">
<fieldset>
<legend>{$lang.admin_user_confirm}</legend>
<span class="light-grey">{$lang.admin_user_confirm_desc}</span><br /><br />

{$lang.admin_user_new_name}<br />
<input type="text" name="new_name" size="40" value="{$cfgNewName}" /><br /><br />

<input type="submit" value="{$lang.admin_user_confirm_submit}" />
</fieldset>
</form>

{include file="admin_footer.tpl"}