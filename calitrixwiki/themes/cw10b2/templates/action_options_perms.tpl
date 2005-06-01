{include file="header.tpl"}

{include file="inline_message.tpl"}

<p><strong>{$lang.perms_change_desc}</strong></p>

<form method="post" action="{wikiurl page="`$pageNameUnique`" action="options" op="perms" o="change" gid="`$groupId`"}">
<table cellspacing="0" cellpadding="0" border="0">
 <tr>
  <td class="td-head">{$lang.perms_right}</td>
  <td class="td-head" width="1%">{$lang.perms_set}</td>
 </tr>
 <tr>
  <td class="td-first">{$lang.perms_right_view}</td>
  <td class="td-last"><input type="checkbox" name="perm_view"{$permViewChecked} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.perms_right_edit}</td>
  <td class="td-last"><input type="checkbox" name="perm_edit"{$permEditChecked} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.perms_right_history}</td>
  <td class="td-last"><input type="checkbox" name="perm_history"{$permHistoryChecked} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.perms_right_restore}</td>
  <td class="td-last"><input type="checkbox" name="perm_restore"{$permRestoreChecked} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.perms_right_rename}</td>
  <td class="td-last"><input type="checkbox" name="perm_rename"{$permRenameChecked} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.perms_right_delete}</td>
  <td class="td-last"><input type="checkbox" name="perm_delete"{$permDeleteChecked} /></td>
 </tr>
</table>
<br />
<input type="submit" value="{$lang.perms_submit}" />
</form>

{include file="page_cmds.tpl"}
{include file="footer.tpl"}