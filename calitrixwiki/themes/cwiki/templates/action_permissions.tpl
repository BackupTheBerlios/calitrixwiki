{include file="header.tpl"}

{include file="inline_message.tpl"}

<table cellspacing="0" cellpadding="0" border="0" width="100%">
 <tr>
  <td class="td-head">{$lang.perms_group_name}</td>
  <td class="td-head" colspan="2">{$lang.perms_access_mask}</td>
 </tr>
 {foreach from="$perms" key="groupId" item="groupData"}
 <tr>
  <td class="td-first">{$groupData.group_name}</td>
  <td class="td-cell">{if $groupData.perm_access_mask == 0}<span class="light-grey">{$lang.perms_mask_unchanged}</span>{else}{$groupData.perm_access_mask}{/if}</td>
  <td class="td-last">
   <a href="{wikiurl page="`$pageName`" action="perms" o="change" gid="`$groupId`"}" class="wiki-internal">{$lang.perms_edit_perms}</a> | 
   <a href="{wikiurl page="`$pageName`" action="perms" o="reset" gid="`$groupId`"}" class="wiki-internal">{$lang.perms_reset_perms}</a></td>
 </tr>
 {/foreach}
</table>

{include file="page_cmds.tpl"}
{include file="footer.tpl"}