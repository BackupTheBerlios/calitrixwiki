{include file="admin_header.tpl"}

{include file="inline_message.tpl"}

<form name="dbtables" method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminDbOptimize"}">
<fieldset>
<legend>{$lang.admin_db_tables}</legend>
<span class="light-grey">{$lang.admin_db_tables_desc}</span><br /><br />

<table cellspacing="0" cellpadding="0" border="0" width="100%">
 <tr>
  <td class="td-head" colspan="2">{$lang.admin_db_table_name}</td>
  <td class="td-head">{$lang.admin_db_table_rows}</td>
  <td class="td-head">{$lang.admin_db_table_size}</td>
  <td class="td-head">{$lang.admin_db_table_overhead}</td>
 </tr>
 {foreach from="$dbTables" item="dbTable"}
 <tr>
  <td class="td-first"{if $dbTable.table_overheaded} style="background-color:#ffca64"{/if}><input type="checkbox" name="tables[]" value="{$dbTable.table_name}" id="{$dbTable.table_name}"{if $dbTable.table_overheaded} checked="checked"{/if} /></td>
  <td class="td-cell"{if $dbTable.table_overheaded} style="background-color:#ffca64"{/if}><label for="{$dbTable.table_name}">{$dbTable.table_name}</label></td>
  <td class="td-cell"{if $dbTable.table_overheaded} style="background-color:#ffca64"{/if}>{$dbTable.table_rows}</td>
  <td class="td-cell"{if $dbTable.table_overheaded} style="background-color:#ffca64"{/if}>{$dbTable.table_size}</td>
  <td class="td-last"{if $dbTable.table_overheaded} style="background-color:#ffca64"{/if}>{$dbTable.table_overhead}</td>
 </tr>
 {/foreach}
</table>
<a href="javascript:void(0)" onclick="setCheckBoxes('dbtables', 'tables[]', true)">{$lang.wiki_select_all}</a> / 
<a href="javascript:void(0)" onclick="setCheckBoxes('dbtables', 'tables[]', false)">{$lang.wiki_deselect_all}</a>
<br /><br />
<input type="submit" value="{$lang.admin_db_optimize_selected}" />
</fieldset>
</form>

{include file="admin_footer.tpl"}