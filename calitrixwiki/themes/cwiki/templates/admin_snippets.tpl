{include file="admin_header.tpl"}

{if $isEdit}
<form method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminSnippets" edit="`$snippetName`"}">
<fieldset>
<legend>{$lang.admin_edit_snippet}</legend>
<span class="light-grey">{$snippetDesc}</span><br /><br />

<textarea rows="5" cols="50" name="snippet_code">{$snippetCode}</textarea><br /><br />

<input type="submit" value="{$lang.admin_submit_snippet}" />
</fieldset>
</form>
<br /><br />{/if}


{include file="inline_message.tpl"}

{foreach from="$codeSnippets" item="codeSnippet"}
<strong>{$codeSnippet.name}</strong> [<a href="{wikiurl page="`$cfg.special_namespace`:AdminSnippets" edit="`$codeSnippet.name`"}" class="wiki-internal">{$lang.admin_edit}</a>]<br />
{$codeSnippet.desc}
<hr />
{/foreach}

{include file="admin_footer.tpl"}