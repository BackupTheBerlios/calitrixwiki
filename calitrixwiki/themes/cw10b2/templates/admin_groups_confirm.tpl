{include file="admin_header.tpl"}

<form method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminGroups" op="del" conf="1" gid="`$cfgGroupId`"}">

<fieldset>
<legend>{$lang.admin_group_confirm}</legend>
<span class="light-grey">{$lang.admin_group_confirm_desc}</span><br /><br />

<input type="radio" name="do" value="del" id="do-del" /><label for="do-del">{$lang.admin_delete_contained}</label><br /><br />
<input type="radio" name="do" value="move" id="do-move" checked="checked" /><label for="do-move">{$lang.admin_move_contained}</label> 
<select name="target_group">
{foreach from="$cfgGroups" item="cfgGroup"}
{if $cfgGroup.group_id != $cfgGroupId}<option value="{$cfgGroup.group_id}">{$cfgGroup.group_name}</option>{/if}
{/foreach}
</select><br /><br />
<input type="submit" value="{$lang.admin_group_submit}" />
</fieldset>
</form>

{include file="admin_footer.tpl"}