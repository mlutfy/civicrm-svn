{* This file provides the HTML for the on-behalf-of form. Can also be used for related contact edit form. *}

<fieldset id="for_organization"><legend>{$fieldSetTitle}</legend>
{if $contact_type eq 'Individual'}

  {if $contactEditMode}<fieldset><legend></legend>{/if}
	<table class="form-layout-compressed">
    <tr>
		<td>{$form.prefix_id.label}</td>
		<td>{$form.first_name.label}</td>
		<td>{$form.middle_name.label}</td>
		<td>{$form.last_name.label}</td>
		<td>{$form.suffix_id.label}</td>
	</tr>
	<tr>
		<td>{$form.prefix_id.html}</td>
		<td>{$form.first_name.html}</td>
		<td>{$form.middle_name.html|crmReplace:class:eight}</td>
		<td>{$form.last_name.html}</td>
		<td>{$form.suffix_id.html}</td>
	</tr>
   	
    </table>
  {if $contactEditMode}</fieldset>{/if}


{elseif $contact_type eq 'Household'}

 {if $contactEditMode}<fieldset><legend></legend>{/if}
   	<table class="form-layout-compressed">
      <tr>
		<td>{$form.household_name.label}</td>
      </tr>
      <tr>
        <td>{$form.household_name.html|crmReplace:class:big}</td>
      </tr>
    </table>   
 {if $contactEditMode}</fieldset>{/if}


{elseif $contact_type eq 'Organization'}

 {if $contactEditMode}<fieldset><legend></legend>{/if}
	<table class="form-layout-compressed">
      {if $relatedOrganizationFound}
      <tr>
		<td>{$form.org_option.html}</td>
      </tr>
      <tr id="select_org">
        <td><span class="tundra" dojoType= "dojo.data.ItemFileReadStore" jsId="employerStore" url="{$employerDataURL}">
            {$form.organization_id.html|crmReplace:class:big}</span>
        </td>
      </tr>
      {/if}  
      <tr id="create_org">
		<td>{$form.organization_name.label}<br/>
            {$form.organization_name.html|crmReplace:class:big}</td>
      </tr>
    </table>
 {if $contactEditMode}</fieldset>{/if}

{/if}

{* Display the address block *}
{assign var=index value=1}

{if $contactEditMode}
  <fieldset><legend>{ts}Phone and Email{/ts}</legend>
    <table class="form-layout-compressed">
		<tr>
            <td width="25%">{$form.location.$index.phone.1.phone.label}</td>
            <td>{$form.location.$index.phone.1.phone.html}</td>
        </tr>
		<tr>
            <td>{$form.location.$index.email.1.email.label}</td>
            <td>{$form.location.$index.email.1.email.html}</td>
        </tr>
    </table>
  </fieldset>
{/if}


