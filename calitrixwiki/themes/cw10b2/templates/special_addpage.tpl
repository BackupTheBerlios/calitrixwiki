{include file="header.tpl"}

<form method="get" action="{wikiurl page=""}">
<input type="text" size="30" maxlength="50" name="page" />
<input type="hidden" name="action" value="edit" />
{if $sessionId != ''}<input type="hidden" name="s" value="{$sessionId}" />{/if}
<input type="submit" value="{$lang.wiki_addpage_submit}" />
</form>

{include file="footer.tpl"}