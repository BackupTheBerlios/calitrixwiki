{include file="admin_header.tpl"}

{include file="form_errors.tpl"}
{include file="inline_message.tpl"}

<form method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminNamespaces"}">
<fieldset>
<legend>{$lang.admin_namespace_add}</legend>
<span class="light-grey">{$lang.admin_namespace_add_desc}</span><br /><br />

{$lang.admin_add_namespace}<br />
<input type="text" size="30" name="namespace" /><br /><br />

<input type="submit" value="{$lang.admin_namespace_submit}" />
</fieldset>
</form>

<br /><br />

<table cellspacing="0" cellpadding="0" border="0" width="100%">
 <tr>
  <td class="td-head">{$lang.admin_namespace_name}</td>
  <td class="td-head" colspan="2">{$lang.admin_namespace_count}</td>
 </tr>
 {foreach from="$cfgNamespaces" key="cfgNamespace" item="pageCount"}
 <tr>
  <td class="td-first">{$cfgNamespace}</td>
  <td class="td-cell" width="33%">{$pageCount}</td>
  <td class="td-last">
   <a href="{wikiurl page="`$cfg.special_namespace`:AdminNamespaces" op="del" nspace="`$cfgNamespace`"}">{$lang.admin_delete}</a>
  </td>
 </tr>
 {/foreach}
</table>

{include file="admin_footer.tpl"}