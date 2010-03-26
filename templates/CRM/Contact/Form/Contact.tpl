{*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2010                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
{* This form is for Contact Add/Edit interface *}
<div class="crm-form-block crm-search-form-block">
{if $addBlock}
{include file="CRM/Contact/Form/Edit/$blockName.tpl"}
{else}
<span style="float:right;"><a href="#expand" id="expand">{ts}Expand all tabs{/ts}</a></span>
<div class="crm-submit-buttons">
   {include file="CRM/common/formButtons.tpl"}
</div>
<div class="crm-accordion-wrapper crm-contactDetails-accordion crm-accordion-open">
 <div class="crm-accordion-header">
  <div class="icon crm-accordion-pointer"></div> 
	{ts}Contact Details{/ts}
	
 </div><!-- /.crm-accordion-header -->
 <div class="crm-accordion-body" id="contactDetails">
    <div id="contactDetails">
        {include file="CRM/Contact/Form/Edit/$contactType.tpl"}
        <br/>
        <table class="form-layout-compressed">
            {foreach from=$blocks item="label" key="block"}
               {include file="CRM/Contact/Form/Edit/$block.tpl"}
            {/foreach}
		</table>
		<table class="form-layout-compressed">
            <tr class="last-row">
              <td>{$form.contact_source.label}<br />
                  {$form.contact_source.html}
              </td>
              <td>{$form.external_identifier.label}<br />
                  {$form.external_identifier.html}
              </td>
              {if $contactId}
				<td><label for="internal_identifier">Internal Id</label><br />{$contactId}</td>
			  {/if}
            </tr>            
        </table>

        {*  add dupe buttons *}
        <span class="crm-button crm-button_qf_Contact_refresh_dedupe">
            {$form._qf_Contact_refresh_dedupe.html}
        </span>
        {if $isDuplicate}
            &nbsp;&nbsp;
            <span class="crm-button crm-button_qf_Contact_upload_duplicate">
                {$form._qf_Contact_upload_duplicate.html}
            </span>
        {/if}
        <div class="spacer"></div>
 </div><!-- /.crm-accordion-body -->
</div><!-- /.crm-accordion-wrapper -->
    
    {foreach from = $editOptions item = "title" key="name"}
        {include file="CRM/Contact/Form/Edit/$name.tpl"}
    {/foreach}
</div>
<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl"}
</div>

{literal}
<script type="text/javascript" >
var action = "{/literal}{$action}{literal}";
showTab[0] = {"spanShow":"span#contact","divShow":"div#contactDetails"};
cj(function( ) {
    cj('.accordion .head').addClass( "ui-accordion-header ui-helper-reset ui-state-default ui-corner-all");

    cj('.accordion .head').hover( function( ) { 
        cj(this).addClass( "ui-state-hover");
    }, function() { 
        cj(this).removeClass( "ui-state-hover");
    }).bind('click', function( ) { 
        var checkClass = cj(this).find('span').attr( 'class' );
        var len        = checkClass.length;
        if ( checkClass.substring( len - 1, len ) == 's' ) {
            cj(this).find('span').removeClass( ).addClass('ui-icon ui-icon-triangle-1-e');
        } else {
            cj(this).find('span').removeClass( ).addClass('ui-icon ui-icon-triangle-1-s');
        }
        cj(this).next( ).toggle(); return false; 
    }).next( ).hide( );
    
    cj(showTab).each( function(){ 
        if( this.spanShow ) {
            cj(this.spanShow).removeClass( ).addClass('ui-icon ui-icon-triangle-1-s');
            cj(this.divShow).show( );
        }
    });
	cj('div.accordion div.ui-accordion-content').each( function() {
		//remove tab which doesn't have any element
		if ( ! cj.trim( cj(this).text() ) ) { 
			ele     = cj(this);
			prevEle = cj(this).prev();
			cj( ele ).remove();
			cj( prevEle).remove();
		}
		//open tab if form rule throws error
		if ( cj(this).children().find('span.error').text() ) {
			cj(this).show().prev().children('span:first').removeClass( ).addClass('ui-icon ui-icon-triangle-1-s');
		}
	});

	if ( action == 2 ) {
		//highlight the tab having data inside.
		cj('.crm-accordion-body :input').each( function() { 
			var element = cj(this).closest(".crm-accordion-body").attr("id");
			if (element) {
			eval('var ' + element + ' = "";');
			switch( cj(this).attr('type') ) {
			case 'checkbox':
			case 'radio':
			  if( cj(this).is(':checked') ) {
			    eval( element + ' = true;'); 
			  }
			  break;
			  
			case 'text':
			case 'textarea':
			  if( cj(this).val() ) {
			    eval( element + ' = true;');
			  }
			  break;
			  
			case 'select-one':
			case 'select-multiple':
			  if( cj('select option:selected' ) && cj(this).val() ) {
			    eval( element + ' = true;');
			  }
			  break;		
			  
			case 'file':
			  if( cj(this).next().html() ) eval( element + ' = true;');
			  break;
			}
			if( eval( element + ';') ) { 
			  cj(this).closest(".crm-accordion-wrapper").addClass('crm-accordion-hasContent');
			}
		   }
		});
	}
});

cj('a#expand').click( function( ){
    if( cj(this).attr('href') == '#expand') {   
        var message     = {/literal}"{ts}Collapse all tabs{/ts}"{literal};
        cj(this).attr('href', '#collapse');
        cj('.crm-accordion-closed').removeClass('crm-accordion-closed').addClass('crm-accordion-open');
    } else {
        var message     = {/literal}{ts}"Expand all tabs"{/ts}{literal};
        cj('.crm-accordion-open').removeClass('crm-accordion-open').addClass('crm-accordion-closed');
        cj(this).attr('href', '#expand');
    }
    cj(this).html(message);
});

//current employer default setting
var employerId = "{/literal}{$currentEmployer}{literal}";
if ( employerId ) {
    var dataUrl = "{/literal}{crmURL p='civicrm/ajax/contactlist' h=0 q="org=1&id=" }{literal}" + employerId ;
    cj.ajax({ 
        url     : dataUrl,   
        async   : false,
        success : function(html){
            //fixme for showing address in div
            htmlText = html.split( '|' , 2);
            cj('input#current_employer').val(htmlText[0]);
            cj('input#current_employer_id').val(htmlText[1]);
        }
    }); 
}

cj("input#current_employer").click( function( ) {
    cj("input#current_employer_id").val('');
});

function showHideSignature( blockId ) {
    cj('#Email_Signature_' + blockId ).toggle( );   
}

 {/literal}
   buildCustomData( '{$contactType}' );
   {if $contactSubType}
   buildCustomData( '{$contactType}', '{$contactSubType}' );
   {/if}
 {literal}
 
</script>
{/literal}

{* include common additional blocks tpl *}
{include file="CRM/common/additionalBlocks.tpl"}

{* include jscript to warn if unsaved form field changes *}
{include file="CRM/common/formNavigate.tpl"}

{/if}

</div>