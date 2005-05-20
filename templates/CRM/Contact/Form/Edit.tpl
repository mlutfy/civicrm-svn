{* This file provides the HTML for the big add contact form *}
{* It provides the templating for Name, Demographics and Contact notes *}
{* The templating for Location and Communication preferences block has been plugged by including the Location.tpl file *}    

{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* Including the javascript source code from the Individual.js *}
 <script type="text/javascript" src="{$config->resourceBase}js/Individual.js"></script>

{if $contact_type eq 'Individual'}
 <div id="name">
 <fieldset><legend>Name and Greeting</legend>
    <div class="form-item">
        <span class="labels"><label>First/Last:</label></span>
        <span class="fields">
            {$form.prefix.html}
            {$form.first_name.html}
            {$form.last_name.html}
            {$form.suffix.html}
        </span>
    </div>
    
    <div class="form-item">
        <span class="labels">
        {$form.greeting_type.label}
        </span>
        <span class="fields">
        {$form.greeting_type.html}
        </span>
    </div>

    <div class="form-item">
        <span class="labels">
        {$form.job_title.label}
        </span>
        <span class="fields">
        {$form.job_title.html}
        </span>
    </div>
    <!-- Spacer div forces fieldset to contain floated elements -->
    <div class="spacer"></div>
 </fieldset>
 </div>
{elseif $contact_type eq 'Household'}
<div id="name">
 <fieldset><legend>Household</legend>
    <!-- <div class="spacer"></div> -->

    <div class="form-item">
        <span class="labels"><label>{$form.household_name.label}</label></span>
        <span class="fields">
            {$form.household_name.html}
        </span>
    </div>

    <div class="form-item">
        <span class="labels"><label>{$form.nick_name.label}</label></span>
        <span class="fields">
            {$form.nick_name.html}
        </span>
    </div>

    <!-- Spacer div forces fieldset to contain floated elements -->
    <div class="spacer"></div>
 </fieldset>
 </div>
{elseif $contact_type eq 'Organization'}
<div id="name">
 <fieldset><legend>Organization</legend>
    <!-- <div class="spacer"></div> -->
    <div class="form-item">
        <span class="labels"><label>{$form.organization_name.label}</label></span>
        <span class="fields">
            {$form.organization_name.html}
        </span>
    </div>

    <div class="form-item">
        <span class="labels"><label>{$form.legal_name.label}</label></span>
        <span class="fields">
            {$form.legal_name.html}
        </span>
    </div>

    <div class="form-item">
        <span class="labels"><label>{$form.nick_name.label}</label></span>
        <span class="fields">
            {$form.nick_name.html}
        </span>
    </div>

    <div class="form-item">
        <span class="labels"><label>{$form.sic_code.label}</label></span>
        <span class="fields">
            {$form.sic_code.html}
        </span>
    </div>
</fieldset>
{/if}

{* Plugging the Communication preferences block *} 
 {include file="CRM/Contact/Form/CommPrefs.tpl"}
 
{* Plugging the Location block *}
 {include file="CRM/Contact/Form/Location.tpl"}

{if $contact_type eq 'Individual'}
 <div id = "demographics[show]" class="data-group">
    {$demographics.show}<label>Demographics</label>
 </div>

 <div id="demographics">
 <fieldset><legend>{$demographics.hide}Demographics</legend>
    <div class="form-item">
        <span class="labels">
        {$form.gender.label}
        </span>
        <span class="fields">
        {$form.gender.html}
        </span>
    </div>
	<div class="form-item">
        <span class="labels">
        {$form.birth_date.label}
        </span>
        <span class="fields">
		{$form.birth_date.html}
        </span>
    </div>
	<div class="form-item">
        {$form.is_deceased.html}
        {$form.is_deceased.label}
    </div>
  </fieldset>
 </div>
{/if}  

 {******************************** ENDING THE DEMOGRAPHICS SECTION **************************************}

 {* Notes block only included for Add Contact (since it navigates from Edit form...) *}
 {if $mode eq 1}
     <div id = "notes[show]" class="data-group">
        {$notes.show}<label>Notes</label>
     </div>

     <div id = "notes">
         <fieldset><legend>{$notes.hide}Contact Notes</legend>
            <div class="form-item">
                {$form.note.html}
            </div>
         </fieldset>
     </div>
{/if}
 <!-- End of "notes" div -->
 
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>

 <script type="text/javascript">
    var showBlocks = new Array({$showBlocks});
    var hideBlocks = new Array({$hideBlocks});

{* hide and display the appropriate blocks as directed by the php code *}
    on_load_init_blocks( showBlocks, hideBlocks );
 </script>
