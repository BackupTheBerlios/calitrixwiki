<div style="margin-top:10px;margin-bottom:10px;text-align:right">
{$lang.wiki_pages}: <a href="{$firstPage}" class="page-link">{$lang.wiki_first}</a> 
{section name="idx" loop="$pageLinks"}
{$pageLinks[idx]}
{/section}
<a href="{$lastPage}" class="page-link">{$lang.wiki_last}</a>
</div>