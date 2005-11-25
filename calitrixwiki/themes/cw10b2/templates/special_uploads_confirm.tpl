{include file="header.tpl"}

{include file="form_errors.tpl"}
{include file="inline_message.tpl"}

<form method="post" action="{wikiurl page="`$pageNameUnique`" op="new" confirm="1"}">
<input type="hidden" name="file_local" value="{$fileLocalName}" />
<input type="hidden" name="file_orig"  value="{$fileName}" />
<input type="hidden" name="upload_user"  value="{$fileUser}" />
<input type="hidden" name="upload_desc"  value="{$fileDesc}" />

<fieldset>
<legend>{$lang.uploads_confirm}</legend>
<span class="light-grey">{$lang.uploads_confirm_desc}</span><br /><br />

<input type="radio" name="confirm_do" value="overwrite" id="confirm-overwrite" checked="checked" /><label for="confirm-overwrite">{$lang.uploads_confirm_overwrite}</label><br /><br />
<input type="radio" name="confirm_do" value="rename" id="confirm-rename" /><label for="confirm-rename">{$lang.uploads_confirm_rename}</label> <input type="text" name="new_name" size="20" /><br /><br />

<input type="submit" value="{$lang.uploads_confirm_submit}" />
</fieldset>
</form>

{include file="footer.tpl"}