{include file="header.tpl"}

<table cellspacing="0" cellpadding="5" border="0" align="center">
 <tr>
  {foreach from="$chars" key="char" item="enabled"}
  <td>{if $enabled == 1}<a href="#{$char}">{$char}</a>{else}<span class="light-grey">{$char}</span>{/if}</td>
  {/foreach}
 </tr>
</table>

{foreach from="$sitemap" key="char" item="pages"}
<a name="{$char}"></a><h1>{$char}</h1>
<ul class="siteMap">
{section name="idx" loop="$pages"}
<li><a href="{wikiurl page="`$pages[idx]`"}" class="wiki-internal">{$pages[idx]}</a></li>
{/section}
</ul>
{/foreach}

{include file="footer.tpl"}