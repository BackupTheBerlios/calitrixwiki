{include file="admin_header.tpl"}

{include file="form_errors.tpl"}
{include file="inline_message.tpl"}

<form method="post" action="{wikiurl page="`$cfg.special_namespace`:AdminInterWiki"}">
<input type="hidden" name="old_wiki" value="{$cfgOldInterWiki}" />
<fieldset>
<legend>{$lang.admin_interwiki_add}</legend>
<span class="light-grey">{$lang.admin_interwiki_add_desc}</span><br /><br />

{$lang.admin_interwiki_add_name}<br />
<input type="text" size="30" name="interwiki_name" value="{$cfgInterWikiName}" /><br /><br />

{$lang.admin_interwiki_add_url}<br />
<input type="text" size="30" name="interwiki_url" value="{$cfgInterWikiUrl}" /><br /><br />

<input type="submit" value="{$lang.admin_interwiki_submit}" />
</fieldset>
</form>

<br /><br />

<table cellspacing="0" cellpadding="0" border="0" width="100%">
 <tr>
  <td class="td-head">{$lang.admin_interwiki_name}</td>
  <td class="td-head" colspan="2">{$lang.admin_interwiki_url}</td>
 </tr>
 {foreach from="$interWikis" key="interWikiName" item="interWikiUrl"}
 <tr>
  <td class="td-first">{$interWikiName}</td>
  <td class="td-cell">{$interWikiUrl}</td>
  <td class="td-last">
   <a href="{wikiurl page="`$cfg.special_namespace`:AdminInterWiki" op="del" wiki="`$interWikiName`"}">{$lang.admin_delete}</a> |
   <a href="{wikiurl page="`$cfg.special_namespace`:AdminInterWiki" op="edit" wiki="`$interWikiName`"}">{$lang.admin_edit}</a>
  </td>
 </tr>
 {/foreach}
</table>

{include file="admin_footer.tpl"}