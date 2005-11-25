{include file="header.tpl"}

{include file="inline_message.tpl"}

<table cellspacing="0" cellpadding="0">
 <tr>
  <td class="td-box" width="70%">
   {if $file.is_image}<img src="{$cfg.url_root}/uploads/img/{$file.file_id}.{$file.file_ext}" alt="{$file.file_description}" /><br />
   {else}<img src="{$urlRoot}/themes/cw10b2/images/mimetypes/{$file.file_ext}.png" alt="" />{/if}
  </td>
  <td style="border-left:1px #efefef solid" valign="top" width="30%">
   {$lang.uploads_view_name}: {$file.file_orig_name}<br />
   {$lang.uploads_view_size}: {$file.file_size}<br />
   {$lang.uploads_view_type}: {$file.file_ext}<br />
   {$lang.uploads_view_version}: {$file.file_version}<br />
   <a href="{wikiurl page="`$pageNameUnique`" op="file" o="download" fid="`$file.file_id`"}">{$lang.uploads_view_download}</a></td>
 </tr>
 {if $file.file_description != ''}<tr>
  <td class="td-box" style="text-align:left" colspan="2">{$file.file_description}</td>
 </tr>{/if}
</table>

<h2>{$lang.uploads_view_versions}</h2>

<table cellpadding="0" cellspacing="0">
 <tr>
  <td class="td-head">{$lang.uploads_view_date}</td>
  <td class="td-head" colspan="2">{$lang.uploads_view_from}</td>
 </tr>
 {foreach from="$versions" item="version"}
 <tr>
  <td class="td-first"><a href="{wikiurl page="`$pageNameUnique`" op="file" fid="`$file.file_id`" v="`$version.file_version`"}">{$version.file_upload_time}</a> {$lang.uploads_view_version} {$version.file_version}</td>
  <td class="td-cell">
   {if $version.user_name != ''}<a href="{wikiurl page="`$cfg.users_namespace`:`$version.user_name_raw`"}">{$version.user_name}</a>{/if}
   {if $version.file_description != ''}<br /> <span class="light-grey">{$version.file_description}</span>{/if}
  </td>
  <td class="td-last">{if $canUpload && $version.file_version != $file.file_version}<a href="{wikiurl page="`$pageNameUnique`" op="file" o="restore" fid="`$file.file_id`" v="`$version.file_version`"}">{$lang.uploads_view_restore}</a>{if $canMUploads} | <a href="{wikiurl page="`$pageNameUnique`" op="file" o="remove" fid="`$file.file_id`" v="`$version.file_version`"}">{$lang.uploads_view_remove}</a>{/if}{/if}</td>
 </tr>
 {/foreach}
</table>

{include file="footer.tpl"}