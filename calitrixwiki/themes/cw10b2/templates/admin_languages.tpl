{include file="admin_header.tpl"}

{include file="inline_message.tpl"}

<table cellspacing="0" cellpadding="0" border="0" width="100%">
 <tr>
  <td class="td-head">{$lang.admin_lang_name}</td>
  <td class="td-head" colspan="2">{$lang.admin_lang_code}</td>
 </tr>
 {foreach from="$cfgLangs" key="cfgLangCode" item="cfgLangData"}
 <tr>
  <td class="td-first">{$cfgLangData.name}{if $cfgLangCode == $cfg.default_lang} <span class="light-grey">({$lang.admin_default})</span>{/if}</td>
  <td class="td-cell">{$cfgLangCode}</td>
  <td class="td-last">
   {if $cfgLangData.installed}
    <a href="{wikiurl page="`$cfg.special_namespace`:AdminLanguages" op="del" lc="`$cfgLangCode`"}">{$lang.admin_delete}</a>
   {else}
    <a href="{wikiurl page="`$cfg.special_namespace`:AdminLanguages" op="inst" lc="`$cfgLangCode`"}">{$lang.admin_install}</a>
   {/if}
   </td>
 </tr>
 {/foreach}
</table>
{include file="admin_footer.tpl"}