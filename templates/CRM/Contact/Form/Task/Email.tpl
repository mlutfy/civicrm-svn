<div class="form-item">
<fieldset>
<legend>{ts}Send an Email{/ts}</legend>
<dl>
<dt>{ts}From{/ts}</dt><dd>{$from|escape}</dd>
{if $single eq false}
<dt>{ts}Recipient(s){/ts}</dt><dd>{$to|escape}</dd>
{else}
<dt>{$form.to.label}</dt><dd>{$form.to.html}</dd>
{/if}
<dt>{$form.subject.label}</dt><dd>{$form.subject.html}</dd>
<dt>{$form.message.label}</dt><dd>{$form.message.html}</dd>
{if $single eq false}
    <dt></dt><dd>{include file="CRM/Contact/Form/Task.tpl"}</dd>
{/if}
<dt></dt><dd>{$form.buttons.html}</dd>
</fieldset>
</div>
