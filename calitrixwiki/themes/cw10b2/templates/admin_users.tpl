{include file="admin_header.tpl"}

{include file="inline_message.tpl"}

{include file="page_links.tpl"}
<table cellspacing="0" cellpadding="0" border="0" width="100%">
 <tr>
  <td class="td-head">{$lang.admin_user_id}</td>
  <td class="td-head">{$lang.admin_user_name}</td>
  <td class="td-head">{$lang.admin_user_email}</td>
  <td class="td-head">{$lang.admin_user_mask}</td>
  <td class="td-head">{$lang.admin_user_reg_time}</td>
  <td class="td-head" colspan="2">{$lang.admin_user_last_visit}</td>
 </tr>
 {foreach from="$cfgUsers" item="cfgUser"}
 <tr>
  <td class="td-first">{$cfgUser.user_id}</td>
  <td class="td-cell">
   <a href="{wikiurl page="`$cfg.users_namespace`:`$cfgUser.user_name`"}">{$cfgUser.user_name}</a>
  </td>
  <td class="td-cell">{$cfgUser.user_email}</td>
  <td class="td-cell">
   {if $cfgUser.user_access_mask < 0}<span class="light-grey">{$lang.admin_user_mask_unchanged}</span>
   {else}{$cfgUser.user_access_mask}
   {/if}
  </td>
  <td class="td-cell">{$cfgUser.user_reg_time}</td>
  <td class="td-cell">{$cfgUser.user_last_visit}</td>
  <td class="td-last">
   <a href="{wikiurl page="`$cfg.special_namespace`:AdminUsers" op="del" uid="`$cfgUser.user_id`" p="`$thisPage`"}">{$lang.admin_delete}</a> |
   <a href="{wikiurl page="`$cfg.special_namespace`:AdminUsers" op="edit" uid="`$cfgUser.user_id`" p="`$thisPage`"}">{$lang.admin_edit}</a>
  </td>
 </tr>
 {/foreach}
</table>
{include file="page_links.tpl"}

{include file="admin_footer.tpl"}