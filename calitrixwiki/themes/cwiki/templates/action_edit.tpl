{include file="header.tpl"}

{include file="inline_message.tpl"}

<form method="post" action="{wikiurl page="`$pageName`" action="edit"}">
<textarea name="page_text" rows="30" cols="60" wrap="physical" style="width:100%">{$editText}</textarea>
{if $allowSubmit}<br /><br />
<div style="float:left;margin-right:10px">
{$lang.edit_author}<br />
<input type="text" name="page_author" size="25" value="{if $loggedIn}{$user.user_name}{/if}" /><br /><br />
</div>
<div>{$lang.edit_summary}<br />
<input type="text" name="page_summary" size="40" /><br /><br />
</div>
<div style="clear:left">
<input type="submit" value="{$lang.edit_submit}" name="save" accesskey="s" title="(Alt + S)" />&nbsp;&nbsp;<input type="submit" value="{$lang.edit_preview}" name="preview" accesskey="p" title="(Alt + P)" />
</div>{/if}
</form>

{if $isPreview}
<h1>{$lang.edit_previewing}</h1>
{$previewText}
{/if}

{include file="page_cmds.tpl"}

{include file="footer.tpl"}