<h2>{$lang.search_results}</h2>
{include file="page_links.tpl"}
{section name="idx" loop="$searchResult"}
{$smarty.section.idx.iteration}. <a href="{wikiurl page="`$searchResult[idx].page_name_raw`"}" class="search-link">{$searchResult[idx].page_name}</a><br />
<span class="search-teaser">{$searchResult[idx].page_text}</span><br /><br />
{sectionelse}
<em>{$lang.search_no_results}</em>
{/section}
{include file="page_links.tpl"}