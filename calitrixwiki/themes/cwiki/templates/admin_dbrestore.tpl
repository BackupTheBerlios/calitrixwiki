{include file="admin_header.tpl"}

{include file="inline_message.tpl"}

<form enctype="multipart/form-data" method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminDbRestore"}">
<fieldset>
<legend>{$lang.admin_db_restore_tables}</legend>
<span class="light-grey">{$lang.admin_db_restore_desc}</span><br /><br />

<input type="file" name="upload_file" size="40" /><br /><br />

<input type="submit" value="{$lang.admin_db_restore_submit}" />
</fieldset>
</form>

{include file="admin_footer.tpl"}