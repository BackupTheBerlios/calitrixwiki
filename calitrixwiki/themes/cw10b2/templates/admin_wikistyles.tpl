{include file="admin_header.tpl"}

{include file="inline_message.tpl"}

<table cellspacing="0" cellpadding="0" border="0" width="100%">
 <tr>
  <td class="td-head">{$lang.admin_wikistyle_name}</td>
  <td class="td-head">{$lang.admin_wikistyle_attribs}</td>
  <td class="td-head" colspan="2">{$lang.admin_wikistyle_display}</td>
 </tr>
 {foreach from="$cfgWikiStyles" key="cfgStyleName" item="cfgStyleAttribs"}
 <tr>
  <td class="td-first">{$cfgStyleName}</td>
  <td class="td-cell">
   {foreach from="$cfgStyleAttribs" key="cfgAttribName" item="cfgAttrib"}
   <tt>{$cfgAttribName}: {$cfgAttrib}</tt><br />
   {/foreach}
  </td>
  <td class="td-cell">
   <span style="{foreach from="$cfgStyleAttribs" key="cfgAttribName" item="cfgAttrib"}{$cfgAttribName}:{$cfgAttrib};{/foreach}">{$cfgStyleName}</span>
  </td>
  <td class="td-last">
   <a href="{wikiurl page="`$cfg.special_namespace`:AdminWikiStyles" op="editstyle" style="`$cfgStyleName`"}">{$lang.admin_edit}</a> |
   <a href="{wikiurl page="`$cfg.special_namespace`:AdminWikiStyles" op="delstyle" style="`$cfgStyleName`"}">{$lang.admin_delete}</a>
   </td>
 </tr>
 {/foreach}
</table>
{include file="admin_footer.tpl"}