{include file="header.tpl"}

<p>
{$lang.updateb2_desc}
</p>


<form method="post" action="{wikiurl step="updateb2" lang="`$currentLang`"}">
<fieldset>
<legend>{$lang.updateb2_paths}</legend>
{$lang.updateb2_set_dir}<br />
<input type="text" name="set_dir" value="{$valSetDir}" size="40" /><br /><br />

<input type="submit" value="{$lang.updateb2_submit}" />
</fieldset>

{if $isError}
<strong>{$lang.updateb2_error}:</strong><br />
{$error}
{/if}

{if $updated}
<strong>{$lang.updateb2_success}:</strong><br />
{$lang.updateb2_success_desc}
{/if}

{include file="footer.tpl"}