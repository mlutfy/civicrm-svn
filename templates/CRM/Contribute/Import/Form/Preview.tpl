{* Contribution Import Wizard - Step 3 (preview import results prior to actual data loading) *}
{* @var $form Contains the array for the form elements and other form associated information assigned to the template by the controller *}

 {* WizardHeader.tpl provides visual display of steps thru the wizard as well as title for current step *}
 {include file="CRM/common/WizardHeader.tpl"}
 
 <div id="help">
    <p>
    {ts}The information below previews the results of importing your data in CiviCRM. Review the totals to ensure that they represent your expected results.{/ts}         
    </p>
    
    {if $invalidRowCount}
        <p class="error">
        {ts 1=$invalidRowCount 2=$downloadErrorRecordsUrl}CiviCRM has detected invalid data or formatting errors in %1 records. If you continue, these records will be skipped. OR, you can download a file with just these problem records - <a href='%2'>Download Errors</a>. Then correct them in the original import file, cancel this import and begin again at step 1.{/ts}
        </p>
    {/if}

    {if $conflictRowCount}
        <p class="error">
        {ts 1=$conflictRowCount 2=$downloadConflictRecordsUrl}CiviCRM has detected %1 records with conflicting transaction ids within this data file. If you continue, these records will be skipped. OR, you can download a file with just these problem records - <a href='%2'>Download Conflicts</a>. Then correct them in the original import file, cancel this import and begin again at step 1.{/ts}
        </p>
    {/if}
    

    <p>{ts}Click 'Import Now' if you are ready to proceed.{/ts}</p>
 </div>
    
 {* Summary Preview (record counts) *}
 <table id="preview-counts" class="report">
    <tr><td class="label">{ts}Total Rows{/ts}</td>
        <td class="data">{$totalRowCount}</td>
        <td class="explanation">{ts}Total rows (contribution records) in uploaded file.{/ts}</td>
    </tr>
    
    {if $invalidRowCount}
    <tr class="error"><td class="label">{ts}Rows with Errors{/ts}</td>
        <td class="data">{$invalidRowCount}</td>
        <td class="explanation">{ts}Rows with invalid data in one or more fields. These rows will be skipped (not imported).{/ts}
            {if $invalidRowCount}
                <p><a href="{$downloadErrorRecordsUrl}">{ts}Download Errors{/ts}</a></p>
            {/if}
        </td>
    </tr>
    {/if}
    
    {if $conflictRowCount}
    <tr class="error"><td class="label">{ts}Conflicting Rows{/ts}</td>
        <td class="data">{$conflictRowCount}</td>
        <td class="explanation">{ts}Rows with conflicting transaction ids within this file. These rows will be skipped (not imported).{/ts}
            {if $conflictRowCount}
                <p><a href="{$downloadConflictRecordsUrl}">{ts}Download Conflicts{/ts}</a></p>
            {/if}
        </td>
    </tr>
    {/if}

    <tr><td class="label">{ts}Valid Rows{/ts}</td>
        <td class="data">{$validRowCount}</td>
        <td class="explanation">{ts}Total rows to be imported.{/ts}</td>
    </tr>
 </table>
  

 {* Table for mapping preview *}
 {include file="CRM/Contribute/Import/Form/MapTable.tpl"}
 
 
 <div id="crm-submit-buttons">
    {$form.buttons.html}
 </div>
 
