{include file="admin_header.tpl"}

{include file="inline_message.tpl"}

<form name="dbtables" method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminDbBackup"}">
<fieldset>
<legend>{$lang.admin_db_backup_tables}</legend>
<span class="light-grey">{$lang.admin_db_backup_desc}</span><br /><br />

<table cellspacing="0" cellpadding="0" border="0" width="100%">
 <tr>
  <td class="td-head" colspan="2">{$lang.admin_db_table_name}</td>
  <td class="td-head">{$lang.admin_db_table_rows}</td>
  <td class="td-head">{$lang.admin_db_table_size}</td>
  <td class="td-head">{$lang.admin_db_table_overhead}</td>
 </tr>
 {foreach from="$dbTables" item="dbTable"}
 <tr>
  <td class="td-first"><input type="checkbox" name="tables[]" value="{$dbTable.table_name}" id="{$dbTable.table_name}" /></td>
  <td class="td-cell"><label for="{$dbTable.table_name}">{$dbTable.table_name}</label></td>
  <td class="td-cell">{$dbTable.table_rows}</td>
  <td class="td-cell">{$dbTable.table_size}</td>
  <td class="td-last">{$dbTable.table_overhead}</td>
 </tr>
 {/foreach}
</table>
<a href="javascript:void(0)" onclick="setCheckBoxes('dbtables', 'tables[]', true)">{$lang.wiki_select_all}</a> / 
<a href="javascript:void(0)" onclick="setCheckBoxes('dbtables', 'tables[]', false)">{$lang.wiki_deselect_all}</a>
</fieldset>

<br />

<fieldset>
<legend>{$lang.admin_db_backup_options}</legend>
<span class="light-grey">{$lang.admin_db_backup_options_desc}</span><br /><br />

<input type="radio" name="sendtype" value="gzip" id="sendgzip" /><label for="sendgzip">{$lang.admin_db_send_gzipped}</label><br />
<input type="radio" name="sendtype" value="raw" id="sendraw" checked="checked" /><label for="sendraw">{$lang.admin_db_send_raw}</label><br />
<input type="radio" name="sendtype" value="none" id="senddisplay" checked="checked" /><label for="senddisplay">{$lang.admin_db_send_display}</label><br /><br />

<input type="submit" value="{$lang.admin_db_backup_selected}" />
</fieldset>
</form>

{include file="admin_footer.tpl"}