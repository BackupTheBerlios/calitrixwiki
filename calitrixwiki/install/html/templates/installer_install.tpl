{include file="header.tpl"}

<p>
{$lang.install_desc}
</p>

{if $isError}
<strong>{$lang.install_error}:</strong><br />
{$error}
{/if}

<div class="next-step"><a href="{wikiurl step="install" lang="`$currentLang`" idf="`$defaultPages`" start="1"}">&gt;&gt; {$lang.next}</a></div>

{include file="footer.tpl"}