{include file="header.tpl"}

{include file="special_preferences_links.tpl"}
{include file="inline_message.tpl"}
{include file="form_errors.tpl"}

<form method="post" action="{wikiurl page="`$cfg.special_namespace`:Preferences" op="bookmarks"}">
<input type="hidden" name="so" value="add" />
<fieldset><legend>{$lang.prefs_bookmarks_add}</legend>
<span class="light-grey">{$lang.prefs_bookmarks_add_desc}</span><br /><br>

{$lang.prefs_bookmarks_add_page}<br />
<input type="text" name="page_name" size="30" maxlength="70" />
&nbsp;<input type="submit" value="{$lang.prefs_bookmarks_add_submit}" />
</fieldset>
</form>

{include file="page_links.tpl"}
<form name="bookmarks" method="post" action="{wikiurl page="`$cfg.special_namespace`:Preferences" op="bookmarks"}">
<input type="hidden" name="so" value="change" />
<table cellspacing="0" cellpadding="0" border="0" width="100%">
 <tr>
  <td class="td-head">{$lang.prefs_bookmarks_page}</td>
  <td class="td-head">{$lang.prefs_bookmarks_version}</td>
  <td class="td-head">{$lang.prefs_bookmarks_last_mod}</td>
  <td class="td-head" width="1%">&nbsp;</td>
 </tr>
 {section name="idx" loop="$bookmarks"}
 <tr>
  <td class="td-first">
   {if $bookmarks[idx].mark_new}<img src="{$urlRoot}/themes/cwiki/images/new.gif" alt="" />&nbsp;&nbsp;{/if}
   <a href="{wikiurl page="`$bookmarks[idx].page_name`"}" class="wiki-internal">{$bookmarks[idx].page_name}</a></td>
  <td class="td-cell">{$bookmarks[idx].page_version}</td>
  <td class="td-cell">{$bookmarks[idx].page_last_change}</td>
  <td class="td-last"><input type="checkbox" name="pid[]" value="{$bookmarks[idx].page_id}" />
 </tr>
 {sectionelse}
 <tr>
  <td colspan="3">{$lang.prefs_bookmarks_none}</td>
 </tr>
 {/section}
</table>
<div style="float:left;margin-top:10px;margin-bottom:10px;">
<a href="javascript:void(0)" onclick="setCheckBoxes('bookmarks', 'pid[]', true)">{$lang.wiki_select_all}</a> / 
<a href="javascript:void(0)" onclick="setCheckBoxes('bookmarks', 'pid[]', false)">{$lang.wiki_deselect_all}</a>
</div>
{include file="page_links.tpl"}

{$lang.wiki_selected}: 
<select name="change_action">
<option value="del" selected="selected">{$lang.wiki_delete}</option>
<option value="delmark">{$lang.prefs_bookmarks_del_mark}</option>
</select>
&nbsp;<input type="submit" value="{$lang.prefs_bookmarks_change_submit}" />
</form>

<p><img src="{$urlRoot}/themes/cwiki/images/new.gif" alt="" />&nbsp;&nbsp;{$lang.prefs_bookmarks_info}</p>

{include file="footer.tpl"}