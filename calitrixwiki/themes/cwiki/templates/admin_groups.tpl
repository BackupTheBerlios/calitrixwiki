{include file="admin_header.tpl"}

{include file="inline_message.tpl"}

<table cellspacing="0" cellpadding="0" border="0" width="100%">
 <tr>
  <td class="td-head">{$lang.admin_group_id}</td>
  <td class="td-head">{$lang.admin_group_name}</td>
  <td class="td-head" colspan="2">{$lang.admin_group_mask}</td>
 </tr>
 {foreach from="$cfgGroups" item="cfgGroup"}
 <tr>
  <td class="td-first" width="1%">{$cfgGroup.group_id}</td>
  <td class="td-cell" width="40%">{$cfgGroup.group_name}</td>
  <td class="td-cell" width="19%">{$cfgGroup.group_access_mask}</td>
  <td class="td-last" width="30%">
   <a href="{wikiurl page="`$cfg.special_namespace`:AdminGroups" op="del" gid="`$cfgGroup.group_id`"}">{$lang.admin_delete}</a> |
   <a href="{wikiurl page="`$cfg.special_namespace`:AdminGroups" op="edit" gid="`$cfgGroup.group_id`"}">{$lang.admin_edit}</a>
  </td>
 </tr>
 {/foreach}
</table>

{include file="admin_footer.tpl"}