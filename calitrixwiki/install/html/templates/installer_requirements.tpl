{include file="header.tpl"}
<p>
{$lang.req_desc}
</p>

<table cellspacing="0" cellpadding="0" border="0" class="inner-table">
{section name="idx" loop="$requirements"}
 <tr>
  <td class="td-first">{$requirements[idx].name}</td>
  <td class="td-last">{if $requirements[idx].ok}<span class="req-ok">{$lang.ok}</span>{else}<span class="req-failed">{$lang.failed}</span>{/if}</td>
 </tr>
{/section}
</table>

<p>
{$lang.req_desc2}
</p>

<form method="get" action="{wikiurl}">
<fieldset>
<legend>{$lang.req_lang_and_type}</legend>
{$lang.req_language}<br />
<select name="lang">
{foreach from="$languages" key="langCode" item="langName"}
<option value="{$langCode}">{$langName}</option>
{/foreach}
</select><br /><br />

{$lang.req_installation_type}<br />
<select name="step">
<option value="license" selected="selected">{$lang.req_install_new}</option>
<option value="updateb1">{$lang.req_update_beta1}</option>
<option value="updateb2">{$lang.req_update_beta2}</option>
</select><br /><br />

<input type="submit" value="{$lang.next}" />
</fieldset>
</form>
{include file="footer.tpl"}