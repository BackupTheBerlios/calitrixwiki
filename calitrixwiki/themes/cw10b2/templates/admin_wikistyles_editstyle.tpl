{include file="admin_header.tpl"}

{include file="form_errors.tpl"}

<form name="editstyle" method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminWikiStyles" op="`$cfgOp`" style="`$cfgStyleName`"}">
<fieldset>
<legend>{$lang.admin_wikistyle_name}</legend>
<span class="light-grey">{$lang.admin_wikistyle_name_desc}</span><br /><br />

<input type="text" name="style_name" size="30" value="{$cfgStyleName}" /><br /><br />
</fieldset>

<br />

<fieldset>
<legend>{$lang.admin_wikistyle_attribs}</legend>
<span class="light-grey">{$lang.admin_wikistyle_attribs_desc}</span><br /><br />

<textarea name="style_attribs" rows="10" cols="50">{foreach from="$cfgStyleAttribs" key="cfgAttribName" item="cfgAttrib"}
{$cfgAttribName}:{$cfgAttrib}
{/foreach}</textarea><br /><br />

<input type="submit" value="{$lang.admin_wikistyle_submit}" /> <input type="button" value="{$lang.admin_wikistyle_test}" onclick="testWikiStyle()" /><br />
</fieldset>
</form>
{include file="admin_footer.tpl"}