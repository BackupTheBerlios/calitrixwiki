{include file="admin_header.tpl"}

{include file="form_errors.tpl"}
{include file="inline_message.tpl"}

<form method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminNamespaces" op="del" conf="1" nspace="`$nspace`"}">
<fieldset>
<legend>{$lang.admin_namespace_confirm}</legend>
<span class="light-grey">{$lang.admin_namespace_confirm_desc}</span><br /><br />

<input type="radio" name="do" value="del" id="do-del" /><label for="do-del">{$lang.admin_namespace_del_pages}</label><br /><br />
<input type="radio" name="do" value="move" id="do-del" checked="checked" /><label for="do-del">{$lang.admin_namespace_move_pages}</label>&nbsp;&nbsp;
<select name="target_space">
{foreach from="$cfgNamespaces" key="cfgNamespace" item="cfgPageCount"}
{if $cfgNamespace != $nspace}
<option value="{$cfgNamespace}">{$cfgNamespace} ({$cfgPageCount})</option>
{/if}
{/foreach}
</select><br /><br />

<input type="submit" value="{$lang.admin_namespace_submit}" />
</fieldset>
</form>

{include file="admin_footer.tpl"}