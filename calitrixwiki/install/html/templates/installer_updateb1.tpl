{include file="header.tpl"}

<p>
{$lang.updateb1_desc}
</p>


<form method="post" action="{wikiurl step="updateb1" lang="`$currentLang`"}">
<fieldset>
<legend>{$lang.updateb1_paths}</legend>
{$lang.updateb1_set_dir}<br />
<input type="text" name="set_dir" value="{$valSetDir}" size="40" /><br /><br />

<input type="submit" value="{$lang.updateb1_submit}" />
</fieldset>

{if $isError}
<strong>{$lang.updateb1_error}:</strong><br />
{$error}
{/if}

{if $updated}
<strong>{$lang.updateb1_success}:</strong><br />
{$lang.updateb1_success_desc}
{/if}

{include file="footer.tpl"}