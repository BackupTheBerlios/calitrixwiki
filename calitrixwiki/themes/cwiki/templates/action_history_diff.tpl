{include file="header.tpl"}

<table cellspacing="0" cellpadding="0" border="0" width="100%">
 <tr>
  <td width="50%" class="td-head">{$lang.history_original}</td>
  <td width="50%" class="td-head">{$lang.history_final}</td>
 </tr>
 {section name="idx" loop="$diff_final"}
 <tr>
  <td class="td-first">
   <span style="font-family:monospace">{$diff_orig[idx]}</span>
  </td>
  <td class="td-last">
  <span style="font-family:monospace">
  {if $diff_final[idx].type == 'edit'}
  <span style="color:orange">{$diff_final[idx].line}</span>
  {elseif $diff_final[idx].type == 'add'}
  <span style="color:green">{$diff_final[idx].line}</span>
  {elseif $diff_final[idx].type == 'subs'}
  <span style="color:red">{$diff_final[idx].line}</span>
  {else}
  {$diff_final[idx].line}
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

{include file="page_cmds.tpl"}

{include file="footer.tpl"}