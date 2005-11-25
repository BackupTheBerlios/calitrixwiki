{include file="admin_header.tpl"}

<form method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminGroups" op="`$cfgOp`" gid="`$cfgGroupId`"}">

{$lang.admin_group_name}<br />
<input type="text" size="30" name="group_name" value="{$cfgGroupName}" />
<br /><br />
<table cellspacing="0" cellpadding="0" border="0" width="100%">
 <tr>
  <td class="td-head">{$lang.admin_perms_right}</td>
  <td class="td-head" width="1%">{$lang.perms_set}</td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_perms_right_view}</td>
  <td class="td-last"><input type="checkbox" name="perm_view"{if $cfgPermView} checked="checked"{/if} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_perms_right_edit}</td>
  <td class="td-last"><input type="checkbox" name="perm_edit"{if $cfgPermEdit} checked="checked"{/if} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_perms_right_history}</td>
  <td class="td-last"><input type="checkbox" name="perm_history"{if $cfgPermHistory} checked="checked"{/if} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_perms_right_restore}</td>
  <td class="td-last"><input type="checkbox" name="perm_restore"{if $cfgPermRestore} checked="checked"{/if} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_perms_right_rename}</td>
  <td class="td-last"><input type="checkbox" name="perm_rename"{if $cfgPermRename} checked="checked"{/if} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_perms_right_delete}</td>
  <td class="td-last"><input type="checkbox" name="perm_delete"{if $cfgPermDelete} checked="checked"{/if} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_perms_right_upload}</td>
  <td class="td-last"><input type="checkbox" name="perm_upload"{if $cfgPermUpload} checked="checked"{/if} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_perms_right_muploads}</td>
  <td class="td-last"><input type="checkbox" name="perm_muploads"{if $cfgPermMUploads} checked="checked"{/if} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_perms_right_ignore_local}</td>
  <td class="td-last"><input type="checkbox" name="perm_iglocal"{if $cfgPermIgLocal} checked="checked"{/if} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_perms_right_set_local}</td>
  <td class="td-last"><input type="checkbox" name="perm_setlocal"{if $cfgPermSetLocal} checked="checked"{/if} /></td>
 </tr>
 <tr>
  <td class="td-first">{$lang.admin_perms_right_use_acp}</td>
  <td class="td-last"><input type="checkbox" name="perm_useacp"{if $cfgPermUseAcp} checked="checked"{/if} /></td>
 </tr>
</table>

<input type="submit" value="{$lang.admin_group_submit}" />
</form>

{include file="admin_footer.tpl"}