{include file="admin_header.tpl"}

{include file="form_errors.tpl"}
{include file="inline_message.tpl"}

<form name="editstyle" method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminWikiStyles" op="editattribs"}">
<fieldset>
<legend>{$lang.admin_style_attribs}</legend>
<span class="light-grey">{$lang.admin_style_attribs_desc}</span><br /><br />

{foreach from="$cfgAttributes" key="cfgAttribName" item="cfgAttribRegex"}
<input type="text" name="attribs[]" size="30" value="{$cfgAttribName}" /> =&gt; <input type="text" name="regexes[]" size="40" value="{$cfgAttribRegex}" /><br /><br />
{/foreach}

<input type="text" name="attribs[]" size="30" value="" /> =&gt; <input type="text" name="regexes[]" size="40" value="" /><br /><br />
<input type="text" name="attribs[]" size="30" value="" /> =&gt; <input type="text" name="regexes[]" size="40" value="" /><br /><br />
<input type="text" name="attribs[]" size="30" value="" /> =&gt; <input type="text" name="regexes[]" size="40" value="" /><br /><br />

<input type="submit" value="{$lang.admin_attribs_submit}" />
</fieldset>
</form>
{include file="admin_footer.tpl"}