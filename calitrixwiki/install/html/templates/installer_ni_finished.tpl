{include file="header.tpl"}

{if !$isError}
<p>
{$lang.admin_finished_desc}
</p>
<p>
&raquo; <a href="{$wikiUrl}">{$lang.admin_wiki}</a><br />
&raquo; <a href="http://www.calitrix.de" target="_new">{$lang.admin_cwiki_home}</a><br />
&raquo; <a href="http://www.calitrix.de/Dokumentation" target="_new">{$lang.admin_cwiki_doc}</a>
</p>
{else}
<p>
{$lang.admin_failed_desc} ({$error})
</p>
{/if}

{include file="footer.tpl"}