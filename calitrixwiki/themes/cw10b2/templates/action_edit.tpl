{include file="header.tpl"}

{include file="form_errors.tpl"}
{include file="inline_message.tpl"}

<form method="post" action="{wikiurl page="`$pageNameUnique`" action="edit"}">
<input type="hidden" name="edit_start" value="{$editStart}" />
<textarea name="page_text" rows="30" cols="60">{$editText}</textarea>
{if $allowSubmit}<br /><br />

<fieldset>
<legend>{$lang.edit_info}</legend>
{$lang.edit_author}<br />
<input type="text" name="page_author" size="25" value="{$valAuthor}" /><br /><br />

{$lang.edit_summary}<br />
<input type="text" name="page_summary" size="40" value="{$valSummary}" /><br /><br />

<input type="submit" value="{$lang.edit_submit}" name="save" accesskey="s" title="(Alt + S)" />&nbsp;&nbsp;<input type="submit" value="{$lang.edit_preview}" name="preview" accesskey="p" title="(Alt + P)" />
{/if}
</fieldset>
</form>

{if $isPreview}
<h1>{$lang.edit_previewing}</h1>
{$previewText}
{/if}

{if $isConflict}
<table cellspacing="0" cellpadding="0" border="0">
 <tr>
  <td width="50%" class="td-head">{$lang.history_original}</td>
  <td width="50%" class="td-head">{$lang.history_final}</td>
 </tr>
 {section name="idx" loop="$diffFinal"}
 <tr>
  <td class="td-first">
   <span style="font-family:monospace">{$diffOrig[idx]}&nbsp;</span>
  </td>
  <td class="td-last">
  <span style="font-family:monospace">
  {if $diffFinal[idx].type == 'edit'}
  <span style="color:orange">{$diffFinal[idx].line}&nbsp;</span>
  {elseif $diffFinal[idx].type == 'add'}
  <span style="color:green">{$diffFinal[idx].line}&nbsp;</span>
  {elseif $diffFinal[idx].type == 'subs'}
  <span style="color:red">{$diffFinal[idx].line}&nbsp;</span>
  {else}
  {$diffFinal[idx].line}&nbsp;
  {/if}
  </span>
  </td>
 </tr>
 {/section}
</table>

<h3>{$lang.history_info}</h3>
{$lang.history_color_info}<br /><br />
<span style="color:orange">{$lang.history_edited}</span><br />
<span style="color:green">{$lang.history_addition}</span><br />
<span style="color:red">{$lang.history_substraction}</span><br />
{$lang.history_no_change}
{/if}

{include file="page_cmds.tpl"}

{include file="footer.tpl"}