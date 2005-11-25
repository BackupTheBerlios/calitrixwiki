{include file="header.tpl"}

{include file="inline_message.tpl"}

<ul>
 <li><a href="{wikiurl page="`$pageNameUnique`" op="new"}">{$lang.uploads_new}</a></li>
</ul>

<table cellpadding="0" cellspacing="0">
 <tr>
  <td colspan="2" class="td-head">{$lang.uploads_file}</td>
  <td class="td-head">{$lang.uploads_size}</td>
  <td class="td-head">{$lang.uploads_uploader}</td>
 </tr>
{section name="idx" loop="$files"}
 <tr>
  <td style="width:32px" class="td-first"><img src="{$urlRoot}/themes/cw10b2/images/mimetypes/{$files[idx].file_ext}.png" alt="" /></td>
  <td class="td-cell">
   <a href="{wikiurl page="`$pageNameUnique`" op="file" fid="`$files[idx].file_id`"}">{$files[idx].file_orig_name}</a><br />
   <span class="light-grey">{$files[idx].file_description}</span>
  </td>
  <td class="td-cell">{$files[idx].file_size}</td>
  <td class="td-last">
   {$files[idx].file_upload_time}<br />
   {if $files[idx].user_name != ''}<a href="{wikiurl page="`$cfg.users_namespace`:`$files[idx].user_name_raw`"}">{$files[idx].user_name}</a>{/if}
  </td>
 </tr>
{/section}
</table>

{include file="footer.tpl"}