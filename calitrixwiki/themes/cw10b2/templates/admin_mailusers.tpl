{include file="admin_header.tpl"}

{include file="inline_message.tpl"}

<form method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminMailUsers"}">
<fieldset>
<legend>{$lang.admin_mail_recipient}</legend>
<span class="light-grey">{$lang.admin_mail_recipient_desc}</span><br /><br />

{$lang.admin_mail_groups}<br />
<select name="rcpt_groups[]" multiple="multiple" size="5" style="width:300px">
{foreach from="$selectGroups" key="groupId" item="groupName"}
<option value="{$groupId}">{$groupName}</option>
{/foreach}
</select><br /><br />

{$lang.admin_mail_users}<br />
<input type="text" name="rcpt_users" style="width:300px;" /><br /><br />
</fieldset>

<br />

<fieldset>
<legend>{$lang.admin_mail_body}</legend>
<span class="light-grey">{$lang.admin_mail_body_desc}</span><br /><br />

{$lang.admin_mail_subject}<br />
<input type="text" name="mail_subject" size="72" /><br /><br />

<textarea name="mail_body" rows="25" cols="72"></textarea><br /><br />

<input type="submit" value="{$lang.admin_mail_submit}" />
</fieldset>
</form>

{include file="admin_footer.tpl"}