{include file="admin_header.tpl"}

{include file="inline_message.tpl"}

<table cellspacing="0" cellpadding="0" border="0" width="100%">
 <tr>
  <td class="td-head">{$lang.admin_theme_name}</td>
  <td class="td-head" colspan="2">{$lang.admin_theme_dir}</td>
 </tr>
 {foreach from="$cfgThemes" key="cfgThemeDir" item="cfgThemeData"}
 <tr>
  <td class="td-first">{$cfgThemeData.name}{if $cfgThemeDir == $cfg.default_theme} <span class="light-grey">({$lang.admin_default})</span>{/if}</td>
  <td class="td-cell">{$cfgThemeDir}</td>
  <td class="td-last">
   {if $cfgThemeData.installed}
    <a href="{wikiurl page="`$cfg.special_namespace`:AdminThemes" op="del" tdir="`$cfgThemeDir`"}">{$lang.admin_delete}</a>
   {else}
    <a href="{wikiurl page="`$cfg.special_namespace`:AdminThemes" op="inst" tdir="`$cfgThemeDir`"}">{$lang.admin_install}</a>
   {/if}
   </td>
 </tr>
 {/foreach}
</table>
{include file="admin_footer.tpl"}