{include file="header.tpl"}

{include file="form_errors.tpl"}
{include file="inline_message.tpl"}

<form method="post" action="{wikiurl page="`$pageNameUnique`" op="new"}" enctype="multipart/form-data">
<fieldset>
<legend>{$lang.uploads_new}</legend>
<span class="light-grey">{$lang.uploads_new_desc}</span><br /><br />

{$lang.uploads_new_file}<br />
<input type="file" size="50" name="upload" /><br /><br />

{$lang.uploads_new_file_desc}<br />
<textarea rows="3" cols="30" name="upload_desc">{$valUploadDesc}</textarea><br  /><br />

{$lang.uploads_new_user}<br />
<input type="text" size="50" name="upload_user" value="{$valUploadUser}" /><br /><br />

<input type="submit" value="{$lang.uploads_new_submit}" />
</fieldset>
</form>

{include file="footer.tpl"}