{if !$contactEditMode}<br/>{/if}

    {if $contactEditMode}<fieldset><legend>{ts}Address{/ts}</legend>{/if}
    <table class="form-layout-compressed">
        {if !$contactEditMode}
		<tr>
            <td>{$form.location.$index.phone.1.phone.label}</td>
            <td>{$form.location.$index.phone.1.phone.html}</td>
        </tr>
		<tr>
            <td>{$form.location.$index.email.1.email.label}</td>
            <td>{$form.location.$index.email.1.email.html}</td>
        </tr>
        {/if}
        {if $addressSequence.street_address}
        <tr>
            <td width="15%">{$form.location.$index.address.street_address.label}</td>
            <td>{$form.location.$index.address.street_address.html}    
                <br class="spacer"/>
                <span class="description font-italic">{ts}Street number, street name, apartment/unit/suite - OR P.O. box{/ts}</span>
            </td>
        </tr>
        {/if}
        {if $addressSequence.supplemental_address_1}
        <tr>
            <td>{$form.location.$index.address.supplemental_address_1.label}</td>
            <td>{$form.location.$index.address.supplemental_address_1.html}    
                <br class="spacer"/>
                <span class="description font-italic">{ts} Supplemental address info, e.g. c/o, department name, building name, etc.{/ts}</span>
            </td>
        </tr>
        {/if}
        {if $addressSequence.supplemental_address_2}
        <tr>
            <td>{$form.location.$index.address.supplemental_address_2.label}</td>
            <td>{$form.location.$index.address.supplemental_address_2.html}    
            </td>
        </tr>
        {/if}
        {if $addressSequence.city}
        <tr>
            <td>{$form.location.$index.address.city.label}</td>
            <td>{$form.location.$index.address.city.html}</td>
        </tr>
        {/if}
        {if $addressSequence.postal_code}
        <tr>
            <td>{$form.location.$index.address.postal_code.label}</td>
            <td>{$form.location.$index.address.postal_code.html}
                {if $form.location.$index.address.postal_code_suffix.html}
                     - {$form.location.$index.address.postal_code_suffix.html}    
                    <br class="spacer"/>
                    <span class="description font-italic">{ts}Enter optional 'add-on' code after the dash ('plus 4' code for U.S. addresses).{/ts}</span>
                {/if}
            </td>
        </tr>
        {/if}
        {if $addressSequenceCountry}
        <tr>
            <td><strong>{ts}Country - State{/ts}</strong></td>
            <td><div name="location[1][address][country_state]" dojoType="civicrm.HierSelect" url1="{$config->resourceBase}bin/ajax.php?return=countries" url2="{$config->resourceBase}bin/ajax.php?return=states" default1="{$countryDefault}" default2="{$stateDefault}" firstInList=true></div>{if $addressSequenceState} - <span class="tundra"><span id="id_location[1][address][country_state]_1"></span></span>{/if}
                <br class="spacer"/>
                <span class="description font-italic">
                    {ts}Type in the first few letters of the country and then select from the drop-down. After selecting a country, the State / Province field provides a choice of states or provinces in that country.{/ts}
                </span>
            </td>
        </tr>
        {/if}
        {if $contactEditMode}
        <tr>
            <td>{$form.location.$index.address.geo_code_1.label}, {$form.location.$index.address.geo_code_2.label}</td>
            <td>{$form.location.$index.address.geo_code_1.html}, {$form.location.$index.address.geo_code_2.html}    
                <br class="spacer"/>
                <span class="description font-italic">
                    {ts 1="http://wiki.civicrm.org/confluence//x/Ois" 2=$docURLTitle}Latitude and longitude may be automatically populated by enabling a Mapping Provider (<a href='%1' target='_blank' title='%2'>read more...</a>).{/ts}</span>
            </td>
        </tr>
        {/if}
    </table>

    {if $contactEditMode}</fieldset>{/if}

</fieldset>

{if $form.is_for_organization}
    {include file="CRM/common/showHideByFieldValue.tpl" 
         trigger_field_id    ="is_for_organization"
         trigger_value       ="true"
         target_element_id   ="for_organization" 
         target_element_type ="block"
         field_type          ="radio"
         invert              = "false"
    }
{/if}

{if $relatedOrganizationFound}
    {include file="CRM/common/showHideByFieldValue.tpl" 
         trigger_field_id    ="org_option"
         trigger_value       ="true"
         target_element_id   ="select_org" 
         target_element_type ="table-row"
         field_type          ="radio"
         invert              = "true"
    }
    {include file="CRM/common/showHideByFieldValue.tpl" 
         trigger_field_id    ="org_option"
         trigger_value       ="true"
         target_element_id   ="create_org" 
         target_element_type ="table-row"
         field_type          ="radio"
         invert              = "false"
    }
{/if}

{* If mid present in the url, take the required action (poping up related existing contact ..etc) *}
{if $membershipContactID}
<script type="text/javascript">
   dojo.addOnLoad( function( ) {ldelim}
   dijit.byId( 'organization_id' ).setValue("{$membershipContactID}");
   {rdelim} );
</script>
{/if}

{* Javascript method to populate the location fields when a different existing related contact is selected *}
{literal}
<script type="text/javascript">
    function loadLocationData( cid ) {
	    var dataUrl = {/literal}"{$locDataURL}"{literal};
        dataUrl = dataUrl + cid;

        var result = dojo.xhrGet({
        url: dataUrl,
        handleAs: "text",
        timeout: 5000, //Time in milliseconds

        // The LOAD function will be called on a successful response.
        load: function(response, ioArgs) {
            var fldVal = response.split(";;");
            for (var i in fldVal) {
                var elem = fldVal[i].split('::');
                if ( elem[0] == 'id_location[1][address][country_state]_0' ) {
                    var countryState = elem[1].split('-');
                    var country = countryState[0];
                    var state   = countryState[1];

                    var selector1 = dijit.byId( elem[0] );
                    var selector2 = dijit.byId( 'id_location[1][address][country_state]_1' );
    
                    selector1.store.fetch({
                        query: {},
                        onComplete: function(items, request) {
                            selector1.setValue(country);
                            selector2.store.fetch({
                                query: {id:state},
                                onComplete: function(items, request) {
                                    selector2.setValue(state);
                                }
                            });
                        }
                    });
                } else if ( elem[0] ) {
                    document.getElementById( elem[0] ).value = elem[1];
                }
            }
        },

        // The ERROR function will be called in an error case.
        error: function(response, ioArgs) {
            console.error("HTTP status code: ", ioArgs.xhr.status);
        }
     });
    }
</script>
{/literal}
