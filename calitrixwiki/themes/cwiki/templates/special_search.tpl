{include file="header.tpl"}

<form method="post" action="{wikiurl page="`$cfg.special_namespace`:Search"}">
<input type="text" size="40" maxlength="512" name="q">
{if $sessionId != ''}<input type="hidden" name="s" value="{$sessionId}">{/if}
<input type="submit" value="{$lang.wiki_search_submit}">
</form>
<br />

{if $search}
{include file="search_results.tpl"}
{/if}

{include file="footer.tpl"}