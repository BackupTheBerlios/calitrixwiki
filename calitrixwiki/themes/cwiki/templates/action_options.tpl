{include file="header.tpl"}

{include file="inline_message.tpl"}
{include file="form_errors.tpl"}

<form method="post" action="{wikiurl page="`$pageName`" action="options"}">
<input type="hidden" name="do" value="rename" />
<fieldset>
<legend>{$lang.options_rename}</legend>
<span class="light-grey">{$lang.options_rename_desc}</span><br /><br />

<select name="new_space">
{section name="idx" loop="$nSpaces"}
<option value="{$nSpaces[idx]}"{if $nSpaces[idx] == $pageNamespace} selected="selected"{/if}>{$nSpaces[idx]}</option>
{/section}
</select>:
<input type="text" name="new_name" size="40" value="{$pageNameOnly}" /><br /><br />

<input type="submit" value="{$lang.options_rename_submit}" />
</fieldset>
</form>

<form method="post" action="{wikiurl page="`$pageName`" action="options"}">
<input type="hidden" name="do" value="delete" />
<fieldset>
<legend>{$lang.options_delete}</legend>
<span class="light-grey">{$lang.options_delete_desc}</span><br /><br />

<input type="checkbox" name="confirm_delete" id="confirm-delete" /><label for="confirm-delete">{$lang.options_delete}</label><br /><br />

<input type="submit" value="{$lang.options_delete_submit}" />
</fieldset>
</form>

{include file="page_cmds.tpl"}
{include file="footer.tpl"}