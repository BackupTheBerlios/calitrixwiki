<h2>{$lang.search_results}</h2>
{section name="idx" loop="$searchResult"}
{$smarty.section.idx.iteration}. <a href="{wikiurl page="`$searchResult[idx].page_name`"}" class="search-link">{$searchResult[idx].page_name}</a><br />
<span class="search-teaser">{$searchResult[idx].page_text}</span><br /><br />
{sectionelse}
<em>{$lang.search_no_results}</em>
{/section}