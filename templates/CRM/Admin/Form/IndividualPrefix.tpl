{* this template is used for adding/editing individual title/ prefix  *}
<div class="form-item">
<fieldset><legend>{if $action eq 1}{ts}New Individual Title Option{/ts}{else}{ts}Edit Individual Title Option{/ts}{/if}</legend>
  <dl>
	<dt>{$form.name.label}</dt><dd>{$form.name.html}</dd>
	<dt>{$form.weight.label}</dt><dd>{$form.weight.html}</dd>
        <dt>{$form.is_active.label}</dt><dd>{$form.is_active.html}</dd>
        <dt></dt><dd>{$form.buttons.html}</dd>
  </dl>
</fieldset>
</div>
