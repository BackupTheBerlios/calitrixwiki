{include file="header.tpl"}

{include file="page_links.tpl"}
<table cellspacing="0" cellpadding="0" border="0" width="100%">
 <tr>
  <td class="td-head">{$lang.wiki_username}</td>
  <td class="td-head">{$lang.wiki_registered_since}</td>
 </tr>
{section name="idx" loop="$users"}
 <tr>
  <td class="td-first"><a href="{wikiurl page="`$cfg.users_namespace`:`$users[idx].user_name`"}" class="wiki-internal">{$users[idx].user_name}</a></td>
  <td class="td-last">{$users[idx].user_reg_time}</td>
 </tr>
{/section}
</table>
{include file="page_links.tpl"}
{include file="footer.tpl"}