{include file="header.tpl"}

{include file="page_links.tpl"}

<ul>
{section name="idx" loop="$changes"}
 <li><a href="{wikiurl page="`$changes[idx].page_name`"}" class="wiki-internal">{$changes[idx].page_name}</a> (<a href="{$changes[idx].diff_url}" class="wiki-internal">{$lang.history_differences}</a> | <a href="{$changes[idx].history_url}">{$lang.history}</a>)<br />
 <span class="small-grey">{$lang.history_version} {$changes[idx].log_page_version} - {$changes[idx].log_time} {if $changes[idx].user_name != ''}<a href="{wikiurl page="`$cfg.users_namespace`:`$changes[idx].user_name`"}" class="wiki-internal">{$changes[idx].user_name}</a>{/if}&nbsp;{if $changes[idx].log_summary != ''}({$changes[idx].log_summary}){/if}</span>
{/section}
</ul>



{include file="page_links.tpl"}

{include file="footer.tpl"}