{include file="header.tpl"}

<form method="get" action="{wikiurl page="`$pageName`"}">
<input type="hidden" name="page" value="{$pageName}" />
<input type="hidden" name="action" value="history" />
<input type="hidden" name="o" value="diff" />

{include file="page_links.tpl"}
<table cellspacing="5" cellpadding="0" border="0">
{foreach from="$versions" key="page_version" item="log"}
 <tr>
  <td nowrap="">
   <input type="radio" name="orig" value="{$log.log_page_version}" />
   <input type="radio" name="final" value="{$log.log_page_version}" />
  </td>
  <td nowrap="">{$lang.history_version} {$log.log_page_version} - {$log.log_time}</td>
  <td nowrap="">
   (<a href="{wikiurl page="`$pageName`" v="`$log.log_page_version`"}" class="wiki-internal">{$lang.history_view}</a> | 
   <a href="{wikiurl page="`$pageName`" action="edit" v="`$log.log_page_version`"}" class="wiki-internal">{$lang.history_restore}</a>)
  </td>
  <td>{if $log.user_name != ''}<a href="{wikiurl page="`$cfg.users_namespace`:`$log.user_name`"}" class="wiki-internal">{$log.user_name}</a>{/if}&nbsp;{if $log.log_summary != ''}<span class="light-grey">({$log.log_summary})</span>{/if}</td>
 </tr>
{/foreach}
</table>
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