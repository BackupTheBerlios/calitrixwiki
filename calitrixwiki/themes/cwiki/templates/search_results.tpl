<h2>{$lang.search_results}</h2>
{section name="idx" loop="$searchResult"}
{$smarty.section.idx.iteration}. <a href="{wikiurl page="`$searchResult[idx].page_name`"}" class="searchLink">{$searchResult[idx].page_name}</a><br />
<span class="searchTeaser">{$searchResult[idx].page_text}</span><br /><br />
{sectionelse}
<span class="wikiItalic">{$lang.search_no_results}</span>
{/section}