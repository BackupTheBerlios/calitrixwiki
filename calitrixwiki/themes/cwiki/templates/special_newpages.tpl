{include file="header.tpl"}

{include file="page_links.tpl"}
<table cellspacing="0" cellpadding="0" border="0" width="100%">
 <tr>
  <td class="td-head">{$lang.wiki_page_name}</td>
  <td class="td-head">{$lang.wiki_date}</td>
  <td class="td-head">{$lang.history_version}</td>
 </tr>
{section name="idx" loop="$newPages"}
 <tr>
  <td class="td-first"><a href="{wikiurl page="`$newPages[idx].page_name`"}" class="wiki-internal">{$newPages[idx].page_name}</a></td>
  <td class="td-cell">{$newPages[idx].page_time}</td>
  <td class="td-last">{$newPages[idx].page_version}</td>
 </tr>
{/section}
</table>
{include file="page_links.tpl"}
{include file="footer.tpl"}