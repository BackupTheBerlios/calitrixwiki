{include file="admin_header.tpl"}

<p>
{$lang.admin_index}
</p>

<table cellspacing="0" cellpadding="0" border="0">
 <tr>
  <td class="td-first"><strong>{$lang.admin_index_version}</strong></td>
  <td class="td-last">{$wikiVersion}</td>
 </tr>
 <tr>
  <td class="td-first"><strong>{$lang.admin_index_page_count}</strong></td>
  <td class="td-last">{$pageCount} ({$pagesPerDay} {$lang.admin_index_per_day})</td>
 </tr>
 <tr>
  <td class="td-first"><strong>{$lang.admin_index_edit_count}</strong></td>
  <td class="td-last">{$editCount} ({$editsPerDay} {$lang.admin_index_per_day})</td>
 </tr>
 <tr>
  <td class="td-first"><strong>{$lang.admin_index_db_size}</strong></td>
  <td class="td-last">{$dbSize}</td>
 </tr>
</table>

{include file="admin_footer.tpl"}