{* Export Wizard - Step 2 *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
{include file="CRM/WizardHeader.tpl"}

<div id="help">
{ts}
<p><strong>Export ALL contact fields</strong> to export all available data values (including custom fields).</p>
<p>Click <strong>Select fields for export</strong> and then <strong>Continue</strong> to choose a subset of fields
for export. This option also allows you to save your selections as a 'field mapping' so you can use it again later.</p>
{/ts}
</div>

<div id="export-type" class="form-item">
 <fieldset>
    <dl>
        <dd>
         {ts count=$totalSelectedContacts plural='%count records selected for export.'}One record selected for export.{/ts}
        </dd> 
        <dd>{$form.exportOption.html}</dd>
    </dl>
 </fieldset>
</div>
<div id="crm-submit-buttons">
    {$form.buttons.html}
</div>
