{if $isError}
 <div class="form-errors">
  <strong>{$lang.wiki_form_errors}</strong><br />
  {$lang.wiki_form_errors_desc}
  <ul>
  {section name="idx" loop="$errors"}
   <li>{$errors[idx]}</li>
  {/section}
  </ul>
 </div>
{/if}