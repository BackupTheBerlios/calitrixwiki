 <div id="page-cmds">
  <a href="{wikiurl page="`$pageName`" action="edit"}" class="wiki-internal">{if $pageId > 0 && !$canEdit}{$lang.wiki_view_source}{else}{$lang.edit_page}{/if}</a>
  {if $pageId > 0}
  {if $canHistory}| <a href="{wikiurl page="`$pageName`" action="history"}" class="wiki-internal">{$lang.history}</a>{/if}
  | <a href="{wikiurl page="`$pageName`" action="print"}">{$lang.print_page}</a>
  {if $loggedIn} | <a href="{wikiurl page="`$pageName`" action="bookmark"}" class="wiki-internal">{$lang.wiki_bookmark_page}</a>{/if}
  {if $canSetLocal} | <a href="{wikiurl page="`$pageName`" action="perms"}" class="wiki-internal">{$lang.wiki_edit_perms}</a>{/if}
  {if $canRename || $canDelete} | <a href="{wikiurl page="`$pageName`" action="options"}" class="wiki-internal">{$lang.options}</a>{/if}<br />
  {$lang.last_modified}: {$lastModified} {$lang.page_version} {$pageVersion}{/if}
 </div>