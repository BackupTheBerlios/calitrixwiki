{include file="header.tpl"}

{include file="inline_message.tpl"}
{include file="form_errors.tpl"}

{if $canSetLocal}<table cellspacing="0" cellpadding="0" border="0" width="100%">
 <tr>
  <td class="td-head">{$lang.perms_group_name}</td>
  <td class="td-head" colspan="2">{$lang.perms_access_mask}</td>
 </tr>
 {foreach from="$perms" key="groupId" item="groupData"}
 <tr>
  <td class="td-first">{$groupData.group_name}</td>
  <td class="td-cell">{if $groupData.perm_access_mask == 0}<span class="light-grey">{$lang.perms_mask_unchanged}</span>{else}{$groupData.perm_access_mask}{/if}</td>
  <td class="td-last">
   <a href="{wikiurl page="`$pageName`" action="options" op="perms" o="change" gid="`$groupId`"}" class="wiki-internal">{$lang.perms_edit_perms}</a> | 
   <a href="{wikiurl page="`$pageName`" action="option" op="perms" o="reset" gid="`$groupId`"}" class="wiki-internal">{$lang.perms_reset_perms}</a></td>
 </tr>
 {/foreach}
</table>

<br />{/if}

{if $canRename}<form method="post" action="{wikiurl page="`$pageName`" action="options" op="rename"}">
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
</form>{/if}

{if $canDelete}<form method="post" action="{wikiurl page="`$pageName`" action="options" op="delete"}">
<fieldset>
<legend>{$lang.options_delete}</legend>
<span class="light-grey">{$lang.options_delete_desc}</span><br /><br />

<input type="checkbox" name="confirm_delete" id="confirm-delete" /><label for="confirm-delete">{$lang.options_delete}</label><br /><br />

<input type="submit" value="{$lang.options_delete_submit}" />
</fieldset>
</form>{/if}

{include file="page_cmds.tpl"}
{include file="footer.tpl"}