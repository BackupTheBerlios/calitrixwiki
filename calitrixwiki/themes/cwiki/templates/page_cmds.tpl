 <div id="page-cmds">
  <a href="{wikiurl page="`$pageName`" action="edit"}">{if $pageId > 0 && !$canEdit}{$lang.wiki_view_source}{else}{$lang.edit_page}{/if}</a>
  {if $pageId > 0}
  {if $canHistory}| <a href="{wikiurl page="`$pageName`" action="history"}">{$lang.history}</a>{/if}
  | <a href="{wikiurl page="`$pageName`" action="print"}">{$lang.print_page}</a>
  {if $loggedIn} | <a href="{wikiurl page="`$pageName`" action="bookmark"}">{$lang.wiki_bookmark_page}</a>{/if}
  {if $canSetLocal} | <a href="{wikiurl page="`$pageName`" action="perms"}">{$lang.wiki_edit_perms}</a>{/if}<br />
  {$lang.last_modified}: {$lastModified} {$lang.page_version} {$pageVersion}{/if}
 </div>