{include file="header.tpl"}

<form method="get" action="{wikiurl page="`$pageNameUnique`"}">
<input type="hidden" name="page" value="{$pageNameUnique}" />
<input type="hidden" name="action" value="history" />
<input type="hidden" name="o" value="diff" />

{include file="page_links.tpl"}
<ul>
{foreach from="$versions" key="page_version" item="log"}
 <li><input type="radio" name="orig" value="{$log.log_page_version}" />&nbsp;<input type="radio" name="final" value="{$log.log_page_version}" />
  {$lang.history_version} {$log.log_page_version} (<a href="{wikiurl page="`$pageNameUnique`" v="`$log.log_page_version`"}" class="wiki-internal">{$lang.history_view}</a> | <a href="{wikiurl page="`$pageNameUnique`" action="edit" v="`$log.log_page_version`"}" class="wiki-internal">{$lang.history_restore}</a>)<br />
  <span class="small-grey">{$log.log_time} {if $log.user_name != ''}<a href="{wikiurl page="`$cfg.users_namespace`:`$log.user_name_raw`"}" class="wiki-internal">{$log.user_name}</a>{/if}
   {if $log.log_summary != ''}({$log.log_summary}){/if}</span>
 </li>
{/foreach}
</ul>
{include file="page_links.tpl"}
<input type="submit" value="{$lang.history_show}" />
</form>

<h3>{$lang.history_info}</h3>
<span class="wikiBold">{$lang.history_version_info}:</span>
{$lang.history_version_desc}<br /><br />

<span class="wikiBold">{$lang.history_restore_info}:</span>
{$lang.history_restore_desc}

{include file="page_cmds.tpl"}

{include file="footer.tpl"}