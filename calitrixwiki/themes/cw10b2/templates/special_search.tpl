{include file="header.tpl"}

<form method="get" action="{wikiurl page="`$cfg.special_namespace`:Search"}">
<input type="hidden" name="page" value="{$cfg.special_namespace}:Search" />
<fieldset>
<legend>{$lang.search}</legend>
<input type="text" size="50" maxlength="512" name="q" />&nbsp;<input type="submit" value="{$lang.search_submit}" /><br /><br />

<input type="radio" name="sw" value="title" id="sw-title" /><label for="sw-title">{$lang.search_title}</label><br />
<input type="radio" name="sw" value="ft" id="sw-ft" checked="checked" /><label for="sw-ft">{$lang.search_fulltext}</label>

{if $sessionId != ''}<input type="hidden" name="s" value="{$sessionId}">{/if}
</fieldset>
</form>
<br />

{if $search}{include file="search_results.tpl"}{/if}

{include file="footer.tpl"}