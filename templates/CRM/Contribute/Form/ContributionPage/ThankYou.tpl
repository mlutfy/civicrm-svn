{* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
{include file="CRM/WizardHeader.tpl}
<div id="help">
    {ts}<p>Use this form to configure the Thank-you message and receipting options. Contributors will see
    a confirmation and thank-you page after whenever an online contribution is successfully processed.
    You provide the content and layout of the thank-you section below. You also control whether an
    electronic receipt is automatically emailed to each contributor - and can add a custom message to that
    receipt.</p>{/ts}
</div>
 
<div class="form-item">
    <fieldset><legend>{ts}Thank-you Message and Receipting{/ts}</legend>
    <dl>
    <dt>{$form.thankyou_text.label}</dt><dd>{$form.thankyou_text.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Enter text (and optional HTML layout tags) for the thank-you
    message that will appear at the top of the confirmation page. If you want to encourage contributors
    to visit another page after completing their transaction - be sure and include that link in your message.{/ts}</dd>
    <dt></dt><dd>{$form.is_email_receipt.html} {$form.is_email_receipt.label}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}Check this box if you want an electronic receipt to be sent automatically.{/ts}</dd>
    <dt>{$form.receipt_text.label}</dt><dd>{$form.receipt_text.html}
    <dt>&nbsp;</dt><dd class="description">{ts}Enter a message you want included at the beginning of emailed receipts.
    NOTE: Receipt emails are TEXT ONLY - do not include HTML tags here.{/ts}</dd>
    <dt>{$form.cc_receipt.label}</dt><dd>{$form.cc_receipt.html}</dd>
    <dt>&nbsp;</dt><dd class="description">{ts}If you want member(s) of your organization to receive a carbon copy
    of each emailed receipt, enter one or more email addresses here. Multiple email addresses should be separated
    by a comma (e.g. jane@example.org, paula@example.org).{/ts}</dd>
    <dt>{$form.bcc_receipt.label}</dt><dd>{$form.bcc_receipt.html}</dd> 
    <dt>&nbsp;</dt><dd class="description">{ts}If you want member(s) of your organization to receive a BLIND carbon copy
    of each emailed receipt, enter one or more email addresses here. Multiple email addresses should be separated
    by a comma (e.g. jane@example.org, paula@example.org).{/ts}</dd>
    </dl>
    </fieldset>
</div>
<div id="crm-submit-buttons">
    {$form.buttons.html}
</div>
