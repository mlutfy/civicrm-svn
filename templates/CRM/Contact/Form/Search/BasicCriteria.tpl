{* Search criteria form elements *}

<fieldset>
    <legend>
        {if $context EQ 'smog'}{ts}Find Members of this Group{/ts}
        {elseif $context EQ 'amtg'}{ts}Find Contacts to Add to this Group{/ts}
        {else}{ts}Search Criteria{/ts}{/if}
    </legend>
 <div class="form-item">
     <span class="horizontal-position">{$form.contact_type.label}{$form.contact_type.html}</span>
     {if $context EQ 'smog'}
        <span class="horizontal-position">
            <span class="label">{$form.cb_group_contact_status.label}</span>
            {$form.cb_group_contact_status.html}
        </span>
     {/if}
     <span class="horizontal-position">{$form.group.label}{$form.group.html}</span>
     <span class="horizontal-position">{$form.tag.label}{$form.tag.html}</span>
 </div>
 <div class="form-item">
     <span class="place-left">
     {$form.sort_name.label} &nbsp;{$form.sort_name.html}
     </span>
     <span class="align-right">{$form.buttons.html}</span>
     <div class="description font-italic">
        <span class="horizontal-position">
        {ts}Complete OR partial contact name OR email. To find individuals by first AND last name, enter 'lastname, firstname'. Example: 'Doe, Jane'.{/ts}
        </span>
     </div>

 </div>
 <div class="form-item">
     <p>
{if $context EQ 'smog'}
     <span class="element-right"><a href="{crmURL p='civicrm/group/search/advanced' q="gid=`$group.id`&reset=1&force=1"}">&raquo; {ts}Advanced Search{/ts}</a></span>
{elseif $context EQ 'amtg'}
     <span class="element-right"><a href="{crmURL p='civicrm/contact/search/advanced' q="context=amtg&amtgID=`$group.id`&reset=1&force=1"}">&raquo; {ts}Advanced Search{/ts}</a></span>
{else}
     <span class="element-right"><a href="{crmURL p='civicrm/contact/search/advanced'}">&raquo; {ts}Advanced Search{/ts}</a></span>
{/if}
     </p>
 </div>
</fieldset>
