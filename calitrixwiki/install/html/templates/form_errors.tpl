{if $isError}
 <div class="form-errors">
  <strong>{$lang.install_form_errors}</strong><br />
  {$lang.install_form_errors_desc}
  <ul>
  {section name="idx" loop="$errors"}
   <li>{$errors[idx]}</li>
  {/section}
  </ul>
 </div>
{/if}