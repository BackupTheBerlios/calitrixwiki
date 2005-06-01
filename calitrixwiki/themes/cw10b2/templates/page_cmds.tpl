 <div id="page-cmds">
  <img src="{$urlRoot}/themes/cw10b2/images/bullet2.gif" alt="" />&nbsp;<a href="{wikiurl page="`$pageNameUnique`" action="edit"}" class="wiki-internal">{if $pageId > 0 && !$canEdit}{$lang.wiki_view_source}{else}{$lang.edit_page}{/if}</a>&nbsp;
  {if $pageId > 0}
  <img src="{$urlRoot}/themes/cw10b2/images/bullet2.gif" alt="" />&nbsp;<a href="{wikiurl page="`$pageNameUnique`" action="print"}" class="wiki-internal">{$lang.print_page}</a>&nbsp;
  {if $canHistory}<img src="{$urlRoot}/themes/cw10b2/images/bullet2.gif" alt="" />&nbsp;<a href="{wikiurl page="`$pageNameUnique`" action="history"}" class="wiki-internal">{$lang.history}</a>&nbsp;{/if}
  {if $loggedIn}<img src="{$urlRoot}/themes/cw10b2/images/bullet2.gif" alt="" />&nbsp;<a href="{wikiurl page="`$pageNameUnique`" action="bookmark"}" class="wiki-internal">{$lang.wiki_bookmark_page}</a>&nbsp;{/if}
  {if $canRename || $canDelete || $canSetLocal}<img src="{$urlRoot}/themes/cw10b2/images/bullet2.gif" alt="" />&nbsp;<a href="{wikiurl page="`$pageNameUnique`" action="options"}" class="wiki-internal">{$lang.options}</a>&nbsp;{/if}<br />
  {$lang.last_modified}: {$lastModified} {$lang.page_version} {$pageVersion}{/if}
 </div>