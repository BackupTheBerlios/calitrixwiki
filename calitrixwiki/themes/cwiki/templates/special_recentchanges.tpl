{include file="header.tpl"}

{include file="page_links.tpl"}

<table cellspacing="5" cellpadding="0" border="0">
{section name="idx" loop="$changes"}
 <tr>
  <td nowrap=""><a href="{wikiurl page="`$changes[idx].page_name`"}">{$changes[idx].page_name}</a></td>
  <td nowrap="">{$lang.history_version} {$changes[idx].log_page_version} - {$changes[idx].log_time}</td>
  <td nowrap="">(<a href="{$changes[idx].diff_url}">{$lang.history_differences}</a> | <a href="{$changes[idx].history_url}">{$lang.history}</a>)</td>
  <td>{if $changes[idx].user_name != ''}<a href="{wikiurl page="`$cfg.users_namespace`:`$changes[idx].user_name`"}">{$changes[idx].user_name}</a>{/if}&nbsp;{if $changes[idx].log_summary != ''}<span class="light-grey">({$changes[idx].log_summary})</span>{/if}</td>
 </tr>
{/section}
</table>

{include file="page_links.tpl"}

{include file="footer.tpl"}